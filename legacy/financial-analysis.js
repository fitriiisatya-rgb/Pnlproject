/* ============================================================================
   FINANCIAL ANALYSIS — "Financial Intelligence Dashboard" (Phase 1)
   Loaded AFTER the main dashboard script (index.php <script>), so it can read
   UNIT_DATA / PERIODS / OUTLET_KEYS / OPEX_CATEGORIES / getLineVals / fmtRp /
   flattenOpexLeaves / categorizeOpexItem / HPP_ROW_NAME_BY_SEGMENT / render()
   etc. directly as globals. This file NEVER mutates UNIT_DATA or any existing
   P&L figure -- it only reads what the existing calc engine already produced
   (rebuildStoreComputed(), rebuildKonsolidasiComputed(), dst sudah jalan lebih
   dulu di script utama). Read-only analytics, sesuai instruksi.

   Struktur file:
   1. Central config (semua ambang batas -- BUKAN angka acak, tapi juga bukan
      hasil analisis statistik rumit; ini kebijakan bisnis yang bisa
      disesuaikan owner/finance kapan saja tanpa cari-cari di banyak tempat).
   2. Calculation engine (murni fungsi, tidak menyentuh DOM).
   3. AI context builder + fetch ke ai_explain.php (interpretasi saja, bukan
      penghitung angka).
   4. Render functions (pakai ulang CSS/komponen dashboard yang sudah ada).
   ========================================================================= */

const faLine = getLineVals; // alias -- fungsi ini sudah ada di script utama, exact-name match + trim tag HTML

const FA_CONFIG = {
  warning_variance_percent: 15,
  critical_variance_percent: 30,
  // Rp10jt -- di bawah ini, variance persen besar pun diabaikan (tidak material).
  // Dituning dari Rp3jt -> Rp10jt berdasarkan validasi data real (revenue Group
  // ~Rp10-11 Miliar/bulan): pada skala itu Rp3jt (~0,03% revenue) terlalu
  // rendah, menghasilkan alert fatigue (item Rp3-9jt ikut tertandai "Critical"
  // hanya krn %-nya besar di atas basis kecil). Rp10jt (~0,1% revenue) tetap
  // menangkap SEMUA item besar yg sebelumnya lolos filter ini, sambil membuang
  // noise di kisaran Rp3-9jt.
  minimum_materiality_amount: 10000000,
  materiality_percent_of_revenue: 1.0,       // 1% dari total Pendapatan Group
  gross_margin_drop_threshold: 2.0,          // percentage points
  opex_ratio_threshold: 2.0,                 // percentage points
  payroll_ratio_threshold: 1.5,              // percentage points
  consecutive_decline_period: 3,             // bulan
  forecast_min_days_for_projection: 5,       // di bawah ini, proyeksi run-rate dianggap TIDAK ANDAL (lihat faForecast) -- confidence dipaksa LOW + tampil peringatan, bukan angka presisi-palsu
  reconciliation_tolerance_amount: 1000,     // Rp1rb -- toleransi pembulatan floating-point utk cek rekonsiliasi bridge (BUKAN toleransi bisnis, murni floating-point)
  health_score_healthy_min: 75,
  health_score_attention_min: 50,
  health_score_weights: {                    // total harus 100
    revenueTrend: 12, grossMargin: 13, opexRatio: 9, payrollRatio: 8,
    operatingMargin: 13, netMargin: 13, historicalTrend: 9, anomalies: 5,
    outletProfitability: 8, dataCompleteness: 10,
  },
};

// Kosakata status TERPUSAT dipakai di seluruh dashboard -- dokumentasi resmi
// supaya tidak ada istilah baru yg bermakna tumpang tindih ditambahkan diam2
// di kode lain. 3 skala berbeda ini SENGAJA berbeda granularitas (bukan
// inkonsistensi): Financial Health & Expense/Anomaly memang cuma perlu 3
// tingkat (cukup utk keputusan cepat), sedangkan Outlet Classification
// SENGAJA 5 kategori sesuai kebutuhan spesifik Phase 1 (STAR harus terpisah
// dari GROWTH krn beda tindakan manajemen: replikasi vs dorong margin).
const FA_STATUS_LEGEND = {
  financialHealth: ['🟢 Healthy', '🟡 Need Attention', '🔴 Critical'],
  expenseAndAnomaly: ['🟢 Normal', '🟡 Watch', '🔴 Critical'],
  // 'NO DATA' bukan tingkat performa -- dipakai HANYA saat outlet belum
  // punya data sama sekali periode ini (dikeluarkan dari skor, poin 7 QA
  // review, ditemukan lewat validasi data real: bulan berjalan bisa punya
  // total Group tapi rincian per-outlet blm terinput).
  outletClassification: ['STAR', 'GROWTH', 'STABLE', 'WATCHLIST', 'CRITICAL', 'NO DATA'],
};

/* ============================== 2. CALC ENGINE ============================== */

function faAvg(vals, idxs){
  if (!vals || !idxs || !idxs.length) return null;
  const nums = idxs.map(i=>vals[i]).filter(v=>v!=null);
  if (!nums.length) return null;
  return nums.reduce((s,v)=>s+v,0)/nums.length;
}

function faHppRowName(unitKey){ return HPP_ROW_NAME_BY_SEGMENT[unitKey] || 'HPP'; }

// Tooltip ringan, native (title attribute) -- tak perlu library baru, cukup
// aksesibel, dipakai di header2 istilah yg berpotensi membingungkan
// management non-akuntan (MTD, Profit Driver, Materiality, dst -- poin 23).
function faInfo(text){
  return `<span title="${faEsc(text)}" style="cursor:help;color:#726C9C;border:1px solid #726C9C;border-radius:50%;width:14px;height:14px;display:inline-flex;align-items:center;justify-content:center;font-size:10px;margin-left:5px;vertical-align:middle;">i</span>`;
}

// bulan terakhir di PERIODS = "bulan berjalan" dashboard ini. OPEN kalau
// bulan-tahunnya sama dgn jam sistem browser SEKARANG; kalau tidak, berarti
// bulan itu sudah lewat -> CLOSED (tampilkan sbg Actual, jangan diproyeksi).
function faPeriodMeta(){
  const idx = PERIODS.length - 1;
  const periodKey = PERIODS[idx];
  const [y, m] = periodKey.split('-').map(Number);
  const now = new Date();
  const isOpen = (now.getFullYear() === y && (now.getMonth()+1) === m);
  const daysInMonth = new Date(y, m, 0).getDate();
  const daysElapsed = isOpen ? Math.max(1, now.getDate()) : daysInMonth;
  return { idx, periodKey, year:y, month:m, isOpen, daysInMonth, daysElapsed, label: periodLabel(periodKey, true), now };
}

function faHistoricalIdxList(curIdx, months){
  const start = Math.max(0, curIdx - months + 1);
  const list = []; for (let i=start;i<=curIdx;i++) list.push(i);
  return list;
}

// Hanya tampilkan opsi comparison yang datanya benar-benar ada. Budget tidak
// pernah dimasukkan -- source data (Google Sheet) belum punya kolom Budget.
function faComparisonOptions(curIdx){
  const opts = [];
  if (curIdx-1 >= 0) opts.push({ key:'prevMonth', label:'Bulan Sebelumnya', indices:[curIdx-1] });
  const idx3 = []; for (let i=curIdx-1;i>=Math.max(0,curIdx-3);i--) idx3.push(i);
  if (idx3.length) opts.push({ key:'avg3', label:'Rata-rata 3 Bulan', indices: idx3 });
  const idx6 = []; for (let i=curIdx-1;i>=Math.max(0,curIdx-6);i--) idx6.push(i);
  if (idx6.length >= 4) opts.push({ key:'avg6', label:'Rata-rata 6 Bulan', indices: idx6 });
  const [y,m] = PERIODS[curIdx].split('-');
  const yoyIdx = PERIODS.indexOf(`${parseInt(y,10)-1}-${m}`);
  if (yoyIdx>=0) opts.push({ key:'yoy', label:'Bulan Sama Tahun Lalu', indices:[yoyIdx] });
  return opts;
}

function faKPISet(u, unitKey, idx){
  const rev = faLine(u,'Pendapatan'), hpp = faLine(u, faHppRowName(unitKey)), gp = faLine(u,'Laba Kotor'),
        opex = faLine(u,'Biaya Operasional'), op = faLine(u,'Laba Operasional'), np = faLine(u,'Laba Bersih');
  const v = (arr)=> arr ? arr[idx] : null;
  const revenue=v(rev), cogs=v(hpp), grossProfit=v(gp), opexVal=v(opex), operatingProfit=v(op), netProfit=v(np);
  return {
    revenue, cogs, grossProfit, opex:opexVal, operatingProfit, netProfit,
    grossMarginPct: (revenue&&grossProfit!=null)? grossProfit/revenue*100 : null,
    opexRatioPct: (revenue&&opexVal!=null)? opexVal/revenue*100 : null,
    operatingMarginPct: (revenue&&operatingProfit!=null)? operatingProfit/revenue*100 : null,
    netMarginPct: (revenue&&netProfit!=null)? netProfit/revenue*100 : null,
    cogsRatioPct: (revenue&&cogs!=null)? cogs/revenue*100 : null,
  };
}

// Payroll didefinisikan sbg kategori Opex "Gaji" + "Insentif" (insentif/bonus/fee
// pada praktiknya bagian dari biaya tenaga kerja) -- konsisten dgn OPEX_CATEGORIES
// yg sudah ada di dashboard, bukan daftar akun baru.
function faPayrollAt(u, idx){
  const opexRow = u.waterfall.find(r=>r.name==='Biaya Operasional');
  if (!opexRow) return null;
  let total = null, any=false;
  flattenOpexLeaves(opexRow).forEach(l=>{
    const cat = categorizeOpexItem(l.name);
    if (cat==='Gaji' || cat==='Insentif'){
      const val = l.vals[idx];
      if (val!=null){ total = (total||0) + val; any=true; }
    }
  });
  return any ? total : null;
}
function faPayrollAvg(u, idxs){
  const opexRow = u.waterfall.find(r=>r.name==='Biaya Operasional');
  if (!opexRow) return null;
  const perIdx = idxs.map(i=>faPayrollAt(u,i));
  const nums = perIdx.filter(v=>v!=null);
  return nums.length ? nums.reduce((s,v)=>s+v,0)/nums.length : null;
}

function faMovingAvgSeries(values, window){
  return values.map((_,i)=>{
    if (i < window-1) return null;
    const slice = values.slice(i-window+1, i+1);
    if (slice.some(v=>v==null)) return null;
    return slice.reduce((s,v)=>s+v,0)/window;
  });
}

// Klasifikasi pola tren -- rule-based dari statistik dasar (bukan ambang acak):
// dilihat dari deret %perubahan bulan-ke-bulan (arah + stdev) dan z-score
// nilai mentah (utk tangkap satu bulan menyimpang jauh sendirian).
function faTrendClassify(values){
  const nums = values.filter(v=>v!=null);
  if (nums.length < 3) return { label:'Data Belum Cukup', detail:'Minimal 3 bulan data diperlukan.' };
  const changes = [];
  for (let i=1;i<values.length;i++){
    if (values[i]!=null && values[i-1]!=null && values[i-1]!==0) changes.push((values[i]-values[i-1])/Math.abs(values[i-1])*100);
  }
  if (!changes.length) return { label:'Data Belum Cukup', detail:'Tidak cukup titik data berurutan.' };
  const mean = changes.reduce((s,v)=>s+v,0)/changes.length;
  const stdev = Math.sqrt(changes.reduce((s,v)=>s+Math.pow(v-mean,2),0)/changes.length);
  const meanVal = nums.reduce((s,v)=>s+v,0)/nums.length;
  const stdevVal = Math.sqrt(nums.reduce((s,v)=>s+Math.pow(v-meanVal,2),0)/nums.length);
  if (stdevVal>0){
    const z = nums.map(v=>(v-meanVal)/stdevVal);
    if (Math.max(...z) >= 2 && z.filter(x=>x>=1.2).length===1) return { label:'One-off Spike', detail:'Satu bulan melonjak jauh di atas pola bulan lain.' };
    if (Math.min(...z) <= -2 && z.filter(x=>x<=-1.2).length===1) return { label:'One-off Drop', detail:'Satu bulan anjlok jauh di bawah pola bulan lain.' };
  }
  if (stdev >= 25) return { label:'Volatile', detail:`Fluktuasi bulan-ke-bulan tinggi (stdev ${stdev.toFixed(0)}%).` };
  const posCount = changes.filter(c=>c>2).length, negCount = changes.filter(c=>c<-2).length;
  if (posCount >= changes.length-1 && posCount>0 && negCount===0) return { label:'Consistent Growth', detail:'Naik hampir di setiap bulan berurutan.' };
  if (negCount >= changes.length-1 && negCount>0 && posCount===0) return { label:'Consistent Decline', detail:'Turun hampir di setiap bulan berurutan.' };
  return { label:'Stable', detail:'Pergerakan relatif flat.' };
}

// Bulan berjalan (OPEN) -> nilai MTD-parsial diskalakan linear ke ekuivalen
// akhir bulan SEBELUM dibandingkan dgn baseline (yg selalu bulan penuh/tutup).
// Ini WAJIB (poin 4/QA review): tanpa ini, membandingkan MTD hari ke-6 lawan
// bulan lalu yg penuh SELALU kelihatan turun drastis semata krn hari yg
// terlewati lebih sedikit -- bukan sinyal performa asli. Faktor skala SAMA
// dipakai ke semua baris (revenue, cogs, tiap kategori opex) supaya proporsi
// antar item tetap terjaga dan bridge tetap rekonsiliasi murni secara aljabar.
function faScaleForOpenMonth(value, pm){
  if (value==null) return null;
  if (!pm.isOpen) return value; // bulan sudah tutup -- APA ADANYA, bukan proyeksi
  return value / pm.daysElapsed * pm.daysInMonth;
}

// ===== Profit Drivers / Profit Bridge =====
// Dekomposisi eksplisit: Revenue (per outlet kalau di level Group), Diskon,
// COGS, tiap kategori Opex. Sisa yang tidak terjelaskan (item di bawah Laba
// Operasional -- Penyusutan/Pajak/Bunga/Cost of Management/Pendapatan
// Lain-lain/channel revenue di luar 14 outlet fisik) dimasukkan sbg SATU
// baris residual "Item Lain", dihitung dari selisih -- supaya total driver
// SELALU rekonsiliasi persis ke Delta Net Profit, brp pun aturan tanda yang
// dipakai formula P&L masing-masing entity (tidak perlu ditiru manual di sini).
//
// PENTING: hanya Top 5 +/- yg ditampilkan ke user, tapi driver lain di luar
// Top 5 (kalau ada) tetap dijumlahkan jadi baris "Lainnya" -- supaya apa yg
// benar2 TAMPIL DI LAYAR juga rekonsiliasi persis ke Delta Net Profit, bukan
// cuma benar di data internal yg tak terlihat (poin 3 QA review).
function faProfitDrivers(u, unitKey, pm, baseIndices){
  const curIdx = pm.idx;
  const rev = faLine(u,'Pendapatan'), diskon = faLine(u,'Diskon'), hpp = faLine(u, faHppRowName(unitKey));
  const opexRow = u.waterfall.find(r=>r.name==='Biaya Operasional');
  const netProfit = faLine(u,'Laba Bersih');
  const curNp = faScaleForOpenMonth(netProfit ? netProfit[curIdx] : null, pm);
  const baseNp = faAvg(netProfit, baseIndices);

  const drivers = [];
  const push = (name, curValRaw, baseVal, isCost)=>{
    const curVal = faScaleForOpenMonth(curValRaw, pm);
    if (curVal==null || baseVal==null) return false;
    const delta = curVal - baseVal;
    const impact = isCost ? -delta : delta;
    if (Math.abs(impact) < 1) return false;
    drivers.push({ name, impact });
    return true;
  };

  if (unitKey === 'konsolidasi') {
    let anyOutletRevenuePushed = false;
    OUTLET_KEYS.forEach(k=>{
      const ou = UNIT_DATA[k];
      const orev = faLine(ou,'Pendapatan');
      if (!orev) return;
      if (push(`Revenue ${ou.label}`, orev[curIdx], faAvg(orev, baseIndices), false)) anyOutletRevenuePushed = true;
    });
    // BUG (ditemukan via validasi data real): kalau SEMUA 14 outlet null utk
    // periode ini (mis. rincian per-outlet blm terinput walau total Group
    // sudah ada), dulu TIDAK ADA driver Revenue sama sekali yg didorong --
    // seluruh efek revenue (bisa ratusan juta) diam2 masuk ke residual
    // "Item Lain" tanpa label, jadi kelihatan seolah "tidak diketahui
    // penyebabnya" padahal sebenarnya cukup diketahui dari angka Group
    // sendiri. Fallback: pakai baris Pendapatan Group langsung.
    if (!anyOutletRevenuePushed) {
      push('Revenue (Group -- rincian per outlet belum tersedia periode ini)', rev ? rev[curIdx] : null, faAvg(rev, baseIndices), false);
    }
  } else {
    push('Revenue', rev ? rev[curIdx] : null, faAvg(rev, baseIndices), false);
  }
  push('Diskon/Komisi', diskon ? diskon[curIdx] : null, faAvg(diskon, baseIndices), false); // sudah negatif di data -> delta langsung benar arahnya
  push(`COGS (${faHppRowName(unitKey)})`, hpp ? hpp[curIdx] : null, faAvg(hpp, baseIndices), true);

  if (opexRow){
    const leaves = flattenOpexLeaves(opexRow);
    const catCur = {}, catBase = {}, catBaseAny = {};
    leaves.forEach(l=>{
      const cat = categorizeOpexItem(l.name);
      catCur[cat] = (catCur[cat]||0) + (l.vals[curIdx]||0);
      const b = faAvg(l.vals, baseIndices);
      // JANGAN default ke 0 kalau baseline benar2 tidak ada datanya sama sekali --
      // itu akan terbaca sbg "biaya naik dari Rp0" (fabrikasi), bukan "data tak
      // tersedia utk dibandingkan". catBaseAny menandai kategori yg PUNYA minimal
      // 1 leaf dgn data baseline nyata (leaf lain yg null di kategori itu tetap 0,
      // konsisten dgn convention sumArr() yg sudah dipakai rebuild*Computed()).
      if (b!=null){ catBase[cat] = (catBase[cat]||0) + b; catBaseAny[cat] = true; }
    });
    Object.keys(catCur).forEach(cat=> push(`Opex: ${cat}`, catCur[cat], catBaseAny[cat] ? catBase[cat] : null, true));
  }

  if (curNp!=null && baseNp!=null){
    const explained = drivers.reduce((s,d)=>s+d.impact,0);
    const residual = (curNp-baseNp) - explained;
    if (Math.abs(residual) >= 1) {
      const label = unitKey==='konsolidasi'
        ? 'Item Lain (revenue channel di luar 14 outlet + item di bawah Laba Operasional)'
        : 'Item Lain (di bawah Laba Operasional)';
      drivers.push({ name: label, impact: residual });
    }
  }

  const deltaNetProfit = (curNp!=null && baseNp!=null) ? curNp-baseNp : null;
  const sortedPos = drivers.filter(d=>d.impact>0).sort((a,b)=>b.impact-a.impact);
  const sortedNeg = drivers.filter(d=>d.impact<0).sort((a,b)=>a.impact-b.impact);
  const positive = sortedPos.slice(0,5);
  const negative = sortedNeg.slice(0,5);
  const otherPos = sortedPos.slice(5).reduce((s,d)=>s+d.impact,0);
  const otherNeg = sortedNeg.slice(5).reduce((s,d)=>s+d.impact,0);
  if (Math.abs(otherPos) >= 1) positive.push({ name:`Driver positif lainnya (${sortedPos.length-5} item)`, impact: otherPos });
  if (Math.abs(otherNeg) >= 1) negative.push({ name:`Driver negatif lainnya (${sortedNeg.length-5} item)`, impact: otherNeg });

  const totalShown = positive.reduce((s,d)=>s+d.impact,0) + negative.reduce((s,d)=>s+d.impact,0);
  const reconciled = (deltaNetProfit==null) ? null : Math.abs(totalShown - deltaNetProfit) <= FA_CONFIG.reconciliation_tolerance_amount;
  if (reconciled === false) console.warn('[Financial Analysis] Profit Driver bridge TIDAK rekonsiliasi -- cek kalkulasi:', { unitKey, totalShown, deltaNetProfit, gap: totalShown-deltaNetProfit });

  return { positive, negative, deltaNetProfit, totalShown, reconciled, isProjected: pm.isOpen };
}

// ===== Expense Analysis (per kategori, SELALU semua kategori ditampilkan) =====
function faExpenseAnalysis(u, unitKey, pm){
  const opexRow = u.waterfall.find(r=>r.name==='Biaya Operasional');
  const rev = faLine(u,'Pendapatan');
  if (!opexRow || !rev) return [];
  const leaves = flattenOpexLeaves(opexRow);
  const curIdx = pm.idx, prevIdx = curIdx-1;
  const idx3 = faHistoricalIdxList(curIdx-1>=0?curIdx-1:0, 3).filter(i=>i<curIdx);
  const catCur={}, catPrev={}, catAvg3={};
  leaves.forEach(l=>{
    const cat = categorizeOpexItem(l.name);
    catCur[cat] = (catCur[cat]||0) + (l.vals[curIdx]||0);
    // Sama spt di faProfitDrivers: hanya jumlahkan bulan lalu kalau leaf itu
    // BENAR punya angka -- jangan default 0 (itu akan terbaca sbg "biaya
    // naik dari Rp0", fabrikasi, bukan "data bulan lalu tak tersedia").
    if (prevIdx>=0 && l.vals[prevIdx]!=null) catPrev[cat] = (catPrev[cat]||0) + l.vals[prevIdx];
    const a3 = faAvg(l.vals, idx3);
    if (a3!=null) catAvg3[cat] = (catAvg3[cat]||0) + a3;
  });
  const revenueCur = rev[curIdx];
  return Object.keys(catCur).sort((a,b)=>catCur[b]-catCur[a]).map(cat=>{
    const current = catCur[cat];
    const projected = pm.isOpen ? (current / pm.daysElapsed * pm.daysInMonth) : current;
    const prevMonth = catPrev[cat] != null ? catPrev[cat] : null;
    const avg3 = catAvg3[cat] != null ? catAvg3[cat] : null;
    const varianceRp = prevMonth!=null ? current-prevMonth : null;
    const variancePct = (prevMonth!=null && prevMonth!==0) ? varianceRp/Math.abs(prevMonth)*100 : null;
    const pctRevenue = revenueCur ? current/revenueCur*100 : null;
    return { category:cat, current, projected, prevMonth, avg3, varianceRp, variancePct, pctRevenue,
      status: faExpenseStatus(varianceRp, variancePct, revenueCur) };
  });
}
function faExpenseStatus(varianceRp, variancePct, revenue){
  if (varianceRp==null || varianceRp<=0) return { code:'normal', label:'🟢 Normal' }; // turun/flat = tak masalah brp pun %-nya
  const materialAmt = Math.abs(varianceRp) >= FA_CONFIG.minimum_materiality_amount;
  const materialPct = revenue ? (Math.abs(varianceRp)/revenue*100) >= FA_CONFIG.materiality_percent_of_revenue : false;
  if (!materialAmt && !materialPct) return { code:'normal', label:'🟢 Normal' }; // naik tapi nominal tak material -- diabaikan sesuai prinsip materialitas
  const pct = variancePct!=null ? Math.abs(variancePct) : 0;
  if (pct >= FA_CONFIG.critical_variance_percent) return { code:'critical', label:'🔴 Critical' };
  return { code:'watch', label:'🟡 Watch' };
}

// ===== Anomaly Detection (lintas Opex + HPP outlet + revenue outlet) =====
// Basis %Revenue & materiality SELALU pakai total Pendapatan Group (bukan
// pendapatan masing2 outlet) -- konsisten dgn maksud "seberapa besar dampak
// ini thd keseluruhan bisnis", bukan cuma thd outlet itu sendiri.
function faEvaluateAnomaly(varianceRp, variancePct, groupRevenue, isNew){
  if (varianceRp==null) return null;
  const materialAmt = Math.abs(varianceRp) >= FA_CONFIG.minimum_materiality_amount;
  const materialPct = groupRevenue ? (Math.abs(varianceRp)/Math.abs(groupRevenue)*100) >= FA_CONFIG.materiality_percent_of_revenue : false;
  // Poin real-data validation: akun baru SEBELUMNYA lolos gate materialitas
  // sepenuhnya (selalu ditandai walau cuma Rp60rb) -- itu persis noise yg
  // instruksi minta dihindari ("new but legitimate accounts" jangan
  // di-warning kalau nominalnya kecil). Akun baru sekarang tunduk pd gate
  // materialitas YANG SAMA spt item lain -- baru dilaporkan kalau jumlahnya
  // sendiri cukup besar utk relevan bagi management.
  if (!materialAmt && !materialPct) return null;
  if (isNew) return { severity:'watch', label:'🟡 Akun/Item Baru' };
  const pct = variancePct!=null ? Math.abs(variancePct) : 0;
  if (pct >= FA_CONFIG.critical_variance_percent) return { severity:'critical', label:'🔴 Critical' };
  if (pct >= FA_CONFIG.warning_variance_percent) return { severity:'watch', label:'🟡 Watch' };
  return null; // material nominal tapi belum lewati ambang persentase -- bukan anomali, cuma item besar yang wajar
}
function faAnomalyScan(pm){
  const curIdx = pm.idx, prevIdx = curIdx-1;
  const idx3 = faHistoricalIdxList(curIdx-1>=0?curIdx-1:0,3).filter(i=>i<curIdx);
  const groupRevenue = (faLine(UNIT_DATA.konsolidasi,'Pendapatan')||[])[curIdx];
  const rows = [];
  const scanOpex = (unitKey, outletLabel)=>{
    const u = UNIT_DATA[unitKey];
    if (!u || !u.waterfall) return;
    const opexRow = u.waterfall.find(r=>r.name==='Biaya Operasional');
    if (!opexRow) return;
    flattenOpexLeaves(opexRow).forEach(l=>{
      const cur = l.vals[curIdx];
      if (cur==null) return;
      const prev = prevIdx>=0 ? l.vals[prevIdx] : null;
      const avg3 = faAvg(l.vals, idx3);
      const baseline = avg3!=null ? avg3 : prev;
      const isNew = (baseline==null && cur!==0);
      const varianceRp = baseline!=null ? cur-baseline : cur;
      const variancePct = (baseline && baseline!==0) ? varianceRp/Math.abs(baseline)*100 : null;
      const ev = faEvaluateAnomaly(varianceRp, variancePct, groupRevenue, isNew);
      if (!ev) return;
      // Poin 11 QA review: prioritaskan pergerakan abnormal yg BERULANG, bukan
      // cuma satu-off -- cek sederhana: apakah bulan LALU juga sudah naik
      // signifikan vs bulan sebelum itu lagi (2 kenaikan besar berturut-turut).
      // Kalau ya, naikkan severity dari Watch -> Critical (tapi TIDAK menurunkan
      // yg sudah Critical) -- alert fatigue dihindari dgn menaikkan sinyal yg
      // benar2 berulang, bukan menambah baris baru.
      let isRepeated = false;
      if (prevIdx>=1 && prev!=null){
        const prevPrev = l.vals[prevIdx-1];
        if (prevPrev!=null && prevPrev!==0 && Math.abs((prev-prevPrev)/Math.abs(prevPrev)*100) >= FA_CONFIG.warning_variance_percent) isRepeated = true;
      }
      const escalated = isRepeated && ev.severity==='watch';
      const severity = escalated ? 'critical' : ev.severity;
      // BUG (ditemukan via validasi data real): label status sebelumnya tetap
      // memakai ev.label ASLI (mis. "🟡 Watch") walau severity sudah dinaikkan
      // ke 'critical' -- kolom Severity & Status jadi kontradiktif di tabel yg
      // sama. Label sekarang ikut mencerminkan hasil eskalasi.
      const status = escalated ? '🔴 Critical (berulang)' : (isRepeated ? `${ev.label} (berulang)` : ev.label);
      rows.push({ account:l.name, outlet:outletLabel, current:cur, previous:prev, avg3, varianceRp, variancePct,
        pctRevenue: groupRevenue ? varianceRp/groupRevenue*100 : null, profitImpact:-varianceRp,
        severity, status, isNew, isRepeated });
    });
  };
  scanOpex('konsolidasi', 'Group');
  OUTLET_KEYS.forEach(k=> scanOpex(k, UNIT_DATA[k].label));
  OUTLET_KEYS.forEach(k=>{
    const ou = UNIT_DATA[k];
    const rev = faLine(ou,'Pendapatan');
    if (!rev) return;
    const cur = rev[curIdx];
    if (cur==null) return;
    const prev = prevIdx>=0 ? rev[prevIdx] : null;
    const avg3 = faAvg(rev, idx3);
    const baseline = avg3!=null ? avg3 : prev;
    if (baseline==null) return;
    const varianceRp = cur-baseline;
    const variancePct = baseline!==0 ? varianceRp/Math.abs(baseline)*100 : null;
    const ev = faEvaluateAnomaly(varianceRp, variancePct, groupRevenue, false);
    if (!ev) return;
    rows.push({ account:'Pendapatan', outlet:ou.label, current:cur, previous:prev, avg3, varianceRp, variancePct,
      pctRevenue: groupRevenue ? varianceRp/groupRevenue*100 : null, profitImpact:varianceRp,
      severity:ev.severity, status:ev.label, isNew:false });
  });
  rows.sort((a,b)=>Math.abs(b.profitImpact)-Math.abs(a.profitImpact));
  return rows;
}

// ===== Data Completeness =====
function faCollectLeafArrays(node, acc){
  if (!node || !node.children) return acc;
  Object.values(node.children).forEach(child=>{
    const arr = Array.isArray(child) ? child : child.vals;
    const hasKids = child && child.children;
    if (arr && !hasKids) acc.push(arr);
    if (hasKids) faCollectLeafArrays(child, acc);
  });
  return acc;
}
function faDataCompleteness(u, unitKey, pm){
  const idx = pm.idx;
  const leaves = [];
  u.waterfall.forEach(row=>{ if (row.children) faCollectLeafArrays(row, leaves); });
  const total = leaves.length;
  const filled = leaves.filter(arr=>arr[idx]!=null).length;
  const coveragePct = total ? filled/total*100 : null;
  let outletCoveragePct = null;
  if (unitKey==='konsolidasi'){
    const withData = OUTLET_KEYS.filter(k=>{ const r=faLine(UNIT_DATA[k],'Pendapatan'); return r && r[idx]!=null; }).length;
    outletCoveragePct = OUTLET_KEYS.length ? withData/OUTLET_KEYS.length*100 : null;
  }
  const parts = [coveragePct, outletCoveragePct].filter(v=>v!=null);
  const overallPct = parts.length ? parts.reduce((s,v)=>s+v,0)/parts.length : null;
  let confidence = 'LOW';
  if (overallPct!=null){ if (overallPct>=95) confidence='HIGH'; else if (overallPct>=80) confidence='MEDIUM'; }
  return { coveragePct, outletCoveragePct, overallPct, confidence, totalLeaves: total, filledLeaves: filled };
}

// ===== Forecast =====
function faForecast(u, unitKey, pm){
  const kpiCur = faKPISet(u, unitKey, pm.idx);
  const prevMonthActual = pm.idx-1>=0 ? faKPISet(u, unitKey, pm.idx-1) : null;
  if (!pm.isOpen) return { mode:'closed', actual: kpiCur, actualToDate: kpiCur, prevMonthActual, note:'Bulan ini sudah tutup — angka Actual, bukan proyeksi.' };

  const frac = pm.daysElapsed / pm.daysInMonth;
  const scale = (v)=> v==null ? null : v / pm.daysElapsed * pm.daysInMonth;
  const base = { revenue: scale(kpiCur.revenue), cogs: scale(kpiCur.cogs), grossProfit: scale(kpiCur.grossProfit),
    opex: scale(kpiCur.opex), operatingProfit: scale(kpiCur.operatingProfit), netProfit: scale(kpiCur.netProfit) };

  // Volatilitas historis Laba Bersih (stdev %MoM, hingga 6 bulan closed
  // terakhir) -- dipakai murni sbg lebar pita Conservative/Optimistic
  // (statistik dari data historis, BUKAN angka AI).
  const npSeries = faLine(u,'Laba Bersih') || [];
  const histIdx = faHistoricalIdxList(pm.idx-1>=0?pm.idx-1:0, 6).filter(i=>i<pm.idx);
  const changes = [];
  for (let i=1;i<histIdx.length;i++){
    const a=npSeries[histIdx[i-1]], b=npSeries[histIdx[i]];
    if (a!=null && b!=null && a!==0) changes.push((b-a)/Math.abs(a));
  }
  let volatility = 0.15; // asumsi moderat default kalau histori kurang
  if (changes.length){
    const m = changes.reduce((s,c)=>s+c,0)/changes.length;
    volatility = Math.sqrt(changes.reduce((s,c)=>s+Math.pow(c-m,2),0)/changes.length);
  }
  const npBase = base.netProfit;
  const band = npBase!=null ? Math.abs(npBase) * Math.min(volatility, 0.6) * (1-frac) : null;
  const scenarios = {
    conservative: (npBase!=null && band!=null) ? npBase - band : npBase,
    base: npBase,
    optimistic: (npBase!=null && band!=null) ? npBase + band : npBase,
  };
  const histMonths = histIdx.length;
  const dataCompleteness = faDataCompleteness(u, unitKey, pm);
  const coverageScore = (frac>=0.75?2:frac>=0.33?1:0) + (histMonths>=3?2:histMonths>=1?1:0)
    + (dataCompleteness.overallPct!=null && dataCompleteness.overallPct>=90?2:dataCompleteness.overallPct!=null && dataCompleteness.overallPct>=70?1:0)
    + (volatility<=0.2?1:0);
  // Poin 15 QA review: SANGAT AWAL bulan (mis. hari ke-1/2), proyeksi run-rate
  // linear tidak andal sama sekali (1 hari data diekstrapolasi 30x) -- JANGAN
  // pura2 presisi. Paksa LOW terlepas dari skor cakupan lain, dan beri
  // peringatan eksplisit yg bisa ditampilkan di UI (bukan cuma label LOW polos).
  const isEarlyMonth = pm.daysElapsed < FA_CONFIG.forecast_min_days_for_projection;
  const confidence = isEarlyMonth ? 'LOW' : (coverageScore>=6 ? 'HIGH' : coverageScore>=3 ? 'MEDIUM' : 'LOW');
  return { mode:'open', base, scenarios, volatility, frac, confidence, dataCompleteness, histMonths, isEarlyMonth,
    actualToDate: kpiCur, prevMonthActual,
    note:`Proyeksi = MTD ÷ ${pm.daysElapsed} hari berjalan × ${pm.daysInMonth} hari sebulan. Pita Conservative/Optimistic dari volatilitas historis Laba Bersih (±${(volatility*100).toFixed(0)}% stdev MoM, ${histMonths} bulan histori tersedia), makin sempit makin banyak hari terlewati bulan ini.${isEarlyMonth ? ` ⚠️ Baru hari ke-${pm.daysElapsed} bulan ini -- proyeksi masih sangat dini dan bisa berubah signifikan, gunakan Previous Month Actual sbg acuan utama sampai data lebih lengkap.` : ''}` };
}

// ===== Financial Health Score =====
function faScoreFromDelta(deltaPP, goodPP, badPP){
  if (deltaPP==null) return 60;
  if (deltaPP >= goodPP) return 100;
  if (deltaPP <= -badPP) return 20;
  const t = (deltaPP + badPP) / (goodPP + badPP);
  return Math.round(20 + t*80);
}
function faTrendScore(label){
  return ({ 'Consistent Growth':100, 'Stable':75, 'Volatile':55, 'One-off Spike':65, 'One-off Drop':40, 'Consistent Decline':20, 'Data Belum Cukup':60 })[label] ?? 60;
}
function faOutletProfitScoreFor(cls){ return ({ STAR:100, GROWTH:85, STABLE:70, WATCHLIST:40, CRITICAL:10 })[cls] ?? 60; }

function faFinancialHealthScore(u, unitKey, pm, ctx){
  const curIdx = pm.idx;
  const kpiCur = faKPISet(u, unitKey, curIdx);
  const cmpIdx = faHistoricalIdxList(curIdx-1>=0?curIdx-1:0,3).filter(i=>i<curIdx);
  const revB = faAvg(faLine(u,'Pendapatan'), cmpIdx);
  const gpB = faAvg(faLine(u,'Laba Kotor'), cmpIdx);
  const opexB = faAvg(faLine(u,'Biaya Operasional'), cmpIdx);
  const opB = faAvg(faLine(u,'Laba Operasional'), cmpIdx);
  const npB = faAvg(faLine(u,'Laba Bersih'), cmpIdx);
  const gmB = (revB && gpB!=null) ? gpB/revB*100 : null;
  const opxB = (revB && opexB!=null) ? opexB/revB*100 : null;
  const omB = (revB && opB!=null) ? opB/revB*100 : null;
  const nmB = (revB && npB!=null) ? npB/revB*100 : null;

  const payrollCur = faPayrollAt(u, curIdx);
  const payrollRatioCur = (kpiCur.revenue && payrollCur!=null) ? payrollCur/kpiCur.revenue*100 : null;
  const payrollB = faPayrollAvg(u, cmpIdx);
  const payrollRatioB = (revB && payrollB!=null) ? payrollB/revB*100 : null;

  const revTrend = faTrendClassify((faLine(u,'Pendapatan')||[]).slice(0,curIdx+1).slice(-6));
  const npTrend = faTrendClassify((faLine(u,'Laba Bersih')||[]).slice(0,curIdx+1).slice(-6));

  const anomalies = ctx.anomalies || [];
  const critCount = anomalies.filter(a=>a.severity==='critical').length;
  const watchCount = anomalies.filter(a=>a.severity==='watch').length;
  const anomalyScore = Math.max(0, 100 - critCount*15 - watchCount*5);

  let outletProfitScore, outletDataNote = '';
  if (unitKey==='konsolidasi' && ctx.outletPerf){
    // BUG (ditemukan via validasi data real): outlet 'NO DATA' sebelumnya
    // ikut dihitung sbg "bukan bad" di penyebut, membuat skor 100/100
    // ("0 outlet perlu perhatian") padahal SEMUA outlet kosong datanya utk
    // periode ini -- itu bukan sinyal bagus, itu sinyal "tidak diketahui".
    // Outlet tanpa data sekarang dikeluarkan dari pembilang MAUPUN penyebut.
    const known = ctx.outletPerf.rows.filter(o=>o.classification!=='NO DATA');
    const noDataCount = ctx.outletPerf.rows.length - known.length;
    const bad = known.filter(o=>o.classification==='CRITICAL'||o.classification==='WATCHLIST').length;
    outletProfitScore = known.length ? Math.round(100 - (bad/known.length*100)) : 60;
    if (noDataCount>0) outletDataNote = `, ${noDataCount} outlet blm ada data periode ini (tidak dihitung)`;
  } else {
    outletProfitScore = faOutletProfitScoreFor(ctx.ownClassification);
  }

  const completeness = ctx.dataCompleteness;
  const dataScore = completeness && completeness.overallPct!=null ? Math.round(completeness.overallPct) : 60;

  const dims = {
    revenueTrend: { score: faTrendScore(revTrend.label), weight: FA_CONFIG.health_score_weights.revenueTrend, detail: revTrend.label },
    grossMargin: { score: faScoreFromDelta(kpiCur.grossMarginPct!=null&&gmB!=null?kpiCur.grossMarginPct-gmB:null, 1, FA_CONFIG.gross_margin_drop_threshold), weight: FA_CONFIG.health_score_weights.grossMargin, detail: kpiCur.grossMarginPct!=null?`${kpiCur.grossMarginPct.toFixed(1)}%`:'-' },
    opexRatio: { score: faScoreFromDelta(kpiCur.opexRatioPct!=null&&opxB!=null?-(kpiCur.opexRatioPct-opxB):null, 1, FA_CONFIG.opex_ratio_threshold), weight: FA_CONFIG.health_score_weights.opexRatio, detail: kpiCur.opexRatioPct!=null?`${kpiCur.opexRatioPct.toFixed(1)}% dari pendapatan`:'-' },
    payrollRatio: { score: faScoreFromDelta(payrollRatioCur!=null&&payrollRatioB!=null?-(payrollRatioCur-payrollRatioB):null, 1, FA_CONFIG.payroll_ratio_threshold), weight: FA_CONFIG.health_score_weights.payrollRatio, detail: payrollRatioCur!=null?`${payrollRatioCur.toFixed(1)}% dari pendapatan`:'Data tidak cukup' },
    operatingMargin: { score: faScoreFromDelta(kpiCur.operatingMarginPct!=null&&omB!=null?kpiCur.operatingMarginPct-omB:null, 1, 3), weight: FA_CONFIG.health_score_weights.operatingMargin, detail: kpiCur.operatingMarginPct!=null?`${kpiCur.operatingMarginPct.toFixed(1)}%`:'-' },
    netMargin: { score: faScoreFromDelta(kpiCur.netMarginPct!=null&&nmB!=null?kpiCur.netMarginPct-nmB:null, 1, 3), weight: FA_CONFIG.health_score_weights.netMargin, detail: kpiCur.netMarginPct!=null?`${kpiCur.netMarginPct.toFixed(1)}%`:'-' },
    historicalTrend: { score: faTrendScore(npTrend.label), weight: FA_CONFIG.health_score_weights.historicalTrend, detail: npTrend.label },
    anomalies: { score: anomalyScore, weight: FA_CONFIG.health_score_weights.anomalies, detail: `${critCount} critical, ${watchCount} watch` },
    outletProfitability: { score: outletProfitScore, weight: FA_CONFIG.health_score_weights.outletProfitability, detail: unitKey==='konsolidasi' ? `${ctx.outletPerf?ctx.outletPerf.rows.filter(o=>o.classification==='CRITICAL'||o.classification==='WATCHLIST').length:0} outlet perlu perhatian${outletDataNote}` : (ctx.ownClassification||'-') },
    dataCompleteness: { score: dataScore, weight: FA_CONFIG.health_score_weights.dataCompleteness, detail: completeness && completeness.overallPct!=null?`${completeness.overallPct.toFixed(0)}% lengkap`:'-' },
  };
  let total=0, wsum=0;
  Object.values(dims).forEach(d=>{ total += d.score*d.weight; wsum += d.weight; });
  const finalScore = wsum ? Math.round(total/wsum) : null;
  let status;
  if (finalScore==null) status = { code:'unknown', label:'Data Tidak Cukup', emoji:'⚪' };
  else if (finalScore >= FA_CONFIG.health_score_healthy_min) status = { code:'healthy', label:'Healthy', emoji:'🟢' };
  else if (finalScore >= FA_CONFIG.health_score_attention_min) status = { code:'attention', label:'Need Attention', emoji:'🟡' };
  else status = { code:'critical', label:'Critical', emoji:'🔴' };
  return { score: finalScore, status, dims };
}

// ===== Group Reconciliation (poin 2 QA review) =====
// Konsolidasi (Group) BUKAN sekadar penjumlahan 14 outlet fisik -- ada
// Manufaktur, Head Office, channel Online/Franchise, dan biaya bersama
// (Penyusutan/Pajak/Bunga/Cost of Management) yg SAH secara akuntansi tidak
// dialokasikan ke outlet manapun. Fungsi ini menghitung selisihnya secara
// eksplisit dan jujur -- BUKAN didiamkan, BUKAN didistribusikan diam2 ke
// outlet -- supaya management tahu persis berapa besar "Corporate / Shared
// Cost" yg berada di luar P&L per-outlet.
function faGroupReconciliation(pm){
  const idx = pm.idx;
  const group = UNIT_DATA.konsolidasi;
  const metrics = [
    { key:'revenue', label:'Revenue', row:'Pendapatan' },
    { key:'grossProfit', label:'Gross Profit', row:'Laba Kotor' },
    { key:'opex', label:'OPEX', row:'Biaya Operasional' },
    { key:'netProfit', label:'Net Profit', row:'Laba Bersih' },
  ];
  return metrics.map(m=>{
    const groupVal = (faLine(group, m.row)||[])[idx];
    let outletSum = null, anyOutlet = false;
    OUTLET_KEYS.forEach(k=>{
      const v = (faLine(UNIT_DATA[k], m.row)||[])[idx];
      if (v!=null){ outletSum = (outletSum||0) + v; anyOutlet = true; }
    });
    const gap = (groupVal!=null && anyOutlet) ? groupVal - outletSum : null;
    return { key:m.key, label:m.label, groupVal, outletSum: anyOutlet?outletSum:null, gap };
  });
}

// ===== Outlet Performance & Classification =====
function faOutletPerformance(pm){
  const curIdx = pm.idx, prevIdx = curIdx-1;
  const idx3 = faHistoricalIdxList(curIdx-1>=0?curIdx-1:0,3).filter(i=>i<curIdx);
  const rows = OUTLET_KEYS.map(k=>{
    const u = UNIT_DATA[k];
    const kpi = faKPISet(u, k, curIdx);
    const revPrevArr = faLine(u,'Pendapatan');
    const revPrev = (prevIdx>=0 && revPrevArr) ? revPrevArr[prevIdx] : null;
    const growthPct = (kpi.revenue!=null && revPrev) ? (kpi.revenue-revPrev)/Math.abs(revPrev)*100 : null;
    const payroll = faPayrollAt(u, curIdx);
    const payrollRatioPct = (kpi.revenue && payroll!=null) ? payroll/kpi.revenue*100 : null;
    const npSeries = (faLine(u,'Laba Bersih')||[]).slice(0,curIdx+1);
    const trend = faTrendClassify(npSeries.slice(-6));
    const rAvg3 = faAvg(faLine(u,'Pendapatan'), idx3), nAvg3 = faAvg(faLine(u,'Laba Bersih'), idx3);
    const netMarginAvg3 = (rAvg3 && nAvg3!=null) ? nAvg3/rAvg3*100 : null;
    return { key:k, label:u.label, revenue:kpi.revenue, growthPct, grossMarginPct:kpi.grossMarginPct,
      opexRatioPct:kpi.opexRatioPct, payrollRatioPct, netProfit:kpi.netProfit, netMarginPct:kpi.netMarginPct,
      netMarginDeltaVs3mo: (kpi.netMarginPct!=null && netMarginAvg3!=null) ? kpi.netMarginPct-netMarginAvg3 : null,
      trend: trend.label };
  });
  const totalPositiveProfit = rows.reduce((s,r)=> s + (r.netProfit>0?r.netProfit:0), 0);
  rows.forEach(r=> r.contributionPct = (totalPositiveProfit>0 && r.netProfit!=null) ? r.netProfit/totalPositiveProfit*100 : null);

  const revs = rows.map(r=>r.revenue).filter(v=>v!=null).sort((a,b)=>a-b);
  const medianRev = revs.length ? revs[Math.floor(revs.length/2)] : null;
  const margins = rows.map(r=>r.netMarginPct).filter(v=>v!=null).sort((a,b)=>a-b);
  const medianMargin = margins.length ? margins[Math.floor(margins.length/2)] : null;
  rows.forEach(r=>{
    let cls;
    // BUG (ditemukan via validasi data real): outlet TANPA data sama sekali
    // periode ini sebelumnya jatuh ke default 'STABLE' -- scr visual & di
    // Health Score itu terbaca sbg "baik-baik saja", padahal kenyataannya
    // "tidak diketahui". Kasus nyata: saat data per-outlet bulan berjalan
    // belum diinput (baru Group-level yg terisi), SEMUA 14 outlet bisa
    // silently ditandai STABLE meski datanya kosong total. Sekarang eksplisit
        // "NO DATA" -- dikeluarkan dari skor & tidak dianggap sinyal positif.
    if (r.revenue==null && r.netProfit==null) cls='NO DATA';
    else if (r.netProfit!=null && r.netProfit<0) cls='CRITICAL';
    else if (r.netMarginDeltaVs3mo!=null && r.netMarginDeltaVs3mo < -FA_CONFIG.gross_margin_drop_threshold) cls='WATCHLIST';
    else if (medianRev!=null && medianMargin!=null && r.revenue>=medianRev && r.netMarginPct!=null && r.netMarginPct>=medianMargin) cls='STAR';
    else if (r.trend==='Consistent Growth') cls='GROWTH';
    else cls='STABLE';
    r.classification = cls;
  });
  const rank = (key, dir=-1) => [...rows].filter(r=>r[key]!=null).sort((a,b)=>dir*(b[key]-a[key])).slice(0,5);
  return { rows, rankings: {
    highestRevenue: rank('revenue'), highestNetProfit: rank('netProfit'), highestNetMargin: rank('netMarginPct'),
    highestGrowth: rank('growthPct'), biggestImprovement: rank('netMarginDeltaVs3mo'), biggestDecline: rank('netMarginDeltaVs3mo', 1),
    highestExpenseRatio: rank('opexRatioPct'), lowestProfitability: rank('netMarginPct', 1),
  } };
}

// ===== Red Flags / Opportunities / Action Plan (rule-based, bukan AI) =====
function faGenerateInsights(ctx){
  const { kpi, trendRevenue, trendNetProfit, drivers, anomalies, expenseRows, outletPerf, unitKey, u } = ctx;
  const redFlags = [], opportunities = [], actions = [];

  if (trendRevenue.label==='Consistent Decline') redFlags.push({ text:`Pendapatan menunjukkan tren turun berturut-turut (${trendRevenue.detail})`, impact: Math.abs(kpi.revenue||0), severity:'critical' });
  if (trendNetProfit.label==='Consistent Decline') redFlags.push({ text:`Laba Bersih turun berturut-turut beberapa bulan terakhir (${trendNetProfit.detail})`, impact: Math.abs(kpi.netProfit||0), severity:'critical' });

  anomalies.filter(a=>a.severity==='critical').slice(0,5).forEach(a=>{
    redFlags.push({ text:`${a.account} (${a.outlet}) melonjak${a.variancePct!=null?' '+Math.abs(a.variancePct).toFixed(0)+'%':''} dibanding baseline — dampak profit ~${fmtRp(Math.abs(a.profitImpact))}`, impact: Math.abs(a.profitImpact), severity:'critical' });
    actions.push({ priority:'Immediate — 7 Hari', issue:`${a.account} di ${a.outlet} melebihi pola historis`,
      why:`Dampak ~${fmtRp(Math.abs(a.profitImpact))} terhadap profit${a.isRepeated?', dan sudah berulang di bulan sebelumnya (bukan kejadian satu kali)':''} — termasuk salah satu perubahan paling material bulan ini.`,
      action:`Investigate ${a.account} ${a.outlet} karena biayanya melonjak${a.variancePct!=null?' '+Math.abs(a.variancePct).toFixed(0)+'%':''} dan menjadi salah satu penyebab material perubahan profit bulan ini.`,
      outlet:a.outlet, pic:'Finance Manager', impact:Math.abs(a.profitImpact), timing:'7 Hari', needsInvestigation:true });
  });

  if (outletPerf){
    outletPerf.rows.filter(r=>r.classification==='CRITICAL').forEach(r=>{
      redFlags.push({ text:`${r.label} mengalami kerugian (Laba Bersih ${fmtRp(r.netProfit)})`, impact:Math.abs(r.netProfit||0), severity:'critical' });
      actions.push({ priority:'Immediate — 7 Hari', issue:`${r.label} rugi bulan ini`,
        why:`Outlet berstatus CRITICAL dgn Laba Bersih negatif (${fmtRp(r.netProfit)}) — kerugian yg dibiarkan berlanjut memperbesar beban Group.`,
        action:`Review P&L ${r.label} secara menyeluruh (Revenue, HPP, Payroll, Opex) karena outlet ini berstatus Critical dan mencatat kerugian.`,
        outlet:r.label, pic:'Operations Manager', impact:Math.abs(r.netProfit||0), timing:'7 Hari', needsInvestigation:true });
    });
    outletPerf.rows.filter(r=>r.classification==='WATCHLIST').forEach(r=>{
      redFlags.push({ text:`${r.label} masuk Watchlist — margin menurun dibanding rata-rata 3 bulan (${r.netMarginDeltaVs3mo!=null?r.netMarginDeltaVs3mo.toFixed(1)+' pp':'-'})`, impact:Math.abs(r.netProfit||0), severity:'watch' });
      actions.push({ priority:'Short Term — 30 Hari', issue:`${r.label} margin menurun`,
        why:`Net margin turun ${r.netMarginDeltaVs3mo!=null?Math.abs(r.netMarginDeltaVs3mo).toFixed(1)+' pp':''} dibanding rata-rata 3 bulan — sinyal deteriorasi, belum tentu satu-off.`,
        action:`Review struktur biaya ${r.label} karena net margin turun ${r.netMarginDeltaVs3mo!=null?Math.abs(r.netMarginDeltaVs3mo).toFixed(1)+' pp':''} dibanding rata-rata 3 bulan terakhir.`,
        outlet:r.label, pic:'Operations Manager', impact:Math.abs(r.netProfit||0)*0.5, timing:'30 Hari', needsInvestigation:true });
    });
    outletPerf.rows.filter(r=>r.classification==='STAR').forEach(r=>{
      opportunities.push({ text:`${r.label} adalah STAR outlet — revenue tinggi & margin di atas rata-rata, kandidat kuat untuk direplikasi ke outlet lain.` });
    });
  }

  expenseRows.filter(r=>r.status.code==='critical' || r.status.code==='watch').slice(0,5).forEach(r=>{
    actions.push({ priority: r.status.code==='critical' ? 'Immediate — 7 Hari' : 'Short Term — 30 Hari',
      issue:`Biaya ${r.category} naik${r.variancePct!=null?' '+r.variancePct.toFixed(0)+'%':''} dibanding bulan lalu`,
      why:`Berdampak ${fmtRp(Math.abs(r.varianceRp||0))} thd profit dan merupakan salah satu negative profit driver terbesar bulan ini${r.pctRevenue!=null?' ('+r.pctRevenue.toFixed(1)+'% dari pendapatan)':''}.`,
      action:`Review kategori biaya ${r.category} karena naik dari ${fmtRp(r.prevMonth)} menjadi ${fmtRp(r.current)}${r.pctRevenue!=null?' ('+r.pctRevenue.toFixed(1)+'% dari pendapatan)':''} dan menjadi salah satu negative profit driver bulan ini.`,
      outlet: unitKey==='konsolidasi'?'Group':u.label, pic:'Finance Manager', impact:Math.abs(r.varianceRp||0),
      timing: r.status.code==='critical'?'7 Hari':'30 Hari', needsInvestigation:false });
  });

  drivers.negative.slice(0,3).forEach(d=>{
    opportunities.push({ text:`Pantau ${d.name} — pastikan kenaikan biaya ini (dampak ${fmtRp(Math.abs(d.impact))} bulan ini) tidak berlanjut ke bulan depan.` });
  });
  drivers.positive.filter(d=>d.name.startsWith('Opex:')).slice(0,2).forEach(d=>{
    opportunities.push({ text:`${d.name.replace('Opex: ','Biaya ')} turun (${fmtRp(Math.abs(d.impact))}) tanpa indikasi penurunan performa — verifikasi apakah ini penghematan struktural yang bisa dipertahankan.` });
  });

  redFlags.sort((a,b)=> (b.severity==='critical'?2:1)*b.impact - (a.severity==='critical'?2:1)*a.impact);
  actions.sort((a,b)=> b.impact-a.impact);
  return { redFlags: redFlags.slice(0,8), opportunities: opportunities.slice(0,8), actionPlan: actions.slice(0,10) };
}

/* ============================== state + context ============================== */

let faUnit = 'konsolidasi';
let faCompareMode = null;
const FA_AI_CACHE = new Map();

function faUnitOptions(){
  const opts = [{ key:'konsolidasi', label:'Semua Outlet (Group)' }];
  [...OUTLET_KEYS].sort((a,b)=>UNIT_DATA[a].label.localeCompare(UNIT_DATA[b].label,'id')).forEach(k=> opts.push({ key:k, label:UNIT_DATA[k].label }));
  return opts;
}
function faSetUnit(k){ faUnit = k; renderFinancialAnalysis(); }
function faSetCompareMode(k){ faCompareMode = k; renderFinancialAnalysis(); }

function faBuildContext(unitKey){
  const u = UNIT_DATA[unitKey];
  const pm = faPeriodMeta();
  const kpi = faKPISet(u, unitKey, pm.idx);
  const cmpOptions = faComparisonOptions(pm.idx);
  const cmpKey = (faCompareMode && cmpOptions.some(o=>o.key===faCompareMode)) ? faCompareMode : (cmpOptions[0] ? cmpOptions[0].key : null);
  const cmpOpt = cmpOptions.find(o=>o.key===cmpKey) || null;
  const baseIndices = cmpOpt ? cmpOpt.indices : (pm.idx-1>=0?[pm.idx-1]:[]);

  const kpiBase = {
    revenue: faAvg(faLine(u,'Pendapatan'), baseIndices), grossProfit: faAvg(faLine(u,'Laba Kotor'), baseIndices),
    opex: faAvg(faLine(u,'Biaya Operasional'), baseIndices), operatingProfit: faAvg(faLine(u,'Laba Operasional'), baseIndices),
    netProfit: faAvg(faLine(u,'Laba Bersih'), baseIndices), cogs: faAvg(faLine(u, faHppRowName(unitKey)), baseIndices),
  };
  kpiBase.grossMarginPct = (kpiBase.revenue && kpiBase.grossProfit!=null) ? kpiBase.grossProfit/kpiBase.revenue*100 : null;
  kpiBase.opexRatioPct = (kpiBase.revenue && kpiBase.opex!=null) ? kpiBase.opex/kpiBase.revenue*100 : null;
  kpiBase.operatingMarginPct = (kpiBase.revenue && kpiBase.operatingProfit!=null) ? kpiBase.operatingProfit/kpiBase.revenue*100 : null;
  kpiBase.netMarginPct = (kpiBase.revenue && kpiBase.netProfit!=null) ? kpiBase.netProfit/kpiBase.revenue*100 : null;

  // Versi Rp KPI KHUSUS UTK PERBANDINGAN (poin 4 QA review): baseline (kpiBase
  // di atas) SELALU bulan penuh/tutup, jadi kalau bulan berjalan msh OPEN,
  // nilai `kpi` MTD-parsial mentah TIDAK adil dibandingkan langsung dgnnya --
  // akan selalu kelihatan turun drastis hanya krn hari yg terlewati lebih
  // sedikit. `kpi` (var lain) TETAP dipakai apa adanya utk DITAMPILKAN sbg
  // "MTD Actual"; kpiForCompare HANYA dipakai internal utk hitung arah panah.
  const kpiForCompare = {
    revenue: faScaleForOpenMonth(kpi.revenue, pm), grossProfit: faScaleForOpenMonth(kpi.grossProfit, pm),
    opex: faScaleForOpenMonth(kpi.opex, pm), operatingProfit: faScaleForOpenMonth(kpi.operatingProfit, pm),
    netProfit: faScaleForOpenMonth(kpi.netProfit, pm),
  };

  const trendRevenue = faTrendClassify((faLine(u,'Pendapatan')||[]).slice(0,pm.idx+1).slice(-6));
  const trendNetProfit = faTrendClassify((faLine(u,'Laba Bersih')||[]).slice(0,pm.idx+1).slice(-6));

  const drivers = faProfitDrivers(u, unitKey, pm, baseIndices);
  const anomaliesAll = faAnomalyScan(pm);
  const anomalies = unitKey==='konsolidasi' ? anomaliesAll : anomaliesAll.filter(a=>a.outlet===u.label || a.outlet==='Group');
  const expenseRows = faExpenseAnalysis(u, unitKey, pm);
  const dataCompleteness = faDataCompleteness(u, unitKey, pm);

  let outletPerf = null, ownClassification = null, groupReconciliation = null;
  if (unitKey==='konsolidasi') { outletPerf = faOutletPerformance(pm); groupReconciliation = faGroupReconciliation(pm); }
  else { const full = faOutletPerformance(pm); const mine = full.rows.find(r=>r.key===unitKey); ownClassification = mine ? mine.classification : null; }

  const forecast = faForecast(u, unitKey, pm);
  const health = faFinancialHealthScore(u, unitKey, pm, { anomalies, outletPerf, ownClassification, dataCompleteness });
  const insights = faGenerateInsights({ pm, kpi, health, trendRevenue, trendNetProfit, drivers, anomalies, expenseRows, outletPerf, unitKey, u });

  return { unitKey, u, pm, kpi, kpiBase, kpiForCompare, cmpOptions, cmpOpt, baseIndices, trendRevenue, trendNetProfit,
    drivers, anomalies, expenseRows, dataCompleteness, outletPerf, ownClassification, groupReconciliation, forecast, health, insights };
}

/* ============================== AI layer ============================== */

function faBuildAIContext(ctx){
  const { unitKey, u, pm, kpi, kpiBase, cmpOpt, trendRevenue, trendNetProfit, drivers, anomalies, expenseRows, dataCompleteness, outletPerf, forecast, health } = ctx;
  return {
    entity: unitKey==='konsolidasi' ? 'Group (Konsolidasi)' : u.label,
    current_period: pm.periodKey,
    is_open_month: pm.isOpen,
    days_elapsed: pm.daysElapsed,
    days_in_month: pm.daysInMonth,
    comparison_period: cmpOpt ? cmpOpt.label : null,
    kpis: { revenue:kpi.revenue, cogs:kpi.cogs, gross_profit:kpi.grossProfit, gross_margin_pct:kpi.grossMarginPct,
      opex:kpi.opex, opex_ratio_pct:kpi.opexRatioPct, operating_profit:kpi.operatingProfit, operating_margin_pct:kpi.operatingMarginPct,
      net_profit:kpi.netProfit, net_margin_pct:kpi.netMarginPct },
    comparison_kpis: kpiBase,
    historical_trends: { revenue: trendRevenue.label, net_profit: trendNetProfit.label },
    expense_variances: expenseRows.slice(0,10).map(r=>({ category:r.category, current:r.current, previous_month:r.prevMonth, variance_rp:r.varianceRp, variance_pct:r.variancePct, pct_revenue:r.pctRevenue, status:r.status.code })),
    anomalies: anomalies.slice(0,10).map(a=>({ account:a.account, outlet:a.outlet, variance_rp:a.varianceRp, variance_pct:a.variancePct, profit_impact:a.profitImpact, severity:a.severity })),
    profit_drivers: { positive: drivers.positive.map(d=>({ name:d.name, impact:d.impact })), negative: drivers.negative.map(d=>({ name:d.name, impact:d.impact })) },
    outlet_performance: outletPerf ? outletPerf.rows.map(r=>({ outlet:r.label, revenue:r.revenue, net_margin_pct:r.netMarginPct, classification:r.classification })) : null,
    // isEarlyMonth: proyeksi run-rate dari <5 hari data tidak andal (poin 15
    // QA review) -- jangan kirim angka semu ke AI, kirim actual:true spy AI
    // tahu utk TIDAK menarasikan proyeksi seolah presisi.
    forecast: (forecast.mode==='open' && !forecast.isEarlyMonth) ? { projected_revenue: forecast.base.revenue, projected_net_profit: forecast.base.netProfit, scenarios: forecast.scenarios, confidence: forecast.confidence } : { actual:true },
    data_completeness: { overall_pct: dataCompleteness.overallPct, confidence: dataCompleteness.confidence },
    financial_health_score: health.score,
  };
}

function faRenderAIResult(box, result){
  if (!box) return;
  if (result && result.ok){
    box.innerHTML = `<ul style="margin:0;padding-left:18px;font-size:12.5px;line-height:1.8;color:#E9E4FF;">${result.summary.map(s=>`<li>${faEsc(s)}</li>`).join('')}</ul>`;
  } else {
    box.innerHTML = `<div style="font-size:12px;color:#9B93C4;background:#1F1B3D;border:1px solid #2A2650;border-radius:8px;padding:10px 14px;">ℹ️ Interpretasi AI sementara tidak tersedia. Perhitungan keuangan di atas tetap berlaku dan tidak terpengaruh.</div>`;
  }
}
function faRequestAIExplanation(ctx, cacheKey, box){
  if (!box) return;
  box.innerHTML = `<div style="font-size:12px;color:#726C9C;padding:6px 0;">⏳ Menghasilkan interpretasi AI...</div>`;
  fetch('ai_explain.php', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(faBuildAIContext(ctx)) })
    .then(r=>r.json())
    .then(data=>{
      const result = (data && data.ok && Array.isArray(data.summary) && data.summary.length) ? data : { ok:false };
      FA_AI_CACHE.set(cacheKey, result);
      faRenderAIResult(document.getElementById('faAiBox'), result);
    })
    .catch(()=>{
      const result = { ok:false };
      FA_AI_CACHE.set(cacheKey, result);
      faRenderAIResult(document.getElementById('faAiBox'), result);
    });
}
function faMaybeRenderAISummary(ctx){
  if (state.unit !== 'faOverview') return;
  const box = document.getElementById('faAiBox');
  if (!box) return;
  const cacheKey = `${ctx.unitKey}|${ctx.pm.periodKey}|${ctx.cmpOpt?ctx.cmpOpt.key:''}`;
  if (FA_AI_CACHE.has(cacheKey)) faRenderAIResult(box, FA_AI_CACHE.get(cacheKey));
  else faRequestAIExplanation(ctx, cacheKey, box);
}
function faRefreshAISummary(){
  const ctx = faBuildContextCached(faUnit);
  const cacheKey = `${ctx.unitKey}|${ctx.pm.periodKey}|${ctx.cmpOpt?ctx.cmpOpt.key:''}`;
  FA_AI_CACHE.delete(cacheKey);
  faRequestAIExplanation(ctx, cacheKey, document.getElementById('faAiBox'));
}

/* ============================== render helpers ============================== */

function faEsc(s){ return (s==null?'':String(s)).replace(/[&<>]/g, c=>({'&':'&amp;','<':'&lt;','>':'&gt;'}[c])); }
function faCard(title, body, extra=''){
  return `<div style="background:#171433;border:1px solid #2A2650;border-radius:12px;padding:18px 20px;margin-bottom:20px;">
    <div style="font-family:'Space Grotesk',sans-serif;font-size:14px;font-weight:700;color:#FFC93C;margin-bottom:12px;display:flex;align-items:center;justify-content:space-between;">
      <span>${title}</span>${extra}
    </div>
    ${body}
  </div>`;
}
function faDeltaBadge(pct, goodIsUp){
  if (pct==null) return `<span style="color:#726C9C;">→ n/a</span>`;
  const improving = goodIsUp ? pct>=0 : pct<=0;
  const flat = Math.abs(pct) < 0.5;
  const arrow = flat ? '→' : (pct>=0?'↑':'↓');
  const color = flat ? '#9B93C4' : (improving ? '#4ADE80' : '#FB7185');
  return `<span style="color:${color};font-weight:700;">${arrow} ${Math.abs(pct).toFixed(1)}%</span>`;
}

function faRenderTopBar(ctx){
  const { unitKey, pm, cmpOptions, cmpOpt, dataCompleteness } = ctx;
  const unitSel = `<select onchange="faSetUnit(this.value)" style="background:#171433;border:1px solid #2A2650;color:#F5F3FF;border-radius:8px;padding:8px 12px;font-size:12.5px;font-family:'Space Grotesk',sans-serif;font-weight:600;">
    ${faUnitOptions().map(o=>`<option value="${o.key}" ${o.key===unitKey?'selected':''}>${o.label}</option>`).join('')}
  </select>`;
  const cmpSel = cmpOptions.length ? `<select onchange="faSetCompareMode(this.value)" style="background:#171433;border:1px solid #2A2650;color:#F5F3FF;border-radius:8px;padding:8px 12px;font-size:12.5px;">
    ${cmpOptions.map(o=>`<option value="${o.key}" ${cmpOpt&&o.key===cmpOpt.key?'selected':''}>vs ${o.label}</option>`).join('')}
  </select>${faInfo('Budget tidak muncul sbg opsi krn source data belum punya kolom Budget. Opsi lain hanya tampil kalau datanya benar2 tersedia.')}` : `<span style="font-size:11.5px;color:#726C9C;">Belum ada periode pembanding.</span>`;

  // "Data as of" pakai tanggal SISTEM (jam browser) sbg proxy -- source data
  // (Google Sheet) belum punya kolom tanggal posting/transaksi eksplisit per
  // baris, jadi TIDAK bisa diklaim sbg tanggal data ter-update sebenarnya.
  // Ini keterbatasan yg didokumentasikan (bukan diklaim presisi) -- lihat
  // faInfo di bawah & Remaining Limitations pada laporan validasi.
  const asOf = pm.isOpen
    ? `${pm.label} MTD — Data as of: ${pm.now.getDate()} ${MONTH_NAMES_ID[pm.now.getMonth()]} ${pm.now.getFullYear()}${faInfo('Tanggal sistem, BUKAN tanggal posting transaksi terakhir -- source data belum menyimpan tanggal posting eksplisit per baris. Kalau data bulan ini belum di-update hari ini, angka MTD bisa tertinggal dari tanggal yg ditampilkan.')}`
    : `${pm.label} — Actual (bulan sudah tutup)`;
  const compBadge = dataCompleteness.overallPct!=null
    ? `<span style="background:#1F1B3D;border:1px solid #2A2650;border-radius:20px;padding:5px 12px;font-size:11.5px;color:#9B93C4;">Data Completeness: <b style="color:${dataCompleteness.confidence==='HIGH'?'#4ADE80':dataCompleteness.confidence==='MEDIUM'?'#FFC93C':'#FB7185'}">${dataCompleteness.overallPct.toFixed(0)}% (${dataCompleteness.confidence})</b></span>`
    : '';
  const warn = (dataCompleteness.overallPct!=null && dataCompleteness.overallPct<95)
    ? `<div style="background:linear-gradient(90deg,#2E2015,#1F160D);border:1px solid #6B4A28;color:#E8D9C4;border-radius:10px;padding:10px 16px;font-size:12px;margin-bottom:16px;">⚠️ Analisis bisa berubah karena data keuangan periode ini belum lengkap.</div>` : '';

  return `<div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;margin-bottom:6px;">${unitSel}${cmpSel}</div>
    <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:center;margin-bottom:14px;">
      <span style="font-size:12px;color:#9B93C4;">${asOf}</span>${compBadge}
    </div>${warn}`;
}

// Tabel breakdown skor -- dipakai di dalam tile compact Management Snapshot
// (bukan lagi kartu terpisah, supaya tak duplikat -- poin 9 & 20 QA review).
// Format "kontribusi/bobot" persis contoh spesifikasi (mis. "9/13"), bukan
// skor mentah 0-100 tanpa konteks bobotnya.
function faHealthScoreBreakdownTable(health){
  const rows = Object.entries(health.dims).map(([k,d])=>{
    const contribution = Math.round(d.score * d.weight / 100);
    return `<tr>
      <td style="padding:6px 10px;font-size:11.5px;color:#C9C3E8;border-bottom:1px solid #2A2650;">${k}</td>
      <td style="padding:6px 10px;font-size:11.5px;color:#726C9C;border-bottom:1px solid #2A2650;">${faEsc(d.detail)}</td>
      <td class="mono" style="padding:6px 10px;font-size:11.5px;text-align:right;border-bottom:1px solid #2A2650;font-weight:700;">${contribution}/${d.weight}</td>
    </tr>`;
  }).join('');
  return `<div class="tbl-wrap" style="margin-top:10px;max-height:280px;"><table>
      <thead><tr><th style="text-align:left;padding:6px 10px;font-size:10.5px;">Dimensi</th><th style="text-align:left;padding:6px 10px;font-size:10.5px;">Detail (level saat ini vs baseline)</th><th style="padding:6px 10px;font-size:10.5px;">Kontribusi/Bobot</th></tr></thead>
      <tbody>${rows}</tbody>
    </table></div>
    <div style="font-size:10px;color:#726C9C;margin-top:6px;">Setiap dimensi dinormalisasi ke skor 0–100 dulu, baru dikali bobotnya (kolom "Kontribusi/Bobot") -- total skor = jumlah semua kontribusi ÷ total bobot (100). Deterministik, tanpa AI.</div>`;
}

function faRenderSnapshot(ctx){
  const { kpi, forecast, dataCompleteness, insights, health, pm } = ctx;
  const revLabel = pm.isOpen ? 'Revenue MTD' : 'Revenue (Actual)';
  const npLabel = pm.isOpen ? 'Net Profit MTD' : 'Net Profit (Actual)';
  const healthColor = health.status.code==='healthy' ? '#4ADE80' : health.status.code==='attention' ? '#FFC93C' : health.status.code==='critical' ? '#FB7185' : '#726C9C';

  const grid = (label, val, sub, info)=>`<div style="flex:1;min-width:150px;">
    <div style="font-size:10.5px;color:#726C9C;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">${label}${info?faInfo(info):''}</div>
    <div class="mono" style="font-size:18px;font-weight:700;color:#F5F3FF;">${val}</div>
    ${sub?`<div style="font-size:10.5px;color:#726C9C;margin-top:2px;">${sub}</div>`:''}
  </div>`;

  const healthTile = `<div style="flex:1;min-width:170px;">
    <div style="font-size:10.5px;color:#726C9C;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">Financial Health Score${faInfo('Skor 0-100 dari 10 dimensi tertimbang (tren revenue, margin, opex ratio, payroll ratio, anomali, kesehatan outlet, kelengkapan data, dst). Deterministik -- AI tidak ikut menghitung skor ini.')}</div>
    <div style="display:flex;align-items:baseline;gap:8px;">
      <span class="mono" style="font-size:24px;font-weight:800;color:${healthColor};">${health.score ?? '–'}<span style="font-size:12px;color:#726C9C;">/100</span></span>
      <span style="font-size:11.5px;font-weight:700;color:${healthColor};">${health.status.emoji} ${health.status.label}</span>
    </div>
    <details style="margin-top:4px;"><summary style="cursor:pointer;font-size:10.5px;color:#9B93C4;">Rincian skor</summary>${faHealthScoreBreakdownTable(health)}</details>
  </div>`;

  const rev = grid(revLabel, fmtRp(kpi.revenue), null, 'Total pendapatan yg SUDAH tercatat sejak awal bulan berjalan sampai hari ini (bukan proyeksi) -- lihat tab Forecast utk estimasi akhir bulan.');
  const gm = grid('Gross Profit Margin', kpi.grossMarginPct!=null?kpi.grossMarginPct.toFixed(1)+'%':'-');
  const np = grid(npLabel, fmtRp(kpi.netProfit));
  const nm = grid('Net Margin', kpi.netMarginPct!=null?kpi.netMarginPct.toFixed(1)+'%':'-');
  const forecastVal = forecast.mode==='open' ? (forecast.isEarlyMonth ? 'Data belum cukup' : fmtRp(forecast.base.netProfit)) : fmtRp(forecast.actual.netProfit);
  const forecastSub = forecast.mode==='open' ? `Base Case · Confidence: ${forecast.confidence}` : 'Actual (bulan sudah tutup)';
  const fc = grid('Forecast Net Profit', forecastVal, forecastSub, 'Proyeksi Net Profit akhir bulan (Base Case, dari run-rate MTD) -- lihat tab Forecast utk skenario Conservative/Optimistic lengkap.');
  const dc = grid('Data Completeness', dataCompleteness.overallPct!=null?dataCompleteness.overallPct.toFixed(0)+'%':'Not Available', dataCompleteness.confidence, 'Persentase baris akun (leaf) yg terisi utk periode ini, dirata-rata dgn cakupan data per-outlet (khusus tampilan Group). Bukan angka tetap -- dihitung ulang tiap periode.');

  const listOf = (arr, empty)=> arr.length ? `<ul style="margin:0;padding-left:18px;font-size:12px;line-height:1.7;color:#C9C3E8;">${arr.map(x=>`<li>${faEsc(x.text||x)}</li>`).join('')}</ul>` : `<div style="font-size:11.5px;color:#726C9C;">${empty}</div>`;
  const top3RedFlags = insights.redFlags.slice(0,3);
  const top3Actions = insights.actionPlan.slice(0,3).map(a=>({ text:`${a.action}` }));
  const top3Pos = ctx.drivers.positive.filter(d=>!d.name.startsWith('Driver positif lainnya')).slice(0,3).map(d=>({ text:`${d.name}: +${fmtRp(d.impact)}` }));
  const top3Neg = ctx.drivers.negative.filter(d=>!d.name.startsWith('Driver negatif lainnya')).slice(0,3).map(d=>({ text:`${d.name}: ${fmtRp(d.impact)}` }));

  const body = `
    <div style="display:flex;flex-wrap:wrap;gap:18px;margin-bottom:18px;">${healthTile}${rev}${gm}${np}${nm}${fc}${dc}</div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(230px,1fr));gap:16px;">
      <div><div style="font-size:11px;font-weight:700;color:#FB7185;margin-bottom:6px;">🔴 Top 3 Red Flags</div>${listOf(top3RedFlags,'Tidak ada red flag material.')}</div>
      <div><div style="font-size:11px;font-weight:700;color:#4ADE80;margin-bottom:6px;">✅ Top 3 Positive Drivers</div>${listOf(top3Pos,'Belum ada driver positif signifikan.')}</div>
      <div><div style="font-size:11px;font-weight:700;color:#FFC93C;margin-bottom:6px;">⚠️ Top 3 Negative Drivers</div>${listOf(top3Neg,'Belum ada driver negatif signifikan.')}</div>
      <div><div style="font-size:11px;font-weight:700;color:#4FC3F7;margin-bottom:6px;">🎯 Top 3 Recommended Actions</div>${listOf(top3Actions,'Belum ada aksi prioritas.')}</div>
    </div>`;
  return faCard('Management Snapshot', body);
}

function faRenderKPICards(ctx){
  const { kpi, kpiBase, kpiForCompare, cmpOpt, pm } = ctx;
  const items = [
    { label:'Revenue', val:kpi.revenue, cmpVal:kpiForCompare.revenue, base:kpiBase.revenue, goodUp:true, fmt:'rp' },
    { label:'Gross Profit', val:kpi.grossProfit, cmpVal:kpiForCompare.grossProfit, base:kpiBase.grossProfit, goodUp:true, fmt:'rp' },
    { label:'Gross Profit Margin', val:kpi.grossMarginPct, base:kpiBase.grossMarginPct, goodUp:true, fmt:'pp' },
    { label:'OPEX', val:kpi.opex, cmpVal:kpiForCompare.opex, base:kpiBase.opex, goodUp:false, fmt:'rp' },
    { label:'OPEX %', val:kpi.opexRatioPct, base:kpiBase.opexRatioPct, goodUp:false, fmt:'pp' },
    { label:'Net Profit', val:kpi.netProfit, cmpVal:kpiForCompare.netProfit, base:kpiBase.netProfit, goodUp:true, fmt:'rp' },
    { label:'Net Profit Margin', val:kpi.netMarginPct, base:kpiBase.netMarginPct, goodUp:true, fmt:'pp' },
  ];
  const cards = items.map(it=>{
    // Rp (fmt='rp'): kalau bulan berjalan msh OPEN, bandingkan versi
    // diproyeksikan-ke-akhir-bulan (cmpVal), BUKAN MTD-parsial mentah,
    // supaya tak salah baca "turun 65%" hanya krn hari yg terlewati lebih
    // sedikit (poin 4 QA review). Rasio (pp) selalu apple-to-apple sbg %,
    // tak perlu diskalakan.
    const compareVal = it.fmt==='rp' ? it.cmpVal : it.val;
    const deltaPct = (compareVal!=null && it.base) ? (it.fmt==='pp' ? (compareVal-it.base) : (compareVal-it.base)/Math.abs(it.base)*100) : null;
    const displayVal = it.val==null ? '-' : (it.fmt==='rp' ? fmtRp(it.val) : it.val.toFixed(1)+'%');
    const badge = it.fmt==='pp'
      ? (deltaPct==null ? `<span style="color:#726C9C;">→ n/a</span>` : `<span style="color:${(it.goodUp?deltaPct>=0:deltaPct<=0)?'#4ADE80':'#FB7185'};font-weight:700;">${deltaPct>=0?'↑':'↓'} ${Math.abs(deltaPct).toFixed(1)}pp</span>`)
      : faDeltaBadge(deltaPct, it.goodUp);
    const methodTag = (it.fmt==='rp' && pm.isOpen) ? ` <span style="color:#4FC3F7;font-size:9.5px;">(Proyeksi)</span>` : '';
    return `<div class="kpi"><div class="bar" style="background:#FFC93C"></div>
      <div class="lbl">${it.label}</div><div class="val mono">${displayVal}</div>
      <div class="delta">${badge}${methodTag} <span style="color:#726C9C;font-size:10.5px;">vs ${cmpOpt?cmpOpt.label:'-'}</span></div>
    </div>`;
  }).join('');
  const methodNote = pm.isOpen
    ? `<div style="font-size:10.5px;color:#726C9C;margin-top:10px;">${faInfo('Bulan berjalan belum penuh. Untuk KPI Rupiah, arah panah membandingkan PROYEKSI akhir bulan (bukan MTD mentah) vs baseline -- supaya tidak salah baca sbg penurunan besar hanya krn hari yg terlewati lebih sedikit. Untuk margin/rasio (pp), MTD-to-date dibandingkan apa adanya krn sudah sepadan sbg persentase.')} Nilai Rupiah dibandingkan menggunakan Proyeksi Akhir Bulan (bukan MTD mentah); rasio (%) dibandingkan apa adanya.</div>`
    : '';
  return faCard('KPI Cards', `<div class="kpis" style="margin-bottom:0;">${cards}</div>${methodNote}`);
}

function faRenderRedFlagsOpportunities(ctx){
  const { insights } = ctx;
  const rf = insights.redFlags.length ? `<ul style="margin:0;padding-left:18px;font-size:12.5px;line-height:1.9;color:#F5C6CE;">${insights.redFlags.map(f=>`<li>${faEsc(f.text)}</li>`).join('')}</ul>` : `<div style="font-size:12px;color:#726C9C;">Tidak ada red flag material terdeteksi.</div>`;
  const op = insights.opportunities.length ? `<ul style="margin:0;padding-left:18px;font-size:12.5px;line-height:1.9;color:#C7F0DA;">${insights.opportunities.map(o=>`<li>${faEsc(o.text)}</li>`).join('')}</ul>` : `<div style="font-size:12px;color:#726C9C;">Belum ada opportunity signifikan teridentifikasi.</div>`;
  return `<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:16px;">
    ${faCard('🔴 Management Red Flags', rf)}
    ${faCard('💡 Profit Opportunities', op)}
  </div>`;
}

function faRenderActionPlan(ctx){
  const groups = ['Immediate — 7 Hari','Short Term — 30 Hari','Medium Term — 90 Hari'];
  const rowsHtml = ctx.insights.actionPlan.length ? ctx.insights.actionPlan.map(a=>`<tr>
      <td style="padding:10px 14px;font-size:12px;border-bottom:1px solid #2A2650;">${a.priority}</td>
      <td style="padding:10px 14px;font-size:12px;border-bottom:1px solid #2A2650;">${faEsc(a.issue)}</td>
      <td style="padding:10px 14px;font-size:11.5px;color:#9B93C4;border-bottom:1px solid #2A2650;max-width:260px;">${faEsc(a.why||'-')}</td>
      <td style="padding:10px 14px;font-size:12px;border-bottom:1px solid #2A2650;max-width:340px;">${faEsc(a.action)}${a.needsInvestigation?' <span style="color:#FFC93C;font-weight:700;">(Needs Investigation)</span>':''}</td>
      <td style="padding:10px 14px;font-size:12px;border-bottom:1px solid #2A2650;">${faEsc(a.outlet)}</td>
      <td style="padding:10px 14px;font-size:12px;border-bottom:1px solid #2A2650;">${faEsc(a.pic)}</td>
      <td class="mono" style="padding:10px 14px;font-size:12px;text-align:right;border-bottom:1px solid #2A2650;">${fmtRp(a.impact)}</td>
    </tr>`).join('') : `<tr><td colspan="7" style="padding:16px;text-align:center;color:#726C9C;font-size:12px;">Tidak ada aksi prioritas bulan ini.</td></tr>`;
  const body = `<div class="tbl-wrap"><table>
    <thead><tr>
      <th style="text-align:left;">Priority</th><th style="text-align:left;">Issue</th><th style="text-align:left;">Why It Matters</th><th style="text-align:left;">Recommended Action</th>
      <th style="text-align:left;">Outlet</th><th style="text-align:left;">Suggested PIC</th><th>Financial Impact</th>
    </tr></thead><tbody>${rowsHtml}</tbody></table></div>
    <div style="font-size:10.5px;color:#726C9C;margin-top:8px;">Kelompok waktu: ${groups.join(' · ')}. PIC berbasis peran (bukan nama individu) — sesuaikan dengan struktur organisasi Anda.</div>`;
  return faCard('Recommended Actions', body);
}

function faRenderAISummary(){
  return faCard('AI Financial Insight <span style="font-size:10px;color:#726C9C;font-weight:500;">(interpretasi, bukan sumber angka)</span>',
    `<div id="faAiBox"><div style="font-size:12px;color:#726C9C;">Memuat...</div></div>`,
    `<button onclick="faRefreshAISummary()" style="background:#241F4D;border:1px solid #2A2650;color:#9B93C4;border-radius:8px;padding:5px 12px;font-size:11px;cursor:pointer;">🔄 Refresh</button>`);
}

function faRenderOverviewPage(ctx){
  return faRenderSnapshot(ctx) + faRenderKPICards(ctx) + faRenderAISummary()
    + faRenderRedFlagsOpportunities(ctx) + faRenderActionPlan(ctx);
}

/* ---- Trend tab ---- */
const FA_TREND_METRICS = [
  { key:'Pendapatan', label:'Revenue', isPct:false },
  { key:'__cogs', label:'COGS', isPct:false },
  { key:'__cogsPct', label:'COGS %', isPct:true },
  { key:'Laba Kotor', label:'Gross Profit', isPct:false },
  { key:'__gpm', label:'Gross Profit Margin', isPct:true },
  { key:'Biaya Operasional', label:'OPEX', isPct:false },
  { key:'__opexPct', label:'OPEX %', isPct:true },
  { key:'Laba Bersih', label:'Net Profit', isPct:false },
  { key:'__npm', label:'Net Profit Margin', isPct:true },
  { key:'__payroll', label:'Payroll', isPct:false },
  { key:'__payrollPct', label:'Payroll %', isPct:true },
];
let faTrendSelected = new Set(['Pendapatan','Laba Bersih']);
let faTrendMA = 3;
function faTrendSeries(u, unitKey, key){
  if (key==='__cogs') return faLine(u, faHppRowName(unitKey));
  if (key==='__cogsPct' || key==='__gpm' || key==='__opexPct' || key==='__npm' || key==='__payrollPct'){
    const rev = faLine(u,'Pendapatan');
    const numMap = { __cogsPct: faLine(u, faHppRowName(unitKey)), __gpm: faLine(u,'Laba Kotor'), __opexPct: faLine(u,'Biaya Operasional'), __npm: faLine(u,'Laba Bersih') };
    if (key==='__payrollPct') return (rev||[]).map((_,i)=>{ const p=faPayrollAt(u,i); return (rev[i]&&p!=null)? p/rev[i]*100 : null; });
    const num = numMap[key];
    return (rev||[]).map((_,i)=> (rev[i] && num && num[i]!=null) ? num[i]/rev[i]*100 : null);
  }
  if (key==='__payroll') return PERIODS.map((_,i)=>faPayrollAt(u,i));
  return faLine(u, key);
}
function faToggleTrendMetric(key){ faTrendSelected.has(key) ? faTrendSelected.delete(key) : faTrendSelected.add(key); if (!faTrendSelected.size) faTrendSelected.add(key); renderFinancialAnalysis(); }
function faSetTrendMA(n){ faTrendMA = n; renderFinancialAnalysis(); }

function faRenderTrendPage(ctx){
  const { u, unitKey, pm } = ctx;
  const months = Math.min(6, pm.idx+1);
  const idxList = faHistoricalIdxList(pm.idx, Math.max(months,1));
  const labels = idxList.map(i=>periodLabel(PERIODS[i], false));
  const colors = ['#4FC3F7','#FFC93C','#4ADE80','#FB7185','#A78BFA','#22D3C5','#F4A6D0'];
  const chips = FA_TREND_METRICS.map(m=>{
    const on = faTrendSelected.has(m.key);
    return `<div onclick="faToggleTrendMetric('${m.key}')" style="cursor:pointer;padding:6px 12px;border-radius:16px;font-size:11.5px;font-weight:600;border:1px solid ${on?'#FFC93C':'#2A2650'};color:${on?'#FFC93C':'#726C9C'};">${m.label}</div>`;
  }).join('');
  const maBtns = [3,6].map(n=>`<button onclick="faSetTrendMA(${n})" class="tab-btn ${faTrendMA===n?'active':''}" style="${faTrendMA===n?'border-color:#FFC93C;color:#FFC93C;background:#241F4D':''}">${n} Month MA</button>`).join('')
    + faInfo('Moving Average = rata-rata bergerak N bulan terakhir, dipakai utk meredam noise bulan-ke-bulan sehingga arah tren jangka menengah lebih kelihatan.');

  const datasets = [];
  [...faTrendSelected].forEach((key,i)=>{
    const meta = FA_TREND_METRICS.find(m=>m.key===key);
    const series = faTrendSeries(u, unitKey, key);
    const data = idxList.map(idx=> series ? series[idx] : null);
    datasets.push({ label:meta.label, color:colors[i%colors.length], data, isCost: !!meta.isPct });
    const maSeries = faMovingAvgSeries(idxList.map(idx=>series?series[idx]:null), faTrendMA);
    if (maSeries.some(v=>v!=null)) datasets.push({ label:`${meta.label} (MA${faTrendMA})`, color:colors[i%colors.length], data:maSeries, isCost: !!meta.isPct });
  });

  const classRev = faTrendClassify(idxList.map(i=>(faLine(u,'Pendapatan')||[])[i]));
  const classNp = faTrendClassify(idxList.map(i=>(faLine(u,'Laba Bersih')||[])[i]));

  const body = `
    <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:12px;">${chips}</div>
    <div style="display:flex;gap:8px;margin-bottom:14px;">${maBtns}</div>
    <div class="chart-box" style="height:320px;"><div id="faTrendChart" style="width:100%;height:100%;"></div></div>
    <div style="display:flex;gap:20px;flex-wrap:wrap;font-size:12px;color:#C9C3E8;">
      <div><b style="color:#4FC3F7;">Revenue:</b> ${classRev.label} — ${classRev.detail}</div>
      <div><b style="color:#FFC93C;">Net Profit:</b> ${classNp.label} — ${classNp.detail}</div>
    </div>`;
  faTrendDatasetsCache = { datasets, labels };
  return faCard(`Historical Trend Analysis — ${months} Bulan Terakhir`, body);
}
let faTrendDatasetsCache = null;
function faDrawTrendChart(){
  if (!faTrendDatasetsCache) return;
  renderSvgLineChart('faTrendChart', faTrendDatasetsCache.datasets, faTrendDatasetsCache.labels, { dualAxis:true });
}

/* ---- Expense tab ---- */
function faRenderProfitDrivers(ctx){
  const { drivers } = ctx;
  const list = (arr, positive)=> arr.length ? arr.map(d=>`<div style="display:flex;justify-content:space-between;padding:7px 0;border-bottom:1px solid #2A2650;font-size:12.5px;">
      <span style="color:#C9C3E8;">${faEsc(d.name)}</span><span class="mono" style="color:${positive?'#4ADE80':'#FB7185'};font-weight:700;">${d.impact>=0?'+':''}${fmtRp(d.impact)}</span>
    </div>`).join('') : `<div style="font-size:11.5px;color:#726C9C;padding:8px 0;">Tidak ada driver signifikan.</div>`;
  // Poin 3 QA review: tampilkan CEK REKONSILIASI eksplisit -- total driver yg
  // TAMPIL (Top5 +/- + baris "Lainnya") harus = Delta Net Profit, bukan cuma
  // benar di data internal yg tak terlihat. Kalau reconciled===false, itu
  // bug kalkulasi nyata -- ditampilkan sbg peringatan, BUKAN didiamkan.
  const reconLine = drivers.deltaNetProfit!=null ? `<div style="margin-top:12px;padding:10px 14px;border-radius:8px;background:${drivers.reconciled===false?'#2A1B1B':'#14201A'};border:1px solid ${drivers.reconciled===false?'#FB7185':'#2E9E6B'};font-size:11px;">
      ${drivers.reconciled===false?'⚠️ TIDAK REKONSILIASI':'✓ Rekonsiliasi'}: Total driver ditampilkan (${fmtRp(drivers.totalShown)}) ${drivers.reconciled===false?'≠':'='} Δ Net Profit (${fmtRp(drivers.deltaNetProfit)}) ${drivers.reconciled===false?`— selisih ${fmtRp(drivers.totalShown-drivers.deltaNetProfit)}, cek kalkulasi.`:''}
    </div>` : '';
  const projNote = drivers.isProjected ? `<div style="font-size:10.5px;color:#4FC3F7;margin-top:6px;">ℹ️ Bulan berjalan msh MTD -- semua angka driver diproyeksikan dulu ke ekuivalen akhir bulan (run-rate linear) sblm dibandingkan, supaya adil (bukan MTD-parsial lawan bulan penuh).</div>` : '';
  const body = `<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:20px;">
    <div><div style="font-size:11px;font-weight:700;color:#4ADE80;margin-bottom:6px;">TOP POSITIVE DRIVERS</div>${list(drivers.positive,true)}</div>
    <div><div style="font-size:11px;font-weight:700;color:#FB7185;margin-bottom:6px;">TOP NEGATIVE DRIVERS</div>${list(drivers.negative,false)}</div>
  </div>
  <div style="font-size:10.5px;color:#726C9C;margin-top:12px;">Ranking berdasarkan dampak nominal (Rp) terhadap Laba Bersih, dibanding periode pembanding terpilih — bukan sekadar variance %.</div>
  ${projNote}${reconLine}`;
  return faCard(`Profit Drivers — Kenapa Profit Berubah?${faInfo('Setiap driver = perubahan Rp item itu vs periode pembanding, dgn arah yg benar (biaya naik = dampak negatif thd profit, biaya turun = dampak positif). Bukan sekadar item terbesar -- item besar yg TIDAK berubah bukan driver.')}`, body);
}

function faRenderExpenseTable(ctx){
  const rows = ctx.expenseRows;
  const tr = rows.map(r=>`<tr>
      <td style="padding:10px 14px;font-size:12.5px;">${faEsc(r.category)}</td>
      <td class="mono" style="padding:10px 14px;text-align:right;font-size:12px;">${fmtRp(r.current)}</td>
      <td class="mono" style="padding:10px 14px;text-align:right;font-size:12px;">${fmtRp(r.projected)}</td>
      <td class="mono" style="padding:10px 14px;text-align:right;font-size:12px;">${r.prevMonth!=null?fmtRp(r.prevMonth):'-'}</td>
      <td class="mono" style="padding:10px 14px;text-align:right;font-size:12px;">${r.avg3!=null?fmtRp(r.avg3):'-'}</td>
      <td class="mono" style="padding:10px 14px;text-align:right;font-size:12px;">${r.varianceRp!=null?fmtRp(r.varianceRp):'-'}</td>
      <td class="mono" style="padding:10px 14px;text-align:right;font-size:12px;">${r.variancePct!=null?fmtPct(r.variancePct):'-'}</td>
      <td class="mono" style="padding:10px 14px;text-align:right;font-size:12px;">${r.pctRevenue!=null?r.pctRevenue.toFixed(1)+'%':'-'}</td>
      <td style="padding:10px 14px;text-align:center;font-size:12px;">${r.status.label}</td>
    </tr>`).join('');
  const body = `<div class="tbl-wrap"><table><thead><tr>
      <th style="text-align:left;">Category</th><th>Current MTD${faInfo('Total kategori biaya ini yg SUDAH tercatat sejak awal bulan sampai hari ini.')}</th><th>Projected Month End${faInfo('Current MTD dibagi hari berjalan, dikali total hari sebulan (run-rate linear). Untuk bulan yg sudah tutup, sama dgn Current MTD (bukan proyeksi).')}</th><th>Previous Month</th>
      <th>3 Month Avg</th><th>Variance Rp</th><th>Variance %</th><th>% Revenue</th><th>Status</th>
    </tr></thead><tbody>${tr || `<tr><td colspan="9" style="padding:16px;text-align:center;color:#726C9C;">Belum ada data Opex.</td></tr>`}</tbody></table></div>
    <div style="font-size:10px;color:#726C9C;margin-top:8px;">Status hanya ditandai Watch/Critical kalau biaya NAIK dan material (≥Rp${(FA_CONFIG.minimum_materiality_amount/1e6).toFixed(0)}jt atau ≥${FA_CONFIG.materiality_percent_of_revenue}% Pendapatan Group) DAN variance% melewati ambang. Biaya turun/flat selalu Normal, berapa pun persentasenya.</div>`;
  return faCard('Expense Analysis (per Kategori)', body);
}

function faRenderAnomalyTable(ctx){
  const rows = ctx.anomalies;
  const tr = rows.map(a=>`<tr>
      <td style="padding:10px 14px;font-size:12px;">${faEsc(a.account)}</td>
      <td style="padding:10px 14px;font-size:12px;">${faEsc(a.outlet)}</td>
      <td class="mono" style="padding:10px 14px;text-align:right;font-size:12px;">${fmtRp(a.current)}</td>
      <td class="mono" style="padding:10px 14px;text-align:right;font-size:12px;">${a.previous!=null?fmtRp(a.previous):'-'}</td>
      <td class="mono" style="padding:10px 14px;text-align:right;font-size:12px;">${a.avg3!=null?fmtRp(a.avg3):'-'}</td>
      <td class="mono" style="padding:10px 14px;text-align:right;font-size:12px;">${fmtRp(a.varianceRp)}</td>
      <td class="mono" style="padding:10px 14px;text-align:right;font-size:12px;">${a.variancePct!=null?fmtPct(a.variancePct):'-'}</td>
      <td class="mono" style="padding:10px 14px;text-align:right;font-size:12px;">${a.pctRevenue!=null?a.pctRevenue.toFixed(1)+'%':'-'}</td>
      <td class="mono" style="padding:10px 14px;text-align:right;font-size:12px;color:${a.profitImpact>=0?'#4ADE80':'#FB7185'};">${a.profitImpact>=0?'+':''}${fmtRp(a.profitImpact)}</td>
      <td style="padding:10px 14px;text-align:center;font-size:11.5px;">${a.severity}</td>
      <td style="padding:10px 14px;text-align:center;font-size:12px;">${a.status}</td>
    </tr>`).join('');
  const body = `<div class="tbl-wrap"><table><thead><tr>
      <th style="text-align:left;">Account</th><th style="text-align:left;">Outlet</th><th>Current</th><th>Previous Month</th>
      <th>3 Month Avg</th><th>Variance Rp</th><th>Variance %</th><th>% Revenue</th><th>Profit Impact</th><th>Severity</th><th>Status</th>
    </tr></thead><tbody>${tr || `<tr><td colspan="11" style="padding:16px;text-align:center;color:#726C9C;">Tidak ada anomali material terdeteksi periode ini.</td></tr>`}</tbody></table></div>
    <div style="font-size:10.5px;color:#726C9C;margin-top:8px;">Diurutkan berdasarkan Profit Impact terbesar. Item dengan variance kecil secara nominal (di bawah Rp${(FA_CONFIG.minimum_materiality_amount/1e6).toFixed(0)}jt dan di bawah ${FA_CONFIG.materiality_percent_of_revenue}% Pendapatan Group) sengaja tidak ditampilkan, sesuai prinsip materialitas.</div>`;
  return faCard('Anomaly Detection', body);
}

function faRenderExpensePage(ctx){
  return faRenderProfitDrivers(ctx) + faRenderExpenseTable(ctx) + faRenderAnomalyTable(ctx);
}

/* ---- Outlet tab ---- */
function faClassColor(c){ return ({ STAR:'#FFC93C', GROWTH:'#4ADE80', STABLE:'#4FC3F7', WATCHLIST:'#FF9F43', CRITICAL:'#FB7185' })[c] || '#726C9C'; }
function faRenderReconciliation(ctx){
  if (!ctx.groupReconciliation) return '';
  const rows = ctx.groupReconciliation.map(m=>{
    const gapKnown = m.gap!=null;
    const gapLabel = !gapKnown ? 'Not Available (data outlet belum lengkap)'
      : Math.abs(m.gap) < 1 ? 'Rekonsiliasi penuh (Rp0)'
      : `${fmtRp(m.gap)} — Corporate / Shared Cost (Manufaktur, Head Office, channel di luar 14 outlet, item bersama lain)`;
    return `<tr>
      <td style="padding:9px 14px;font-size:12.5px;">${m.label}</td>
      <td class="mono" style="padding:9px 14px;text-align:right;font-size:12px;">${fmtRp(m.groupVal)}</td>
      <td class="mono" style="padding:9px 14px;text-align:right;font-size:12px;">${m.outletSum!=null?fmtRp(m.outletSum):'-'}</td>
      <td style="padding:9px 14px;text-align:right;font-size:11.5px;color:${gapKnown&&Math.abs(m.gap)>=1?'#FFC93C':'#726C9C'};">${gapLabel}</td>
    </tr>`;
  }).join('');
  const body = `<div class="tbl-wrap"><table><thead><tr>
      <th style="text-align:left;">Metrik</th><th>Group (Konsolidasi)</th><th>Jumlah 14 Outlet</th><th style="text-align:right;">Selisih</th>
    </tr></thead><tbody>${rows}</tbody></table></div>
    <div style="font-size:10.5px;color:#726C9C;margin-top:8px;">Group mencakup Manufaktur, Head Office, dan channel di luar 14 outlet fisik -- selisih ini SAH secara akuntansi dan TIDAK didistribusikan diam2 ke outlet manapun.</div>`;
  return faCard('Group Reconciliation — Group vs Jumlah Outlet', body);
}

function faRenderOutletPage(ctx){
  if (!ctx.outletPerf) return faCard('Outlet Performance', `<div style="font-size:12.5px;color:#726C9C;">Pilih "Semua Outlet (Group)" di filter atas untuk melihat perbandingan antar outlet.</div>`);
  const rows = [...ctx.outletPerf.rows].sort((a,b)=>(b.revenue||0)-(a.revenue||0));
  const tr = rows.map(r=>`<tr>
      <td style="padding:10px 14px;font-size:12.5px;">${faEsc(r.label)}</td>
      <td class="mono" style="padding:10px 14px;text-align:right;font-size:12px;">${fmtRp(r.revenue)}</td>
      <td class="mono" style="padding:10px 14px;text-align:right;font-size:12px;">${r.growthPct!=null?fmtPct(r.growthPct):'-'}</td>
      <td class="mono" style="padding:10px 14px;text-align:right;font-size:12px;">${r.grossMarginPct!=null?r.grossMarginPct.toFixed(1)+'%':'-'}</td>
      <td class="mono" style="padding:10px 14px;text-align:right;font-size:12px;">${r.opexRatioPct!=null?r.opexRatioPct.toFixed(1)+'%':'-'}</td>
      <td class="mono" style="padding:10px 14px;text-align:right;font-size:12px;">${r.payrollRatioPct!=null?r.payrollRatioPct.toFixed(1)+'%':'-'}</td>
      <td class="mono" style="padding:10px 14px;text-align:right;font-size:12px;">${fmtRp(r.netProfit)}</td>
      <td class="mono" style="padding:10px 14px;text-align:right;font-size:12px;">${r.netMarginPct!=null?r.netMarginPct.toFixed(1)+'%':'-'}</td>
      <td class="mono" style="padding:10px 14px;text-align:right;font-size:12px;">${r.contributionPct!=null?r.contributionPct.toFixed(1)+'%':'-'}</td>
      <td style="padding:10px 14px;font-size:11.5px;">${r.trend}</td>
      <td style="padding:10px 14px;text-align:center;"><span style="background:${faClassColor(r.classification)}22;color:${faClassColor(r.classification)};border:1px solid ${faClassColor(r.classification)};border-radius:8px;padding:2px 8px;font-size:10.5px;font-weight:700;">${r.classification}</span></td>
    </tr>`).join('');
  const table = `<div class="tbl-wrap"><table><thead><tr>
      <th style="text-align:left;">Outlet</th><th>Revenue</th><th>Growth</th><th>GP Margin</th><th>OPEX %</th><th>Payroll %</th>
      <th>Net Profit</th><th>Net Margin</th><th>Contribution</th><th style="text-align:left;">Trend</th><th>Status</th>
    </tr></thead><tbody>${tr}</tbody></table></div>`;

  const rk = ctx.outletPerf.rankings;
  const rankBlock = (title, list, key, fmt)=> `<div style="min-width:220px;flex:1;">
    <div style="font-size:10.5px;font-weight:700;color:#9B93C4;margin-bottom:6px;text-transform:uppercase;">${title}</div>
    ${list.length ? list.map((r,i)=>`<div style="display:flex;justify-content:space-between;font-size:12px;padding:4px 0;border-bottom:1px solid #2A2650;"><span>${i+1}. ${faEsc(r.label)}</span><span class="mono">${fmt(r[key])}</span></div>`).join('') : `<div style="font-size:11px;color:#726C9C;">-</div>`}
  </div>`;
  const rankings = `<div style="display:flex;flex-wrap:wrap;gap:20px;">
    ${rankBlock('Highest Revenue', rk.highestRevenue, 'revenue', fmtRp)}
    ${rankBlock('Highest Net Profit', rk.highestNetProfit, 'netProfit', fmtRp)}
    ${rankBlock('Highest Net Margin', rk.highestNetMargin, 'netMarginPct', v=>v.toFixed(1)+'%')}
    ${rankBlock('Highest Growth', rk.highestGrowth, 'growthPct', v=>fmtPct(v))}
    ${rankBlock('Biggest Improvement', rk.biggestImprovement, 'netMarginDeltaVs3mo', v=>v.toFixed(1)+'pp')}
    ${rankBlock('Biggest Decline', rk.biggestDecline, 'netMarginDeltaVs3mo', v=>v.toFixed(1)+'pp')}
    ${rankBlock('Highest Expense Ratio', rk.highestExpenseRatio, 'opexRatioPct', v=>v.toFixed(1)+'%')}
    ${rankBlock('Lowest Profitability', rk.lowestProfitability, 'netMarginPct', v=>v.toFixed(1)+'%')}
  </div>`;
  return faRenderReconciliation(ctx) + faCard('Outlet Performance', table) + faCard('Outlet Ranking (Top 5)', rankings);
}

/* ---- Forecast tab ---- */
function faRenderForecastPage(ctx){
  const { forecast, pm } = ctx;
  const margin = (obj)=> (obj && obj.revenue && obj.netProfit!=null) ? (obj.netProfit/obj.revenue*100).toFixed(1)+'%' : '-';
  const gpMargin = (obj)=> (obj && obj.revenue && obj.grossProfit!=null) ? (obj.grossProfit/obj.revenue*100).toFixed(1)+'%' : '-';

  // ACTUAL (solid, bold, putih) vs PROYEKSI (italic, badge biru, warna beda)
  // -- dibedakan SECARA VISUAL, bukan cuma teks label (poin 16 QA review).
  const actualRow = (label, obj, badgeColor)=> obj ? `<tr><td style="padding:9px 14px;font-size:12.5px;font-weight:700;">${label} <span style="background:${badgeColor}22;color:${badgeColor};border:1px solid ${badgeColor};border-radius:6px;padding:1px 6px;font-size:9px;font-weight:800;margin-left:4px;">ACTUAL</span></td>
    <td class="mono" style="padding:9px 14px;text-align:right;font-weight:700;">${fmtRp(obj.revenue)}</td>
    <td class="mono" style="padding:9px 14px;text-align:right;">${fmtRp(obj.grossProfit)}</td>
    <td class="mono" style="padding:9px 14px;text-align:right;">${gpMargin(obj)}</td>
    <td class="mono" style="padding:9px 14px;text-align:right;">${fmtRp(obj.opex)}</td>
    <td class="mono" style="padding:9px 14px;text-align:right;font-weight:700;">${fmtRp(obj.netProfit)}</td>
    <td class="mono" style="padding:9px 14px;text-align:right;">${margin(obj)}</td></tr>` : '';
  const projectedRow = (label, obj)=> `<tr style="background:#16233A;"><td style="padding:9px 14px;font-size:12.5px;font-style:italic;color:#7FD1FF;">${label} <span style="background:#4FC3F722;color:#4FC3F7;border:1px solid #4FC3F7;border-radius:6px;padding:1px 6px;font-size:9px;font-weight:800;margin-left:4px;font-style:normal;">PROYEKSI</span></td>
    <td class="mono" style="padding:9px 14px;text-align:right;font-style:italic;color:#7FD1FF;">${fmtRp(obj.revenue)}</td>
    <td class="mono" style="padding:9px 14px;text-align:right;font-style:italic;color:#7FD1FF;">${fmtRp(obj.grossProfit)}</td>
    <td class="mono" style="padding:9px 14px;text-align:right;font-style:italic;color:#7FD1FF;">${gpMargin(obj)}</td>
    <td class="mono" style="padding:9px 14px;text-align:right;font-style:italic;color:#7FD1FF;">${fmtRp(obj.opex)}</td>
    <td class="mono" style="padding:9px 14px;text-align:right;font-style:italic;font-weight:700;color:#7FD1FF;">${fmtRp(obj.netProfit)}</td>
    <td class="mono" style="padding:9px 14px;text-align:right;font-style:italic;color:#7FD1FF;">${margin(obj)}</td></tr>`;
  // Poin 15 QA review: hari ke-1/2/3 bulan berjalan, run-rate linear dari 1-2
  // hari data BISA menghasilkan angka ekstrapolasi yg tak masuk akal (mis.
  // 1 hari dikali 30). Daripada tampilkan angka presisi-palsu itu, sembunyikan
  // nilai numeriknya dan jelaskan kenapa -- JANGAN pura2 tahu proyeksi akhir
  // bulan padahal datanya belum cukup utk itu.
  const suppressedProjectedRow = (label)=> `<tr style="background:#16233A;"><td style="padding:9px 14px;font-size:12.5px;font-style:italic;color:#7FD1FF;">${label} <span style="background:#4FC3F722;color:#4FC3F7;border:1px solid #4FC3F7;border-radius:6px;padding:1px 6px;font-size:9px;font-weight:800;margin-left:4px;font-style:normal;">PROYEKSI</span></td>
    <td colspan="6" style="padding:9px 14px;text-align:center;font-style:italic;color:#726C9C;font-size:11.5px;">Data belum cukup (hari ke-${pm.daysElapsed}) — lihat Previous Month Actual sbg acuan sementara</td></tr>`;

  const thead = `<thead><tr><th style="text-align:left;">Basis</th><th>Revenue</th><th>Gross Profit</th><th>GP Margin</th><th>OPEX</th><th>Net Profit</th><th>Net Margin</th></tr></thead>`;

  if (forecast.mode==='closed'){
    const rows = actualRow(`${pm.label} Actual (Bulan Penuh)`, forecast.actualToDate, '#4ADE80')
      + (forecast.prevMonthActual ? actualRow('Previous Month Actual', forecast.prevMonthActual, '#9B93C4') : '');
    const body = `<div style="font-size:12.5px;color:#9B93C4;margin-bottom:14px;">${forecast.note}</div>
      <div class="tbl-wrap"><table>${thead}<tbody>${rows}</tbody></table></div>`;
    return faCard(`Month-End Forecast — ${pm.label} (bulan sudah tutup)`, body);
  }

  const rows = actualRow(`Actual MTD (hari ke-${pm.daysElapsed})`, forecast.actualToDate, '#F5F3FF')
    + (forecast.isEarlyMonth ? suppressedProjectedRow('Projected Month End (Base Case)') : projectedRow('Projected Month End (Base Case)', forecast.base))
    + (forecast.prevMonthActual ? actualRow('Previous Month Actual', forecast.prevMonthActual, '#9B93C4') : '')
    + (forecast.isEarlyMonth ? '' : `<tr><td style="padding:7px 14px;font-size:11.5px;color:#FB7185;">↳ Conservative</td><td colspan="4"></td>
        <td class="mono" style="padding:7px 14px;text-align:right;font-size:11.5px;color:#FB7185;">${fmtRp(forecast.scenarios.conservative)}</td><td></td></tr>
       <tr><td style="padding:7px 14px;font-size:11.5px;color:#4ADE80;">↳ Optimistic</td><td colspan="4"></td>
        <td class="mono" style="padding:7px 14px;text-align:right;font-size:11.5px;color:#4ADE80;">${fmtRp(forecast.scenarios.optimistic)}</td><td></td></tr>`);

  const confColor = forecast.confidence==='HIGH' ? '#4ADE80' : forecast.confidence==='MEDIUM' ? '#FFC93C' : '#FB7185';
  const earlyWarn = forecast.isEarlyMonth ? `<div style="background:linear-gradient(90deg,#2E2015,#1F160D);border:1px solid #6B4A28;color:#E8D9C4;border-radius:10px;padding:10px 16px;font-size:12px;margin-bottom:14px;">⚠️ Baru hari ke-${pm.daysElapsed} bulan ini — proyeksi masih sangat dini. Gunakan <b>Previous Month Actual</b> sbg acuan utama sampai hari berjalan lebih banyak.</div>` : '';
  const body = `
    ${earlyWarn}
    <div style="display:flex;align-items:center;gap:16px;margin-bottom:16px;flex-wrap:wrap;">
      <span style="background:#1F1B3D;border:1px solid #2A2650;border-radius:20px;padding:6px 14px;font-size:12px;">Hari Berjalan: <b>${pm.daysElapsed}/${pm.daysInMonth}</b></span>
      <span style="background:#1F1B3D;border:1px solid #2A2650;border-radius:20px;padding:6px 14px;font-size:12px;">Forecast Confidence: <b style="color:${confColor}">${forecast.confidence}</b>${faInfo('Mempertimbangkan: hari berjalan/total hari, jumlah bulan histori tersedia, kelengkapan data, dan volatilitas historis Laba Bersih. LOW = jangan andalkan angka proyeksi sbg presisi.')}</span>
    </div>
    <div class="tbl-wrap"><table>${thead}<tbody>${rows}</tbody></table></div>
    <div style="font-size:10.5px;color:#726C9C;margin-top:10px;">${forecast.note}</div>`;
  return faCard(`Month-End Forecast — ${pm.label} MTD`, body);
}

/* ============================== entry point ============================== */

// Poin 25 QA review (performance): berpindah antar TAB (Overview/Trend/dst)
// TIDAK perlu hitung ulang seluruh kalkulasi kalau unit/periode/comparison
// tidak berubah -- hanya rebuild saat salah satu key itu benar2 beda (mis.
// live-fetch menambah periode baru, atau user ganti outlet/comparison).
let FA_CTX_CACHE = null;
function faBuildContextCached(unitKey){
  const cacheKey = `${unitKey}|${PERIODS[PERIODS.length-1]}|${PERIODS.length}|${faCompareMode||''}`;
  if (FA_CTX_CACHE && FA_CTX_CACHE.key === cacheKey) return FA_CTX_CACHE.ctx;
  const ctx = faBuildContext(unitKey);
  FA_CTX_CACHE = { key: cacheKey, ctx };
  return ctx;
}

function renderFinancialAnalysis(){
  const content = document.getElementById('content');
  if (!content) return;
  const ctx = faBuildContextCached(faUnit);
  let html = faRenderTopBar(ctx);
  if (state.unit==='faOverview') html += faRenderOverviewPage(ctx);
  else if (state.unit==='faTrend') html += faRenderTrendPage(ctx);
  else if (state.unit==='faExpense') html += faRenderExpensePage(ctx);
  else if (state.unit==='faOutlet') html += faRenderOutletPage(ctx);
  else if (state.unit==='faForecast') html += faRenderForecastPage(ctx);
  content.innerHTML = html;
  if (state.unit==='faTrend') faDrawTrendChart();
  faMaybeRenderAISummary(ctx);
}
