<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Keuangan 2026 — Dashboard</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Fira+Code:wght@400;500&display=swap" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-datalabels/2.2.0/chartjs-plugin-datalabels.min.js"></script>
<style>
  :root {
    --bg: #0E0C22; --panel: #171433; --border: #2A2650; --text: #F5F3FF; --muted: #9B93C4; --dim: #726C9C;
    --yellow: #FFC93C; --good: #4ADE80; --bad: #FB7185; --olive: #22D3C5; --neg-num: #9B93C4;
  }
  * { box-sizing: border-box; }
  body { margin: 0; background: var(--bg); color: var(--text); font-family: 'Inter', sans-serif; }
  h1,h2,.disp { font-family: 'Space Grotesk', sans-serif; }
  .mono { font-family: 'Plus Jakarta Sans', sans-serif; font-variant-numeric: tabular-nums; }
  .wrap { max-width: 1180px; margin: 0 auto; padding: 28px 24px 60px; }
  .eyebrow { font-size: 11px; letter-spacing: .1em; text-transform: uppercase; color: var(--yellow); font-weight: 700; margin-bottom: 6px; }
  h1 { font-size: 34px; font-weight: 800; letter-spacing: -0.5px; margin: 0 0 20px; background: linear-gradient(90deg, #FFFFFF, #C9C3E8); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; }
  .tabs { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 16px; }

  /* ===== SIDEBAR NAVIGASI UTAMA (kiri) ===== */
  .app-shell { display: flex; align-items: flex-start; min-height: 100vh; }
  .sidebar { width: 236px; flex-shrink: 0; background: #14112B; border-right: 1px solid #2A2650; min-height: 100vh; padding: 20px 12px; position: sticky; top: 0; transition: width .18s ease, padding .18s ease; overflow: hidden; }
  .sb-toggle-btn { display:flex; align-items:center; justify-content:center; width:26px; height:26px; border-radius:8px; border:1px solid var(--border); background:#1C1840; color:#9B93C4; cursor:pointer; margin-bottom:14px; font-size:11px; flex-shrink:0; }
  .sb-toggle-btn:hover { color:#F5F3FF; border-color:#FFC93C; }
  .sidebar.collapsed { width: 46px; padding: 20px 10px; }
  .sidebar.collapsed #sidebarInner { display: none; }
  .sidebar-logo { font-family: 'Space Grotesk', sans-serif; font-weight: 800; font-size: 13px; line-height: 1.35; color: #F5F3FF; padding: 4px 10px 18px; letter-spacing: -.01em; }
  .sb-group { margin-bottom: 4px; }
  .sb-group-head { display: flex; align-items: center; justify-content: space-between; padding: 10px 10px; border-radius: 9px; cursor: pointer; font-family: 'Space Grotesk', sans-serif; font-size: 12.5px; font-weight: 700; color: #9B93C4; letter-spacing: .01em; }
  .sb-group-head:hover { background: #1C1840; color: #F5F3FF; }
  .sb-group-head.open { color: #F5F3FF; }
  .sb-group-head .arrow { font-size: 9px; transition: transform .15s; opacity: .6; }
  .sb-group-head.open .arrow { transform: rotate(90deg); }
  .sb-items { max-height: 0; overflow: hidden; transition: max-height .18s ease; }
  .sb-items.open { max-height: 900px; }
  .sb-item { display: block; padding: 8px 14px 8px 26px; border-radius: 8px; font-size: 12.5px; color: #726C9C; cursor: pointer; margin: 1px 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .sb-item:hover { background: #1C1840; color: #C9C3E8; }
  .sb-item.active { background: #241F4D; color: #FFC93C; font-weight: 700; }
  .sb-empty-note { padding: 8px 14px 8px 26px; font-size: 11px; color: #4A4568; font-style: italic; }
  .main-content { flex: 1; min-width: 0; padding: 24px 28px; }
  @media (max-width: 860px) {
    .app-shell { flex-direction: column; }
    .sidebar { width: 100%; min-height: auto; position: relative; border-right: none; border-bottom: 1px solid #2A2650; }
    .sidebar.collapsed { width: 100%; height: 46px; min-height: 46px; padding: 10px 12px; }
    .main-content { padding: 18px 16px; }
  }
  .tab-btn { display: flex; align-items: center; gap: 7px; padding: 9px 16px; border-radius: 10px; cursor: pointer; font-family: 'Space Grotesk',sans-serif; font-size: 13px; font-weight: 600; background: var(--panel); color: var(--muted); border: 1px solid var(--border); }
  .tab-btn.active { color: #0E0C22; }
  .sub-tabs { display: flex; gap: 4px; border-bottom: 1px solid var(--border); margin-bottom: 22px; overflow-x: auto; }
  .sub-btn { background: transparent; border: none; cursor: pointer; padding: 9px 14px; font-size: 12.5px; font-weight: 500; white-space: nowrap; color: var(--dim); border-bottom: 2px solid transparent; margin-bottom: -1px; }
  .sub-btn.active { color: var(--text); }
  .banner { border-radius: 12px; padding: 14px 18px; margin-bottom: 20px; display: flex; gap: 10px; font-size: 12.5px; line-height: 1.6; }
  .banner.warn { background: linear-gradient(90deg,#2E2015,#1F160D); border: 1px solid #6B4A28; color: #E8D9C4; }
  .banner.dummy { background: linear-gradient(90deg,#2A1246,#1A0F33); border: 1px solid #7C3AED; color: #E9E4FF; }
  .banner.ok { background: linear-gradient(90deg,#0F2E1F,#0B1F16); border: 1px solid #2E9E6B; color: #C8F0DD; }
  .section-label { margin-bottom: 14px; }
  .section-label .eyebrow2 { font-size: 11px; letter-spacing: .12em; text-transform: uppercase; margin-bottom: 4px; font-weight: 600; }
  .section-label .title { font-size: 18px; font-weight: 700; }
  .kpis { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 24px; }
  .kpi { background: var(--panel); border: 1px solid var(--border); border-radius: 12px; padding: 16px 18px; flex: 1 1 160px; min-width: 160px; position: relative; overflow: hidden; }
  .kpi .bar { position: absolute; top:0; left:0; right:0; height:3px; }
  .kpi .lbl { font-size: 10.5px; letter-spacing: .06em; text-transform: uppercase; color: var(--muted); margin-bottom: 8px; }
  .kpi .val { font-size: 21px; font-weight: 700; }
  .kpi .delta { display:flex; align-items:center; gap:4px; margin-top:6px; font-size:11.5px; }
  .chart-box { background: var(--panel); border: 1px solid var(--border); border-radius: 12px; padding: 18px 14px 6px; margin-bottom: 24px; height: 280px; position: relative; }
  table { width: 100%; border-collapse: collapse; }
  .tbl-wrap { background: var(--panel); border: 1px solid var(--border); border-radius: 12px; overflow-x: auto; overflow-y: auto; max-height: 72vh; }
  th { text-align: right; padding: 12px 14px; color: var(--yellow); font-weight: 800; font-size: 11px; letter-spacing: .05em; text-transform: uppercase; background: #241F4D; border-bottom: 2px solid var(--yellow); font-family: 'Space Grotesk', sans-serif; }
  th:first-child, td:first-child { text-align: left; }
  th:first-child { color: var(--text); }
  /* FREEZE kolom Akun saat scroll horizontal -- tiap jenis baris butuh latar
     SOLID sendiri (bukan transparan/gradient) supaya isi sel lain tidak
     kelihatan menembus di belakangnya saat digeser */
  th:first-child, td:first-child { position: sticky; left: 0; z-index: 2; background: var(--panel); }
  /* FREEZE baris judul bulan (thead) saat scroll VERTIKAL -- supaya saat
     dropdown/rincian dibuka & tabel jadi panjang, header bulan (Mei/Jun/Jul dst)
     tetap kelihatan di atas layar, tidak perlu scroll ke atas lagi utk cek
     kolom itu bulan apa. z-index thead th:first-child paling tinggi krn dia
     freeze DUA arah sekaligus (atas + kiri, jadi "sudut" tabel). */
  thead th { position: sticky; top: 0; z-index: 3; }
  thead th:first-child { z-index: 4; background: #241F4D; }
  tr.l0 td:first-child { background: #1F1B3D; }
  tr.row-highlight td:first-child { background: #2A2456; }
  th.total-col, td.total-col { background: #2A2456; border-left: 2px solid var(--yellow); font-weight: 800; }
  td.total-col { color: var(--text); }
  th.key-col, td.key-col { background: #2E2857; border-left: 2px solid var(--yellow); }
  td.key-col .keybadge { display:inline-block; background:#241F4D; color:var(--yellow); font-weight:700; padding:2px 9px; border-radius:8px; font-family:'Space Grotesk',sans-serif; font-size:12px; }
  .pctTag.pct-good { color: var(--good); font-weight:700; }
  .pctTag.pct-bad { color: var(--bad); font-weight:700; }
  .key-note { font-size:11.5px; color:var(--muted); line-height:1.6; background:var(--panel); border:1px solid var(--border); border-radius:10px; padding:10px 14px; margin-bottom:12px; }
  td { padding: 9px 14px; text-align: right; font-size: 12.5px; border-top: 1px solid #2A2650; }
  tr.l0 { background: #1F1B3D; font-weight: 700; }
  tr.l0 td:first-child { font-size: 13px; }
  tr.toggle-row td:first-child { color: var(--dim); font-style: italic; font-weight: 500; }
  tr.toggle-row .chev { color: var(--dim); }
  tr.row-highlight { background: linear-gradient(90deg,#2A2456,#1F1B3D); border-top: 2px solid var(--yellow); border-bottom: 2px solid var(--yellow); }
  tr.row-highlight td:first-child { color: var(--yellow); font-weight: 800; }
  .neg { color: var(--neg-num); }
  .pctTag { color: var(--dim); font-size: 10.5px; font-family: 'Inter', sans-serif; }
  .pos-delta { color: var(--good); }
  .neg-delta { color: var(--neg-num); }
  details summary { cursor: pointer; list-style: none; display:flex; align-items:center; gap:6px; }
  details summary::-webkit-details-marker { display: none; }
  details summary .chev { transition: transform .15s; color: var(--yellow); font-size: 11px; }
  details[open] summary .chev { transform: rotate(90deg); }
  .child-row td:first-child { padding-left: 40px; color: var(--muted); font-weight: 400; }
  .csv-new-tag { display:inline-block; margin-left:8px; padding:2px 7px; border-radius:6px; background:rgba(34,211,197,.15); border:1px solid var(--olive); color: var(--olive); font-size:9.5px; font-weight:700; letter-spacing:.04em; text-transform:uppercase; font-family:'Space Grotesk',sans-serif; vertical-align:middle; }
  .footnote { margin-top: 28px; font-size: 11px; color: var(--dim); display: flex; gap: 6px; }
  #lockScreen { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
  .lock-card { background: var(--panel); border: 1px solid var(--border); border-radius: 16px; padding: 36px 32px; max-width: 380px; width: 100%; }
  .lock-card input { width: 100%; background: var(--bg); border: 1px solid var(--border); border-radius: 8px; padding: 10px 14px; color: var(--text); font-size: 14px; font-family: 'Fira Code',monospace; margin-bottom: 10px; }
  .lock-card button { width: 100%; background: var(--yellow); border: none; border-radius: 8px; padding: 10px 14px; color: #0E0C22; font-weight: 700; font-family: 'Space Grotesk',sans-serif; font-size: 14px; cursor: pointer; }
  #dashboard { display: none; }

  /* ===== PANEL BERDAMPINGAN -- Sanding PNL Cabang Biasa vs Franchise, multi-bakery (dari mockup_pnl_gabungan.html) ===== */
  .cd-dual-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; align-items:start; }
  .cd-panel { background:var(--panel); border:1px solid var(--border); border-radius:12px; overflow:hidden; min-width:0; }
  .cd-panel-head { padding:14px 16px 12px; border-bottom:1px solid var(--border); }
  .cd-panel-head .tag { font-family:'Space Grotesk',sans-serif; font-weight:800; font-size:12.5px; letter-spacing:.03em; }
  .cd-panel-head .note { font-size:10.5px; color:var(--dim); margin-top:4px; line-height:1.5; }
  .cd-est-tag { font-size:9px; color:var(--yellow); font-weight:700; margin-left:5px; padding:1px 5px; border:1px solid var(--yellow); border-radius:5px; vertical-align:middle; }
  .cd-block { margin-bottom:26px; padding-bottom:22px; border-bottom:1px dashed var(--border); }
  .cd-block:last-child { border-bottom:none; margin-bottom:0; padding-bottom:0; }
  .cd-block-title { font-family:'Space Grotesk',sans-serif; font-size:15px; font-weight:800; color:var(--text); margin-bottom:10px; display:flex; align-items:center; gap:8px; }
  .cd-block-title .n { display:inline-flex; align-items:center; justify-content:center; width:20px; height:20px; border-radius:6px; background:#241F4D; color:var(--dim); font-size:11px; font-weight:700; flex-shrink:0; }
  .cd-multi-grid { display:flex; gap:14px; overflow-x:auto; padding-bottom:8px; }
  .cd-multi-grid > .cd-panel { flex:0 0 300px; min-width:300px; }
  @media (max-width:760px) { .cd-dual-grid { grid-template-columns:1fr; } }
</style>
</head>
<body>

<!-- ================= LOCK SCREEN (cosmetic — see chat notes on real security) ================= -->
<div id="lockScreen">
  <div class="lock-card">
    <div class="eyebrow">Dashboard Laporan Keuangan</div>
    <h1 style="font-size:22px;margin:0 0 20px;">Laporan Keuangan 2026</h1>
    <input type="password" id="pwInput" placeholder="Masukkan password" onkeydown="if(event.key==='Enter')tryUnlock()">
    <div id="pwError" style="color:var(--bad);font-size:12px;margin-bottom:10px;display:none;">Password salah.</div>
    <button onclick="tryUnlock()">Masuk</button>
    <div style="margin-top:18px;font-size:11px;color:var(--dim);line-height:1.6;">
      ⚠️ Gerbang password ini kosmetik (cek password jalan di browser/JavaScript) — bisa dilewati siapa pun yang buka DevTools. Bukan proteksi sungguhan untuk data sensitif.
    </div>
  </div>
</div>

<!-- ================= DASHBOARD ================= -->
<div id="dashboard">
<div class="app-shell">
  <nav class="sidebar" id="sidebar">
    <button class="sb-toggle-btn" id="sbToggleBtn" onclick="toggleSidebarCollapse()" title="Sembunyikan/tampilkan menu">◀</button>
    <div id="sidebarInner"></div>
  </nav>
  <div class="main-content">
  <div class="wrap">
  
  <h1>Dashboard Laporan Keuangan</h1>

  <div style="display:flex; align-items:center; gap:10px; margin:12px 0 16px; flex-wrap:wrap;">
    <span id="liveBadge" style="display:inline-flex; align-items:center; gap:6px; background:#1F1B3D; border:1px solid #2A2650; border-radius:20px; padding:6px 14px; font-size:12px; color:#9B93C4;">
      <span id="liveDot" style="width:8px; height:8px; border-radius:50%; background:#726C9C; display:inline-block;"></span>
      <span id="liveBadgeText">Belum terhubung</span>
    </span>
    <button onclick="loadLiveData()" style="display:inline-flex; align-items:center; gap:6px; background:#5B8DEF; border:none; border-radius:20px; padding:7px 16px; font-size:12px; font-weight:600; color:#fff; cursor:pointer;">
      🔄 Refresh
    </button>
    <span id="liveMeta" style="font-size:11.5px; color:#726C9C;"></span>
  </div>


  <!-- Tab bar horizontal lama digantikan sidebar kiri (id="sidebar") -->
  <!-- Tombol upload CSV disembunyikan sesuai permintaan -- fungsi handleCSVUpload()
       masih ada di kode (dormant), bisa dimunculkan lagi kalau live-fetch gagal.
       Untuk munculkan lagi: hapus komentar HTML di bawah ini. -->
  <!--
  <div style="display:flex; align-items:center; gap:10px; margin-bottom:16px; flex-wrap:wrap;">
    <label for="csvUpload" style="background:#171433; border:1px solid #2A2650; border-radius:8px; padding:7px 14px; font-size:12px; cursor:pointer; color:#C9C3E8;">
      📤 Upload CSV Data (Period/Entity/Account/...)
    </label>
    <input type="file" id="csvUpload" accept=".csv" style="display:none" onchange="handleCSVUpload(event)">
    <span id="csvStatus" style="font-size:11.5px; color:#726C9C;"></span>
  </div>
  -->
  <div id="liveStatus" style="font-size:11.5px; color:#726C9C; margin-bottom:12px;"></div>
  <div class="sub-tabs" id="analysisTabs"></div>
  <div id="monthFilterBar" style="display:flex; flex-wrap:wrap; gap:8px; align-items:center; margin-bottom:14px;"></div>
  <div id="bannerArea"></div>
  <div class="section-label"><div class="eyebrow2" id="secEyebrow"></div><div class="title" id="secTitle"></div></div>
  <div id="content"></div>

  <div class="footnote">
    <span>ℹ️ Store, Head Office, Manufaktur (CV Amor Group), dan Ownership pakai data REAL tervalidasi. Franchise: revenue per-outlet real, HPP/OPEX estimasi rasio. YoY memakai data asli (bulan tanpa pembanding tahun sebelumnya ditandai "-"). Eliminasi intercompany sudah terverifikasi bersih: baris ber-Counterparty "Internal" tidak ikut masuk ke Konsolidasi.</span>
  </div>
  </div>
  </div>
</div>
</div>

<script>
const DEMO_PASSWORD = '0';
function tryUnlock(){
  const v = document.getElementById('pwInput').value;
  if (v === DEMO_PASSWORD) {
    document.getElementById('lockScreen').style.display = 'none';
    document.getElementById('dashboard').style.display = 'block';
    initDashboard();
  } else {
    document.getElementById('pwError').style.display = 'block';
  }
}

/* ============ DATA (real, validated Jan-Mei 2026 kecuali disebutkan lain) ============
   Struktur baru: tiap entity punya "waterfall" -- daftar berurutan baris P&L
   PERSIS sepanjang yang benar-benar ada di source entity itu (TIDAK dipaksa
   seragam antar entity -- Store/HO cuma sampai "Laba Bersih", Manufaktur
   sampai EBIT/EAT, Konsolidasi/Ownership paling lengkap sampai EAT NET).
   Baris pertama waterfall SELALU "Pendapatan" -- dipakai sbg basis %. */
let PERIODS = ['2026-01','2026-02','2026-03','2026-04','2026-05','2026-06']; // FIX BUG: sebelumnya cuma ['2026-06'], menyebabkan nilai statis Juni salah geser ke slot Januari saat live-fetch menambah periode baru di TENGAH array (bukan di akhir). Sekarang semua slot Jan-Mei sudah ada dari awal (null di data statis saya -- TAPI live-fetch masih bisa menariknya balik dari data historis lama di spreadsheet Anda, itu bukan bug kode, itu data lama yg masih ada di sheet).
const GRAFIK_BUILD_VERSION = '2026-07-28-v11-subtotal'; // ganti setiap kali dashboard dikirim ulang -- dipakai di banner diagnostik Grafik Custom, supaya user bisa konfirmasi via TEKS apakah file yg dipakai memang versi terbaru
const OUTLET_KEYS = ['sudirman','cikole','dramaga','bangbarung','cibubur','mekarsari','pekapuran','jatimelati','nusaindah','cibinong','bantargebang','abdulgani','pangleseran','outletsby']; // dipindah ke awal file -- const tidak di-hoist spt function, dipanggil oleh rebuildAllOutletsComputed() yg dieksekusi saat init
const MONTH_NAMES_ID = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
function periodLabel(period, withYear){
  const [y,m] = period.split('-');
  const mn = MONTH_NAMES_ID[parseInt(m,10)-1] || m;
  return withYear ? `${mn} '${y.slice(2)}` : mn;
}
function currentLabels(){
  const years = new Set(PERIODS.map(p=>p.split('-')[0]));
  return PERIODS.map(p => periodLabel(p, years.size>1));
}
// timpa semua array values (rekursif, termasuk children bersarang) supaya
// panjangnya selalu = PERIODS.length -- kalau CSV baru punya lebih banyak
// bulan dari sebelumnya, baris yg TIDAK ikut ter-update dari CSV itu diberi
// null di bulan2 baru (bukan crash/misalign kolom). ASUMSI: periode baru
// selalu ditambahkan di AKHIR (kronologis maju), bukan disisipkan di tengah.
function normalizeLength(arr){
  if (!Array.isArray(arr)) return arr;
  if (arr.length === PERIODS.length) return arr;
  if (arr.length > PERIODS.length) return arr.slice(0, PERIODS.length);
  return [...arr, ...Array(PERIODS.length - arr.length).fill(null)];
}
function normalizeNode(node){
  if (!node) return;
  if (Array.isArray(node.values)) node.values = normalizeLength(node.values);
  if (node.children) {
    Object.values(node.children).forEach(child => {
      if (Array.isArray(child)) return; // ditangani di pemanggil
      else normalizeNode(child);
    });
    Object.keys(node.children).forEach(k => {
      if (Array.isArray(node.children[k])) node.children[k] = normalizeLength(node.children[k]);
    });
  }
}
function normalizeAllData(){
  Object.values(UNIT_DATA).forEach(u => {
    if (u.waterfall) u.waterfall.forEach(row => normalizeNode(row));
    if (u.revChildrenRaw) Object.keys(u.revChildrenRaw).forEach(k => {
      u.revChildrenRaw[k] = normalizeLength(u.revChildrenRaw[k]);
    });
  });
}

const UNIT_DATA = {
  store: {
    label: 'Store & Brand', color: '#FF6B6B', grounded: 'real',
    waterfall: [
      { name:'Pendapatan', highlight:true, accountLabel:'Pendapatan', forceFormula:true, computeFromChildren:true, values:[null,null,null,null,null,7412411285.0],
        children: {
          "Pendapatan Offline": [null,null,null,null,null,4267749617.0],
          "Pendapatan Online": [null,null,null,null,null,2936849027.0],
          "Pendapatan Konsinyasi": [null,null,null,null,null,207812641.0]
        } },
      { name:'Diskon', accountLabel:'Diskon', forceFormula:true, computeFromChildren:true, noNegColor:true, values:[null,null,null,null,null,-1291949603.0],
        children: {
          "Diskon Offline": [null,null,null,null,null,-159857364.0],
          "Diskon Online": [null,null,null,null,null,-717240872.0],
          "Diskon MBG": [null,null,null,null,null,-801000.0],
          "Cashback MBG": [null,null,null,null,null,-178000.0],
          "Potongan/Komisi Aplikasi/Merchant": [null,null,null,null,null,-413872367.0]
        } },
      { name:'Pendapatan Bersih', accountLabel:'', computed:true, values:[null,null,null,null,null,null] },
      { name:'HPP', accountLabel:'Harga Pokok Penjualan', forceFormula:true, computeFromChildren:true, values:[null,null,null,null,null,4308942801.0],
        children: {
          "HPP Produk Amor": [null,null,null,null,null,4209486042.0],
          "HPP Produk Konsinyasi": [null,null,null,null,null,99092034.0],
          "HPP Pembelian Bahan Baku Langsung": [null,null,null,null,null,364725.0]
        } },
      { name:'Laba Kotor', accountLabel:'', computed:true, values:[null,null,null,null,null,null] },
      { name:'Biaya Operasional', accountLabel:'Biaya Operasional', forceFormula:true, computeFromChildren:true, values:[null,null,null,null,null,546900570.72],
        children: {
          "Bakery Sudirman": { vals:[null,null,null,null,null,72829826.0], children: {
              "Biaya Listrik & Air BKSM": [null,null,null,null,null,11513650.0],
              "Biaya Internet & Telepon BKSM": [null,null,null,null,null,137663.0],
              "Biaya Alat Tulis Kantor BKSM": [null,null,null,null,null,1012500.0],
              "Beban Tunjangan Hari Raya BKSM        ": [null,null,null,null,null,null],
              "Biaya System POS BKSM": [null,null,null,null,null,608083.0],
              "Biaya Transport BKSM": [null,null,null,null,null,22500.0],
              "Biaya Kebersihan & Pengharum BKSM": [null,null,null,null,null,1524300.0],
              "Biaya Operasionalonal Lainnya BKSM": [null,null,null,null,null,1269537.0],
              "Biaya Sewa Bangunan BKSM": [null,null,null,null,null,17500000.0],
              "Biaya Maintenance Aset BKSM": [null,null,null,null,null,477500.0],
              "Biaya Gaji Karyawan BKSM": [null,null,null,null,null,28773931.0],
              "Biaya Insentif, Bonus, Fee BKSM": [null,null,null,null,null,9990162.0],
              "Biaya Freelance BKSM": [null,null,null,null,null,null],
              "Beban Perjalanan Dinas BKSM": [null,null,null,null,null,null]
          } },
          "Bakery Cikole": { vals:[null,null,null,null,null,35335951.0], children: {
              "Biaya Listrik & Air BKGD": [null,null,null,null,null,4472468.0],
              "Biaya Internet & Telepon BKGD": [null,null,null,null,null,596863.0],
              "Biaya Alat Tulis Kantor BKGD": [null,null,null,null,null,304000.0],
              "Beban Tunjangan Hari Raya BKGD": [null,null,null,null,null,null],
              "Biaya System POS BKGD": [null,null,null,null,null,499750.0],
              "Biaya Transport BKGD": [null,null,null,null,null,null],
              "Biaya Kebersihan & Pengharum BKGD": [null,null,null,null,null,760500.0],
              "Biaya Operasionalonal Lainnya BKGD": [null,null,null,null,null,506861.0],
              "Biaya Sewa Bangunan BKGD": [null,null,null,null,null,11158333.0],
              "Biaya Maintenance Aset BKGD": [null,null,null,null,null,150000.0],
              "Biaya Gaji Karyawan BKGD": [null,null,null,null,null,12788559.0],
              "Biaya Insentif, Bonus, Fee BKGD": [null,null,null,null,null,4098617.0],
              "Biaya Freelance BKGD": [null,null,null,null,null,null],
              "Beban Perjalanan Dinas BKGD": [null,null,null,null,null,null]
          } },
          "Bakery Bangbarung": { vals:[null,null,null,null,null,45997429.0], children: {
              "Biaya Listrik & Air BKBG": [null,null,null,null,null,5853500.0],
              "Biaya Internet & Telepon BKBG": [null,null,null,null,null,513613.0],
              "Biaya Alat Tulis Kantor BKBG": [null,null,null,null,null,1158000.0],
              "Beban Tunjangan Hari Raya BKBG       ": [null,null,null,null,null,null],
              "Biaya System POS BKBG": [null,null,null,null,null,250000.0],
              "Biaya Transport BKBG": [null,null,null,null,null,221636.0],
              "Biaya Kebersihan & Pengharum BKBG": [null,null,null,null,null,1211600.0],
              "Biaya Operasionalonal Lainnya BKBG": [null,null,null,null,null,2595832.0],
              "Biaya Sewa Bangunan BKBG": [null,null,null,null,null,8125000.0],
              "Biaya Maintenance Aset BKBG": [null,null,null,null,null,1050000.0],
              "Biaya Insentif, Bonus, Fee BKBG": [null,null,null,null,null,5467054.0],
              "Biaya Gaji Karyawan BKBG": [null,null,null,null,null,19516994.0],
              "Biaya Perjalanan Dinas BKBG": [null,null,null,null,null,34200.0],
              "Beban Freelance BKBG": [null,null,null,null,null,null]
          } },
          "Bakery Dramaga": { vals:[null,null,null,null,null,48906527.0], children: {
              "Biaya Listrik & Air BKDR": [null,null,null,null,null,7301500.0],
              "Biaya Internet & Telepon BKDR": [null,null,null,null,null,458113.0],
              "Biaya Alat Tulis Kantor BKDR": [null,null,null,null,null,677000.0],
              "Beban Tunjangan Hari Raya BKDR       ": [null,null,null,null,null,null],
              "Biaya System POS BKDR": [null,null,null,null,null,250000.0],
              "Biaya Transport BKDR": [null,null,null,null,null,null],
              "Biaya Kebersihan & Pengharum BKDR": [null,null,null,null,null,937069.0],
              "Biaya Operasionalonal Lainnya BKDR": [null,null,null,null,null,2076691.0],
              "Biaya Sewa Bangunan BKDR": [null,null,null,null,null,12500000.0],
              "Biaya Maintenance Aset BKDR": [null,null,null,null,null,350000.0],
              "Biaya Gaji Karyawan BKDR": [null,null,null,null,null,18435088.0],
              "Biaya Insentif. Bonus. Fee BKDR": [null,null,null,null,null,5886866.0],
              "Beban Freelance BKDR": [null,null,null,null,null,null],
              "Biaya Perjalanan Dinas BKDR": [null,null,null,null,null,34200.0]
          } },
          "Bakery Cibubur": { vals:[null,null,null,null,null,36619102.62], children: {
              "Biaya Listrik & Air BKCB": [null,null,null,null,null,6010500.0],
              "Biaya Internet & Telepon BKCB": [null,null,null,null,null,649812.62],
              "Biaya Alat Tulis Kantor BKCB": [null,null,null,null,null,466304.0],
              "Beban Tunjangan Hari Raya BKCB       ": [null,null,null,null,null,null],
              "Biaya System POS BKCB": [null,null,null,null,null,250000.0],
              "Beban Transport BKCB": [null,null,null,null,null,34119.0],
              "Biaya Kebersihan & Pengharum BKCB": [null,null,null,null,null,744700.0],
              "Biaya Operasionalonal Lainnya BKCB": [null,null,null,null,null,3124620.0],
              "Biaya Sewa Bangunan BKCB": [null,null,null,null,null,9375000.0],
              "Biaya Maintenance Aset BKCB": [null,null,null,null,null,null],
              "Biaya Gaji Karyawan BKCB": [null,null,null,null,null,12355404.0],
              "Biaya Insentif. Bonus. Fee BKCB": [null,null,null,null,null,3510443.0],
              "Biaya Perjalanan Dinas BKCB": [null,null,null,null,null,98200.0]
          } },
          "Bakery Pekapuran": { vals:[null,null,null,null,null,23786185.0], children: {
              "Biaya Listrik & Air BKPK": [null,null,null,null,null,3010500.0],
              "Biaya Internet & Telepon BKPK": [null,null,null,null,null,461113.0],
              "Biaya Alat Tulis Kantor BKPK": [null,null,null,null,null,258000.0],
              "Beban Tunjangan Hari Raya BKPK       ": [null,null,null,null,null,null],
              "Biaya System POS BKPK": [null,null,null,null,null,250000.0],
              "Biaya Transport BKPK": [null,null,null,null,null,35000.0],
              "Biaya Kebersihan & Pengharum BKPK": [null,null,null,null,null,734500.0],
              "Biaya Operasionalonal Lainnya BKPK": [null,null,null,null,null,970421.0],
              "Biaya Sewa Bangunan BKPK": [null,null,null,null,null,5750000.0],
              "Beban Maintenance Aset Pekapuran": [null,null,null,null,null,null],
              "Biaya Gaji Karyawan BKPK": [null,null,null,null,null,9443444.0],
              "Biaya Insentif. Bonus. Fee BKPK": [null,null,null,null,null,2839007.0],
              "Biaya Perjalanan Dinas BKPK": [null,null,null,null,null,34200.0]
          } },
          "Bakery Mekarsari": { vals:[null,null,null,null,null,20578212.62], children: {
              "Biaya Listrik & Air BKMK": [null,null,null,null,null,2007000.0],
              "Biaya Internet & Telepon BKMK": [null,null,null,null,null,477762.62],
              "Biaya Alat Tulis Kantor BKMK": [null,null,null,null,null,240500.0],
              "Beban Tunjangan Hari Raya BKMK": [null,null,null,null,null,null],
              "Biaya System POS BKMK": [null,null,null,null,null,250000.0],
              "Biaya Transport BKMK": [null,null,null,null,null,100000.0],
              "Biaya Kebersihan & Pengharum BKMK": [null,null,null,null,null,null],
              "Biaya Operasionalonal Lainnya BKMK": [null,null,null,null,null,1068472.0],
              "Biaya Sewa Bangunan BKMK": [null,null,null,null,null,6250000.0],
              "Biaya Maintenance Aset BKMK": [null,null,null,null,null,null],
              "Biaya Gaji Karyawan BKMK": [null,null,null,null,null,7970599.0],
              "Biaya Insentif, Bonus, Fee BKMK": [null,null,null,null,null,2179679.0],
              "Biaya Perjalanan Dinas BKMK": [null,null,null,null,null,34200.0]
          } },
          "Outlet Sukabumi": { vals:[null,null,null,null,null,108020737.0], children: {
              "Biaya Listrik & Air OPSM": [null,null,null,null,null,2963994.0],
              "Biaya Internet & Telepon OPSM": [null,null,null,null,null,1780000.0],
              "Biaya Alat Tulis Kantor OPSM": [null,null,null,null,null,420500.0],
              "Beban Tunjangan Hari Raya OPSM": [null,null,null,null,null,null],
              "Biaya System POS OPSM": [null,null,null,null,null,2300000.0],
              "Biaya Transport OPSM": [null,null,null,null,null,456500.0],
              "Beban Kebersihan & Pengharum OPSM       ": [null,null,null,null,null,null],
              "Beban Operasional Lainnya OPSM": [null,null,null,null,null,1975500.0],
              "Biaya Sewa Bangunan OPSM": [null,null,null,null,null,28079167.0],
              "Beban Maintenance Aset OPSM": [null,null,null,null,null,735210.0],
              "Biaya Gaji Karyawan OPSM": [null,null,null,null,null,51904394.0],
              "Biaya Insentif, Bonus, Fee OPSM": [null,null,null,null,null,17355472.0],
              "Beban Freelance OPSM": [null,null,null,null,null,50000.0],
              "Biaya Perjalanan Dinas OPSM": [null,null,null,null,null,null]
          } },
          "Bakery Jati Melati": { vals:[null,null,null,null,null,35365176.62], children: {
              "Biaya Listrik & Air BKJM": [null,null,null,null,null,3207000.0],
              "Biaya Internet & Telepon BKJM": [null,null,null,null,null,557682.62],
              "Biaya Alat Tulis Kantor BKJM": [null,null,null,null,null,620500.0],
              "Beban Tunjangan Hari Raya BKJM": [null,null,null,null,null,null],
              "Biaya System POS BKJM": [null,null,null,null,null,250000.0],
              "Biaya Transport BKJM": [null,null,null,null,null,395500.0],
              "Biaya Kebersihan & Pengharum BKJM": [null,null,null,null,null,949300.0],
              "Biaya Operasionalonal Lainnya BKJM": [null,null,null,null,null,1266925.0],
              "Biaya Sewa Bangunan BKJM": [null,null,null,null,null,7083333.0],
              "Beban Maintenance Aset BKJM": [null,null,null,null,null,null],
              "Biaya Gaji Karyawan BKJM": [null,null,null,null,null,15968245.0],
              "Biaya Insentif, Bonus, Fee BKJM": [null,null,null,null,null,4795491.0],
              "Beban Freelance BKJM": [null,null,null,null,null,null],
              "Biaya Perjalanan Dinas BKJM": [null,null,null,null,null,271200.0]
          } },
          "Bakery Nusa Indah": { vals:[null,null,null,null,null,24464937.0], children: {
              "Biaya Listrik & Air BKNI": [null,null,null,null,null,2268700.0],
              "Biaya Internet & Telepon BKNI": [null,null,null,null,null,513613.0],
              "Biaya Alat Tulis Kantor BKNI": [null,null,null,null,null,494000.0],
              "Beban Tunjangan Hari Raya BKNI": [null,null,null,null,null,null],
              "Biaya System POS BKNI": [null,null,null,null,null,250000.0],
              "Biaya Transport BKNI": [null,null,null,null,null,237000.0],
              "Biaya Kebersihan & Pengharum BKNI": [null,null,null,null,null,771800.0],
              "Biaya Operasionalonal Lainnya BKNI": [null,null,null,null,null,854475.0],
              "Biaya Sewa Bangunan BKNI": [null,null,null,null,null,4583333.0],
              "Biaya Maintenance Aset BKNI": [null,null,null,null,null,null],
              "Biaya Gaji Karyawan BKNI": [null,null,null,null,null,10755285.0],
              "Biaya Insentif, Bonus, Fee BKNI": [null,null,null,null,null,3465531.0],
              "Beban Freelance BKNI": [null,null,null,null,null,null],
              "Biaya Perjalanan Dinas BKNI": [null,null,null,null,null,271200.0]
          } },
          "Mini Bakery Cibinong": { vals:[null,null,null,null,null,19249238.0], children: {
              "Biaya Listrik & Air BKCN": [null,null,null,null,null,1025736.0],
              "Biaya Internet & Telepon BKCN": [null,null,null,null,null,300163.0],
              "Biaya Alat Tulis Kantor BKCN": [null,null,null,null,null,68212.0],
              "Beban Tunjangan Hari Raya BKCN": [null,null,null,null,null,null],
              "Biaya System POS BKCN": [null,null,null,null,null,250000.0],
              "Beban Transport BKCN": [null,null,null,null,null,null],
              "Biaya Kebersihan & Pengharum BKCN": [null,null,null,null,null,805629.0],
              "Biaya Operasionalonal Lainnya BKCN": [null,null,null,null,null,1211445.0],
              "Biaya Sewa Bangunan BKCN": [null,null,null,null,null,3250000.0],
              "Beban Maintenance Aset BKCN": [null,null,null,null,null,833000.0],
              "Biaya Gaji Karyawan BKCN": [null,null,null,null,null,9261226.0],
              "Biaya Insentif, Bonus, Fee BKCN": [null,null,null,null,null,2209627.0],
              "Beban Freelance BKCN": [null,null,null,null,null,null],
              "Biaya Perjalanan Dinas BKCN": [null,null,null,null,null,34200.0]
          } },
          "Bakery Bantar Gebang": { vals:[null,null,null,null,null,29315218.62], children: {
              "Biaya Listrik & Air BKBN": [null,null,null,null,null,3507000.0],
              "Biaya Internet & Telepon BKBN": [null,null,null,null,null,458112.62],
              "Biaya Alat Tulis Kantor BKBN": [null,null,null,null,null,284000.0],
              "Beban Tunjangan Hari Raya BKBN": [null,null,null,null,null,null],
              "Biaya System POS BKBN": [null,null,null,null,null,250000.0],
              "Beban Transport BKBN": [null,null,null,null,null,91000.0],
              "Biaya Kebersihan & Pengharum BKBN": [null,null,null,null,null,710800.0],
              "Biaya Operasionalonal Lainnya BKBN": [null,null,null,null,null,641101.0],
              "Biaya Sewa Bangunan BKBN": [null,null,null,null,null,7500000.0],
              "Biaya Maintenance Aset BKBN": [null,null,null,null,null,423600.0],
              "Biaya Gaji Karyawan BKBN": [null,null,null,null,null,11797023.0],
              "Biaya Insentif, Bonus, Fee BKBN": [null,null,null,null,null,3618382.0],
              "Beban Freelance BKBN": [null,null,null,null,null,null],
              "Biaya Perjalanan Dinas BKBN": [null,null,null,null,null,34200.0]
          } },
          "Bakery Abdul Gani": { vals:[null,null,null,null,null,45218892.24], children: {
              "Biaya Listrik & Air BKAG": [null,null,null,null,null,4365031.0],
              "Biaya Internet & Telepon BKAG": [null,null,null,null,null,483312.62],
              "Biaya Alat Tulis Kantor BKAG": [null,null,null,null,null,262174.0],
              "Beban Tunjangan Hari Raya BKAG": [null,null,null,null,null,null],
              "Biaya System POS BKAG": [null,null,null,null,null,250000.0],
              "Biaya Transport BKAG": [null,null,null,null,null,null],
              "Biaya Kebersihan & Pengharum BKAG": [null,null,null,null,null,723436.0],
              "Biaya Operasionalonal Lainnya BKAG": [null,null,null,null,null,1141497.0],
              "Biaya Sewa Bangunan BKAG": [null,null,null,null,null,7083333.0],
              "Biaya Maintenance Aset BKAG": [null,null,null,null,null,null],
              "Biaya Gaji Karyawan BKAG": [null,null,null,null,null,12185027.0],
              "Biaya Insentif, Bonus, Fee BKAG": [null,null,null,null,null,4485568.0],
              "Beban Freelance BKAG": [null,null,null,null,null,null],
              "Biaya Perjalanan Dinas BKAG": [null,null,null,null,null,34200.0]
          } },
          "Bakery Pangleseran": { vals:[null,null,null,null,null,1213138.0], children: {
              "Biaya Alat Tulis Kantor BKPS": [null,null,null,null,null,353500.0],
              "Biaya Transport BKPS": [null,null,null,null,null,60500.0],
              "Biaya Kebersihan & Pengharum BKPS": [null,null,null,null,null,103000.0],
              "Biaya Operasionalonal Lainnya BKPS": [null,null,null,null,null,696138.0],
              "Biaya Listrik & Air BKPS": [null,null,null,null,null,1523172.0],
              "Biaya Internet & Telepon BKPS": [null,null,null,null,null,125112.62],
              "Beban Tunjangan Hari Raya BKPS": [null,null,null,null,null,null],
              "Biaya System POS BKPS": [null,null,null,null,null,191667.0],
              "Biaya Sewa Bangunan BKPS": [null,null,null,null,null,1875000.0],
              "Biaya Maintenance Aset BKPS": [null,null,null,null,null,null],
              "Biaya Gaji Karyawan BKPS": [null,null,null,null,null,7472837.0],
              "Biaya Insentif, Bonus, Fee BKPS": [null,null,null,null,null,3017525.0],
              "Beban Freelance BKPS": [null,null,null,null,null,null],
              "Biaya Perjalanan Dinas BKPS": [null,null,null,null,null,null]
          } }
        } },
      { name:'Pendapatan Lain-lain', accountLabel:'Pendapatan Lain-lain', computed:true, forceFormula:true, signRule:'absSubtractExceptPendapatan', values:[null,null,null,null,null,null],
        note:'DIHITUNG: Pendapatan Bunga Bank + Pendapatan Lainnya - Biaya Administrasi Bank (pakai signRule supaya aman dari salah tanda kalau live data mengisi Biaya Administrasi Bank sbg angka positif -- akan tetap dikurangkan, bukan ditambah).',
        children: {
          "Pendapatan Bunga Bank": [null,null,null,null,null,184572.0],
          "Pendapatan Lainnya": [null,null,null,null,null,50000.0],
          "Biaya Administrasi Bank": { vals:[null,null,null,null,null,1262414.0], pathHint: 'Biaya lain-lain' },
        } },
      { name:'Laba Operasional', highlight:true, accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Laba Kotor - Biaya Operasional + Pendapatan Lain-lain (sesuai instruksi eksplisit user).' },
      { name:'Biaya Depresiasi', accountLabel:'Biaya Depresiasi', values:[null,null,null,null,null,6718077.0] },
      { name:'Biaya Pajak', accountLabel:'Biaya Pajak', forceFormula:true, computeFromChildren:true, treatMissingAsZero:true, values:[null,null,null,null,null,null],
        children: {
          "Biaya PPh Final": [null,null,null,null,null,2376733.0],
          "Biaya Pajak Daerah & Reklame": [null,null,null,null,null,35185493.0],
          "Biaya PPh 21 Karyawan": [null,null,null,null,null,null],
          "Pajak Badan": [null,null,null,null,null,null]
        } },
      { name:'Cost of Management', accountLabel:'Cost of Management', values:[null,null,null,null,null,370853224.0] },
      { name:'Laba Bersih', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Laba Operasional - Biaya Depresiasi - Biaya Pajak - Cost of Management.' },
    ]
  },
  manufaktur: {
    label: 'Manufaktur', color: '#22D3C5', grounded: 'real',
    waterfall: [
      { name:'Pendapatan', highlight:true, accountLabel:'Pendapatan', computed:true, forceFormula:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Pendapatan Factory + Aksesoris + Margin Kresek - Retur (TIDAK termasuk Pendapatan Lain-lain, itu di tahap Pendapatan Bersih).',
        children: {
          "Pendapatan Factory": [null,null,null,null,null,null],
          "Pendapatan Aksesoris": [null,null,null,null,null,null],
          "Pendapatan Margin Kresek": [null,null,null,null,null,null],
          "Retur": [null,null,null,null,null,null]
        } },
      { name:'Pendapatan Lain-lain', accountLabel:'Pendapatan Lain-lain', computed:true, forceFormula:true, values:[null,null,null,null,null,null],
        children: {
          "Pendapatan Limbah": [null,null,null,null,null,null],
          "Pendapatan Bunga Bank": [null,null,null,null,null,null]
        } },
      { name:'Pendapatan Bersih', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Pendapatan + Pendapatan Lain-lain.' },
      { name:'HPP Bahan Baku', accountLabel:'HPP Bahan Baku', values:[null,null,null,null,null,null] },
      { name:'Biaya Overhead Pabrik', accountLabel:'Biaya Overhead Pabrik', computed:true, forceFormula:true, values:[null,null,null,null,null,null],
        children: {
          "Biaya Listrik & Air Factory": [null,null,null,null,null,null],
          "Biaya Gaji Produksi": [null,null,null,null,null,null],
          "Biaya Freelance & Lembur Factory": [null,null,null,null,null,null],
          "Biaya Tunjangan Hari Raya": [null,null,null,null,null,null]
        } },
      { name:'Harga Pokok Produksi', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: HPP Bahan Baku + Biaya Overhead Pabrik.' },
      { name:'Laba Kotor', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Pendapatan Bersih - Harga Pokok Produksi.' },
      { name:'Biaya Operasional', accountLabel:'Biaya Operasional', computed:true, forceFormula:true, values:[null,null,null,null,null,null],
        children: {
          "Biaya Gaji Administrasi & Managerial": [null,null,null,null,null,null],
          "Biaya Freelance Delivery & Non Produksi": [null,null,null,null,null,null],
          "Biaya Sewa": [null,null,null,null,null,null],
          "Biaya Internet & Telepon": [null,null,null,null,null,null],
          "Biaya ATK": [null,null,null,null,null,null],
          "Biaya Distribusi": [null,null,null,null,null,null],
          "Biaya Maintenance Aset": [null,null,null,null,null,null],
          "Biaya Maintenance Kendaraan": [null,null,null,null,null,null],
          "Biaya Benefit Bulanan": [null,null,null,null,null,null],
          "Biaya Bpjs Tk & Kesehatan": [null,null,null,null,null,null],
          "Biaya Perjalanan Dinas": [null,null,null,null,null,null],
          "Biaya Kebersihan": [null,null,null,null,null,null],
          "Biaya Entertain & Jamuan": [null,null,null,null,null,null],
          "Biaya Kompensasi Karyawan": [null,null,null,null,null,null],
          "Biaya Operasional Lain-Lain": [null,null,null,null,null,null],
          "Biaya Administrasi Bank": [null,null,null,null,null,null]
        } },
      { name:'Laba Operasional', highlight:true, accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Laba Kotor - Biaya Operasional.' },
      { name:'Biaya Penyusutan', accountLabel:'Biaya Penyusutan', computed:true, forceFormula:true, values:[null,null,null,null,null,null],
        children: {
          "Biaya Penyusutan Mesin & Peralatan Cibadak": [null,null,null,null,null,null],
          "Biaya Penyusutan Mesin & Peralatan Warehouse": [null,null,null,null,null,null],
          "Biaya Penyusutan Mesin & Peralatan Karang Tengah": [null,null,null,null,null,null],
          "Biaya Penyusutan Bangunan": [null,null,null,null,null,null],
          "Biaya Penyusutan Kendaraan Cibadak": [null,null,null,null,null,null],
          "Biaya Penyusutan Kendaraan Karang Tengah": [null,null,null,null,null,null]
        } },
      { name:'Biaya Pajak', accountLabel:'Biaya Pajak', computed:true, forceFormula:true, treatMissingAsZero:true, values:[null,null,null,null,null,null],
        note:'PERINGATAN: Path 4 item pajak ini di source SALAH (2 item share Path yg sama scr keliru) -- diperlakukan sbg 4 item independen sesuai rumus eksplisit Anda, mengabaikan Path yg salah itu.',
        children: {
          "PPN": [null,null,null,null,null,null],
          "Pph 25": [null,null,null,null,null,null],
          "Pph 21": [null,null,null,null,null,null],
          "Pajak Daerah": [null,null,null,null,null,null]
        } },
      { name:'Cost of Management', accountLabel:'Cost Of Management', values:[null,null,null,null,null,null] },
      { name:'Laba Bersih', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Laba Operasional - Biaya Penyusutan - Biaya Pajak - Cost of Management.' },
    ]
  },
  ho: {
    label: 'Head Office', color: '#A78BFA', grounded: 'real',
    waterfall: [
      { name:'Pendapatan', highlight:true, accountLabel:'Pendapatan', computed:true, forceFormula:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Management Fee Ownership + Pendapatan Camelia + HPP Operasional 5% Ownership + Pendapatan dari Prospek Franchise + Sewa Mobil FUSO + Management Fee & Sharing Profit Franchise + Pendapatan Sales Eksekutif (bersih setelah potongan).',
        children: {
          'Management Fee Ownership': { vals:[null,null,null,null,null,null], children: {
            "Bakery Sudirman": [null,null,null,null,null,null],
            "Bakery Cikole": [null,null,null,null,null,null],
            "Bakery Bangbarung": [null,null,null,null,null,null],
            "Bakery Dramaga": [null,null,null,null,null,null],
            "Bakery Cibubur": [null,null,null,null,null,null],
            "Bakery Pekapuran": [null,null,null,null,null,null],
            "Bakery Mekarsari": [null,null,null,null,null,null],
            "Bakery Jati Melati": [null,null,null,null,null,null],
            "Bakery Nusa Indah": [null,null,null,null,null,null],
            "Lapis Durian Medan": [null,null,null,null,null,null],
            "Bakery Cibinong": [null,null,null,null,null,null],
            "Bakery Bantar Gebang": [null,null,null,null,null,null],
            "Bakery Abdul Gani": [null,null,null,null,null,null],
            "Bakery Pangleseran": [null,null,null,null,null,null],
            "Factory": [null,null,null,null,null,null],
            "Outlet": [null,null,null,null,null,null]
          } },
          'Pendapatan Camelia': [null,null,null,null,null,null],
          'HPP Operasional 5% Ownership': [null,null,null,null,null,null],
          'Pendapatan Dari Prospek Franchise': [null,null,null,null,null,null],
          'Sewa Mobil FUSO': [null,null,null,null,null,null],
          'Management Fee & Sharing Profit Franchise': { vals:[null,null,null,null,null,null], children: {
            "Bakery Pagelaran": [null,null,null,null,null,null],
            "Bakery Benhil": [null,null,null,null,null,null],
            "Bakery Cipayung": [null,null,null,null,null,null],
            "Bakery Harjamukti": [null,null,null,null,null,null],
            "Bakery Cikaret": [null,null,null,null,null,null],
            "Bakery Rawalumbu": [null,null,null,null,null,null],
            "Bakery Perumnas": [null,null,null,null,null,null],
            "Bakery Kejayaan": [null,null,null,null,null,null],
            "Bakery Sukahati": [null,null,null,null,null,null],
            "Bakery RTM Depok": [null,null,null,null,null,null],
            "Bakery Vila Bogor Indah": [null,null,null,null,null,null]
          } },
          'Pendapatan Sales Eksekutif (Bersih)': { vals:[null,null,null,null,null,null], computed:true, forceFormula:true, children: {
              'Pendapatan Sales Eksekutif': [null,null,null,null,null,null],
              'Potongan Pendapatan Sales Eksekutif': [null,null,null,null,null,null],
          } },
          'Pendapatan Live Tiktok (Bersih)': { vals:[null,null,null,null,null,null], computed:true, forceFormula:true, children: {
              'Pendapatan Live Tiktok': [null,null,null,null,null,43219000.0],
              'Potongan Pendapatan Live Tiktok': { vals:[null,null,null,null,null,null], pathHint: 'Pendapatan Live Tiktok' },
              'Potongan Komisi Live Tiktok': { vals:[null,null,null,null,null,null], pathHint: 'Pendapatan Sales Eksekutif' },
          } },
          // CATATAN PENTING: Path "Potongan Pendapatan Live Tiktok" di source
          // SALAH -- ke-nested di bawah "Pendapatan Sales Eksekutif" (bug
          // copy-paste), BUKAN di bawah "Pendapatan Live Tiktok" spt harusnya.
          // Saya taruh manual di sini sesuai maksud user ("berikut dgn
          // potongannya"), pakai pathHint 'Pendapatan Live Tiktok' agar tetap
          // bisa live-match meski Path aslinya keliru.
          'Pendapatan Kangridwan': [null,null,null,null,null,5250000.0],
          // CATATAN: item di atas dijumlahkan APA ADANYA (bukan dikurangkan
          // eksplisit) -- ASUMSI belum terverifikasi (file ini blank semua,
          // tidak ada angka utk dicek): "Potongan" diasumsikan SUDAH tersimpan
          // negatif di source, mengikuti pola "Diskon"/"Potongan" lain yg
          // sudah dikonfirmasi negatif di entity lain. Kalau ternyata positif
          // di source, hasil akan salah arah (menambah, bukan mengurangi).
        } },
      { name:'Harga Pokok Penjualan', accountLabel:'Harga Pokok Penjualan', computed:true, forceFormula:true, values:[null,null,null,null,null,null],
        children: {
            "Gaji Staf & Manajemen": [null,null,null,null,null,null],
            "Biaya BPJS Kesehatan & TK": [null,null,null,null,null,null],
            "HPP Pendapatan Sales Eksekutif": [null,null,null,null,null,null],
            "HPP Pendapatan Live Tiktok": [null,null,null,null,null,null]
          } },
      { name:'Laba Kotor', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Pendapatan - Harga Pokok Penjualan.' },
      { name:'Biaya Operasional', accountLabel:'Biaya Operasional', computed:true, forceFormula:true, values:[null,null,null,null,null,null],
        children: {
          "Divisi Marketing": { vals:[null,null,null,null,null,null], children: {
            "Biaya Activity Marketing": [null,null,null,null,null,null],
            "Biaya Kang Ridwan Official": [null,null,null,null,null,null]
          } },
          "Divisi FA": { vals:[null,null,null,null,null,null], children: {
            "Biaya Perjalanan Dinas": [null,null,null,null,null,null],
            "Biaya Sistem Keuangan": [null,null,null,null,null,null],
            "Biaya Konsultan Pajak": [null,null,null,null,null,null]
          } },
          "Divisi HR": { vals:[null,null,null,null,null,null], children: {
            "Biaya Perjalanan Dinas HR": [null,null,null,null,null,null],
            "Biaya IT System HR": [null,null,null,null,null,null],
            "Biaya Transport Lokal": [null,null,null,null,null,null],
            "Biaya Training Khusus HR": [null,null,null,null,null,null]
          } },
          "Sales Eksekutif (Divisi)": { vals:[null,null,null,null,null,null], children: {
            "Biaya Perjalanan Dinas Sales": [null,null,null,null,null,null],
            "Biaya Sales Tools": [null,null,null,null,null,null],
            "Biaya Transport Lokal Sales": [null,null,null,null,null,null],
            "Biaya Akomodasi Sales": [null,null,null,null,null,null],
            "Biaya Entertain & Jamuan Sales": [null,null,null,null,null,null],
            "Biaya Sales Event": [null,null,null,null,null,null],
            "Insentif Sales": [null,null,null,null,null,null]
          } },
          "Corsec": { vals:[null,null,null,null,null,null], children: {
            "Biaya Perjalanan Dinas Corsec": [null,null,null,null,null,null],
            "Biaya Entertain & Jamuan Corsec": [null,null,null,null,null,null]
          } },
          "Top Level": { vals:[null,null,null,null,null,null], children: {
            "Biaya Perjalanan Dinas Dirut": [null,null,null,null,null,null],
            "Biaya Akomodasi Dirut": [null,null,null,null,null,null],
            "Biaya Perjalanan Dinas GM": [null,null,null,null,null,null],
            "Biaya Entertain & Jamuan": [null,null,null,null,null,null],
            "Biaya Training & Seminar": [null,null,null,null,null,null]
          } },
          "Umum": { vals:[null,null,null,null,null,null], children: {
            "Biaya General Internal Training": [null,null,null,null,null,null],
            "Biaya General External Training": [null,null,null,null,null,null],
            "Biaya Coaching & Pendampingan External": [null,null,null,null,null,null],
            "Biaya Konsumsi & Meeting Office": [null,null,null,null,null,null],
            "Biaya Sharing Season & Seminar Internal": [null,null,null,null,null,null],
            "Biaya Listrik & Air Office": [null,null,null,null,null,null],
            "Biaya Telp & Internet Office": [null,null,null,null,null,null],
            "Biaya Sewa Bangunan Office": [null,null,null,null,null,null],
            "Biaya Maintenance Aset Office": [null,null,null,null,null,null],
            "Biaya Sumbangan & Hadiah": [null,null,null,null,null,null],
            "Biaya ATK, Fotocopy, Materai": [null,null,null,null,null,null],
            "Biaya Maintenance Kendaraan Office": [null,null,null,null,null,null],
            "Biaya Operasional Head Office": [null,null,null,null,null,null],
            "Kompensasi, Benefit, Bonus Karyawan": [null,null,null,null,null,null],
            "Biaya Akad Bank": [null,null,null,null,null,null],
            "Biaya External Training": [null,null,null,null,null,null],
            "Biaya Administrasi Bank": [null,null,null,null,null,null]
          } },
          "Camelia": { vals:[null,null,null,null,null,null], children: {
            "Beban Listrik Camelia": [null,null,null,null,null,null],
            "Beban Internet & Telp Camelia": [null,null,null,null,null,null],
            "Beban Gaji Camelia": [null,null,null,null,null,null],
            "Beban Operasional Lainnya Camelia": [null,null,null,null,null,null]
          } }
        } },
      { name:'Laba Operasional', highlight:true, accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Laba Kotor - Biaya Operasional.' },
      { name:'Biaya Depresiasi', accountLabel:'Biaya Depresiasi', values:[null,null,null,null,null,null] },
      { name:'Bunga Bank', accountLabel:'Bunga Bank', computed:true, forceFormula:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Pendapatan Bunga Bank - Biaya Bunga Bank (net, sesuai rumus: -bunga bank +pendapatan bunga bank).',
        children: {
          'Pendapatan Bunga Bank': [null,null,null,null,null,null],
          'Biaya Bunga Bank': [null,null,null,null,null,null],
        } },
      { name:'Laba Bersih', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Laba Operasional - Biaya Depresiasi + Bunga Bank (net, sudah termasuk tanda didalamnya).' },
    ]
  },
  ownership: {
    label: 'Ownership', color: '#FFC93C', grounded: 'real',
    waterfall: [
      { name:'Pendapatan', highlight:true, accountLabel:'Pendapatan', computed:true, forceFormula:true, values:[null,null,null,null,null,null],
        children: {
            "Pendapatan Offline": [null,null,null,null,null,null],
            "Pendapatan Online": [null,null,null,null,null,null],
            "Pendapatan Konsinyasi": [null,null,null,null,null,null],
            "Pendapatan Sales Eksekutif": [null,null,null,null,null,null],
            "MBG Manufactur": [null,null,null,null,null,null],
            "Management Fee & Sharing Profit Franchise": { vals:[null,null,null,null,null,null], children: {
              "Bakery Pagelaran": { vals:[null,null,null,null,null,null], pathHint: "Management Fee & Sharing Profit Franchise" },
              "Bakery Benhil": { vals:[null,null,null,null,null,null], pathHint: "Management Fee & Sharing Profit Franchise" },
              "Bakery Cipayung": { vals:[null,null,null,null,null,null], pathHint: "Management Fee & Sharing Profit Franchise" },
              "Bakery Harjamukti": { vals:[null,null,null,null,null,null], pathHint: "Management Fee & Sharing Profit Franchise" },
              "Bakery Cikaret": { vals:[null,null,null,null,null,null], pathHint: "Management Fee & Sharing Profit Franchise" },
              "Bakery Rawalumbu": { vals:[null,null,null,null,null,null], pathHint: "Management Fee & Sharing Profit Franchise" },
              "Bakery Perumnas": { vals:[null,null,null,null,null,null], pathHint: "Management Fee & Sharing Profit Franchise" },
              "Bakery Kejayaan": { vals:[null,null,null,null,null,null], pathHint: "Management Fee & Sharing Profit Franchise" },
              "Bakery Sukahati": { vals:[null,null,null,null,null,null], pathHint: "Management Fee & Sharing Profit Franchise" },
              "Bakery RTM Depok": { vals:[null,null,null,null,null,null], pathHint: "Management Fee & Sharing Profit Franchise" },
              "Bakery Vila Bogor Indah": { vals:[null,null,null,null,null,null], pathHint: "Management Fee & Sharing Profit Franchise" }
            } },
            "HPP Franchise": { vals:[null,null,null,null,null,null], children: {
              "Bakery Pagelaran": { vals:[null,null,null,null,null,null], pathHint: "HPP Franchise" },
              "Bakery Benhil": { vals:[null,null,null,null,null,null], pathHint: "HPP Franchise" },
              "Bakery Cipayung": { vals:[null,null,null,null,null,null], pathHint: "HPP Franchise" },
              "Bakery Harjamukti": { vals:[null,null,null,null,null,null], pathHint: "HPP Franchise" },
              "Bakery Cikaret": { vals:[null,null,null,null,null,null], pathHint: "HPP Franchise" },
              "Bakery Rawalumbu": { vals:[null,null,null,null,null,null], pathHint: "HPP Franchise" },
              "Bakery Perumnas": { vals:[null,null,null,null,null,null], pathHint: "HPP Franchise" },
              "Bakery Kejayaan": { vals:[null,null,null,null,null,null], pathHint: "HPP Franchise" },
              "Bakery Sukahati": { vals:[null,null,null,null,null,null], pathHint: "HPP Franchise" },
              "Bakery RTM Depok": { vals:[null,null,null,null,null,null], pathHint: "HPP Franchise" },
              "Bakery Vila Bogor Indah": { vals:[null,null,null,null,null,null], pathHint: "HPP Franchise" }
            } },
            "Pendapatan Camelia": [null,null,null,null,null,null],
            "Pendapatan Lain-Lain": [null,null,null,null,null,null],
            "Pendapatan Live Tiktok": [null,null,null,null,null,null],
            "Pendapatan Kangridwan": [null,null,null,null,null,null]
        } },
      { name:'Diskon', accountLabel:'Diskon', computed:true, forceFormula:true, noNegColor:true, values:[null,null,null,null,null,null],
        children: {
            "Diskon Offline": [null,null,null,null,null,null],
            "Diskon Online": [null,null,null,null,null,null],
            "Diskon MBG": [null,null,null,null,null,null],
            "Cashback MBG": [null,null,null,null,null,null],
            "Potongan/Komisi Aplikasi/Merchant": [null,null,null,null,null,null],
            "Diskon Penjualan Sales": [null,null,null,null,null,null],
            "Potongan/Komisi Aplikasi/Merchant Live Tiktok": [null,null,null,null,null,null],
            "Diskon Pendapatan Live Tiktok": [null,null,null,null,null,null]
        } },
      { name:'Pendapatan Bersih', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Pendapatan + Diskon (diskon sudah negatif di source).' },
      { name:'Harga Pokok Penjualan', accountLabel:'Harga Pokok Penjualan', computed:true, forceFormula:true, values:[null,null,null,null,null,null],
        note:'Berisi HPP Bahan Baku Utama + HPP Konsinyasi (bersama = "HPP Material" scr konsep) + Biaya Overhead Pabrik -- "HPP Material" bukan grup Path yg nyata di source, cuma penjumlahan 2 item pertama scr konsep, hasil akhir sama.',
        children: {
            "HPP Bahan Baku Utama": [null,null,null,null,null,null],
            "HPP Konsinyasi": [null,null,null,null,null,null],
            "Biaya Overhead Pabrik": { vals:[null,null,null,null,null,null], children: {
              "Biaya Listrik & Air Factory": { vals:[null,null,null,null,null,null], children: {
                "Biaya Listrik & Air": [null,null,null,null,null,null]
              } },
              "Biaya Gaji Produksi": [null,null,null,null,null,null],
              "Biaya Freelance & Lembur Factory": [null,null,null,null,null,null],
              "Biaya Tunjangan Hari Raya": [null,null,null,null,null,null],
              "Biaya Distribusi": [null,null,null,null,null,null]
            } }
        } },
      { name:'Laba Kotor', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Pendapatan Bersih - Harga Pokok Penjualan.' },
      { name:'Biaya Operasional', accountLabel:'Biaya Operasional', computed:true, forceFormula:true, values:[null,null,null,null,null,null],
        children: {
            "Biaya Operasional Manufaktur": { vals:[null,null,null,null,null,null], children: {
              "Biaya Gaji Administrasi & Managerial": [null,null,null,null,null,null],
              "Biaya Freelance Delivery": [null,null,null,null,null,null],
              "Biaya Sewa": [null,null,null,null,null,null],
              "Biaya Internet & Telepon": [null,null,null,null,null,null],
              "Biaya ATK": [null,null,null,null,null,null],
              "Biaya Maintetance Aset": [null,null,null,null,null,null],
              "Biaya Maintenance Kendaraan": [null,null,null,null,null,null],
              "Biaya Tunjangan Bpjs Tk & Ketenagakerjaan": [null,null,null,null,null,null],
              "Biaya Perjalanan Dinas": [null,null,null,null,null,null],
              "Biaya Kebersihan": [null,null,null,null,null,null],
              "Biaya Entertain & Jamuan": { vals:[null,null,null,null,null,null], pathHint: "Biaya Operasional Manufaktur" },
              "Biaya Kompensasi Karyawan": [null,null,null,null,null,null],
              "Biaya Operasional Lainnya": [null,null,null,null,null,null],
              "Biaya Administrasi Bank": { vals:[null,null,null,null,null,null], pathHint: "Biaya Operasional Manufaktur" }
            } },
            "Biaya Operasional Head Office": { vals:[null,null,null,null,null,null], children: {
              "Gaji Staf & Manajemen": [null,null,null,null,null,null],
              "Tunjangan & Insentif Office": [null,null,null,null,null,null],
              "Biaya BPJS Kesehatan & TK": [null,null,null,null,null,null],
              "Biaya Perjalan Dinas FA": [null,null,null,null,null,null],
              "Biaya System Keuangan": [null,null,null,null,null,null],
              "Biaya Konsultan Pajak": [null,null,null,null,null,null],
              "Biaya Transport Lokal FA": [null,null,null,null,null,null],
              "Biaya Perjalanan Dinas HR": [null,null,null,null,null,null],
              "Biaya IT System HR": [null,null,null,null,null,null],
              "Biaya Transport Lokal HR": [null,null,null,null,null,null],
              "Biaya Training Khusus HR": [null,null,null,null,null,null],
              "Biaya Perjalanan Dinas Sales": [null,null,null,null,null,null],
              "Biaya Sales Tools": [null,null,null,null,null,null],
              "Biaya Transport Lokal Sales": [null,null,null,null,null,null],
              "Biaya Akomodasi Sales": [null,null,null,null,null,null],
              "Biaya Entertain & Jamuan Sales": [null,null,null,null,null,null],
              "Biaya Sales Event": [null,null,null,null,null,null],
              "Biaya Insentif Sales": [null,null,null,null,null,null],
              "Biaya Perjalanan Dinas Corsec": [null,null,null,null,null,null],
              "Biaya Entertain & Jamuan Corsec": [null,null,null,null,null,null],
              "Biaya Perjalanan Dinas Dirut": [null,null,null,null,null,null],
              "Biaya Akomodasi Dirut": [null,null,null,null,null,null],
              "Biaya Perjalanan Dinas GM": [null,null,null,null,null,null],
              "Biaya Entertain & Jamuan": { vals:[null,null,null,null,null,null], pathHint: "Biaya Operasional Head Office" },
              "Biaya Lain-lain Dirut": [null,null,null,null,null,null],
              "Biaya Activity Marketing": [null,null,null,null,null,null],
              // DITAMBAHKAN: sebelumnya akun ini TIDAK ADA sama sekali di struktur
              // entity Ownership (padahal ADA di Head Office & Konsolidasi) --
              // itu sebabnya data dari sheet (Entity=Ownership) tidak pernah bisa
              // muncul, BUKAN soal live-fetch/nama akun tidak cocok. Posisi
              // ditaruh sejajar dgn Konsolidasi (setelah Biaya Activity Marketing).
              "Biaya Kang Ridwan Official": [null,null,null,null,null,null],
              "Biaya Training & Seminar": [null,null,null,null,null,null],
              "Biaya General": { vals:[null,null,null,null,null,null], children: {
                "Biaya External Training": [null,null,null,null,null,null],
                "Biaya Coaching & Pendampingan External": [null,null,null,null,null,null],
                "Biaya Konsumsi & Meeting Office": [null,null,null,null,null,null],
                "Biaya Sharing Season & Seminar Internal": [null,null,null,null,null,null],
                "Biaya Listrik & Air Office": [null,null,null,null,null,null],
                "Biaya Telp & Internet Office": [null,null,null,null,null,null],
                "Biaya Sewa Bangunan Office": [null,null,null,null,null,null],
                "Biaya Maintenance Aset Office": [null,null,null,null,null,null],
                "Biaya Sumbangan & Hadiah": [null,null,null,null,null,null],
                "Biaya ATK, Fotocopy, Materai": [null,null,null,null,null,null],
                "Biaya Maintenance Kendaraan Office": [null,null,null,null,null,null],
                "Biaya Operasional Head Office": [null,null,null,null,null,null],
                "Biaya Administrasi Bank": { vals:[null,null,null,null,null,null], pathHint: "Biaya General" },
                "Kompensasi, Benefit, Bonus Karyawan": [null,null,null,null,null,null],
                "Biaya Akad Bank": [null,null,null,null,null,null]
              } },
              "Camelia Kost": { vals:[null,null,null,null,null,null], children: {
                "Biaya Listrik Camelia": [null,null,null,null,null,null],
                "Biaya Internet & Telp Camelia": [null,null,null,null,null,null],
                "Biaya Gaji Camelia": [null,null,null,null,null,null],
                "Biaya Operasional Lainnya Camelia": [null,null,null,null,null,null]
              } }
            } },
            "Biaya Operasional Store Brand": { vals:[null,null,null,null,null,null], children: {
              "Biaya Operasional Bakery Sudirman": { vals:[null,null,null,null,null,null], children: {
                "Biaya Listrik & Air BKSM": [null,null,null,null,null,null],
                "Biaya Internet & Telepon BKSM": [null,null,null,null,null,null],
                "Biaya Alat Tulis Kantor BKSM": [null,null,null,null,null,null],
                "Beban Tunjangan Hari Raya BKSM": [null,null,null,null,null,null],
                "Biaya System POS BKSM": [null,null,null,null,null,null],
                "Biaya Transport BKSM": [null,null,null,null,null,null],
                "Biaya Kebersihan & Pengharum BKSM": [null,null,null,null,null,null],
                "Biaya Operasionalonal Lainnya BKSM": [null,null,null,null,null,null],
                "Biaya Sewa Bangunan BKSM": [null,null,null,null,null,null],
                "Biaya Maintenance Aset BKSM": [null,null,null,null,null,null],
                "Biaya Gaji Karyawan BKSM": [null,null,null,null,null,null],
                "Biaya Insentif, Bonus, Fee BKSM": [null,null,null,null,null,null],
                "Biaya Freelance BKSM": [null,null,null,null,null,null],
                "Beban Perjalanan Dinas BKSM": [null,null,null,null,null,null]
              } },
              "Biaya Operasional Bakery Cikole": { vals:[null,null,null,null,null,null], children: {
                "Biaya Listrik & Air BKGD": [null,null,null,null,null,null],
                "Biaya Internet & Telepon BKGD": [null,null,null,null,null,null],
                "Biaya Alat Tulis Kantor BKGD": [null,null,null,null,null,null],
                "Beban Tunjangan Hari Raya BKGD": [null,null,null,null,null,null],
                "Biaya System POS BKGD": [null,null,null,null,null,null],
                "Biaya Transport BKGD": [null,null,null,null,null,null],
                "Biaya Kebersihan & Pengharum BKGD": [null,null,null,null,null,null],
                "Biaya Operasionalonal Lainnya BKGD": [null,null,null,null,null,null],
                "Biaya Sewa Bangunan BKGD": [null,null,null,null,null,null],
                "Biaya Maintenance Aset BKGD": [null,null,null,null,null,null],
                "Biaya Gaji Karyawan BKGD": [null,null,null,null,null,null],
                "Biaya Insentif, Bonus, Fee BKGD": [null,null,null,null,null,null],
                "Biaya Freelance BKGD": [null,null,null,null,null,null],
                "Beban Perjalanan Dinas BKGD": [null,null,null,null,null,null]
              } },
              "Biaya Operasional Bakery Bangbarung": { vals:[null,null,null,null,null,null], children: {
                "Biaya Listrik & Air BKBG": [null,null,null,null,null,null],
                "Biaya Internet & Telepon BKBG": [null,null,null,null,null,null],
                "Biaya Alat Tulis Kantor BKBG": [null,null,null,null,null,null],
                "Beban Tunjangan Hari Raya BKBG": [null,null,null,null,null,null],
                "Biaya System POS BKBG": [null,null,null,null,null,null],
                "Biaya Transport BKBG": [null,null,null,null,null,null],
                "Biaya Kebersihan & Pengharum BKBG": [null,null,null,null,null,null],
                "Biaya Operasionalonal Lainnya BKBG": [null,null,null,null,null,null],
                "Biaya Sewa Bangunan BKBG": [null,null,null,null,null,null],
                "Biaya Maintenance Aset BKBG": [null,null,null,null,null,null],
                "Biaya Insentif, Bonus, Fee BKBG": [null,null,null,null,null,null],
                "Biaya Gaji Karyawan BKBG": [null,null,null,null,null,null],
                "Biaya Perjalanan Dinas BKBG": [null,null,null,null,null,null],
                "Beban Freelance BKBG": [null,null,null,null,null,null]
              } },
              "Biaya Operasional Bakery Dramaga": { vals:[null,null,null,null,null,null], children: {
                "Biaya Listrik & Air BKDR": [null,null,null,null,null,null],
                "Biaya Internet & Telepon BKDR": [null,null,null,null,null,null],
                "Biaya Alat Tulis Kantor BKDR": [null,null,null,null,null,null],
                "Beban Tunjangan Hari Raya BKDR": [null,null,null,null,null,null],
                "Biaya System POS BKDR": [null,null,null,null,null,null],
                "Biaya Transport BKDR": [null,null,null,null,null,null],
                "Biaya Kebersihan & Pengharum BKDR": [null,null,null,null,null,null],
                "Biaya Operasionalonal Lainnya BKDR": [null,null,null,null,null,null],
                "Biaya Sewa Bangunan BKDR": [null,null,null,null,null,null],
                "Biaya Maintenance Aset BKDR": [null,null,null,null,null,null],
                "Biaya Gaji Karyawan BKDR": [null,null,null,null,null,null],
                "Biaya Insentif. Bonus. Fee BKDR": [null,null,null,null,null,null],
                "Beban Freelance BKDR": [null,null,null,null,null,null],
                "Biaya Perjalanan Dinas BKDR": [null,null,null,null,null,null]
              } },
              "Biaya Operasional Bakery Cibubur": { vals:[null,null,null,null,null,null], children: {
                "Biaya Listrik & Air BKCB": [null,null,null,null,null,null],
                "Biaya Internet & Telepon BKCB": [null,null,null,null,null,null],
                "Biaya Alat Tulis Kantor BKCB": [null,null,null,null,null,null],
                "Beban Tunjangan Hari Raya BKCB": [null,null,null,null,null,null],
                "Biaya System POS BKCB": [null,null,null,null,null,null],
                "Beban Transport BKCB": [null,null,null,null,null,null],
                "Biaya Kebersihan & Pengharum BKCB": [null,null,null,null,null,null],
                "Biaya Operasionalonal Lainnya BKCB": [null,null,null,null,null,null],
                "Biaya Sewa Bangunan BKCB": [null,null,null,null,null,null],
                "Biaya Maintenance Aset BKCB": [null,null,null,null,null,null],
                "Biaya Gaji Karyawan BKCB": [null,null,null,null,null,null],
                "Biaya Insentif. Bonus. Fee BKCB": [null,null,null,null,null,null],
                "Biaya Perjalanan Dinas BKCB": [null,null,null,null,null,null]
              } },
              "Biaya Operasional Bakery Pekapuran": { vals:[null,null,null,null,null,null], children: {
                "Biaya Listrik & Air BKPK": [null,null,null,null,null,null],
                "Biaya Internet & Telepon BKPK": [null,null,null,null,null,null],
                "Biaya Alat Tulis Kantor BKPK": [null,null,null,null,null,null],
                "Beban Tunjangan Hari Raya BKPK": [null,null,null,null,null,null],
                "Biaya System POS BKPK": [null,null,null,null,null,null],
                "Biaya Transport BKPK": [null,null,null,null,null,null],
                "Biaya Kebersihan & Pengharum BKPK": [null,null,null,null,null,null],
                "Biaya Operasionalonal Lainnya BKPK": [null,null,null,null,null,null],
                "Biaya Sewa Bangunan BKPK": [null,null,null,null,null,null],
                "Beban Maintenance Aset Pekapuran": [null,null,null,null,null,null],
                "Biaya Gaji Karyawan BKPK": [null,null,null,null,null,null],
                "Biaya Insentif. Bonus. Fee BKPK": [null,null,null,null,null,null],
                "Biaya Perjalanan Dinas BKPK": [null,null,null,null,null,null]
              } },
              "Biaya Operasional Bakery Mekarsari": { vals:[null,null,null,null,null,null], children: {
                "Biaya Listrik & Air BKMK": [null,null,null,null,null,null],
                "Biaya Internet & Telepon BKMK": [null,null,null,null,null,null],
                "Biaya Alat Tulis Kantor BKMK": [null,null,null,null,null,null],
                "Beban Tunjangan Hari Raya BKMK": [null,null,null,null,null,null],
                "Biaya System POS BKMK": [null,null,null,null,null,null],
                "Biaya Transport BKMK": [null,null,null,null,null,null],
                "Biaya Kebersihan & Pengharum BKMK": [null,null,null,null,null,null],
                "Biaya Operasionalonal Lainnya BKMK": [null,null,null,null,null,null],
                "Biaya Sewa Bangunan BKMK": [null,null,null,null,null,null],
                "Biaya Maintenance Aset BKMK": [null,null,null,null,null,null],
                "Biaya Gaji Karyawan BKMK": [null,null,null,null,null,null],
                "Biaya Insentif, Bonus, Fee BKMK": [null,null,null,null,null,null],
                "Biaya Perjalanan Dinas BKMK": [null,null,null,null,null,null]
              } },
              "Beban Freelance BKMK": [null,null,null,null,null,null],
              "Biaya Operasional Outlet Sukabumi": { vals:[null,null,null,null,null,null], children: {
                "Biaya Listrik & Air OPSM": [null,null,null,null,null,null],
                "Biaya Internet & Telepon OPSM": [null,null,null,null,null,null],
                "Biaya Alat Tulis Kantor OPSM": [null,null,null,null,null,null],
                "Beban Tunjangan Hari Raya OPSM": [null,null,null,null,null,null],
                "Biaya System POS OPSM": [null,null,null,null,null,null],
                "Biaya Transport OPSM": [null,null,null,null,null,null],
                "Beban Kebersihan & Pengharum OPSM": [null,null,null,null,null,null],
                "Beban Operasional Lainnya OPSM": [null,null,null,null,null,null],
                "Biaya Sewa Bangunan OPSM": [null,null,null,null,null,null],
                "Beban Maintenance Aset OPSM": [null,null,null,null,null,null],
                "Biaya Gaji Karyawan OPSM": [null,null,null,null,null,null],
                "Biaya Insentif, Bonus, Fee OPSM": [null,null,null,null,null,null],
                "Beban Freelance OPSM": [null,null,null,null,null,null],
                "Biaya Perjalanan Dinas OPSM": [null,null,null,null,null,null]
              } },
              "Biaya Operasional Bakery Jati Melati": { vals:[null,null,null,null,null,null], children: {
                "Biaya Listrik & Air BKJM": [null,null,null,null,null,null],
                "Biaya Internet & Telepon BKJM": [null,null,null,null,null,null],
                "Biaya Alat Tulis Kantor BKJM": [null,null,null,null,null,null],
                "Beban Tunjangan Hari Raya BKJM": [null,null,null,null,null,null],
                "Biaya System POS BKJM": [null,null,null,null,null,null],
                "Biaya Transport BKJM": [null,null,null,null,null,null],
                "Biaya Kebersihan & Pengharum BKJM": [null,null,null,null,null,null],
                "Biaya Operasionalonal Lainnya BKJM": [null,null,null,null,null,null],
                "Biaya Sewa Bangunan BKJM": [null,null,null,null,null,null],
                "Beban Maintenance Aset BKJM": [null,null,null,null,null,null],
                "Biaya Gaji Karyawan BKJM": [null,null,null,null,null,null],
                "Biaya Insentif, Bonus, Fee BKJM": [null,null,null,null,null,null],
                "Beban Freelance BKJM": [null,null,null,null,null,null],
                "Biaya Perjalanan Dinas BKJM": [null,null,null,null,null,null]
              } },
              "Biaya Operasional Bakery Nusa Indah": { vals:[null,null,null,null,null,null], children: {
                "Biaya Listrik & Air BKNI": [null,null,null,null,null,null],
                "Biaya Internet & Telepon BKNI": [null,null,null,null,null,null],
                "Biaya Alat Tulis Kantor BKNI": [null,null,null,null,null,null],
                "Beban Tunjangan Hari Raya BKNI": [null,null,null,null,null,null],
                "Biaya System POS BKNI": [null,null,null,null,null,null],
                "Biaya Transport BKNI": [null,null,null,null,null,null],
                "Biaya Kebersihan & Pengharum BKNI": [null,null,null,null,null,null],
                "Biaya Operasionalonal Lainnya BKNI": [null,null,null,null,null,null],
                "Biaya Sewa Bangunan BKNI": [null,null,null,null,null,null],
                "Biaya Maintenance Aset BKNI": [null,null,null,null,null,null],
                "Biaya Gaji Karyawan BKNI": [null,null,null,null,null,null],
                "Biaya Insentif, Bonus, Fee BKNI": [null,null,null,null,null,null],
                "Beban Freelance BKNI": [null,null,null,null,null,null],
                "Biaya Perjalanan Dinas BKNI": [null,null,null,null,null,null]
              } },
              "Biaya Operasional Mini Bakery Cibinong": { vals:[null,null,null,null,null,null], children: {
                "Biaya Listrik & Air BKCN": [null,null,null,null,null,null],
                "Biaya Internet & Telepon BKCN": [null,null,null,null,null,null],
                "Biaya Alat Tulis Kantor BKCN": [null,null,null,null,null,null],
                "Beban Tunjangan Hari Raya BKCN": [null,null,null,null,null,null],
                "Biaya System POS BKCN": [null,null,null,null,null,null],
                "Beban Transport BKCN": [null,null,null,null,null,null],
                "Biaya Kebersihan & Pengharum BKCN": [null,null,null,null,null,null],
                "Biaya Operasionalonal Lainnya BKCN": [null,null,null,null,null,null],
                "Biaya Sewa Bangunan BKCN": [null,null,null,null,null,null],
                "Beban Maintenance Aset BKCN": [null,null,null,null,null,null],
                "Biaya Gaji Karyawan BKCN": [null,null,null,null,null,null],
                "Biaya Insentif, Bonus, Fee BKCN": [null,null,null,null,null,null],
                "Beban Freelance BKCN": [null,null,null,null,null,null],
                "Biaya Perjalanan Dinas BKCN": [null,null,null,null,null,null]
              } },
              "Biaya Operasional Bakery Bantar Gebang": { vals:[null,null,null,null,null,null], children: {
                "Biaya Listrik & Air BKBN": [null,null,null,null,null,null],
                "Biaya Internet & Telepon BKBN": [null,null,null,null,null,null],
                "Biaya Alat Tulis Kantor BKBN": [null,null,null,null,null,null],
                "Beban Tunjangan Hari Raya BKBN": [null,null,null,null,null,null],
                "Biaya System POS BKBN": [null,null,null,null,null,null],
                "Beban Transport BKBN": [null,null,null,null,null,null],
                "Biaya Kebersihan & Pengharum BKBN": [null,null,null,null,null,null],
                "Biaya Operasionalonal Lainnya BKBN": [null,null,null,null,null,null],
                "Biaya Sewa Bangunan BKBN": [null,null,null,null,null,null],
                "Biaya Maintenance Aset BKBN": [null,null,null,null,null,null],
                "Biaya Gaji Karyawan BKBN": [null,null,null,null,null,null],
                "Biaya Insentif, Bonus, Fee BKBN": [null,null,null,null,null,null],
                "Beban Freelance BKBN": [null,null,null,null,null,null],
                "Biaya Perjalanan Dinas BKBN": [null,null,null,null,null,null]
              } },
              "Biaya Operasional Bakery Abdul Gani": { vals:[null,null,null,null,null,null], children: {
                "Biaya Listrik & Air BKAG": [null,null,null,null,null,null],
                "Biaya Internet & Telepon BKAG": [null,null,null,null,null,null],
                "Biaya Alat Tulis Kantor BKAG": [null,null,null,null,null,null],
                "Beban Tunjangan Hari Raya BKAG": [null,null,null,null,null,null],
                "Biaya System POS BKAG": [null,null,null,null,null,null],
                "Biaya Transport BKAG": [null,null,null,null,null,null],
                "Biaya Kebersihan & Pengharum BKAG": [null,null,null,null,null,null],
                "Biaya Operasionalonal Lainnya BKAG": [null,null,null,null,null,null],
                "Biaya Sewa Bangunan BKAG": [null,null,null,null,null,null],
                "Biaya Maintenance Aset BKAG": [null,null,null,null,null,null],
                "Biaya Gaji Karyawan BKAG": [null,null,null,null,null,null],
                "Biaya Insentif, Bonus, Fee BKAG": [null,null,null,null,null,null],
                "Beban Freelance BKAG": [null,null,null,null,null,null],
                "Biaya Perjalanan Dinas BKAG": [null,null,null,null,null,null]
              } },
              "Biaya Operasional Bakery Pangleseran": { vals:[null,null,null,null,null,null], children: {
                "Biaya Alat Tulis Kantor BKPS": [null,null,null,null,null,null],
                "Biaya Transport BKPS": [null,null,null,null,null,null],
                "Biaya Kebersihan & Pengharum BKPS": [null,null,null,null,null,null],
                "Biaya Operasionalonal Lainnya BKPS": [null,null,null,null,null,null],
                "Biaya Listrik & Air BKPS": [null,null,null,null,null,null],
                "Biaya Internet & Telepon BKPS": [null,null,null,null,null,null],
                "Beban Tunjangan Hari Raya BKPS": [null,null,null,null,null,null],
                "Biaya System POS BKPS": [null,null,null,null,null,null],
                "Biaya Sewa Bangunan BKPS": [null,null,null,null,null,null],
                "Biaya Maintenance Aset BKPS": [null,null,null,null,null,null],
                "Biaya Gaji Karyawan BKPS": [null,null,null,null,null,null],
                "Biaya Insentif, Bonus, Fee BKPS": [null,null,null,null,null,null],
                "Beban Freelance BKPS": [null,null,null,null,null,null],
                "Biaya Perjalanan Dinas BKPS": [null,null,null,null,null,null]
              } }
            } }
        } },
      { name:'Laba Operasional', highlight:true, accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Laba Kotor - Biaya Operasional.' },
      { name:'Biaya Penyusutan', accountLabel:'Biaya Penyusutan', computed:true, forceFormula:true, values:[null,null,null,null,null,null],
        children: {
          "Biaya Penyusutan Ownership": [null,null,null,null,null,null]
        } },
      { name:'Biaya Pajak', accountLabel:'Biaya Pajak', computed:true, forceFormula:true, signRule:'absSubtractExceptPendapatan', treatMissingAsZero:true, values:[null,null,null,null,null,null],
        note:'Aturan tanda: semua item DIKURANGKAN (nilai absolut) krn source tidak konsisten tanda utk pajak (temuan sebelumnya di entity lain) -- BELUM terverifikasi numerik utk entity ini krn file blank total.',
        children: {
          "Pajak Daerah & Reklame": [null,null,null,null,null,null],
          "PPh Sewa": [null,null,null,null,null,null],
          "Pajak PPh Badan": [null,null,null,null,null,null],
          "PPh Final": [null,null,null,null,null,null],
          "PPh 21 Karyawan": [null,null,null,null,null,null],
          "PPN": [null,null,null,null,null,null]
        } },
      { name:'Bunga', accountLabel:'Bunga', computed:true, forceFormula:true, signRule:'absSubtractExceptPendapatan', values:[null,null,null,null,null,null],
        note:'Biaya Bunga Bank dikurangkan, Pendapatan Bunga Bank ditambahkan (net).',
        children: {
          "Biaya Bunga Bank": [null,null,null,null,null,null],
          "Pendapatan Bunga Bank": [null,null,null,null,null,null]
        } },
      { name:'Laba Bersih', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Laba Operasional - Biaya Penyusutan - Biaya Pajak - Bunga.' },
    ]
  },
  konsolidasi: {
    label: 'Konsolidasi', color: '#B4E61D', grounded: 'real',
    waterfall: [
      { name:'Pendapatan', highlight:true, accountLabel:'Pendapatan', computed:true, forceFormula:true, values:[null,null,null,null,null,null],
        children: {
            "Pendapatan Ownership": { vals:[null,null,null,null,null,null], children: {
              "Pendapatan Offline": { vals:[null,null,null,null,null,null], pathHint: "Pendapatan Ownership" },
              "Pendapatan Online": { vals:[null,null,null,null,null,null], pathHint: "Pendapatan Ownership" },
              "Pendapatan Konsinyasi": { vals:[null,null,null,null,null,null], pathHint: "Pendapatan Ownership" },
              "Pendapatan Sales Eksekutif": [null,null,null,null,null,null],
              "MBG Manufactur": [null,null,null,null,null,null],
              "Pendapatan Live Tiktok": [null,null,null,null,null,null],
              "Pendapatan Kangridwan": [null,null,null,null,null,null]
            } },
            "Pendapatan Franchise": { vals:[null,null,null,null,null,null], children: {
              "Pendapatan Offline": { vals:[null,null,null,null,null,null], pathHint: "Pendapatan Franchise" },
              "Pendapatan Online": { vals:[null,null,null,null,null,null], pathHint: "Pendapatan Franchise" },
              "Pendapatan Konsinyasi": { vals:[null,null,null,null,null,null], pathHint: "Pendapatan Franchise" }
            } },
            "Pendapatan Camelia": [null,null,null,null,null,null],
            "Pendapatan Lain-Lain": [null,null,null,null,null,null]
        } },
      { name:'Diskon', accountLabel:'Diskon', computed:true, forceFormula:true, noNegColor:true, values:[null,null,null,null,null,null],
        children: {
            "Diskon Offline": { vals:[null,null,null,null,null,null], signRule:'absSubtractExceptPendapatan', children: {
              "Ownership": { vals:[null,null,null,null,null,null], pathHint: "Diskon Offline" },
              "Franchise": { vals:[null,null,null,null,null,null], pathHint: "Diskon Offline" }
            } },
            "Diskon Online": { vals:[null,null,null,null,null,null], signRule:'absSubtractExceptPendapatan', children: {
              "Ownership": { vals:[null,null,null,null,null,null], pathHint: "Diskon Online" },
              "Franchise": { vals:[null,null,null,null,null,null], pathHint: "Diskon Online" }
            } },
            "Diskon MBG": [null,null,null,null,null,null],
            "Cashback MBG": [null,null,null,null,null,null],
            "Diskon Live Tiktok": [null,null,null,null,null,null],
            "Potongan/Komisi Aplikasi/Merchant Live Tiktok": [null,null,null,null,null,null],
            "Potongan/Komisi Aplikasi/Merchant": { vals:[null,null,null,null,null,null], signRule:'absSubtractExceptPendapatan', children: {
              "Ownership": { vals:[null,null,null,null,null,null], pathHint: "Potongan/Komisi Aplikasi/Merchant" },
              "Franchise": { vals:[null,null,null,null,null,null], pathHint: "Potongan/Komisi Aplikasi/Merchant" }
            } },
            "Diskon Penjualan Sales": [null,null,null,null,null,null]
        } },
      { name:'Pendapatan Bersih', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Pendapatan + Diskon (diskon sudah negatif di source).' },
      { name:'Harga Pokok Penjualan', accountLabel:'Harga Pokok Penjualan', computed:true, forceFormula:true, values:[null,null,null,null,null,null],
        note:'Berisi HPP Bahan Baku Utama + HPP Konsinyasi (bersama = "HPP Material" scr konsep) + Biaya Overhead Pabrik -- "HPP Material" bukan grup Path yg nyata di source, cuma penjumlahan 2 item pertama scr konsep, hasil akhir sama.',
        children: {
            "HPP Bahan Baku Utama": [null,null,null,null,null,null],
            "HPP Konsinyasi Ownership": [null,null,null,null,null,null],
            "HPP Konsinyasi Franchise": [null,null,null,null,null,null],
            "Biaya Overhead Pabrik": { vals:[null,null,null,null,null,null], children: {
              "Biaya Listrik & Air": [null,null,null,null,null,null],
              "Biaya Gaji Produksi": [null,null,null,null,null,null],
              "Biaya Freelance & Lembur Factory": [null,null,null,null,null,null],
              "Biaya Tunjangan Hari Raya": [null,null,null,null,null,null],
              "Biaya Distribusi": [null,null,null,null,null,null]
            } }
        } },
      { name:'Laba Kotor', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Pendapatan Bersih - Harga Pokok Penjualan.' },
      { name:'Biaya Operasional', accountLabel:'Biaya Operasional', computed:true, forceFormula:true, values:[null,null,null,null,null,null],
        children: {
            "Biaya Gaji Administrasi & Managerial": { vals:[null,null,null,null,null,null], children: {
              "Biaya Gaji Administrasi & Managerial": [null,null,null,null,null,null],
              "Biaya Freelance Delivery": [null,null,null,null,null,null]
            } },
            "Biaya Sewa Factory": { vals:[null,null,null,null,null,null], children: {
              "Biaya Sewa": [null,null,null,null,null,null]
            } },
            "Biaya Internet & Telepon Factory": { vals:[null,null,null,null,null,null], children: {
              "Biaya Internet & Telepon": [null,null,null,null,null,null]
            } },
            "Biaya ATK Factory": { vals:[null,null,null,null,null,null], children: {
              "Biaya ATK": [null,null,null,null,null,null]
            } },
            "Biaya Maintetance Aset": { vals:[null,null,null,null,null,null], children: {
              "Biaya Maintetance Aset": [null,null,null,null,null,null]
            } },
            "Biaya Maintenance Kendaraan": { vals:[null,null,null,null,null,null], children: {
              "Biaya Maintenance Kendaraan": [null,null,null,null,null,null],
              "Biaya Tunjangan Bpjs Tk & Ketenagakerjaan": [null,null,null,null,null,null]
            } },
            "Biaya Perjalanan Dinas": { vals:[null,null,null,null,null,null], children: {
              "Biaya Perjalanan Dinas": [null,null,null,null,null,null]
            } },
            "Biaya Kebersihan": { vals:[null,null,null,null,null,null], children: {
              "Biaya Kebersihan": [null,null,null,null,null,null]
            } },
            "Biaya Entertain & Jamuan": { vals:[null,null,null,null,null,null], children: {
              "Biaya Entertain & Jamuan": { vals:[null,null,null,null,null,null], pathHint: "Biaya Entertain & Jamuan" }
            } },
            "Biaya Kompensasi Karyawan": [null,null,null,null,null,null],
            "Biaya Operasional Lain-Lain": { vals:[null,null,null,null,null,null], children: {
              "Operasional Lain-Lain": [null,null,null,null,null,null]
            } },
            "Biaya Administrasi Bank": { vals:[null,null,null,null,null,null], pathHint: "Biaya Operasional" },
            "Biaya Operasional Head Office": { vals:[null,null,null,null,null,null], children: {
              "Gaji Staff & Management Office": [null,null,null,null,null,null],
              "Tunjangan & Insentif Office": [null,null,null,null,null,null],
              "Biaya BPJS Kesehatan & TK": [null,null,null,null,null,null],
              "Biaya Perjalan Dinas FA": [null,null,null,null,null,null],
              "Biaya System Keuangan": [null,null,null,null,null,null],
              "Biaya Konsultan Pajak": [null,null,null,null,null,null],
              "Biaya Transport Lokal FA": [null,null,null,null,null,null],
              "Biaya Perjalanan Dinas HR": [null,null,null,null,null,null],
              "Biaya IT System HR": [null,null,null,null,null,null],
              "Biaya Transport Lokal HR": [null,null,null,null,null,null],
              "Biaya Training Khusus HR": [null,null,null,null,null,null],
              "Biaya Perjalanan Dinas Sales": [null,null,null,null,null,null],
              "Biaya Sales Tools": [null,null,null,null,null,null],
              "Biaya Transport Lokal Sales": [null,null,null,null,null,null],
              "Biaya Akomodasi Sales": [null,null,null,null,null,null],
              "Biaya Entertain & Jamuan Sales": [null,null,null,null,null,null],
              "Biaya Sales Event": [null,null,null,null,null,null],
              "Biaya Insentif Sales": [null,null,null,null,null,null],
              "Biaya Perjalanan Dinas Corsec": [null,null,null,null,null,null],
              "Biaya Entertain & Jamuan Corsec": [null,null,null,null,null,null],
              "Biaya Activity Marketing": [null,null,null,null,null,null],
              "Biaya Kang Ridwan Official": [null,null,null,null,null,null],
              "Biaya Perjalanan Dinas Dirut": [null,null,null,null,null,null],
              "Biaya Akomodasi Dirut": [null,null,null,null,null,null],
              "Biaya Perjalanan Dinas GM": [null,null,null,null,null,null],
              "Biaya Entertain & Jamuan": { vals:[null,null,null,null,null,null], pathHint: "Biaya Operasional Head Office" },
              "Biaya Lain-lain Dirut": [null,null,null,null,null,null],
              "Biaya Perjalanan Dinas Team Marketing": [null,null,null,null,null,null],
              "Biaya Internal Training": [null,null,null,null,null,null],
              "Biaya General": { vals:[null,null,null,null,null,null], children: {
                "Biaya External Training": [null,null,null,null,null,null],
                "Biaya Coaching & Pendampingan External": [null,null,null,null,null,null],
                "Biaya Konsumsi & Meeting Office": [null,null,null,null,null,null],
                "Biaya Sharing Season & Seminar Internal": [null,null,null,null,null,null],
                "Biaya Listrik & Air Office": [null,null,null,null,null,null],
                "Biaya Telp & Internet Office": [null,null,null,null,null,null],
                "Biaya Sewa Bangunan Office": [null,null,null,null,null,null],
                "Biaya Maintenance Aset Office": [null,null,null,null,null,null],
                "Biaya Sumbangan & Hadiah": [null,null,null,null,null,null],
                "Biaya ATK, Fotocopy, Materai": [null,null,null,null,null,null],
                "Biaya Maintenance Kendaraan Office": [null,null,null,null,null,null],
                "Biaya Operasional Head Office": [null,null,null,null,null,null],
                "Biaya Administrasi Bank": { vals:[null,null,null,null,null,null], pathHint: "Biaya General" },
                "Kompensasi, Benefit, Bonus Karyawan": [null,null,null,null,null,null],
                "Biaya Akad Bank": [null,null,null,null,null,null]
              } },
              "Camelia Kost": { vals:[null,null,null,null,null,null], children: {
                "Biaya Listrik Camelia": [null,null,null,null,null,null],
                "Biaya Internet & Telp Camelia": [null,null,null,null,null,null],
                "Biaya Gaji Camelia": [null,null,null,null,null,null],
                "Biaya Operasional Lainnya Camelia": [null,null,null,null,null,null]
              } }
            } },
            "Biaya Operasional Store Brand": { vals:[null,null,null,null,null,null], children: {
              "Biaya Operasional Bakery Sudirman": { vals:[null,null,null,null,null,null], children: {
                "Biaya Listrik & Air BKSM": [null,null,null,null,null,null],
                "Biaya Internet & Telepon BKSM": [null,null,null,null,null,null],
                "Biaya Alat Tulis Kantor BKSM": [null,null,null,null,null,null],
                "Beban Tunjangan Hari Raya BKSM": [null,null,null,null,null,null],
                "Biaya System POS BKSM": [null,null,null,null,null,null],
                "Biaya Transport BKSM": [null,null,null,null,null,null],
                "Biaya Kebersihan & Pengharum BKSM": [null,null,null,null,null,null],
                "Biaya Operasionalonal Lainnya BKSM": [null,null,null,null,null,null],
                "Biaya Sewa Bangunan BKSM": [null,null,null,null,null,null],
                "Biaya Maintenance Aset BKSM": [null,null,null,null,null,null],
                "Biaya Gaji Karyawan BKSM": [null,null,null,null,null,null],
                "Biaya Insentif, Bonus, Fee BKSM": [null,null,null,null,null,null],
                "Biaya Freelance BKSM": [null,null,null,null,null,null],
                "Beban Perjalanan Dinas BKSM": [null,null,null,null,null,null]
              } },
              "Biaya Operasional Bakery Cikole": { vals:[null,null,null,null,null,null], children: {
                "Biaya Listrik & Air BKGD": [null,null,null,null,null,null],
                "Biaya Internet & Telepon BKGD": [null,null,null,null,null,null],
                "Biaya Alat Tulis Kantor BKGD": [null,null,null,null,null,null],
                "Beban Tunjangan Hari Raya BKGD": [null,null,null,null,null,null],
                "Biaya System POS BKGD": [null,null,null,null,null,null],
                "Biaya Transport BKGD": [null,null,null,null,null,null],
                "Biaya Kebersihan & Pengharum BKGD": [null,null,null,null,null,null],
                "Biaya Operasionalonal Lainnya BKGD": [null,null,null,null,null,null],
                "Biaya Sewa Bangunan BKGD": [null,null,null,null,null,null],
                "Biaya Maintenance Aset BKGD": [null,null,null,null,null,null],
                "Biaya Gaji Karyawan BKGD": [null,null,null,null,null,null],
                "Biaya Insentif, Bonus, Fee BKGD": [null,null,null,null,null,null],
                "Biaya Freelance BKGD": [null,null,null,null,null,null],
                "Beban Perjalanan Dinas BKGD": [null,null,null,null,null,null]
              } },
              "Biaya Operasional Bakery Bangbarung": { vals:[null,null,null,null,null,null], children: {
                "Biaya Listrik & Air BKBG": [null,null,null,null,null,null],
                "Biaya Internet & Telepon BKBG": [null,null,null,null,null,null],
                "Biaya Alat Tulis Kantor BKBG": [null,null,null,null,null,null],
                "Beban Tunjangan Hari Raya BKBG": [null,null,null,null,null,null],
                "Biaya System POS BKBG": [null,null,null,null,null,null],
                "Biaya Transport BKBG": [null,null,null,null,null,null],
                "Biaya Kebersihan & Pengharum BKBG": [null,null,null,null,null,null],
                "Biaya Operasional Lainnya BKBG": [null,null,null,null,null,null],
                "Biaya Sewa Bangunan BKBG": [null,null,null,null,null,null],
                "Biaya Maintenance Aset BKBG": [null,null,null,null,null,null],
                "Biaya Insentif, Bonus, Fee BKBG": [null,null,null,null,null,null],
                "Biaya Gaji Karyawan BKBG": [null,null,null,null,null,null],
                "Biaya Perjalanan Dinas BKBG": [null,null,null,null,null,null],
                "Beban Freelance BKBG": [null,null,null,null,null,null]
              } },
              "Biaya Operasional Bakery Dramaga": { vals:[null,null,null,null,null,null], children: {
                "Biaya Listrik & Air BKDR": [null,null,null,null,null,null],
                "Biaya Internet & Telepon BKDR": [null,null,null,null,null,null],
                "Biaya Alat Tulis Kantor BKDR": [null,null,null,null,null,null],
                "Beban Tunjangan Hari Raya BKDR": [null,null,null,null,null,null],
                "Biaya System POS BKDR": [null,null,null,null,null,null],
                "Biaya Transport BKDR": [null,null,null,null,null,null],
                "Biaya Kebersihan & Pengharum BKDR": [null,null,null,null,null,null],
                "Biaya Operasionalonal Lainnya BKDR": [null,null,null,null,null,null],
                "Biaya Sewa Bangunan BKDR": [null,null,null,null,null,null],
                "Biaya Maintenance Aset BKDR": [null,null,null,null,null,null],
                "Biaya Gaji Karyawan BKDR": [null,null,null,null,null,null],
                "Biaya Insentif. Bonus. Fee BKDR": [null,null,null,null,null,null],
                "Beban Freelance BKDR": [null,null,null,null,null,null],
                "Biaya Perjalanan Dinas BKDR": [null,null,null,null,null,null]
              } },
              "Biaya Operasional Bakery Cibubur": { vals:[null,null,null,null,null,null], children: {
                "Biaya Listrik & Air BKCB": [null,null,null,null,null,null],
                "Biaya Internet & Telepon BKCB": [null,null,null,null,null,null],
                "Biaya Alat Tulis Kantor BKCB": [null,null,null,null,null,null],
                "Beban Tunjangan Hari Raya BKCB": [null,null,null,null,null,null],
                "Biaya System POS BKCB": [null,null,null,null,null,null],
                "Beban Transport BKCB": [null,null,null,null,null,null],
                "Biaya Kebersihan & Pengharum BKCB": [null,null,null,null,null,null],
                "Biaya Operasionalonal Lainnya BKCB": [null,null,null,null,null,null],
                "Biaya Sewa Bangunan BKCB": [null,null,null,null,null,null],
                "Biaya Maintenance Aset BKCB": [null,null,null,null,null,null],
                "Biaya Gaji Karyawan BKCB": [null,null,null,null,null,null],
                "Biaya Insentif. Bonus. Fee BKCB": [null,null,null,null,null,null],
                "Biaya Perjalanan Dinas BKCB": [null,null,null,null,null,null]
              } },
              "Biaya Operasional Bakery Pekapuran": { vals:[null,null,null,null,null,null], children: {
                "Biaya Listrik & Air BKPK": [null,null,null,null,null,null],
                "Biaya Internet & Telepon BKPK": [null,null,null,null,null,null],
                "Biaya Alat Tulis Kantor BKPK": [null,null,null,null,null,null],
                "Beban Tunjangan Hari Raya BKPK": [null,null,null,null,null,null],
                "Biaya System POS BKPK": [null,null,null,null,null,null],
                "Biaya Transport BKPK": [null,null,null,null,null,null],
                "Biaya Kebersihan & Pengharum BKPK": [null,null,null,null,null,null],
                "Biaya Operasionalonal Lainnya BKPK": [null,null,null,null,null,null],
                "Biaya Sewa Bangunan BKPK": [null,null,null,null,null,null],
                "Beban Maintenance Aset Pekapuran": [null,null,null,null,null,null],
                "Biaya Gaji Karyawan BKPK": [null,null,null,null,null,null],
                "Biaya Insentif. Bonus. Fee BKPK": [null,null,null,null,null,null],
                "Biaya Perjalanan Dinas BKPK": [null,null,null,null,null,null]
              } },
              "Biaya Operasional Bakery Mekarsari": { vals:[null,null,null,null,null,null], children: {
                "Biaya Listrik & Air BKMK": [null,null,null,null,null,null],
                "Biaya Internet & Telepon BKMK": [null,null,null,null,null,null],
                "Biaya Alat Tulis Kantor BKMK": [null,null,null,null,null,null],
                "Beban Tunjangan Hari Raya BKMK": [null,null,null,null,null,null],
                "Biaya System POS BKMK": [null,null,null,null,null,null],
                "Biaya Transport BKMK": [null,null,null,null,null,null],
                "Biaya Kebersihan & Pengharum BKMK": [null,null,null,null,null,null],
                "Biaya Operasionalonal Lainnya BKMK": [null,null,null,null,null,null],
                "Biaya Sewa Bangunan BKMK": [null,null,null,null,null,null],
                "Biaya Maintenance Aset BKMK": [null,null,null,null,null,null],
                "Biaya Gaji Karyawan BKMK": [null,null,null,null,null,null],
                "Biaya Insentif, Bonus, Fee BKMK": [null,null,null,null,null,null],
                "Biaya Perjalanan Dinas BKMK": [null,null,null,null,null,null]
              } },
              "Beban Freelance BKMK": [null,null,null,null,null,null],
              "Biaya Operasional Outlet Sukabumi": { vals:[null,null,null,null,null,null], children: {
                "Biaya Listrik & Air OPSM": [null,null,null,null,null,null],
                "Biaya Internet & Telepon OPSM": [null,null,null,null,null,null],
                "Biaya Alat Tulis Kantor OPSM": [null,null,null,null,null,null],
                "Beban Tunjangan Hari Raya OPSM": [null,null,null,null,null,null],
                "Biaya System POS OPSM": [null,null,null,null,null,null],
                "Biaya Transport OPSM": [null,null,null,null,null,null],
                "Beban Kebersihan & Pengharum OPSM": [null,null,null,null,null,null],
                "Beban Operasional Lainnya OPSM": [null,null,null,null,null,null],
                "Biaya Sewa Bangunan OPSM": [null,null,null,null,null,null],
                "Beban Maintenance Aset OPSM": [null,null,null,null,null,null],
                "Biaya Gaji Karyawan OPSM": [null,null,null,null,null,null],
                "Biaya Insentif, Bonus, Fee OPSM": [null,null,null,null,null,null],
                "Beban Freelance OPSM": [null,null,null,null,null,null],
                "Biaya Perjalanan Dinas OPSM": [null,null,null,null,null,null]
              } },
              "Biaya Operasional Bakery Jati Melati": { vals:[null,null,null,null,null,null], children: {
                "Biaya Listrik & Air BKJM": [null,null,null,null,null,null],
                "Biaya Internet & Telepon BKJM": [null,null,null,null,null,null],
                "Biaya Alat Tulis Kantor BKJM": [null,null,null,null,null,null],
                "Beban Tunjangan Hari Raya BKJM": [null,null,null,null,null,null],
                "Biaya System POS BKJM": [null,null,null,null,null,null],
                "Biaya Transport BKJM": [null,null,null,null,null,null],
                "Biaya Kebersihan & Pengharum BKJM": [null,null,null,null,null,null],
                "Biaya Operasionalonal Lainnya BKJM": [null,null,null,null,null,null],
                "Biaya Sewa Bangunan BKJM": [null,null,null,null,null,null],
                "Beban Maintenance Aset BKJM": [null,null,null,null,null,null],
                "Biaya Gaji Karyawan BKNI": { vals:[null,null,null,null,null,null], pathHint: "Biaya Operasional Bakery Jati Melati" },
                "Biaya Insentif, Bonus, Fee BKJM": [null,null,null,null,null,null],
                "Beban Freelance BKJM": [null,null,null,null,null,null],
                "Biaya Perjalanan Dinas BKJM": [null,null,null,null,null,null]
              } },
              "Biaya Operasional Bakery Nusa Indah": { vals:[null,null,null,null,null,null], children: {
                "Biaya Listrik & Air BKNI": [null,null,null,null,null,null],
                "Biaya Internet & Telepon BKNI": [null,null,null,null,null,null],
                "Biaya Alat Tulis Kantor BKNI": [null,null,null,null,null,null],
                "Beban Tunjangan Hari Raya BKNI": [null,null,null,null,null,null],
                "Biaya System POS BKNI": [null,null,null,null,null,null],
                "Biaya Transport BKNI": [null,null,null,null,null,null],
                "Biaya Kebersihan & Pengharum BKNI": [null,null,null,null,null,null],
                "Biaya Operasionalonal Lainnya BKNI": [null,null,null,null,null,null],
                "Biaya Sewa Bangunan BKNI": [null,null,null,null,null,null],
                "Biaya Maintenance Aset BKNI": [null,null,null,null,null,null],
                "Biaya Gaji Karyawan BKNI": { vals:[null,null,null,null,null,null], pathHint: "Biaya Operasional Bakery Nusa Indah" },
                "Biaya Insentif, Bonus, Fee BKNI": [null,null,null,null,null,null],
                "Beban Freelance BKNI": [null,null,null,null,null,null],
                "Biaya Perjalanan Dinas BKNI": [null,null,null,null,null,null]
              } },
              "Biaya Operasional Mini Bakery Cibinong": { vals:[null,null,null,null,null,null], children: {
                "Biaya Listrik & Air BKCN": [null,null,null,null,null,null],
                "Biaya Internet & Telepon BKCN": [null,null,null,null,null,null],
                "Biaya Alat Tulis Kantor BKCN": [null,null,null,null,null,null],
                "Beban Tunjangan Hari Raya BKCN": [null,null,null,null,null,null],
                "Biaya System POS BKCN": [null,null,null,null,null,null],
                "Beban Transport BKCN": [null,null,null,null,null,null],
                "Biaya Kebersihan & Pengharum BKCN": [null,null,null,null,null,null],
                "Biaya Operasionalonal Lainnya BKCN": [null,null,null,null,null,null],
                "Biaya Sewa Bangunan BKCN": [null,null,null,null,null,null],
                "Beban Maintenance Aset BKCN": [null,null,null,null,null,null],
                "Biaya Gaji Karyawan BKCN": [null,null,null,null,null,null],
                "Biaya Insentif, Bonus, Fee BKCN": [null,null,null,null,null,null],
                "Beban Freelance BKCN": [null,null,null,null,null,null],
                "Biaya Perjalanan Dinas BKCN": [null,null,null,null,null,null]
              } },
              "Biaya Operasional Bakery Bantar Gebang": { vals:[null,null,null,null,null,null], children: {
                "Biaya Listrik & Air BKBN": [null,null,null,null,null,null],
                "Biaya Internet & Telepon BKBN": [null,null,null,null,null,null],
                "Biaya Alat Tulis Kantor BKBN": [null,null,null,null,null,null],
                "Beban Tunjangan Hari Raya BKBN": [null,null,null,null,null,null],
                "Biaya System POS BKBN": [null,null,null,null,null,null],
                "Beban Transport BKBN": [null,null,null,null,null,null],
                "Biaya Kebersihan & Pengharum BKBN": [null,null,null,null,null,null],
                "Biaya Operasionalonal Lainnya BKBN": [null,null,null,null,null,null],
                "Biaya Sewa Bangunan BKBN": [null,null,null,null,null,null],
                "Biaya Maintenance Aset BKBN": [null,null,null,null,null,null],
                "Biaya Gaji Karyawan BKBN": [null,null,null,null,null,null],
                "Biaya Insentif, Bonus, Fee BKBN": [null,null,null,null,null,null],
                "Beban Freelance BKBN": [null,null,null,null,null,null],
                "Biaya Perjalanan Dinas BKBN": [null,null,null,null,null,null]
              } },
              "Biaya Operasional Bakery Abdul Gani": { vals:[null,null,null,null,null,null], children: {
                "Biaya Listrik & Air BKAG": [null,null,null,null,null,null],
                "Biaya Internet & Telepon BKAG": [null,null,null,null,null,null],
                "Biaya Alat Tulis Kantor BKAG": [null,null,null,null,null,null],
                "Beban Tunjangan Hari Raya BKAG": [null,null,null,null,null,null],
                "Biaya System POS BKAG": [null,null,null,null,null,null],
                "Biaya Transport BKAG": [null,null,null,null,null,null],
                "Biaya Kebersihan & Pengharum BKAG": [null,null,null,null,null,null],
                "Biaya Operasionalonal Lainnya BKAG": [null,null,null,null,null,null],
                "Biaya Sewa Bangunan BKAG": [null,null,null,null,null,null],
                "Biaya Maintenance Aset BKAG": [null,null,null,null,null,null],
                "Biaya Gaji Karyawan BKAG": [null,null,null,null,null,null],
                "Biaya Insentif, Bonus, Fee BKAG": [null,null,null,null,null,null],
                "Beban Freelance BKAG": [null,null,null,null,null,null],
                "Biaya Perjalanan Dinas BKAG": [null,null,null,null,null,null]
              } },
              "Biaya Operasional Bakery Pangleseran": { vals:[null,null,null,null,null,null], children: {
                "Biaya Alat Tulis Kantor BKPS": [null,null,null,null,null,null],
                "Biaya Transport BKPS": [null,null,null,null,null,null],
                "Biaya Kebersihan & Pengharum BKPS": [null,null,null,null,null,null],
                "Biaya Operasionalonal Lainnya BKPS": [null,null,null,null,null,null],
                "Biaya Listrik & Air BKPS": [null,null,null,null,null,null],
                "Biaya Internet & Telepon BKPS": [null,null,null,null,null,null],
                "Beban Tunjangan Hari Raya BKPS": [null,null,null,null,null,null],
                "Biaya System POS BKPS": [null,null,null,null,null,null],
                "Biaya Sewa Bangunan BKPS": [null,null,null,null,null,null],
                "Biaya Maintenance Aset BKPS": [null,null,null,null,null,null],
                "Biaya Gaji Karyawan BKPS": [null,null,null,null,null,null],
                "Biaya Insentif, Bonus, Fee BKPS": [null,null,null,null,null,null],
                "Beban Freelance BKPS": [null,null,null,null,null,null],
                "Biaya Perjalanan Dinas BKPS": [null,null,null,null,null,null]
              } }
            } },
            "Biaya Operasional Franchise": { vals:[null,null,null,null,null,null], children: {
              "Biaya Operasional Pagelaran": [null,null,null,null,null,null],
              "Gaji Pagelaran": [null,null,null,null,null,null],
              "Biaya Operasional Cipayung": [null,null,null,null,null,null],
              "Gaji Cipayung": [null,null,null,null,null,null],
              "Biaya Operasional Harjamukti": [null,null,null,null,null,null],
              "Gaji Harjamukti": [null,null,null,null,null,null],
              "Biaya Operasional Cikaret": [null,null,null,null,null,null],
              "Gaji Cikaret": [null,null,null,null,null,null],
              "Biaya Operasional Benhil": [null,null,null,null,null,null],
              "Gaji Benhil": [null,null,null,null,null,null],
              "Biaya Operasional Perumnas 3": [null,null,null,null,null,null],
              "Gaji Perumnas 3": [null,null,null,null,null,null],
              "Biaya Operasional Rawalumbu": [null,null,null,null,null,null],
              "Gaji Rawalumbu": [null,null,null,null,null,null],
              "Biaya Operasional Kejayaan": [null,null,null,null,null,null],
              "Gaji Kejayaan": [null,null,null,null,null,null],
              "Biaya Operasional Sukahati": [null,null,null,null,null,null],
              "Gaji Sukahati": [null,null,null,null,null,null],
              "Biaya Operasional RTM Depok": [null,null,null,null,null,null],
              "Gaji RTM Depok": [null,null,null,null,null,null],
              "Biaya Operasional Vila Bogor Indah": [null,null,null,null,null,null],
              "Gaji Vila Bogor Indah": [null,null,null,null,null,null],
              "THR Franchise": [null,null,null,null,null,null]
            } }
        } },
      { name:'Laba Operasional', highlight:true, accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Laba Kotor - Biaya Operasional.' },
      { name:'Biaya Penyusutan', accountLabel:'Biaya Penyusutan', computed:true, forceFormula:true, values:[null,null,null,null,null,null],
        children: {
          "Biaya Penyusutan Konsolidasi": [null,null,null,null,null,null]
        } },
      { name:'Biaya Pajak', accountLabel:'Biaya Pajak', computed:true, forceFormula:true, signRule:'absSubtractExceptPendapatan', treatMissingAsZero:true, values:[null,null,null,null,null,null],
        note:'Aturan tanda: semua item DIKURANGKAN (nilai absolut) krn source tidak konsisten tanda utk pajak (temuan sebelumnya di entity lain) -- BELUM terverifikasi numerik utk entity ini krn file blank total.',
        children: {
          "Pajak Daerah & Reklame": [null,null,null,null,null,null],
          "PPh Sewa": [null,null,null,null,null,null],
          "Pajak PPh Badan": [null,null,null,null,null,null],
          "PPh Final": [null,null,null,null,null,null],
          "PPh 21 Karyawan": [null,null,null,null,null,null],
          "PPN": [null,null,null,null,null,null]
        } },
      { name:'Bunga', accountLabel:'Bunga', computed:true, forceFormula:true, signRule:'absSubtractExceptPendapatan', values:[null,null,null,null,null,null],
        note:'Biaya Bunga Bank dikurangkan, Pendapatan Bunga Bank ditambahkan (net).',
        children: {
          "Biaya Bunga Bank": [null,null,null,null,null,null],
          "Pendapatan Bunga Bank": [null,null,null,null,null,null]
        } },
      { name:'Laba Bersih', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Laba Operasional - Biaya Penyusutan - Biaya Pajak - Bunga.' },
    ]
  },
  sudirman: {
    label: 'Sudirman', color: '#8B8FA8', grounded: 'real',
    waterfall: [
      { name:'Pendapatan', accountLabel:'Pendapatan', computed:true, forceFormula:true, values:[null,null,null,null,null,null],
        children: {
          "Produk Utama": { vals:[953553151.0,722477290.0,1058068795.0,782262872.0,1179775020.0,1103477367.0], pathHint: "Pendapatan" },
          "Konsinyasi": { vals:[65375999.0,58046010.0,75889105.0,57454503.0,72243569.0,73263232.35], pathHint: "Pendapatan" }
        } },
      { name:'Diskon', accountLabel:'Diskon', computed:true, forceFormula:true, noNegColor:true, values:[null,null,null,null,null,null],
        children: {
          "Diskon": [-92055600.0,-52255710.0,-71818800.0,-50774348.0,-96592148.0,-86820565.0],
          "Komisi Online": [-40234154.18,-27538190.0,-24015120.0,-26532156.0,-39859335.0,-36558457.0]
        } },
      { name:'Pendapatan Bersih', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Pendapatan + Diskon (diskon sudah negatif).' },
      { name:'HPP', accountLabel:'Harga Pokok Penjualan', computed:true, forceFormula:true, values:[null,null,null,null,null,null],
        children: {
          "Produk Utama": { vals:[549705990.0,453394977.2,605215060.0,472192923.5,686783145.0,664592573.0], pathHint: "Harga Pokok Penjualan" },
          "Konsinyasi": { vals:[47253924.0,35302668.0,51950600.0,48873932.0,60351222.0,53276300.0], pathHint: "Harga Pokok Penjualan" },
          "Pembelian Langsung": [356000.0,0.0,0.0,0.0,0.0,0.0]
        } },
      { name:'Laba Kotor', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Pendapatan Bersih - HPP.' },
      { name:'Biaya Operasional', accountLabel:'Biaya Operasional', computed:true, forceFormula:true, signRule:'subtractPendapatanFromCosts', values:[null,null,null,null,null,null],
        note:'"Pendapatan Lain-lain" di dalam grup ini SENGAJA jadi PENGURANG Biaya Operasional.',
        children: {
            "Biaya Listrik & Air BKSM": [11296846.0,11042631.0,10218329.0,10789390.0,10951724.0,11513650.0],
            "Biaya Internet & Telepon BKSM": [520707.0,520707.0,520707.0,538640.0,529040.0,137663.0],
            "Biaya Alat Tulis Kantor BKSM": [816500.0,909500.0,873500.0,828500.0,1222000.0,1012500.0],
            "Biaya Tunjangan Hari Raya BKSM": [null,null,null,null,null,null],
            "Biaya System POS BKSM": [787118.0,787118.0,787118.0,697600.0,608083.0,608083.0],
            "Biaya Transport BKSM": [23000.0,114000.0,24000.0,26000.0,113000.0,22500.0],
            "Biaya Kebersihan & Pengharum BKSM": [1510410.0,2610300.0,1464400.0,1525100.0,1678500.0,1524300.0],
            "Biaya Operasionalonal Lainnya BKSM": [4862550.0,1462097.0,1670590.0,1222334.0,5421915.0,1269537.0],
            "Biaya Sewa Bangunan BKSM": [17500000.0,17500000.0,17500000.0,17500000.0,17500000.0,17500000.0],
            "Biaya Maintenance Aset BKSM": [734120.0,520000.0,2359500.0,1450000.0,600000.0,297500.0],
            "Biaya Gaji Karyawan BKSM": [29895769.0,30190769.0,30358930.0,31023769.0,29834396.0,28773931.0],
            "Biaya Insentif, Bonus, Fee BKSM": [2385794.0,2879229.0,14364465.0,1194616.0,11693281.0,9990162.0],
            "Biaya Freelance BKSM": [null,null,600000.0,null,225000.0,null],
            "Biaya Perjalanan Dinas BKSM": [93750.0,500000.0,null,25025.0,null,null],
            "Biaya Administrasi Bank": [127206.52,104430.11,91088.3,113711.97,124922.55,114522.62],
            "Pendapatan Lain-lain": [0.0,0.0,0.0,0.0,0.0,0.0]
        } },
      { name:'Laba Operasional', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Laba Kotor - Biaya Operasional.' },
      { name:'Biaya Penyusutan', accountLabel:'Biaya Penyusutan', values:[933814.1,933814.1,933814.1,933814.1,933814.1,933814.1] },
      { name:'Biaya Pajak', accountLabel:'Biaya Pajak', values:[16049.0,27285387.0,18152732.0,19277284.3,16750150.38,14122682.0] },
      { name:'Cost of Management', accountLabel:'Cost of Management', values:[50946457.5,39026165.0,56697895.0,41985868.75,62600929.45,58843629.95] },
      { name:'Laba Bersih', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Laba Operasional - Biaya Penyusutan - Biaya Pajak - Cost of Management.' },
    ]
  },
  cikole: {
    label: 'Cikole', color: '#8B8FA8', grounded: 'real',
    waterfall: [
      { name:'Pendapatan', accountLabel:'Pendapatan', computed:true, forceFormula:true, values:[null,null,null,null,null,null],
        children: {
          "Produk Utama": { vals:[327239695.0,252513496.0,378012995.0,246243280.0,288160753.0,334523380.0], pathHint: "Pendapatan" },
          "Konsinyasi": { vals:[15283785.0,12905494.0,20885495.0,11198026.0,10395137.0,11588570.0], pathHint: "Pendapatan" }
        } },
      { name:'Diskon', accountLabel:'Diskon', computed:true, forceFormula:true, noNegColor:true, values:[null,null,null,null,null,null],
        children: {
          "Diskon": [-45268720.0,-26325690.0,-41583785.0,-29115635.0,-37622074.0,-46343010.0],
          "Komisi Online": [-30922538.0,-23176221.0,-31217723.0,-33294500.0,-43716633.0,-39018189.0]
        } },
      { name:'Pendapatan Bersih', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Pendapatan + Diskon (diskon sudah negatif).' },
      { name:'HPP', accountLabel:'Harga Pokok Penjualan', computed:true, forceFormula:true, values:[null,null,null,null,null,null],
        children: {
          "Produk Utama": { vals:[236034260.0,192121645.0,267886835.0,203201156.0,194821315.0,223072825.0], pathHint: "Harga Pokok Penjualan" },
          "Konsinyasi": { vals:[13894500.0,6342400.0,13527000.0,14032500.0,7872400.0,5102700.0], pathHint: "Harga Pokok Penjualan" },
          "Pembelian Langsung": [null,null,null,null,null,null]
        } },
      { name:'Laba Kotor', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Pendapatan Bersih - HPP.' },
      { name:'Biaya Operasional', accountLabel:'Biaya Operasional', computed:true, forceFormula:true, signRule:'subtractPendapatanFromCosts', values:[null,null,null,null,null,null],
        note:'"Pendapatan Lain-lain" di dalam grup ini SENGAJA jadi PENGURANG Biaya Operasional.',
        children: {
            "Biaya Listrik & Air BKGD": [5470419.0,5331324.0,4828045.0,5410973.0,5283597.0,4472468.0],
            "Biaya Internet & Telepon BKGD": [588417.0,588417.0,588417.0,606350.0,596750.0,596863.0],
            "Biaya Alat Tulis Kantor BKGD": [520000.0,150000.0,497500.0,318000.0,323600.0,304000.0],
            "Beban Tunjangan Hari Raya BKGD": [null,null,null,null,null,null],
            "Biaya System POS BKGD": [678784.0,678784.0,678784.0,589267.0,499750.0,499750.0],
            "Biaya Transport BKGD": [29500.0,null,null,null,201500.0,null],
            "Biaya Kebersihan & Pengharum BKGD": [760500.0,760500.0,760500.0,760500.0,820238.0,760500.0],
            "Biaya Operasionalonal Lainnya BKGD": [798000.0,536034.0,1711368.0,2127695.0,748646.0,506861.0],
            "Biaya Sewa Bangunan BKGD": [11158333.0,11158333.0,11158333.0,11158333.0,11158333.0,11158333.0],
            "Biaya Maintenance Aset BKGD": [1150000.0,null,1600000.0,null,890000.0,150000.0],
            "Biaya Gaji Karyawan BKGD": [14503597.0,14685640.0,14795405.0,15404395.0,14903796.0,12788559.0],
            "Biaya Insentif, Bonus, Fee BKGD": [1113893.0,1547328.0,6547237.0,1194616.0,6220420.0,4098617.0],
            "Biaya Freelance BKGD": [null,null,450000.0,null,150000.0,null],
            "Beban Perjalanan Dinas BKGD": [93750.0,null,null,25025.0,null,null],
            "Biaya Administrasi Bank": [118372.0,91409.0,92875.0,91743.0,110122.0,142295.0],
            "Pendapatan Lain-lain": [null,null,null,null,null,null]
        } },
      { name:'Laba Operasional', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Laba Kotor - Biaya Operasional.' },
      { name:'Biaya Penyusutan', accountLabel:'Biaya Penyusutan', values:[445833.0,445833.0,445833.0,445833.0,445833.0,445833.0] },
      { name:'Biaya Pajak', accountLabel:'Biaya Pajak', values:[16049.0,9191749.0,6349169.0,7180173.0,13138944.0,3933086.0] },
      { name:'Cost of Management', accountLabel:'Cost of Management', values:[17126174.0,13270950.0,19944925.0,12872065.0,14927795.0,17312798.0] },
      { name:'Laba Bersih', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Laba Operasional - Biaya Penyusutan - Biaya Pajak - Cost of Management.' },
    ]
  },
  dramaga: {
    label: 'Dramaga', color: '#8B8FA8', grounded: 'real',
    waterfall: [
      { name:'Pendapatan', accountLabel:'Pendapatan', computed:true, forceFormula:true, values:[null,null,null,null,null,null],
        children: {
          "Produk Utama": { vals:[359790810.0,342180399.0,400035579.0,332698768.0,475212408.0,520081072.0], pathHint: "Pendapatan" },
          "Konsinyasi": { vals:[14032490.0,11935451.0,36234021.0,11053082.0,15736422.0,16600678.0], pathHint: "Pendapatan" }
        } },
      { name:'Diskon', accountLabel:'Diskon', computed:true, forceFormula:true, noNegColor:true, values:[null,null,null,null,null,null],
        children: {
          "Diskon": [-40249270.0,-29393480.0,-44132955.0,-39372039.0,-62932622.0,-64522159.0],
          "Komisi Online": [-26474044.0,-22113239.0,-20369735.0,-24654757.0,-31189221.0,-33479628.0],
          "Diskon MBG": [null,-6611500.0,-5949800.0,null,-858000.0,-801000.0]
        } },
      { name:'Pendapatan Bersih', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Pendapatan + Diskon (diskon sudah negatif).' },
      { name:'HPP', accountLabel:'Harga Pokok Penjualan', computed:true, forceFormula:true, values:[null,null,null,null,null,null],
        children: {
          "Produk Utama": { vals:[219893482.0,203861053.0,234239401.0,198713803.0,283238945.0,311293757.0], pathHint: "Harga Pokok Penjualan" },
          "Konsinyasi": { vals:[19404428.0,3328823.0,8415264.0,19188758.0,6615049.0,14732998.0], pathHint: "Harga Pokok Penjualan" },
          "Pembelian Langsung": [null,null,null,null,null,null]
        } },
      { name:'Laba Kotor', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Pendapatan Bersih - HPP.' },
      { name:'Biaya Operasional', accountLabel:'Biaya Operasional', computed:true, forceFormula:true, signRule:'subtractPendapatanFromCosts', values:[null,null,null,null,null,null],
        note:'"Pendapatan Lain-lain" di dalam grup ini SENGAJA jadi PENGURANG Biaya Operasional.',
        children: {
            "Biaya Listrik & Air BKDR": [6408800.0,6605300.0,6190300.0,6196800.0,6496800.0,7301500.0],
            "Biaya Internet & Telepon BKDR": [449667.0,449667.0,449667.0,467600.0,458000.0,458113.0],
            "Biaya Alat Tulis Kantor BKDR": [419000.0,608504.0,786000.0,560500.0,512000.0,677000.0],
            "Beban Tunjangan Hari Raya BKDR": [null,null,null,null,null,null],
            "Biaya System POS BKDR": [429034.0,429034.0,429034.0,339517.0,250000.0,250000.0],
            "Biaya Transport BKDR": [65500.0,111000.0,143500.0,472000.0,33000.0,12000.0],
            "Biaya Kebersihan & Pengharum BKDR": [1099550.0,825880.0,758200.0,849800.0,765000.0,937069.0],
            "Biaya Operasionalonal Lainnya BKDR": [1292896.0,1713645.0,1678967.0,1884472.0,4305129.0,2076691.0],
            "Biaya Sewa Bangunan BKDR": [12500000.0,12500000.0,12500000.0,12500000.0,12500000.0,12500000.0],
            "Biaya Maintenance Aset BKDR": [350000.0,453225.0,1300000.0,null,390500.0,350000.0],
            "Biaya Gaji Karyawan BKDR": [18340083.0,17731238.0,19044250.0,19046886.0,19148188.0,18435088.0],
            "Biaya Insentif. Bonus. Fee BKDR": [1197743.0,3071328.0,5130428.0,1206838.0,6365822.0,5886866.0],
            "Beban Freelance BKDR": [null,null,null,null,null,null],
            "Biaya Perjalanan Dinas BKDR": [205864.0,32700.0,204000.0,117175.0,111700.0,34200.0],
            "Biaya Administrasi Bank": [112000.0,231320.0,145236.0,133624.0,146697.0,137848.0],
            "Pendapatan Lain-lain": [null,null,null,null,null,null]
        } },
      { name:'Laba Operasional', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Laba Kotor - Biaya Operasional.' },
      { name:'Biaya Penyusutan', accountLabel:'Biaya Penyusutan', values:[3063870.0,3063870.0,3063870.0,3063870.0,3063870.0,3063870.0] },
      { name:'Biaya Pajak', accountLabel:'Biaya Pajak', values:[462298.0,341966.0,308507.0,1031406.0,301225.0,4313191.0] },
      { name:'Cost of Management', accountLabel:'Cost of Management', values:[18691165.0,17705793.0,21813480.0,17187593.0,24547442.0,26840088.0] },
      { name:'Laba Bersih', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Laba Operasional - Biaya Penyusutan - Biaya Pajak - Cost of Management.' },
    ]
  },
  bangbarung: {
    label: 'Bangbarung', color: '#8B8FA8', grounded: 'real',
    waterfall: [
      { name:'Pendapatan', accountLabel:'Pendapatan', computed:true, forceFormula:true, values:[null,null,null,null,null,null],
        children: {
          "Produk Utama": { vals:[481676252.0,378825803.0,510071388.0,415616249.0,529669822.0,597292257.0], pathHint: "Pendapatan" },
          "Konsinyasi": { vals:[14808098.0,17193997.0,15647612.0,11347401.0,15011803.0,13284993.0], pathHint: "Pendapatan" }
        } },
      { name:'Diskon', accountLabel:'Diskon', computed:true, forceFormula:true, noNegColor:true, values:[null,null,null,null,null,null],
        children: {
          "Diskon": [-39836740.0,-29608260.0,-46060135.0,-32132473.0,-51290810.0,-59366315.0],
          "Komisi Online": [-33590711.0,-30623912.0,-31794652.0,-32814391.0,-42017310.0,-41330407.0],
          "Diskon MBG": [null,null,-3786000.0,null,null,null]
        } },
      { name:'Pendapatan Bersih', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Pendapatan + Diskon (diskon sudah negatif).' },
      { name:'HPP', accountLabel:'Harga Pokok Penjualan', computed:true, forceFormula:true, values:[null,null,null,null,null,null],
        children: {
          "Produk Utama": { vals:[287164952.0,222509976.0,297503290.0,251153484.0,305345129.0,345785693.0], pathHint: "Harga Pokok Penjualan" },
          "Konsinyasi": { vals:[9208500.0,2070400.0,15721000.0,3624900.0,12996600.0,4644000.0], pathHint: "Harga Pokok Penjualan" },
          "Pembelian Langsung": [37000.0,227847.0,268708.0,287215.0,518800.0,266226.0]
        } },
      { name:'Laba Kotor', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Pendapatan Bersih - HPP.' },
      { name:'Biaya Operasional', accountLabel:'Biaya Operasional', computed:true, forceFormula:true, signRule:'subtractPendapatanFromCosts', values:[null,null,null,null,null,null],
        note:'"Pendapatan Lain-lain" di dalam grup ini SENGAJA jadi PENGURANG Biaya Operasional.',
        children: {
            "Biaya Listrik & Air BKBG": [5656678.0,4557418.0,4909471.0,5866946.0,5644298.0,5853500.0],
            "Biaya Internet & Telepon BKBG": [505167.0,505167.0,505167.0,538600.0,513500.0,513613.0],
            "Biaya Alat Tulis Kantor BKBG": [860000.0,1195900.0,894100.0,813000.0,1392300.0,1158000.0],
            "Beban Tunjangan Hari Raya BKBG": [null,null,null,null,null,null],
            "Biaya System POS BKBG": [429034.0,429034.0,429034.0,339517.0,250000.0,250000.0],
            "Biaya Transport BKBG": [357500.0,290000.0,182500.0,187000.0,67000.0,209636.0],
            "Biaya Kebersihan & Pengharum BKBG": [944500.0,1750400.0,899600.0,924600.0,1193600.0,1211600.0],
            "Biaya Operasionalonal Lainnya BKBG": [2454000.0,1733267.0,2344661.0,2551642.0,5688881.0,2595832.0],
            "Biaya Sewa Bangunan BKBG": [8125000.0,8125000.0,8125000.0,8125000.0,8125000.0,8125000.0],
            "Biaya Maintenance Aset BKBG": [439000.0,1600000.0,null,1085000.0,200000.0,1050000.0],
            "Biaya Insentif, Bonus, Fee BKBG": [2909657.0,2201688.0,6244064.0,781838.0,6448641.0,5467054.0],
            "Biaya Gaji Karyawan BKBG": [18817633.0,19232049.0,18933877.0,20190407.0,19042287.0,19516994.0],
            "Biaya Perjalanan Dinas BKBG": [205864.0,94200.0,561333.0,349575.0,357100.0,34200.0],
            "Beban Freelance BKBG": [null,null,null,null,null,null],
            "Biaya Administrasi Bank": [150009.0,136375.0,146534.0,136842.0,372405.0,178723.0],
            "Pendapatan Lain-lain": [null,null,null,null,null,null]
        } },
      { name:'Laba Operasional', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Laba Kotor - Biaya Operasional.' },
      { name:'Biaya Penyusutan', accountLabel:'Biaya Penyusutan', values:[null,null,null,null,null,null] },
      { name:'Biaya Pajak', accountLabel:'Biaya Pajak', values:[462298.0,341966.0,308507.0,1031406.0,301225.0,5287735.0] },
      { name:'Cost of Management', accountLabel:'Cost of Management', values:[24824218.0,19800990.0,26285950.0,21348183.0,27234081.0,30585863.0] },
      { name:'Laba Bersih', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Laba Operasional - Biaya Penyusutan - Biaya Pajak - Cost of Management.' },
    ]
  },
  cibubur: {
    label: 'Cibubur', color: '#8B8FA8', grounded: 'real',
    waterfall: [
      { name:'Pendapatan', accountLabel:'Pendapatan', computed:true, forceFormula:true, values:[null,null,null,null,null,null],
        children: {
          "Produk Utama": { vals:[295079941.0,231175718.0,296624688.0,264031025.0,332088480.0,393504391.0], pathHint: "Pendapatan" },
          "Konsinyasi": { vals:[5156809.0,3995732.0,12815212.0,5209500.0,5727000.0,6663300.0], pathHint: "Pendapatan" }
        } },
      { name:'Diskon', accountLabel:'Diskon', computed:true, forceFormula:true, noNegColor:true, values:[null,null,null,null,null,null],
        children: {
          "Diskon": [-31703100.0,-21977425.0,-28604515.0,-27182141.0,-40239995.0,-48792413.0],
          "Komisi Online": [-23352108.0,-19211231.0,-21067400.0,-19673563.0,-26181717.0,-29002591.0],
          "Diskon MBG": [null,null,null,null,null,null]
        } },
      { name:'Pendapatan Bersih', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Pendapatan + Diskon (diskon sudah negatif).' },
      { name:'HPP', accountLabel:'Harga Pokok Penjualan', computed:true, forceFormula:true, values:[null,null,null,null,null,null],
        children: {
          "Produk Utama": { vals:[176091735.0,155321390.0,159397800.0,162688034.0,196083935.0,225016100.0], pathHint: "Harga Pokok Penjualan" },
          "Konsinyasi": { vals:[1587789.0,8043000.0,2109502.0,2919532.0,2415475.0,905000.0], pathHint: "Harga Pokok Penjualan" },
          "Pembelian Langsung": [null,122000.0,null,null,77911.0,35499.0]
        } },
      { name:'Laba Kotor', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Pendapatan Bersih - HPP.' },
      { name:'Biaya Operasional', accountLabel:'Biaya Operasional', computed:true, forceFormula:true, signRule:'subtractPendapatanFromCosts', values:[null,null,null,null,null,null],
        note:'"Pendapatan Lain-lain" di dalam grup ini SENGAJA jadi PENGURANG Biaya Operasional.',
        children: {
            "Biaya Listrik & Air BKCB": [6021000.0,6014000.0,6007000.0,6021000.0,6017500.0,6010500.0],
            "Biaya Internet & Telepon BKCB": [638738.0,656492.0,651389.0,664147.0,649700.0,649813.0],
            "Biaya Alat Tulis Kantor BKCB": [163636.0,241602.0,193773.0,409320.0,230354.0,466304.0],
            "Beban Tunjangan Hari Raya BKCB": [null,null,null,null,null,null],
            "Biaya System POS BKCB": [429034.0,429034.0,429034.0,339517.0,250000.0,250000.0],
            "Beban Transport BKCB": [null,null,216266.0,79000.0,null,34119.0],
            "Biaya Kebersihan & Pengharum BKCB": [788199.0,737400.0,831100.0,834748.0,850500.0,744700.0],
            "Biaya Operasionalonal Lainnya BKCB": [1026711.0,368457.0,1894030.0,2383929.0,562718.0,3124620.0],
            "Biaya Sewa Bangunan BKCB": [9375000.0,9375000.0,9375000.0,9375000.0,9375000.0,9375000.0],
            "Biaya Maintenance Aset BKCB": [3601000.0,null,552000.0,1650000.0,5240000.0,null],
            "Biaya Gaji Karyawan BKCB": [11798761.0,11996665.0,12491451.0,12806639.0,12662562.0,12355404.0],
            "Biaya Insentif. Bonus. Fee BKCB": [876618.0,1922448.0,5778628.0,1016838.0,4080314.0,3510443.0],
            "Biaya Perjalanan Dinas BKCB": [326198.0,32700.0,161333.0,117175.0,200767.0,98200.0],
            "Biaya Perjalanan Dinas BKDR": [null,null,null,null,null,null],
            "Biaya Administrasi Bank": [36248.0,35500.0,40000.0,32500.0,31000.0,40500.0],
            "Pendapatan Lain-lain": [null,null,null,null,null,null]
        } },
      { name:'Laba Operasional', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Laba Kotor - Biaya Operasional.' },
      { name:'Biaya Penyusutan', accountLabel:'Biaya Penyusutan', values:[1368417.0,1368417.0,1368417.0,1368417.0,1368417.0,1368417.0] },
      { name:'Biaya Pajak', accountLabel:'Biaya Pajak', values:[462298.0,341966.0,308507.0,1031406.0,301225.0,385600.0] },
      { name:'Cost of Management', accountLabel:'Cost of Management', values:[15011838.0,11758573.0,15471995.0,13462026.0,16890774.0,20019185.0] },
      { name:'Laba Bersih', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Laba Operasional - Biaya Penyusutan - Biaya Pajak - Cost of Management.' },
    ]
  },
  mekarsari: {
    label: 'Mekarsari', color: '#8B8FA8', grounded: 'real',
    waterfall: [
      { name:'Pendapatan', accountLabel:'Pendapatan', computed:true, forceFormula:true, values:[null,null,null,null,null,null],
        children: {
          "Produk Utama": { vals:[214019300.0,181374400.0,220004000.0,196717975.0,256710080.0,284207025.0], pathHint: "Pendapatan" },
          "Konsinyasi": { vals:[6745000.0,5533500.0,9991500.0,7350000.0,5555700.0,9514300.0], pathHint: "Pendapatan" }
        } },
      { name:'Diskon', accountLabel:'Diskon', computed:true, forceFormula:true, noNegColor:true, values:[null,null,null,null,null,null],
        children: {
          "Diskon": [-22074510.0,-18953060.0,-20612570.0,-18796759.0,-28098093.0,-35537763.0],
          "Komisi Online": [-16863472.0,-16715568.0,-15227344.0,-15171425.0,-21840523.0,-18057575.0],
          "Diskon MBG": [null,null,null,null,null,null]
        } },
      { name:'Pendapatan Bersih', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Pendapatan + Diskon (diskon sudah negatif).' },
      { name:'HPP', accountLabel:'Harga Pokok Penjualan', computed:true, forceFormula:true, values:[null,null,null,null,null,null],
        children: {
          "Produk Utama": { vals:[125827850.0,106776615.0,127927700.0,115911035.0,158087740.0,162701250.0], pathHint: "Harga Pokok Penjualan" },
          "Konsinyasi": { vals:[6797000.0,4250500.0,4717000.0,7774500.0,3856500.0,1563000.0], pathHint: "Harga Pokok Penjualan" },
          "Pembelian Langsung": [null,210000.0,null,6500.0,null,null]
        } },
      { name:'Laba Kotor', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Pendapatan Bersih - HPP.' },
      { name:'Biaya Operasional', accountLabel:'Biaya Operasional', computed:true, forceFormula:true, signRule:'subtractPendapatanFromCosts', values:[null,null,null,null,null,null],
        note:'"Pendapatan Lain-lain" di dalam grup ini SENGAJA jadi PENGURANG Biaya Operasional.',
        children: {
            "Biaya Listrik & Air BKMK": [2007000.0,2007000.0,2000000.0,2007000.0,2007000.0,2007000.0],
            "Biaya Internet & Telepon BKMK": [469317.0,469317.0,469317.0,487250.0,477650.0,477763.0],
            "Biaya Alat Tulis Kantor BKMK": [138000.0,153000.0,346500.0,404000.0,369000.0,240500.0],
            "Beban Tunjangan Hari Raya BKMK": [null,null,null,null,null,null],
            "Biaya System POS BKMK": [429034.0,429034.0,429034.0,339517.0,250000.0,250000.0],
            "Biaya Transport BKMK": [180500.0,161250.0,93500.0,70000.0,62000.0,100000.0],
            "Biaya Kebersihan & Pengharum BKMK": [171000.0,200500.0,248500.0,121000.0,227000.0,null],
            "Biaya Operasionalonal Lainnya BKMK": [1228000.0,610781.0,1540603.0,638667.0,1248694.0,1068472.0],
            "Biaya Sewa Bangunan BKMK": [6250000.0,6250000.0,6250000.0,6250000.0,6250000.0,6250000.0],
            "Biaya Maintenance Aset BKMK": [605000.0,937000.0,2119292.0,448000.0,180000.0,180000.0],
            "Biaya Gaji Karyawan BKMK": [9040692.0,9163067.0,9011908.0,8917357.0,9016496.0,7970599.0],
            "Biaya Insentif, Bonus, Fee BKMK": [736618.0,1929706.0,3301777.0,1211838.0,2948605.0,2179679.0],
            "Beban Freelance BKMK": [null,null,null,null,null,null],
            "Biaya Perjalanan Dinas BKMK": [326198.0,60450.0,223583.0,145425.0,200767.0,34200.0],
            "Biaya Administrasi Bank": [5000.0,5000.0,11000.0,5000.0,5000.0,12500.0],
            "Pendapatan Lain-lain": [null,null,null,null,null,null]
        } },
      { name:'Laba Operasional', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Laba Kotor - Biaya Operasional.' },
      { name:'Biaya Penyusutan', accountLabel:'Biaya Penyusutan', values:[1210313.0,1210313.0,1210313.0,1210313.0,1210313.0,1210313.0] },
      { name:'Biaya Pajak', accountLabel:'Biaya Pajak', values:[462298.0,341966.0,308507.0,1031406.0,301225.0,3885600.0] },
      { name:'Cost of Management', accountLabel:'Cost of Management', values:[11038215.0,9345395.0,11499775.0,10203399.0,13113289.0,14696866.0] },
      { name:'Laba Bersih', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Laba Operasional - Biaya Penyusutan - Biaya Pajak - Cost of Management.' },
    ]
  },
  pekapuran: {
    label: 'Pekapuran', color: '#8B8FA8', grounded: 'real',
    waterfall: [
      { name:'Pendapatan', accountLabel:'Pendapatan', computed:true, forceFormula:true, values:[null,null,null,null,null,null],
        children: {
          "Produk Utama": { vals:[255376800.0,231577800.0,264503100.0,237283391.0,297052318.0,336503902.0], pathHint: "Pendapatan" },
          "Konsinyasi": { vals:[5212500.0,6638500.0,8301000.0,5224999.0,6260497.0,7383098.0], pathHint: "Pendapatan" }
        } },
      { name:'Diskon', accountLabel:'Diskon', computed:true, forceFormula:true, noNegColor:true, values:[null,null,null,null,null,null],
        children: {
          "Diskon": [-26959735.0,-22046765.0,-26594430.0,-24932598.0,-35433704.0,-39163239.0],
          "Komisi Online": [-18214627.0,-16945316.0,-16696074.0,-15328874.0,-22872399.0,-20006050.0],
          "Diskon MBG": [null,-5100000.0,null,null,null,null]
        } },
      { name:'Pendapatan Bersih', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Pendapatan + Diskon (diskon sudah negatif).' },
      { name:'HPP', accountLabel:'Harga Pokok Penjualan', computed:true, forceFormula:true, values:[null,null,null,null,null,null],
        children: {
          "Produk Utama": { vals:[153509880.0,129863735.0,148741175.0,144320677.0,177541130.0,121712025.0], pathHint: "Harga Pokok Penjualan" },
          "Konsinyasi": { vals:[5084700.0,3258500.0,4509000.0,6642500.0,3281000.0,3361500.0], pathHint: "Harga Pokok Penjualan" },
          "Pembelian Langsung": [null,165000.0,null,null,32000.0,null]
        } },
      { name:'Laba Kotor', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Pendapatan Bersih - HPP.' },
      { name:'Biaya Operasional', accountLabel:'Biaya Operasional', computed:true, forceFormula:true, signRule:'subtractPendapatanFromCosts', values:[null,null,null,null,null,null],
        note:'"Pendapatan Lain-lain" di dalam grup ini SENGAJA jadi PENGURANG Biaya Operasional.',
        children: {
            "Biaya Listrik & Air BKPK": [3010500.0,3010500.0,3000000.0,3010500.0,3010500.0,3010500.0],
            "Biaya Internet & Telepon BKPK": [449667.0,452667.0,452667.0,470600.0,461000.0,461113.0],
            "Biaya Alat Tulis Kantor BKPK": [269000.0,266500.0,null,268000.0,100500.0,258000.0],
            "Beban Tunjangan Hari Raya BKPK": [null,null,null,null,null,null],
            "Biaya System POS BKPK": [429034.0,429034.0,429034.0,339517.0,250000.0,250000.0],
            "Biaya Transport BKPK": [114871.0,49750.0,419000.0,null,122000.0,35000.0],
            "Biaya Kebersihan & Pengharum BKPK": [660500.0,739400.0,695500.0,685800.0,793800.0,734500.0],
            "Biaya Operasionalonal Lainnya BKPK": [536900.0,371114.0,1431793.0,1153151.0,1625950.0,970421.0],
            "Biaya Sewa Bangunan BKPK": [5750000.0,5750000.0,5750000.0,5750000.0,5750000.0,5750000.0],
            "Beban Maintenance Aset Pekapuran": [415000.0,200000.0,400000.0,226000.0,null,null],
            "Biaya Gaji Karyawan BKPK": [8892123.0,8807080.0,9249728.0,8755958.0,9439649.0,9443444.0],
            "Biaya Insentif. Bonus. Fee BKPK": [526618.0,2206297.0,4306173.0,581838.0,3096898.0,2839007.0],
            "Biaya Perjalanan Dinas BKPK": [377198.0,60450.0,367583.0,145425.0,310767.0,34200.0],
            "Biaya Perjalanan Dinas BKMK": [null,null,null,null,null,null],
            "Biaya Administrasi Bank": [null,null,7000.0,null,null,2500.0],
            "Pendapatan Lain-lain": [null,null,null,null,null,null]
        } },
      { name:'Laba Operasional', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Laba Kotor - Biaya Operasional.' },
      { name:'Biaya Penyusutan', accountLabel:'Biaya Penyusutan', values:[1471871.0,1471871.0,1471871.0,1471871.0,1471871.0,1471871.0] },
      { name:'Biaya Pajak', accountLabel:'Biaya Pajak', values:[462298.0,341966.0,308507.0,1031406.0,301225.0,385600.0] },
      { name:'Cost of Management', accountLabel:'Cost of Management', values:[13029465.0,11910815.0,13640205.0,12021720.0,15165641.0,17216550.0] },
      { name:'Laba Bersih', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Laba Operasional - Biaya Penyusutan - Biaya Pajak - Cost of Management.' },
    ]
  },
  jatimelati: {
    label: 'Jatimelati', color: '#8B8FA8', grounded: 'real',
    waterfall: [
      { name:'Pendapatan', accountLabel:'Pendapatan', computed:true, forceFormula:true, values:[null,null,null,null,null,null],
        children: {
          "Produk Utama": { vals:[375754500.0,319832610.0,454152600.0,325222900.0,408097760.0,476498810.0], pathHint: "Pendapatan" },
          "Konsinyasi": { vals:[7934500.0,8926000.0,9586000.0,6564500.0,5250700.0,8092700.0], pathHint: "Pendapatan" }
        } },
      { name:'Diskon', accountLabel:'Diskon', computed:true, forceFormula:true, noNegColor:true, values:[null,null,null,null,null,null],
        children: {
          "Diskon": [-40359300.0,-29883940.0,-41037560.0,-30926341.0,-47751695.0,-54738034.0],
          "Komisi Online": [-15028898.0,-15570950.0,-17320125.0,-14470287.0,-22128899.0,-22418479.0],
          "Diskon MBG": [null,null,-10657500.0,-1185000.0,null,null]
        } },
      { name:'Pendapatan Bersih', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Pendapatan + Diskon (diskon sudah negatif).' },
      { name:'HPP', accountLabel:'Harga Pokok Penjualan', computed:true, forceFormula:true, values:[null,null,null,null,null,null],
        children: {
          "Produk Utama": { vals:[238232625.0,204311920.0,265117850.0,202405146.0,244600810.0,278427775.0], pathHint: "Harga Pokok Penjualan" },
          "Konsinyasi": { vals:[3596000.0,4459001.0,5217000.0,7077580.0,2161000.0,7206036.0], pathHint: "Harga Pokok Penjualan" },
          "Pembelian Langsung": [195000.0,105000.0,281500.0,null,null,10000.0]
        } },
      { name:'Laba Kotor', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Pendapatan Bersih - HPP.' },
      { name:'Biaya Operasional', accountLabel:'Biaya Operasional', computed:true, forceFormula:true, signRule:'subtractPendapatanFromCosts', values:[null,null,null,null,null,null],
        note:'"Pendapatan Lain-lain" di dalam grup ini SENGAJA jadi PENGURANG Biaya Operasional.',
        children: {
            "Biaya Listrik & Air BKJM": [3421000.0,3214000.0,3207000.0,3214000.0,3207000.0,3207000.0],
            "Biaya Internet & Telepon BKJM": [546237.0,546237.0,882237.0,567170.0,557570.0,557683.0],
            "Biaya Alat Tulis Kantor BKJM": [503000.0,477000.0,522000.0,586000.0,843000.0,620500.0],
            "Beban Tunjangan Hari Raya BKJM": [null,null,null,null,null,null],
            "Biaya System POS BKJM": [429034.0,429034.0,429034.0,339517.0,250000.0,250000.0],
            "Biaya Transport BKJM": [231402.0,327844.0,410000.0,433000.0,279000.0,395500.0],
            "Biaya Kebersihan & Pengharum BKJM": [800090.0,814001.0,747300.0,833600.0,906650.0,949300.0],
            "Biaya Operasionalonal Lainnya BKJM": [1426500.0,991389.0,2041751.0,1741819.0,1514900.0,1266925.0],
            "Biaya Sewa Bangunan BKJM": [7083333.0,7083333.0,7083333.0,7083333.0,7083333.0,7083333.0],
            "Beban Maintenance Aset BKJM": [null,150000.0,1643000.0,270000.0,null,null],
            "Biaya Gaji Karyawan BKJM": [11814271.0,13316042.0,13017128.0,16786760.0,15349245.0,15968245.0],
            "Biaya Insentif, Bonus, Fee BKJM": [1027600.0,3383542.0,4730187.0,317600.0,4671058.0,4795491.0],
            "Beban Freelance BKJM": [null,null,null,null,null,null],
            "Biaya Perjalanan Dinas BKJM": [297448.0,153700.0,400333.0,413620.0,258700.0,271200.0],
            "Biaya Administrasi Bank": [156154.0,139011.0,155099.0,144553.0,203399.0,174733.0],
            "Pendapatan Lain-lain": [null,null,null,null,null,null]
        } },
      { name:'Laba Operasional', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Laba Kotor - Biaya Operasional.' },
      { name:'Biaya Penyusutan', accountLabel:'Biaya Penyusutan', values:[1336125.0,1336125.0,1336125.0,1336125.0,1336125.0,1336125.0] },
      { name:'Biaya Pajak', accountLabel:'Biaya Pajak', values:[null,195024.0,163349.0,427146.0,178810.0,5024367.0] },
      { name:'Cost of Management', accountLabel:'Cost of Management', values:[19184450.0,16437931.0,23186930.0,16589370.0,20617423.0,24257176.0] },
      { name:'Laba Bersih', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Laba Operasional - Biaya Penyusutan - Biaya Pajak - Cost of Management.' },
    ]
  },
  nusaindah: {
    label: 'Nusa Indah', color: '#8B8FA8', grounded: 'real',
    waterfall: [
      { name:'Pendapatan', accountLabel:'Pendapatan', computed:true, forceFormula:true, values:[null,null,null,null,null,null],
        children: {
          "Produk Utama": { vals:[235922796.0,180906609.0,219415496.0,190761360.0,245789529.0,284222223.0], pathHint: "Pendapatan" },
          "Konsinyasi": { vals:[5744675.0,3448725.0,4266145.0,3197745.0,4698760.0,5944955.0], pathHint: "Pendapatan" }
        } },
      { name:'Diskon', accountLabel:'Diskon', computed:true, forceFormula:true, noNegColor:true, values:[null,null,null,null,null,null],
        children: {
          "Diskon": [-26282280.0,-16402845.0,-19980745.0,-18187679.0,-26322378.0,-28655850.0],
          "Komisi Online": [-14714498.0,-11818437.0,-11785101.0,-12955650.0,-17545735.0,-18077339.0],
          "Diskon MBG": [null,null,null,null,null,null]
        } },
      { name:'Pendapatan Bersih', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Pendapatan + Diskon (diskon sudah negatif).' },
      { name:'HPP', accountLabel:'Harga Pokok Penjualan', computed:true, forceFormula:true, values:[null,null,null,null,null,null],
        children: {
          "Produk Utama": { vals:[151636050.0,117962600.0,142567270.0,120822958.0,145365300.0,174748825.0], pathHint: "Harga Pokok Penjualan" },
          "Konsinyasi": { vals:[559000.0,749500.0,6892000.0,5320000.0,null,198000.0], pathHint: "Harga Pokok Penjualan" },
          "Pembelian Langsung": [null,51000.0,210000.0,null,null,9000.0]
        } },
      { name:'Laba Kotor', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Pendapatan Bersih - HPP.' },
      { name:'Biaya Operasional', accountLabel:'Biaya Operasional', computed:true, forceFormula:true, signRule:'subtractPendapatanFromCosts', values:[null,null,null,null,null,null],
        note:'"Pendapatan Lain-lain" di dalam grup ini SENGAJA jadi PENGURANG Biaya Operasional.',
        children: {
            "Biaya Listrik & Air BKNI": [2280200.0,2276700.0,2284700.0,1279200.0,3279200.0,2268700.0],
            "Biaya Internet & Telepon BKNI": [505167.0,505167.0,505167.0,523100.0,513500.0,513613.0],
            "Biaya Alat Tulis Kantor BKNI": [513000.0,415500.0,259000.0,1370500.0,648500.0,494000.0],
            "Beban Tunjangan Hari Raya BKNI": [null,null,null,null,null,null],
            "Biaya System POS BKNI": [429034.0,429034.0,429034.0,339517.0,250000.0,250000.0],
            "Biaya Transport BKNI": [185000.0,126000.0,121000.0,205500.0,161500.0,237000.0],
            "Biaya Kebersihan & Pengharum BKNI": [782300.0,1332200.0,656310.0,749700.0,745000.0,771800.0],
            "Biaya Operasionalonal Lainnya BKNI": [1444500.0,761437.0,876693.0,1028436.0,1039694.0,854475.0],
            "Biaya Sewa Bangunan BKNI": [4583333.0,4583333.0,4583333.0,4583333.0,4583333.0,4583333.0],
            "Biaya Maintenance Aset BKNI": [null,945000.0,478080.0,170000.0,815000.0,null],
            "Biaya Gaji Karyawan BKNI": [8742314.0,10109083.0,9121117.0,11268285.0,10517285.0,10755285.0],
            "Biaya Insentif, Bonus, Fee BKNI": [1856102.0,712600.0,3402996.0,292600.0,3166849.0,3465531.0],
            "Beban Freelance BKNI": [null,null,null,null,null,null],
            "Biaya Perjalanan Dinas BKNI": [297448.0,153700.0,400333.0,413620.0,239700.0,271200.0],
            "Biaya Administrasi Bank": [131959.0,115689.0,126536.0,111856.0,118226.0,170409.0],
            "Pendapatan Lain-lain": [null,null,null,null,null,null]
        } },
      { name:'Laba Operasional', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Laba Kotor - Biaya Operasional.' },
      { name:'Biaya Penyusutan', accountLabel:'Biaya Penyusutan', values:[1342708.0,1342708.0,1342708.0,1342708.0,1342708.0,1342708.0] },
      { name:'Biaya Pajak', accountLabel:'Biaya Pajak', values:[null,195024.0,163349.0,427146.0,178810.0,224367.0] },
      { name:'Cost of Management', accountLabel:'Cost of Management', values:[12083374.0,9217767.0,11184082.0,9697955.0,12524414.0,14513759.0] },
      { name:'Laba Bersih', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Laba Operasional - Biaya Penyusutan - Biaya Pajak - Cost of Management.' },
    ]
  },
  cibinong: {
    label: 'Cibinong', color: '#8B8FA8', grounded: 'real',
    waterfall: [
      { name:'Pendapatan', accountLabel:'Pendapatan', computed:true, forceFormula:true, values:[null,null,null,null,null,null],
        children: {
          "Produk Utama": { vals:[166522000.0,125073700.0,173540601.0,131130975.0,186291100.0,205064211.0], pathHint: "Pendapatan" },
          "Konsinyasi": { vals:[93000.0,279000.0,418499.0,232500.0,1438000.0,9394014.0], pathHint: "Pendapatan" }
        } },
      { name:'Diskon', accountLabel:'Diskon', computed:true, forceFormula:true, noNegColor:true, values:[null,null,null,null,null,null],
        children: {
          "Diskon": [-16372630.0,-13454275.0,-21684050.0,-15555078.0,-28119537.0,-33832226.0],
          "Komisi Online": [null,null,null,null,null,null],
          "Diskon MBG": [-13736985.0,-10326620.0,-13841150.0,-11761481.0,-17092676.0,-18629945.0]
        } },
      { name:'Pendapatan Bersih', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Pendapatan + Diskon (diskon sudah negatif).' },
      { name:'HPP', accountLabel:'Harga Pokok Penjualan', computed:true, forceFormula:true, values:[null,null,null,null,null,null],
        children: {
          "Produk Utama": { vals:[104522050.0,79388705.0,100272910.0,78391750.0,113191369.0,134229925.0], pathHint: "Harga Pokok Penjualan" },
          "Konsinyasi": { vals:[264000.0,null,264000.0,297000.0,null,264000.0], pathHint: "Harga Pokok Penjualan" },
          "Pembelian Langsung": [null,21888.0,null,null,null,null]
        } },
      { name:'Laba Kotor', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Pendapatan Bersih - HPP.' },
      { name:'Biaya Operasional', accountLabel:'Biaya Operasional', computed:true, forceFormula:true, signRule:'subtractPendapatanFromCosts', values:[null,null,null,null,null,null],
        note:'"Pendapatan Lain-lain" di dalam grup ini SENGAJA jadi PENGURANG Biaya Operasional.',
        children: {
            "Biaya Listrik & Air BKCN": [981552.0,1047016.0,929640.0,868840.0,867220.0,1025736.0],
            "Biaya Internet & Telepon BKCN": [288717.0,288717.0,291717.0,309650.0,300050.0,300163.0],
            "Biaya Alat Tulis Kantor BKCN": [164580.0,59678.0,82360.0,95605.0,89782.0,68212.0],
            "Beban Tunjangan Hari Raya BKCN": [null,null,null,null,null,null],
            "Biaya System POS BKCN": [429034.0,429034.0,429034.0,339517.0,250000.0,250000.0],
            "Beban Transport BKCN": [null,51000.0,287000.0,253700.0,null,null],
            "Biaya Kebersihan & Pengharum BKCN": [134950.0,265409.0,161400.0,96679.0,235045.0,805629.0],
            "Biaya Operasionalonal Lainnya BKCN": [1329206.0,835007.0,1562070.0,796350.0,1448983.0,1211445.0],
            "Biaya Sewa Bangunan BKCN": [3250000.0,3250000.0,3250000.0,3250000.0,3250000.0,3250000.0],
            "Beban Maintenance Aset BKCN": [135000.0,null,120000.0,180000.0,null,833000.0],
            "Biaya Gaji Karyawan BKCN": [6469295.0,9081397.0,9482742.0,8913842.0,9440326.0,9261226.0],
            "Biaya Insentif, Bonus, Fee BKCN": [1917498.0,1042600.0,4083031.0,187600.0,2061728.0,2209627.0],
            "Beban Freelance BKCN": [null,null,null,null,null,null],
            "Biaya Perjalanan Dinas BKCN": [159150.0,32700.0,332500.0,197175.0,393933.0,34200.0],
            "Biaya Administrasi Bank": [null,null,7500.0,50000.0,50000.0,42500.0],
            "Pendapatan Lain-lain": [null,null,null,null,null,null]
        } },
      { name:'Laba Operasional', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Laba Kotor - Biaya Operasional.' },
      { name:'Biaya Penyusutan', accountLabel:'Biaya Penyusutan', values:[1117295.0,1117295.0,1117295.0,1117295.0,1117295.0,1117295.0] },
      { name:'Biaya Pajak', accountLabel:'Biaya Pajak', values:[null,null,null,null,null,null] },
      { name:'Cost of Management', accountLabel:'Cost of Management', values:[8330750.0,6267635.0,8697955.0,6568174.0,9386455.0,10724111.0] },
      { name:'Laba Bersih', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Laba Operasional - Biaya Penyusutan - Biaya Pajak - Cost of Management.' },
    ]
  },
  bantargebang: {
    label: 'Bantargebang', color: '#8B8FA8', grounded: 'real',
    waterfall: [
      { name:'Pendapatan', accountLabel:'Pendapatan', computed:true, forceFormula:true, values:[null,null,null,null,null,null],
        children: {
          "Produk Utama": { vals:[275195300.0,212905650.0,262833600.0,232961900.0,299463315.0,318705975.0], pathHint: "Pendapatan" },
          "Konsinyasi": { vals:[6349500.0,4625500.0,9484500.0,4770500.0,5235400.0,6234500.0], pathHint: "Pendapatan" }
        } },
      { name:'Diskon', accountLabel:'Diskon', computed:true, forceFormula:true, noNegColor:true, values:[null,null,null,null,null,null],
        children: {
          "Diskon": [-31298695.0,-29148240.0,-27671525.0,-30482041.0,-41306404.0,-39764700.0],
          "Komisi Online": [-8372134.0,-10794078.0,-13796139.0,-11980040.0,-18176074.0,-17952837.0],
          "Diskon MBG": [null,null,null,null,null,null]
        } },
      { name:'Pendapatan Bersih', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Pendapatan + Diskon (diskon sudah negatif).' },
      { name:'HPP', accountLabel:'Harga Pokok Penjualan', computed:true, forceFormula:true, values:[null,null,null,null,null,null],
        children: {
          "Produk Utama": { vals:[176839275.0,138345215.0,175339954.0,145711690.0,188934535.0,190428150.0], pathHint: "Harga Pokok Penjualan" },
          "Konsinyasi": { vals:[10477500.0,1967000.0,4056500.0,9417000.0,1683500.0,3817000.0], pathHint: "Harga Pokok Penjualan" },
          "Pembelian Langsung": [null,210000.0,null,12000.0,null,null]
        } },
      { name:'Laba Kotor', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Pendapatan Bersih - HPP.' },
      { name:'Biaya Operasional', accountLabel:'Biaya Operasional', computed:true, forceFormula:true, signRule:'subtractPendapatanFromCosts', values:[null,null,null,null,null,null],
        note:'"Pendapatan Lain-lain" di dalam grup ini SENGAJA jadi PENGURANG Biaya Operasional.',
        children: {
            "Biaya Listrik & Air BKBN": [3514000.0,3510500.0,2507000.0,3514000.0,2507000.0,3507000.0],
            "Biaya Internet & Telepon BKBN": [469667.0,475667.0,129667.0,467600.0,490000.0,458113.0],
            "Biaya Alat Tulis Kantor BKBN": [298000.0,328000.0,1310000.0,194000.0,320000.0,284000.0],
            "Beban Tunjangan Hari Raya BKBN": [null,null,null,null,null,null],
            "Biaya System POS BKBN": [250000.0,250000.0,250000.0,250000.0,250000.0,250000.0],
            "Beban Transport BKBN": [20000.0,69000.0,null,10000.0,null,91000.0],
            "Biaya Kebersihan & Pengharum BKBN": [762000.0,610500.0,77500.0,911000.0,704000.0,710800.0],
            "Biaya Operasionalonal Lainnya BKBN": [861900.0,376140.0,1610588.0,815947.0,1310914.0,641101.0],
            "Biaya Sewa Bangunan BKBN": [7500000.0,7500000.0,7500000.0,7500000.0,7500000.0,7500000.0],
            "Biaya Maintenance Aset BKBN": [621500.0,null,200000.0,null,710000.0,423600.0],
            "Biaya Gaji Karyawan BKBN": [11808794.0,11802001.0,12022198.0,12025508.0,10935624.0,11797023.0],
            "Biaya Insentif, Bonus, Fee BKBN": [467600.0,1622600.0,4285815.0,117600.0,1801206.0,3618382.0],
            "Beban Freelance BKBN": [null,null,null,null,null,null],
            "Biaya Perjalanan Dinas BKBN": [350733.0,32700.0,142500.0,197175.0,329533.0,34200.0],
            "Biaya Administrasi Bank": [null,38764.0,7000.0,25000.0,44564.0,7000.0],
            "Pendapatan Lain-lain": [null,null,null,null,null,null]
        } },
      { name:'Laba Operasional', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Laba Kotor - Biaya Operasional.' },
      { name:'Biaya Penyusutan', accountLabel:'Biaya Penyusutan', values:[null,null,null,null,null,null] },
      { name:'Biaya Pajak', accountLabel:'Biaya Pajak', values:[null,null,null,null,null,null] },
      { name:'Cost of Management', accountLabel:'Cost of Management', values:[14077240.0,10876558.0,13615905.0,11886620.0,15234936.0,16253624.0] },
      { name:'Laba Bersih', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Laba Operasional - Biaya Penyusutan - Biaya Pajak - Cost of Management.' },
    ]
  },
  abdulgani: {
    label: 'Abdul Gani', color: '#8B8FA8', grounded: 'real',
    waterfall: [
      { name:'Pendapatan', accountLabel:'Pendapatan', computed:true, forceFormula:true, values:[null,null,null,null,null,null],
        children: {
          "Produk Utama": { vals:[342942998.0,306507501.0,381370142.0,311557350.0,397507655.0,473591505.0], pathHint: "Pendapatan" },
          "Konsinyasi": { vals:[7197600.0,3588999.0,6938997.0,6399600.0,4234000.0,6493300.0], pathHint: "Pendapatan" }
        } },
      { name:'Diskon', accountLabel:'Diskon', computed:true, forceFormula:true, noNegColor:true, values:[null,null,null,null,null,null],
        children: {
          "Diskon": [-56917923.0,-49316400.0,-46408600.0,-33640703.0,-47769338.0,-56200693.0],
          "Komisi Online": [null,null,null,null,null,null],
          "Diskon MBG": [-4060865.0,-9061927.0,-13231383.0,-11032079.0,-18304398.0,-22315890.0]
        } },
      { name:'Pendapatan Bersih', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Pendapatan + Diskon (diskon sudah negatif).' },
      { name:'HPP', accountLabel:'Harga Pokok Penjualan', computed:true, forceFormula:true, values:[null,null,null,null,null,null],
        children: {
          "Produk Utama": { vals:[206971782.0,191667606.0,230557349.0,172119250.0,239977778.0,278116685.0], pathHint: "Harga Pokok Penjualan" },
          "Konsinyasi": { vals:[1877500.0,2192000.0,4991500.0,2418500.0,653500.0,3077000.0], pathHint: "Harga Pokok Penjualan" },
          "Pembelian Langsung": [8800.0,null,7588.0,null,null,44000.0]
        } },
      { name:'Laba Kotor', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Pendapatan Bersih - HPP.' },
      { name:'Biaya Operasional', accountLabel:'Biaya Operasional', computed:true, forceFormula:true, signRule:'subtractPendapatanFromCosts', values:[null,null,null,null,null,null],
        note:'"Pendapatan Lain-lain" di dalam grup ini SENGAJA jadi PENGURANG Biaya Operasional.',
        children: {
            "Biaya Listrik & Air BKAG": [3025908.0,1509233.0,1991450.0,3475810.0,3865942.0,4365031.0],
            "Biaya Internet & Telepon BKAG": [471867.0,471867.0,474867.0,492800.0,483200.0,483313.0],
            "Biaya Alat Tulis Kantor BKAG": [258523.0,269302.0,206457.0,277304.0,287002.0,262174.0],
            "Beban Tunjangan Hari Raya BKAG": [null,null,null,null,null,null],
            "Biaya System POS BKAG": [250000.0,250000.0,250000.0,250000.0,250000.0,250000.0],
            "Biaya Transport BKAG": [378000.0,277958.0,222000.0,228000.0,109804.0,null],
            "Biaya Kebersihan & Pengharum BKAG": [736800.0,846230.0,638500.0,723315.0,806130.0,723436.0],
            "Biaya Operasionalonal Lainnya BKAG": [1274200.0,1249335.0,1783452.0,1097048.0,1617071.0,1141497.0],
            "Biaya Sewa Bangunan BKAG": [7083333.0,7083333.0,7083333.0,7083333.0,7083333.0,7083333.0],
            "Biaya Maintenance Aset BKAG": [350000.0,null,null,null,200000.0,null],
            "Biaya Gaji Karyawan BKAG": [13438531.0,12048270.0,12125973.0,12307577.0,12510478.0,12185027.0],
            "Biaya Insentif, Bonus, Fee BKAG": [2218444.0,2601591.0,3893323.0,852600.0,4635058.0,4485568.0],
            "Beban Freelance BKAG": [null,null,null,null,null,null],
            "Biaya Perjalanan Dinas BKAG": [201400.0,32700.0,1547500.0,51775.0,148533.0,34200.0],
            "Biaya Administrasi Bank": [null,null,null,null,2500.0,null],
            "Pendapatan Lain-lain": [null,null,null,null,null,null]
        } },
      { name:'Laba Operasional', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Laba Kotor - Biaya Operasional.' },
      { name:'Biaya Penyusutan', accountLabel:'Biaya Penyusutan', values:[null,null,null,null,null,null] },
      { name:'Biaya Pajak', accountLabel:'Biaya Pajak', values:[null,null,null,null,null,null] },
      { name:'Cost of Management', accountLabel:'Cost of Management', values:[17507030.0,15504825.0,19415457.0,15897848.0,20087083.0,24004240.0] },
      { name:'Laba Bersih', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Laba Operasional - Biaya Penyusutan - Biaya Pajak - Cost of Management.' },
    ]
  },
  pangleseran: {
    label: 'Pangleseran', color: '#8B8FA8', grounded: 'real',
    waterfall: [
      { name:'Pendapatan', accountLabel:'Pendapatan', computed:true, forceFormula:true, values:[null,null,null,null,null,null],
        children: {
          "Produk Utama": { vals:[null,null,null,null,254583600.0,469565900.0], pathHint: "Pendapatan" },
          "Konsinyasi": { vals:[null,null,null,null,null,33355000.0], pathHint: "Pendapatan" }
        } },
      { name:'Diskon', accountLabel:'Diskon', computed:true, forceFormula:true, noNegColor:true, values:[null,null,null,null,null,null],
        children: {
          "Diskon": [null,null,null,null,-30717531.0,-60732641.0],
          "Komisi Online": [null,null,null,null,-8794986.0,-10680112.0],
          "Diskon MBG": [null,null,null,null,null,null]
        } },
      { name:'Pendapatan Bersih', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Pendapatan + Diskon (diskon sudah negatif).' },
      { name:'HPP', accountLabel:'Harga Pokok Penjualan', computed:true, forceFormula:true, values:[null,null,null,null,null,null],
        children: {
          "Produk Utama": { vals:[null,null,null,null,110208225.0,358818625.0], pathHint: "Harga Pokok Penjualan" },
          "Konsinyasi": { vals:[null,null,null,null,null,1000000.0], pathHint: "Harga Pokok Penjualan" },
          "Pembelian Langsung": [null,null,null,null,null,null]
        } },
      { name:'Laba Kotor', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Pendapatan Bersih - HPP.' },
      { name:'Biaya Operasional', accountLabel:'Biaya Operasional', computed:true, forceFormula:true, signRule:'subtractPendapatanFromCosts', values:[null,null,null,null,null,null],
        note:'"Pendapatan Lain-lain" di dalam grup ini SENGAJA jadi PENGURANG Biaya Operasional.',
        children: {
            "Biaya Listrik & Air BKPS": [null,null,null,null,null,1523172.0],
            "Biaya Internet & Telepon BKPS": [null,null,null,null,null,125113.0],
            "Biaya Alat Tulis Kantor BKPS": [null,null,null,null,82000.0,353500.0],
            "Beban Tunjangan Hari Raya BKPS": [null,null,null,null,null,null],
            "Biaya System POS BKPS": [null,null,null,null,null,191667.0],
            "Biaya Transport BKPS": [null,null,null,null,45000.0,60500.0],
            "Biaya Kebersihan & Pengharum BKPS": [null,null,null,null,105000.0,103000.0],
            "Biaya Operasionalonal Lainnya BKPS": [null,null,null,null,201458.0,1196138.0],
            "Biaya Sewa Bangunan BKPS": [null,null,null,null,null,1875000.0],
            "Biaya Maintenance Aset BKPS": [null,null,null,null,null,null],
            "Biaya Gaji Karyawan BKPS": [null,null,null,null,null,7472837.0],
            "Biaya Insentif, Bonus, Fee BKPS": [null,null,null,null,null,3017525.0],
            "Beban Freelance BKPS": [null,null,null,null,null,null],
            "Biaya Perjalanan Dinas BKPS": [null,null,null,null,null,null],
            "Biaya Administrasi Bank": [null,null,null,null,null,null],
            "Pendapatan Lain-lain": [null,null,null,null,null,null]
        } },
      { name:'Laba Operasional', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Laba Kotor - Biaya Operasional.' },
      { name:'Biaya Penyusutan', accountLabel:'Biaya Penyusutan', values:[null,null,null,null,null,null] },
      { name:'Biaya Pajak', accountLabel:'Biaya Pajak', values:[null,null,null,null,null,null] },
      { name:'Cost of Management', accountLabel:'Cost of Management', values:[null,null,null,null,12729180.0,25148445.0] },
      { name:'Laba Bersih', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Laba Operasional - Biaya Penyusutan - Biaya Pajak - Cost of Management.' },
    ]
  },
  outletsby: {
    label: 'Outlet', color: '#8B8FA8', grounded: 'real',
    waterfall: [
      { name:'Pendapatan', accountLabel:'Pendapatan', computed:true, forceFormula:true, values:[null,null,null,null,null,null],
        children: {
          "Produk Utama": { vals:[1155725573.0,860194561.0,1180252902.0,976363929.0,1319202224.0,1407360626.0], pathHint: "Pendapatan" },
          "Konsinyasi": { vals:[null,null,null,null,null,null], pathHint: "Pendapatan" }
        } },
      { name:'Diskon', accountLabel:'Diskon', computed:true, forceFormula:true, noNegColor:true, values:[null,null,null,null,null,null],
        children: {
          "Diskon": [-137605800.0,-114296100.0,-160121510.0,-140811911.0,-195981040.0,-224408628.0],
          "Komisi Online": [-56392475.0,-43118105.0,-51448481.0,-54167283.0,-84526815.0,-86344866.0],
          "Diskon MBG": [null,null,null,null,null,null]
        } },
      { name:'Pendapatan Bersih', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Pendapatan + Diskon (diskon sudah negatif).' },
      { name:'HPP', accountLabel:'Harga Pokok Penjualan', computed:true, forceFormula:true, values:[null,null,null,null,null,null],
        children: {
          "Produk Utama": { vals:[649675540.0,482199245.0,664581308.0,522122095.0,782494929.0,740541834.0], pathHint: "Harga Pokok Penjualan" },
          "Konsinyasi": { vals:[null,null,null,null,null,null], pathHint: "Harga Pokok Penjualan" },
          "Pembelian Langsung": [null,null,null,null,null,null]
        } },
      { name:'Laba Kotor', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Pendapatan Bersih - HPP.' },
      { name:'Biaya Operasional', accountLabel:'Biaya Operasional', computed:true, forceFormula:true, signRule:'subtractPendapatanFromCosts', values:[null,null,null,null,null,null],
        note:'"Pendapatan Lain-lain" di dalam grup ini SENGAJA jadi PENGURANG Biaya Operasional.',
        children: {
            "Biaya Listrik & Air OPSM": [3223282.0,3105077.0,2968728.0,2988288.0,3040390.0,2963994.0],
            "Biaya Internet & Telepon OPSM": [1920000.0,1920000.0,1920000.0,2220000.0,1920000.0,1780000.0],
            "Biaya Alat Tulis Kantor OPSM": [611500.0,461000.0,320000.0,700500.0,642000.0,420500.0],
            "Beban Tunjangan Hari Raya OPSM": [null,null,null,null,null,null],
            "Biaya System POS OPSM": [4819112.0,4819112.0,4819112.0,3655389.0,2491667.0,2300000.0],
            "Biaya Transport OPSM": [711000.0,546000.0,561000.0,879800.0,374300.0,456500.0],
            "Beban Kebersihan & Pengharum OPSM": [null,null,null,null,null,null],
            "Beban Operasional Lainnya OPSM": [1990000.0,3904572.0,5540883.0,3074616.0,4387769.0,1475500.0],
            "Biaya Sewa Bangunan OPSM": [29954167.0,29954167.0,29954167.0,29954167.0,null,null],
            "Beban Maintenance Aset OPSM": [750572.0,500000.0,1266000.0,50000.0,null,735210.0],
            "Biaya Gaji Karyawan OPSM": [48998170.0,53362109.0,51826843.0,53918240.0,54872845.0,51904394.0],
            "Biaya Insentif, Bonus, Fee OPSM": [3223150.0,18068121.0,21545626.0,569225.0,14739686.0,17355472.0],
            "Beban Freelance OPSM": [null,null,150000.0,255000.0,null,50000.0],
            "Biaya Perjalanan Dinas OPSM": [598000.0,1025000.0,1297100.0,995000.0,165231.0,null],
            "Biaya Administrasi Bank": [142379.0,148328.0,312518.0,84500.0,41252.0,5000.0],
            "Pendapatan Lain-lain": [null,null,null,null,null,null]
        } },
      { name:'Laba Operasional', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Laba Kotor - Biaya Operasional.' },
      { name:'Biaya Penyusutan', accountLabel:'Biaya Penyusutan', values:[null,null,null,null,null,null] },
      { name:'Biaya Pajak', accountLabel:'Biaya Pajak', values:[null,null,17418497.0,null,null,null] },
      { name:'Cost of Management', accountLabel:'Cost of Management', values:[57786279.0,43009728.0,59012645.0,48818196.0,65960111.0,70426091.0] },
      { name:'Laba Bersih', accountLabel:'', computed:true, values:[null,null,null,null,null,null],
        note:'DIHITUNG: Laba Operasional - Biaya Penyusutan - Biaya Pajak - Cost of Management.' },
    ]
  },
  outletPerf: { label: 'Per Cabang', color: '#4FC3F7', grounded: 'real', waterfall: [] },
  cabangRinci: { label: 'Cabang Rinci', color: '#B4E61D', grounded: 'real', waterfall: [] },
  splitOnline: { label: 'Split Online/Offline', color: '#4FC3F7', grounded: 'real', waterfall: [] },
  franchiseUnit: { label: 'Franchise', color: '#B4E61D', grounded: 'estimasi', waterfall: [] },
  bankPlaceholder: { label: 'Bank', color: '#726C9C', grounded: 'kosong', waterfall: [] },
  grafikCustom: { label: 'Grafik Custom', color: '#E066FF', grounded: 'kosong', waterfall: [] },
  leakRingkas: { label: 'Kebocoran · Ringkasan & Tren', color: '#F97316', grounded: 'real', waterfall: [] },
  leakJenis:   { label: 'Kebocoran · Rincian per Jenis', color: '#F97316', grounded: 'real', waterfall: [] },
  leakOutlet:  { label: 'Kebocoran · Peringkat per Outlet', color: '#F97316', grounded: 'real', waterfall: [] },
  rekonsiliasi:{ label: 'Validasi · Rekonsiliasi Otomatis', color: '#38BDF8', grounded: 'real', waterfall: [] },
  outletCompare:   { label: 'Perbandingan Custom Outlet', color: '#4ADE80', grounded: 'real', waterfall: [] },
  franchiseCompare:{ label: 'Perbandingan Custom (Franchise)', color: '#22D3C5', grounded: 'estimasi', waterfall: [] },
  cabangDiff:      { label: 'Validasi · Biasa vs Franchise', color: '#E066FF', grounded: 'real', waterfall: [] },
  home: { label: 'Beranda', color: '#FFC93C', grounded: 'kosong', waterfall: [] },
  opexTrend: { label: 'Opex · Tren % Pendapatan', color: '#FFC93C', grounded: 'real', waterfall: [] },
  opexPareto: { label: 'Opex · Pareto Biaya', color: '#FFC93C', grounded: 'real', waterfall: [] },
  opexOutlet: { label: 'Opex · Efisiensi per Outlet', color: '#FFC93C', grounded: 'real', waterfall: [] },
  opexAnomaly: { label: 'Opex · Deteksi Lonjakan', color: '#FFC93C', grounded: 'real', waterfall: [] },
  opexLeverage: { label: 'Opex · Operating Leverage', color: '#FFC93C', grounded: 'real', waterfall: [] },
  hppRingkasan: { label: 'HPP Bahan Baku · Ringkasan', color: '#F4A6D0', grounded: 'real', waterfall: [] },
  hppTren:      { label: 'HPP Bahan Baku · Tren % Pendapatan', color: '#F4A6D0', grounded: 'real', waterfall: [] },
  hppAnomaly:   { label: 'HPP Bahan Baku · Deteksi Lonjakan', color: '#F4A6D0', grounded: 'real', waterfall: [] },
};
// Stub UNIT_DATA utk grup "PNL Cabang Versi Franchise" -- key DINAMIS per
// outlet (franchiseOutlet_sudirman dst) + 1 utk perbandingan. Digenerate
// loop drpd ditulis manual 15x -- lebih sedikit sumber typo.
UNIT_DATA.franchiseOutletPerf = { label: 'Perbandingan PNL Cabang (Franchise)', color: '#22D3C5', grounded: 'estimasi', waterfall: [] };
OUTLET_KEYS.forEach(k => {
  UNIT_DATA['franchiseOutlet_'+k] = { label: UNIT_DATA[k].label + ' (Franchise)', color: '#22D3C5', grounded: 'estimasi', waterfall: [] };
});

// ---- helper: ambil array values dari 1 baris waterfall by name ----
function getLine(u, name){
  const row = u.waterfall.find(r => r.name === name);
  return row ? row.values : null;
}
// baris terakhir yg PUNYA nilai sendiri (bukan baris toggle/wrapper kosong
// spt "Item Tambahan..." yang sengaja null semua) -- dipakai KPI & chart
// supaya tidak nampilin '-' cuma krn baris fisik terakhir di array itu wrapper.
function finalProfitRow(u){
  for (let i=u.waterfall.length-1; i>=0; i--){
    const r = u.waterfall[i];
    if (r.values && r.values.some(v => v!==null)) return r;
  }
  return u.waterfall[u.waterfall.length-1];
}
// Store & Brand: RANTAI RUMUS PENUH sesuai 9 poin yang diminta user. PRINSIP:
// baris agregat (Pendapatan Bersih, HPP, Laba Kotor, Biaya Operasional, Laba
// Operasional, Laba Bersih) SELALU dihitung dari komponennya -- TIDAK PERNAH
// percaya subtotal langsung dari CSV, meskipun CSV punya angka sendiri utk
// baris itu. Ini sengaja, supaya bug di source (spt "EBIT=EBITDA ke-copy" dan
// "Laba Bersih sama di 5 bulan" yg sudah ditemukan sebelumnya) tidak lolos
// diam-diam ke dashboard. Kalau hasil hitungan beda dari subtotal CSV, itu
// dicatat sbg discrepancy (lihat console + note) -- bukan disembunyikan.
function rebuildStoreComputed(){
  const u = UNIT_DATA.store;
  if (!u || !u.waterfall) return;
  const get = (name) => { const r = u.waterfall.find(x=>x.name.replace(/<[^>]*>/g,'').trim().startsWith(name)); return r ? r.values : null; };
  const sumArr = (...arrs) => PERIODS.map((_,i) => {
    if (arrs.every(a => !a || a[i]==null)) return null;
    return arrs.reduce((s,a)=> s + (a && a[i]!=null ? a[i] : 0), 0);
  });
  const subArr = (a,b) => PERIODS.map((_,i) => {
    if (!a || a[i]==null || !b || b[i]==null) return null;
    return a[i] - b[i];
  });

  const pendapatan = get('Pendapatan');
  const diskon = get('Diskon');
  const pendapatanBersihRow = u.waterfall.find(r => r.name==='Pendapatan Bersih');
  if (pendapatanBersihRow && pendapatan && diskon) pendapatanBersihRow.values = sumArr(pendapatan, diskon); // diskon sudah negatif

  const hpp = get('HPP');
  const labaKotorRow = u.waterfall.find(r => r.name==='Laba Kotor');
  const pendapatanBersih = pendapatanBersihRow ? pendapatanBersihRow.values : null;
  if (labaKotorRow && pendapatanBersih && hpp) labaKotorRow.values = subArr(pendapatanBersih, hpp);

  const biayaOperasional = get('Biaya Operasional');
  const pendapatanLain = get('Pendapatan Lain-lain');
  // Laba Operasional = Laba Kotor - Biaya Operasional + Pendapatan Lain-lain
  // (formula direvisi sesuai instruksi eksplisit user 2026-07-05: Pendapatan
  // Lain-lain masuk di TAHAP INI, bukan di Laba Bersih spt versi sebelumnya)
  const labaOperasionalRow = u.waterfall.find(r => r.name==='Laba Operasional');
  const labaKotor = labaKotorRow ? labaKotorRow.values : null;
  if (labaOperasionalRow && labaKotor && biayaOperasional) {
    labaOperasionalRow.values = labaKotor.map((lk,i) => {
      if (lk===null || biayaOperasional[i]==null) return null;
      const pl = (pendapatanLain && pendapatanLain[i]!=null) ? pendapatanLain[i] : 0;
      return lk - biayaOperasional[i] + pl;
    });
  }

  const biayaPajak = get('Biaya Pajak');
  const biayaDepresiasi = get('Biaya Depresiasi');
  const costMgmt = get('Cost of Management');
  // Laba Bersih = Laba Operasional - Biaya Depresiasi - Biaya Pajak - Cost of
  // Management. Biaya Administrasi Bank TIDAK LAGI dikurangkan di sini --
  // sudah dinetokan ke dalam "Pendapatan Lain-lain" sesuai rumus baru user.
  const labaBersihRow = u.waterfall.find(r => r.computed && r.name==='Laba Bersih');
  const labaOperasional = labaOperasionalRow ? labaOperasionalRow.values : null;
  if (labaBersihRow && labaOperasional) {
    labaBersihRow.values = labaOperasional.map((lo,i) => {
      if (lo===null) return null;
      const d = (biayaDepresiasi && biayaDepresiasi[i]!=null) ? biayaDepresiasi[i] : 0;
      const p = (biayaPajak && biayaPajak[i]!=null) ? biayaPajak[i] : 0;
      const c = (costMgmt && costMgmt[i]!=null) ? costMgmt[i] : 0;
      return lo - d - p - c;
    });
  }
}

function rebuildManufakturComputed(){
  const u = UNIT_DATA.manufaktur;
  if (!u || !u.waterfall) return;
  const get = (name) => { const r = u.waterfall.find(x=>x.name.replace(/<[^>]*>/g,'').trim()===name); return r ? r.values : null; };
  const sumArr = (a,b) => PERIODS.map((_,i) => {
    if (a==null||a[i]==null||b==null||b[i]==null) return null;
    return a[i] + b[i];
  });
  const subArr = (a,b) => PERIODS.map((_,i) => {
    if (!a || a[i]==null || !b || b[i]==null) return null;
    return a[i] - b[i];
  });

  const pendapatan = get('Pendapatan');
  const pendapatanLain = get('Pendapatan Lain-lain');
  const pendapatanBersihRow = u.waterfall.find(r => r.name==='Pendapatan Bersih');
  if (pendapatanBersihRow && pendapatan) pendapatanBersihRow.values = sumArr(pendapatan, pendapatanLain || pendapatan.map(()=>0));

  const hppBahanBaku = get('HPP Bahan Baku');
  const biayaOverhead = get('Biaya Overhead Pabrik');
  const hppRow = u.waterfall.find(r => r.name==='Harga Pokok Produksi');
  if (hppRow) hppRow.values = sumArr(hppBahanBaku, biayaOverhead);

  const pendapatanBersih = pendapatanBersihRow ? pendapatanBersihRow.values : null;
  const hpp = hppRow ? hppRow.values : null;
  const labaKotorRow = u.waterfall.find(r => r.name==='Laba Kotor');
  if (labaKotorRow && pendapatanBersih && hpp) labaKotorRow.values = subArr(pendapatanBersih, hpp);

  const biayaOperasional = get('Biaya Operasional');
  const labaKotor = labaKotorRow ? labaKotorRow.values : null;
  const labaOperasionalRow = u.waterfall.find(r => r.name==='Laba Operasional');
  if (labaOperasionalRow && labaKotor && biayaOperasional) labaOperasionalRow.values = subArr(labaKotor, biayaOperasional);

  const biayaPenyusutan = get('Biaya Penyusutan');
  const biayaPajak = get('Biaya Pajak');
  const costMgmt = get('Cost of Management');
  const labaOperasional = labaOperasionalRow ? labaOperasionalRow.values : null;
  const labaBersihRow = u.waterfall.find(r => r.name==='Laba Bersih');
  if (labaBersihRow && labaOperasional) {
    labaBersihRow.values = labaOperasional.map((lo,i) => {
      if (lo===null) return null;
      const bp = (biayaPenyusutan && biayaPenyusutan[i]!=null) ? biayaPenyusutan[i] : 0;
      const pj = (biayaPajak && biayaPajak[i]!=null) ? biayaPajak[i] : 0;
      const cm = (costMgmt && costMgmt[i]!=null) ? costMgmt[i] : 0;
      return lo - bp - pj - cm;
    });
  }
}

function rebuildHOComputed(){
  const u = UNIT_DATA.ho;
  if (!u || !u.waterfall) return;
  const get = (name) => { const r = u.waterfall.find(x=>x.name.replace(/<[^>]*>/g,'').trim()===name); return r ? r.values : null; };
  const subArr = (a,b) => PERIODS.map((_,i) => {
    if (!a || a[i]==null || !b || b[i]==null) return null;
    return a[i] - b[i];
  });

  const pendapatan = get('Pendapatan');
  const hpp = get('Harga Pokok Penjualan');
  const labaKotorRow = u.waterfall.find(r => r.name==='Laba Kotor');
  if (labaKotorRow && pendapatan && hpp) labaKotorRow.values = subArr(pendapatan, hpp);

  const biayaOperasional = get('Biaya Operasional');
  const labaKotor = labaKotorRow ? labaKotorRow.values : null;
  const labaOperasionalRow = u.waterfall.find(r => r.name==='Laba Operasional');
  if (labaOperasionalRow && labaKotor && biayaOperasional) labaOperasionalRow.values = subArr(labaKotor, biayaOperasional);

  const biayaDepresiasi = get('Biaya Depresiasi');
  const bungaBank = get('Bunga Bank');
  const labaOperasional = labaOperasionalRow ? labaOperasionalRow.values : null;
  const labaBersihRow = u.waterfall.find(r => r.name==='Laba Bersih');
  if (labaBersihRow && labaOperasional) {
    labaBersihRow.values = labaOperasional.map((lo,i) => {
      if (lo===null) return null;
      const d = (biayaDepresiasi && biayaDepresiasi[i]!=null) ? biayaDepresiasi[i] : 0;
      const bb = (bungaBank && bungaBank[i]!=null) ? bungaBank[i] : 0;
      return lo - d + bb; // bungaBank sudah net (Pendapatan Bunga - Biaya Bunga)
    });
  }
}

function rebuildKonsolidasiComputed(){
  const u = UNIT_DATA.konsolidasi;
  if (!u || !u.waterfall) return;
  const get = (name) => { const r = u.waterfall.find(x=>x.name.replace(/<[^>]*>/g,'').trim()===name); return r ? r.values : null; };
  const sumArr = (a,b) => PERIODS.map((_,i) => {
    if (a==null||a[i]==null||b==null||b[i]==null) return null;
    return a[i] + b[i];
  });
  const subArr = (a,b) => PERIODS.map((_,i) => {
    if (!a || a[i]==null || !b || b[i]==null) return null;
    return a[i] - b[i];
  });

  const pendapatan = get('Pendapatan');
  const diskon = get('Diskon');
  const pendapatanBersihRow = u.waterfall.find(r => r.name==='Pendapatan Bersih');
  if (pendapatanBersihRow && pendapatan && diskon) pendapatanBersihRow.values = sumArr(pendapatan, diskon); // diskon sudah negatif

  const hpp = get('Harga Pokok Penjualan');
  const labaKotorRow = u.waterfall.find(r => r.name==='Laba Kotor');
  const pendapatanBersih = pendapatanBersihRow ? pendapatanBersihRow.values : null;
  if (labaKotorRow && pendapatanBersih && hpp) labaKotorRow.values = subArr(pendapatanBersih, hpp);

  const biayaOperasional = get('Biaya Operasional');
  const labaOperasionalRow = u.waterfall.find(r => r.name==='Laba Operasional');
  const labaKotor = labaKotorRow ? labaKotorRow.values : null;
  if (labaOperasionalRow && labaKotor && biayaOperasional) labaOperasionalRow.values = subArr(labaKotor, biayaOperasional);

  const biayaPenyusutan = get('Biaya Penyusutan'); // plain sum, POSITIF -> harus DIKURANGKAN
  const biayaPajak = get('Biaya Pajak'); // signRule -> hasilnya SUDAH negatif (net) -> harus DITAMBAHKAN
  const bunga = get('Bunga'); // signRule -> hasilnya SUDAH net (bisa +/-) -> harus DITAMBAHKAN
  const labaOperasional = labaOperasionalRow ? labaOperasionalRow.values : null;
  const labaBersihRow = u.waterfall.find(r => r.computed && r.name==='Laba Bersih');
  if (labaBersihRow && labaOperasional) {
    labaBersihRow.values = labaOperasional.map((lo,i) => {
      if (lo===null) return null;
      const bp = (biayaPenyusutan && biayaPenyusutan[i]!=null) ? biayaPenyusutan[i] : 0;
      const pj = (biayaPajak && biayaPajak[i]!=null) ? biayaPajak[i] : 0;
      const bg = (bunga && bunga[i]!=null) ? bunga[i] : 0;
      return lo - bp + pj + bg; // pj & bg SUDAH bertanda negatif dari signRule, jadi DITAMBAH bukan dikurang
    });
  }
}

// Formula GENERIK utk semua 14 outlet Store -- strukturnya identik (Pendapatan
// -> Diskon -> Pendapatan Bersih -> HPP -> Laba Kotor -> Biaya Operasional ->
// Laba Operasional -> Biaya Penyusutan/Pajak/CostMgmt -> Laba Bersih), cuma
// datanya beda per outlet. 1 fungsi dipakai berulang lewat OUTLET_KEYS.
function rebuildOutletComputed(unitKey){
  const u = UNIT_DATA[unitKey];
  if (!u || !u.waterfall) return;
  const get = (name) => { const r = u.waterfall.find(x=>x.name.replace(/<[^>]*>/g,'').trim()===name); return r ? r.values : null; };
  const sumArr = (a,b) => PERIODS.map((_,i) => {
    if (a==null||a[i]==null||b==null||b[i]==null) return null;
    return a[i] + b[i];
  });
  const subArr = (a,b) => PERIODS.map((_,i) => {
    if (!a || a[i]==null || !b || b[i]==null) return null;
    return a[i] - b[i];
  });

  const pendapatan = get('Pendapatan');
  const diskon = get('Diskon');
  const pendapatanBersihRow = u.waterfall.find(r => r.name==='Pendapatan Bersih');
  if (pendapatanBersihRow && pendapatan && diskon) pendapatanBersihRow.values = sumArr(pendapatan, diskon);

  const hpp = get('HPP');
  const labaKotorRow = u.waterfall.find(r => r.name==='Laba Kotor');
  const pendapatanBersih = pendapatanBersihRow ? pendapatanBersihRow.values : null;
  if (labaKotorRow && pendapatanBersih && hpp) labaKotorRow.values = subArr(pendapatanBersih, hpp);

  const biayaOperasional = get('Biaya Operasional');
  const labaOperasionalRow = u.waterfall.find(r => r.name==='Laba Operasional');
  const labaKotor = labaKotorRow ? labaKotorRow.values : null;
  if (labaOperasionalRow && labaKotor && biayaOperasional) labaOperasionalRow.values = subArr(labaKotor, biayaOperasional);

  const biayaPenyusutan = get('Biaya Penyusutan');
  const biayaPajak = get('Biaya Pajak');
  const costMgmt = get('Cost of Management');
  const labaOperasional = labaOperasionalRow ? labaOperasionalRow.values : null;
  const labaBersihRow = u.waterfall.find(r => r.computed && r.name==='Laba Bersih');
  if (labaBersihRow && labaOperasional) {
    labaBersihRow.values = labaOperasional.map((lo,i) => {
      if (lo===null) return null;
      const bp = (biayaPenyusutan && biayaPenyusutan[i]!=null) ? biayaPenyusutan[i] : 0;
      const pj = (biayaPajak && biayaPajak[i]!=null) ? biayaPajak[i] : 0;
      const cm = (costMgmt && costMgmt[i]!=null) ? costMgmt[i] : 0;
      return lo - bp - pj - cm;
    });
  }
}
function rebuildAllOutletsComputed(){
  OUTLET_KEYS.forEach(k => rebuildOutletComputed(k));
}

function rebuildOwnershipComputed(){
  const u = UNIT_DATA.ownership;
  if (!u || !u.waterfall) return;
  const get = (name) => { const r = u.waterfall.find(x=>x.name.replace(/<[^>]*>/g,'').trim()===name); return r ? r.values : null; };
  const sumArr = (a,b) => PERIODS.map((_,i) => {
    if (a==null||a[i]==null||b==null||b[i]==null) return null;
    return a[i] + b[i];
  });
  const subArr = (a,b) => PERIODS.map((_,i) => {
    if (!a || a[i]==null || !b || b[i]==null) return null;
    return a[i] - b[i];
  });

  const pendapatan = get('Pendapatan');
  const diskon = get('Diskon');
  const pendapatanBersihRow = u.waterfall.find(r => r.name==='Pendapatan Bersih');
  if (pendapatanBersihRow && pendapatan && diskon) pendapatanBersihRow.values = sumArr(pendapatan, diskon);

  const hpp = get('Harga Pokok Penjualan');
  const labaKotorRow = u.waterfall.find(r => r.name==='Laba Kotor');
  const pendapatanBersih = pendapatanBersihRow ? pendapatanBersihRow.values : null;
  if (labaKotorRow && pendapatanBersih && hpp) labaKotorRow.values = subArr(pendapatanBersih, hpp);

  const biayaOperasional = get('Biaya Operasional');
  const labaOperasionalRow = u.waterfall.find(r => r.name==='Laba Operasional');
  const labaKotor = labaKotorRow ? labaKotorRow.values : null;
  if (labaOperasionalRow && labaKotor && biayaOperasional) labaOperasionalRow.values = subArr(labaKotor, biayaOperasional);

  const biayaPenyusutan = get('Biaya Penyusutan');
  const biayaPajak = get('Biaya Pajak');
  const bunga = get('Bunga');
  const labaOperasional = labaOperasionalRow ? labaOperasionalRow.values : null;
  const labaBersihRow = u.waterfall.find(r => r.computed && r.name==='Laba Bersih');
  if (labaBersihRow && labaOperasional) {
    labaBersihRow.values = labaOperasional.map((lo,i) => {
      if (lo===null) return null;
      const bp = (biayaPenyusutan && biayaPenyusutan[i]!=null) ? biayaPenyusutan[i] : 0;
      const pj = (biayaPajak && biayaPajak[i]!=null) ? biayaPajak[i] : 0;
      const bg = (bunga && bunga[i]!=null) ? bunga[i] : 0;
      return lo - bp + pj + bg; // pj & bg SUDAH bertanda negatif dari signRule
    });
  }
}
rebuildStoreComputed();
rebuildManufakturComputed();
rebuildHOComputed();
rebuildOwnershipComputed();
rebuildAllOutletsComputed();
rebuildKonsolidasiComputed();

const ENTITY_ORDER = ['store','manufaktur','ho','ownership','konsolidasi','outletPerf','cabangRinci']; // TIDAK LAGI dipakai utk navigasi (sidebar SIDEBAR_GROUPS yg dipakai sekarang) -- ditinggal sbg referensi daftar unit. 'cabangRinci' (dropdown pilih outlet) kini tergantikan fungsinya oleh klik-langsung-outlet di sidebar; kodenya tetap ada tapi tidak lagi ada jalan navigasi ke situ.
const ANALYSIS_TABS = [
  {id:'preview', label:'Preview'},
  {id:'mom', label:'Month to Month'},
  {id:'yoy', label:'Year on Year'},
  {id:'ytd', label:'Year to Date'},
  {id:'qs', label:'Quartal, Semester & Tahunan'},
];

let state = { unit: 'home', analysis: 'preview' };
let chartInstance = null;

function fmtRp(v){
  if (v===null||v===undefined) return '-';
  const sign = v<0?'-':'';
  return sign+Math.round(Math.abs(v)).toLocaleString('id-ID');
}
function fmtPct(v){ return (v>=0?'+':'')+v.toFixed(1)+'%'; }
// gabungan Rp + % dari basis revenue, dipakai di sel tabel waterfall.
// keyClass (opsional) = class tambahan utk span %, dipakai kolom Key (Konsolidasi)
// utk mewarnai % aktual thd target (hijau/merah).
function fmtRpPct(v, basis, keyClass){
  if (v===null||v===undefined) return '-';
  const pct = basis ? (v/basis*100) : null;
  const cls = 'pctTag' + (keyClass ? ' '+keyClass : '');
  const pctStr = (pct!==null && isFinite(pct)) ? ` <span class="${cls}">${pct.toFixed(1)}%</span>` : '';
  return fmtRp(v) + pctStr;
}

// ===== KOLOM KEY (target %) -- KHUSUS tab Konsolidasi =====
// Target dari struktur P&L yg diberikan user (2026-08-07). "dir" menentukan
// arah baik: 'cost' = aktual makin KECIL (mendekati/di bawah target) makin
// baik (nilai di source memang bisa negatif spt Diskon, makanya dibandingkan
// pakai nilai absolut); 'revenue' = aktual makin BESAR makin baik;
// 'neutral' = baris basis (Pendapatan itu sendiri), tak diwarnai.
const KEY_TARGETS = {
  'Pendapatan': { pct:100.00, dir:'neutral' },
  'Diskon': { pct:16.00, dir:'cost' },
  'Diskon Offline': { pct:2.50, dir:'cost' },
  'Diskon Online': { pct:8.50, dir:'cost' },
  'Potongan/Komisi Aplikasi/Merchant': { pct:5.00, dir:'cost' },
  'Pendapatan Bersih': { pct:84.00, dir:'revenue' },
  'Harga Pokok Penjualan': { pct:48.10, dir:'cost' },
  'HPP Bahan Baku Utama': { pct:41.00, dir:'cost' },
  'HPP Konsinyasi Ownership': { pct:1.00, dir:'cost' },
  'HPP Konsinyasi Franchise': { pct:0.60, dir:'cost' },
  'Biaya Overhead Pabrik': { pct:5.50, dir:'cost' },
  'Laba Kotor': { pct:35.90, dir:'revenue' },
  'Biaya Operasional': { pct:16.50, dir:'cost' },
  'Laba Operasional': { pct:19.40, dir:'revenue' },
};
function keyPctClass(name, pct){
  const t = KEY_TARGETS[name];
  if (!t || t.dir==='neutral' || pct===null || pct===undefined || !isFinite(pct)) return '';
  const cmp = t.dir==='cost' ? Math.abs(pct) : pct;
  return (t.dir==='cost' ? cmp<=t.pct : cmp>=t.pct) ? 'pct-good' : 'pct-bad';
}
function keyCellHtml(name){
  const t = KEY_TARGETS[name];
  if (!t) return `<td class="mono key-col">–</td>`;
  return `<td class="mono key-col"><span class="keybadge">${t.pct.toFixed(2).replace('.',',')}%</span></td>`;
}

function renderTabs(){
  renderSidebar();

  const at = document.getElementById('analysisTabs');
  at.innerHTML = ANALYSIS_TABS.map(t=>{
    const active = state.analysis===t.id;
    const color = (UNIT_DATA[state.unit] && UNIT_DATA[state.unit].color) || '#4FC3F7';
    return `<button class="sub-btn ${active?'active':''}" style="${active?`border-bottom-color:${color}`:''}" onclick="setAnalysis('${t.id}')">${t.label}</button>`;
  }).join('');
}
function setUnit(k){ state.unit=k; render(); }

// ============ SIDEBAR NAVIGASI UTAMA ============
// Struktur menu kiri sesuai permintaan user: PNL Utama / PNL Unit / PNL
// Cabang / Opex / Bank. Item PNL Cabang diisi otomatis dari OUTLET_KEYS
// (urut abjad) supaya nambah outlet baru tak perlu ubah struktur ini.
const SIDEBAR_GROUPS = [
  { key:'financialAnalysis', label:'Financial Analysis', items:[
      { key:'faOverview', label:'Overview' },
      { key:'faTrend',    label:'Trend' },
      { key:'faExpense',  label:'Expense' },
      { key:'faOutlet',   label:'Outlet Performance' },
      { key:'faForecast', label:'Forecast' },
  ]},
  { key:'pnlUtama', label:'PNL Utama', items:[
      { key:'konsolidasi', label:'Konsolidasi' },
      { key:'ownership',   label:'Ownership' },
      { key:'splitOnline', label:'Split Online/Offline' },
  ]},
  { key:'pnlUnit', label:'PNL Unit', items:[
      { key:'store',      label:'Store & Brand' },
      { key:'manufaktur', label:'Manufaktur' },
      { key:'ho',         label:'Head Office' },
      { key:'franchiseUnit', label:'Franchise' },
  ]},
  // Digabung dari 2 menu terpisah (PNL Cabang + PNL Cabang Versi Franchise)
  // jadi 1 menu -- isinya panel berdampingan Biasa|Franchise per bakery,
  // toggle multi-select, spt request user (gantikan drill-down per-outlet lama).
  { key:'pnlCabang', label:'PNL Cabang', items:[
      { key:'cabangDiff', label:'Biasa vs Franchise' },
  ]},
  { key:'opex', label:'Opex', items:[
      { key:'opexTrend',   label:'Tren % Pendapatan' },
      { key:'opexPareto',  label:'Pareto Biaya' },
      { key:'opexOutlet',  label:'Efisiensi per Outlet' },
      { key:'opexAnomaly', label:'Deteksi Lonjakan' },
      { key:'opexLeverage',label:'Operating Leverage' },
  ]},
  // Fokus akun HPP Bahan Baku (raw material) -- HANYA level Manufaktur/
  // Konsolidasi krn bahan baku dibeli pabrik, bukan per-cabang. Datanya jg
  // 1 akun flat (blm ada breakdown per jenis bahan) -- jadi cuma 3 fitur yg
  // relevan (tak ada varian "per outlet"/"Pareto" spt Opex).
  { key:'hpp', label:'HPP Bahan Baku', items:[
      { key:'hppRingkasan', label:'Ringkasan' },
      { key:'hppTren',      label:'Tren % Pendapatan' },
      { key:'hppAnomaly',   label:'Deteksi Lonjakan' },
  ]},
  { key:'kebocoran', label:'Kebocoran Pendapatan', items:[
      { key:'leakRingkas', label:'Ringkasan & Tren' },
      { key:'leakJenis',   label:'Rincian per Jenis' },
      { key:'leakOutlet',  label:'Peringkat per Outlet' },
  ]},
  { key:'grafik', label:'Grafik', items:[
      { key:'grafikCustom', label:'Grafik Custom' },
  ]},
  { key:'validasi', label:'Validasi Data', items:[
      { key:'rekonsiliasi', label:'Rekonsiliasi Otomatis' },
  ]},
  { key:'bank', label:'Bank', items:[
      { key:'bankPlaceholder', label:'(segera hadir)' },
  ]},
];
// grup mana yg sedang terbuka (bisa lebih dari 1) -- default: grup berisi unit aktif
let sidebarOpenGroups = new Set(['financialAnalysis', 'pnlUtama']);
let sidebarCollapsed = false;
function toggleSidebarCollapse(){
  sidebarCollapsed = !sidebarCollapsed;
  const el = document.getElementById('sidebar');
  const btn = document.getElementById('sbToggleBtn');
  if (el) el.classList.toggle('collapsed', sidebarCollapsed);
  if (btn) btn.textContent = sidebarCollapsed ? '▶' : '◀';
}

function findGroupForUnit(unitKey){
  for (const g of SIDEBAR_GROUPS){
    if (g.items.some(it => it.key === unitKey)) return g.key;
  }
  return null;
}
function toggleSidebarGroup(gKey){
  if (sidebarOpenGroups.has(gKey)) sidebarOpenGroups.delete(gKey);
  else sidebarOpenGroups.add(gKey);
  renderSidebar();
}
function navigateTo(unitKey){
  const g = findGroupForUnit(unitKey);
  if (g) sidebarOpenGroups.add(g);
  setUnit(unitKey); // render() dipanggil di dalam setUnit, termasuk renderSidebar()
}
function renderSidebar(){
  const el = document.getElementById('sidebarInner');
  if (!el) return;
  // ikon SVG monoline kecil, warna beda per grup -- lebih elegan drpd glyph
  // unicode datar, dan warnanya membantu scan cepat (bukan cuma dekorasi)
  const GROUP_ICON = {
    financialAnalysis: { color:'#4ADE80', svg:`<path d="M3 3v18h18"/><path d="M7 16l3-4 3 2 4-6"/><circle cx="17" cy="8" r="1.4"/>` }, // grafik naik -- financial intelligence
    pnlUtama: { color:'#FFC93C', svg:`<path d="M3 8l9-5 9 5-9 5-9-5z"/><path d="M3 12l9 5 9-5"/><path d="M3 16l9 5 9-5"/>` }, // layers -- ringkasan utama
    pnlUnit:  { color:'#A78BFA', svg:`<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>` }, // grid -- unit terpisah
    pnlCabang:{ color:'#4ADE80', svg:`<path d="M12 21s-7-6.5-7-11.5A7 7 0 0 1 19 9.5C19 14.5 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.3"/>` }, // pin -- lokasi cabang
    opex:     { color:'#FB7185', svg:`<path d="M12 2s-5 5.5-5 10a5 5 0 0 0 10 0c0-1.5-.7-2.8-1.5-4 .1 1.5-.6 2.5-1.6 2.5.6-3-1-6.5-1.9-8.5z"/>` }, // api -- biaya/burn
    hpp:      { color:'#F4A6D0', svg:`<path d="M21 8l-9-5-9 5 9 5 9-5z"/><path d="M3 8v8l9 5 9-5V8"/><path d="M12 13v8"/>` }, // kotak/material -- bahan baku
    kebocoran:{ color:'#F97316', svg:`<path d="M12 3s5 6 5 10a5 5 0 0 1-10 0c0-1.2.4-2.4 1-3.5"/><path d="M4 20l16-16"/>` }, // tetesan tercoret -- kebocoran pendapatan
    grafik:   { color:'#E066FF', svg:`<path d="M3 3v18h18"/><path d="M7 15l4-5 3 3 5-7"/>` }, // garis tren -- grafik custom
    validasi: { color:'#38BDF8', svg:`<path d="M9 12l2 2 4-4"/><path d="M12 3l7 3v6c0 4-3 7.5-7 9-4-1.5-7-5-7-9V6z"/>` }, // perisai centang -- validasi data
    bank:     { color:'#4FC3F7', svg:`<path d="M3 10l9-6 9 6"/><path d="M5 10v9M9 10v9M15 10v9M19 10v9"/><path d="M3 21h18"/>` }, // bangunan bank klasik
  };
  const anomalyCount = countOpexAnomalies().count;
  const hppAnomalyCount = countHppAnomalies().count;
  let html = `<div class="sidebar-logo">PT Inovasi Sukses Persada</div>`;
  SIDEBAR_GROUPS.forEach(g => {
    const items = g.items;
    const isOpen = sidebarOpenGroups.has(g.key);
    const badge = (g.key==='opex' && anomalyCount>0) ? `<span style="background:#FB7185;color:#1F0E12;font-size:9.5px;font-weight:800;border-radius:9px;padding:1px 7px;margin-left:6px;">${anomalyCount}</span>`
                : (g.key==='hpp' && hppAnomalyCount>0) ? `<span style="background:#FB7185;color:#1F0E12;font-size:9.5px;font-weight:800;border-radius:9px;padding:1px 7px;margin-left:6px;">${hppAnomalyCount}</span>` : '';
    const icon = GROUP_ICON[g.key];
    const iconHtml = icon ? `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="${icon.color}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="margin-right:9px;vertical-align:-3px;flex-shrink:0;">${icon.svg}</svg>` : '';
    html += `<div class="sb-group">
      <div class="sb-group-head ${isOpen?'open':''}" onclick="toggleSidebarGroup('${g.key}')">
        <span style="display:flex;align-items:center;">${iconHtml}${g.label}${badge}</span><span class="arrow">▶</span>
      </div>
      <div class="sb-items ${isOpen?'open':''}">
        ${items.length ? items.map(it => `<div class="sb-item ${state.unit===it.key?'active':''}" onclick="navigateTo('${it.key}')">${it.label}</div>`).join('')
                       : `<div class="sb-empty-note">Belum ada data</div>`}
      </div>
    </div>`;
  });
  el.innerHTML = html;
}
function setAnalysis(a){ state.analysis=a; render(); }

function renderBanner(){
  const u = UNIT_DATA[state.unit];
  const area = document.getElementById('bannerArea');
  area.innerHTML = ''; // semua entity sekarang grounded:'real' -- banner peringatan lama (utk grounded:'partial'/'real-reported'/'no-elim') sudah tak relevan & dihapus, itu sisa versi awal proyek yg datanya sudah diganti data real
  // Banner "YoY dummy" DIHAPUS: renderYoY sekarang pakai data ASLI (bandingkan
  // bulan yg sama antar tahun), dan bulan tanpa pembanding ditandai jujur di
  // dalam tabelnya sendiri -- tak perlu banner global lagi.
}

function renderKPIs(u){
  const last = 4;
  // pilih beberapa baris kunci utk kartu ringkas: Pendapatan, Laba Kotor (kalau ada), baris TERAKHIR (final profit line)
  const rev = u.waterfall[0];
  const lk = u.waterfall.find(r => r.name==='Laba Kotor');
  const finalLine = finalProfitRow(u);
  const items = [rev, lk, finalLine].filter(Boolean);
  return `<div class="kpis">` + items.map((row)=>{
    const v = row.values[last], prev = row.values[last-1];
    const delta = prev? ((v-prev)/Math.abs(prev))*100 : null;
    const up = delta!==null && delta>=0;
    return `<div class="kpi"><div class="bar" style="background:${u.color}"></div>
      <div class="lbl">${row.name}</div><div class="val mono">${fmtRp(v)}</div>
      ${delta!==null?`<div class="delta ${up?'pos-delta':'neg-delta'}">${up?'▲':'▼'} ${Math.abs(delta).toFixed(1)}%</div>`:''}
    </div>`;
  }).join('') + `</div>`;
}

function renderChart(u){
  return `<div class="chart-box"><canvas id="trendChart"></canvas></div>`;
}
function drawChart(u){
  const ctx = document.getElementById('trendChart');
  if (!ctx) return;
  if (typeof Chart === 'undefined') {
    console.error('Chart.js gagal dimuat (kemungkinan diblokir jaringan/firewall/ad-blocker) -- grafik dilewati, tapi tabel akun tetap jalan.');
    ctx.parentElement.innerHTML = '<div style="padding:20px;color:#9B93C4;font-size:12px;">Grafik tidak bisa ditampilkan -- Chart.js gagal dimuat dari CDN (cek koneksi/firewall). Tabel di bawah tetap berfungsi.</div>';
    return;
  }
  try {
    if (chartInstance) chartInstance.destroy();
    forceCanvasSize(ctx);
    const rev = u.waterfall[0].values;
    const lk = (u.waterfall.find(r=>r.name==='Laba Kotor')||{}).values;
    const opexRow = u.waterfall.find(r=>['OPEX','Biaya Operasi','Biaya Operasional'].includes(r.name));
    const finalRow = finalProfitRow(u);
    const finalLine = finalRow.values;
    const datasets = [{ label:'Pendapatan', data:rev, borderColor:u.color, backgroundColor:u.color+'55', fill:true, tension:.3 }];
    if (lk) datasets.push({ label:'Laba Kotor', data:lk, borderColor:'#FFC93C', fill:false, tension:.3 });
    if (opexRow) datasets.push({ label:opexRow.name, data:opexRow.values, borderColor:'#FB7185', fill:false, tension:.3 });
    datasets.push({ label:finalRow.name, data:finalLine, borderColor:'#C9C3E8', borderDash:[4,3], fill:false, tension:.3 });
    chartInstance = new Chart(ctx, {
      type: 'line',
      data: { labels: currentLabels(), datasets },
      options: { responsive:false, maintainAspectRatio:false,
        plugins:{ legend:{ labels:{ color:'#C9C3E8', font:{size:11} } },
          tooltip:{ callbacks:{ label: (c)=> c.dataset.label+': '+fmtRp(c.parsed.y) } } },
        scales:{ x:{ type:'category', ticks:{color:'#9B93C4'}, grid:{color:'#2A2650'} },
          y:{ type:'linear', ticks:{color:'#9B93C4', callback:(v)=>(v/1e9).toFixed(1)+'M'}, grid:{color:'#2A2650'} } }
      }
    });
  } catch (e) {
    console.error('drawChart error (grafik dilewati, tabel tetap jalan):', e);
    if (ctx && ctx.parentElement) ctx.parentElement.innerHTML = `<div style="padding:20px;color:#FB7185;font-size:12px;">⚠️ Grafik gagal digambar: ${e.message}<br><span style="color:#726C9C;font-size:10.5px;">Tabel di bawah tetap berfungsi. Screenshot pesan ini & kirim ke saya.</span></div>`;
  }
}

function renderTree(u){
  const labels = currentLabels();
  const vIdx = getVisibleIdx();
  const ths = vIdx.map(i => `<th style="color:${MONTH_COLORS[i % MONTH_COLORS.length]};border-bottom-color:${MONTH_COLORS[i % MONTH_COLORS.length]}">${labels[i]}</th>`).join('');
  const showKey = state.unit === 'konsolidasi';
  const keyTh = showKey ? `<th class="key-col">Key</th>` : '';
  const note = showKey ? `<div class="key-note">🔑 <b>Kolom Key</b> = target % thd Pendapatan (struktur P&L yg Anda tetapkan). Angka % pada tiap sel bulan &amp; Total ikut diwarnai: <span style="color:var(--good);font-weight:700">hijau</span> = sesuai/lebih baik dari target, <span style="color:var(--bad);font-weight:700">merah</span> = meleset dari target. Klik baris Diskon / Harga Pokok Penjualan utk buka rincian per komponen (masing-masing juga punya Key sendiri).</div>` : '';
  return `${note}<div class="tbl-wrap"><table><thead><tr><th>Akun</th>${ths}<th class="total-col">Total</th>${keyTh}</tr></thead><tbody id="treeBody"></tbody></table></div>`;
}
function fillTree(u){
  const body = document.getElementById('treeBody');
  if (!body) return;
  const revBasis = u.waterfall[0].values; // basis % = Pendapatan per bulan
  const showKey = state.unit === 'konsolidasi';
  body.innerHTML = u.waterfall.map((r,i) => renderNode(r.name, r.values, r.children, 0, `${i}`, [], revBasis, r.highlight, r.noNegColor, showKey)).join('');
}

// renderNode menghasilkan STRING <tr> SEJAJAR (tidak nested <table> --
// itu merusak alignment kolom). Kedalaman ditandai via padding-left dan
// class "group-<parentPath>" utk show/hide. revBasis = array Pendapatan
// per bulan, dipakai hitung % di tiap sel (kecuali baris Pendapatan itu sendiri).
let visibleIdx = null; // index bulan yg TAMPIL, SELALU dalam 1 tahun (selectedYear) -- tidak pernah lagi berarti "semua tahun sekaligus", itu sumber bug pencampuran 2025+2026 (HPP 4588% dsb)
let selectedYear = null; // tahun aktif; null = belum diinisialisasi, akan diisi otomatis ke tahun TERBARU saat render pertama
function getVisibleIdx(){
  ensureYearSelected();
  // JARING PENGAMAN: live-fetch bisa merombak ulang array PERIODS (nambah
  // periode baru) SAAT visibleIdx masih menyimpan angka index dari PERIODS
  // versi LAMA. Kalau ada index yg sudah di luar batas (>= PERIODS.length),
  // itu bisa bikin PERIODS[i] balik undefined & downstream code crash diam2
  // (gejala: secTitle/secEyebrow ke-update tapi #content tertinggal versi
  // lama). Validasi ulang di sini, buang index yg sudah tak valid.
  if (visibleIdx && visibleIdx.length) {
    const valid = visibleIdx.filter(i => i>=0 && i<PERIODS.length && periodMeta(PERIODS[i]).year===selectedYear);
    if (valid.length) return valid;
    // semua index lama sudah tak valid utk tahun ini -> reset wajar
    visibleIdx = monthsInYear(selectedYear);
  }
  return monthsInYear(selectedYear);
}
function availableYearsAll(){ return [...new Set(PERIODS.map(p=>periodMeta(p).year))].sort(); }
function monthsInYear(y){ return PERIODS.map((p,i)=>({y:periodMeta(p).year,i})).filter(o=>o.y===y).map(o=>o.i); }
function ensureYearSelected(){
  const years = availableYearsAll();
  if (!years.length) return;
  if (selectedYear===null || !years.includes(selectedYear)) {
    selectedYear = years[years.length-1]; // default: tahun PALING BARU yg ada datanya
    visibleIdx = latestMonthOfYear(selectedYear); // default 1 BULAN TERAKHIR saja (BUKAN semua) -- lihat catatan di setSelectedYear
  }
}
function latestMonthOfYear(y){
  const m = monthsInYear(y);
  return m.length ? [m[m.length-1]] : [];
}
function setSelectedYear(y){
  selectedYear = y;
  // Default ganti tahun = 1 BULAN TERAKHIR tahun itu SAJA, bukan semua bulan.
  // Alasan: kalau default-nya "semua bulan aktif", klik 1x pada bulan yg
  // MEMANG SUDAH aktif justru MEMATIKANNYA (toggle off) -- user yg berniat
  // "isolasi ke bulan ini saja" malah dapat kebalikannya (bulan itu hilang,
  // sisanya tetap aktif). Lihat histori bug 2026-07-26: user klik "Jun"
  // mengira mengisolasi, tapi krn Jun sudah aktif dari default, Jun justru
  // OFF dan Jan-Mei tertinggal aktif -> total 5 bulan bukan 1 bulan.
  visibleIdx = latestMonthOfYear(y);
  render();
}
const MONTH_COLORS = ['#4FC3F7','#FFC93C','#B4E61D','#22D3C5','#A78BFA','#F4A6D0','#FF9F43','#54A0FF','#A0E86A','#E066FF','#FFD93D','#6BCB77'];

function renderNode(name, vals, children, depth, path, ancestorPaths, revBasis, highlight, noNegColor, showKey){
  const hasChildren = children && Object.keys(children).length > 0;
  const rowClass = depth===0 ? 'l0' : 'child-row';
  const indent = 16 + depth*24;
  const hiddenClasses = ancestorPaths.map(p => `group-${p}`).join(' ');
  const isHidden = ancestorPaths.length > 0;
  const styleAttr = hasChildren
    ? `style="cursor:pointer;${isHidden?'display:none':''}"`
    : (isHidden ? `style="display:none"` : '');
  const vIdx = getVisibleIdx();
  const cells = vIdx.map(i => {
    const v = vals[i];
    if (v===null || v===undefined) return '<td class="mono">-</td>';
    const basis = revBasis ? revBasis[i] : null;
    const negClass = (v<0 && !noNegColor) ? 'neg' : '';
    const pctVal = basis ? (v/basis*100) : null;
    const kCls = showKey ? keyPctClass(name, pctVal) : '';
    return `<td class="mono ${negClass}">${fmtRpPct(v, basis, kCls)}</td>`;
  }).join('');
  const allNull = vIdx.every(i => vals[i]===null || vals[i]===undefined);
  // Total SEKARANG cuma menjumlahkan bulan yg sedang tampil (visibleIdx),
  // BUKAN selalu semua PERIODS -- sesuai koreksi eksplisit user (2026-07-07):
  // "total menyesuaikan jumlah bulan yg dipilih". Kalkulasi internal/live-fetch
  // tetap pakai PERIODS penuh, ini cuma soal apa yg DITAMPILKAN sbg Total.
  const total = allNull ? null : vIdx.reduce((s,i)=> s + (vals[i]==null?0:vals[i]), 0);
  const totalBasis = revBasis ? vIdx.reduce((s,i)=> s + (revBasis[i]==null?0:revBasis[i]), 0) : null;
  const totalNegClass = (total!==null && total<0 && !noNegColor) ? 'neg' : '';
  const totalPctVal = (totalBasis && total!==null) ? (total/totalBasis*100) : null;
  const totalKCls = showKey ? keyPctClass(name, totalPctVal) : '';
  const totalCell = `<td class="mono total-col ${totalNegClass}">${fmtRpPct(total, totalBasis, totalKCls)}</td>`;
  const keyCell = showKey ? keyCellHtml(name) : '';
  const toggleClass = (hasChildren && allNull) ? 'toggle-row' : '';
  const highlightClass = highlight ? 'row-highlight' : '';
  let html = `<tr class="${rowClass} ${toggleClass} ${highlightClass} ${hiddenClasses}" ${hasChildren?`data-path="${path}" onclick="toggleRow('${path}')"`:''} ${styleAttr}>
    <td style="padding-left:${indent}px">${hasChildren?`<span class="chev" id="chev-${path}">▶</span> `:''}${name}</td>${cells}${totalCell}${keyCell}</tr>`;
  if (hasChildren) {
    Object.entries(children).forEach(([childName, childVal], ci) => {
      const childPath = `${path}-${ci}`;
      const childAncestors = [...ancestorPaths, path];
      if (Array.isArray(childVal)) {
        html += renderNode(childName, childVal, null, depth+1, childPath, childAncestors, revBasis, false, noNegColor, showKey);
      } else {
        html += renderNode(childName, childVal.vals, childVal.children, depth+1, childPath, childAncestors, revBasis, false, noNegColor, showKey);
      }
    });
  }
  return html;
}

function toggleRow(path){
  const chev = document.getElementById('chev-'+path);
  const directChildren = document.querySelectorAll('.group-'+path);
  const open = chev.style.transform === 'rotate(90deg)';
  chev.style.transform = open? '' : 'rotate(90deg)';
  directChildren.forEach(r => { r.style.display = open ? 'none' : 'table-row'; });
  if (open) {
    document.querySelectorAll(`tr[data-path^="${path}-"]`).forEach(r => {
      r.style.display = 'none';
      const c = r.querySelector('.chev');
      if (c) c.style.transform = '';
    });
    document.querySelectorAll(`[class*="group-${path}-"]`).forEach(r => r.style.display = 'none');
  }
}

function renderMoM(u){
  const labels = currentLabels();
  const cols = labels.slice(1).map((m,i) => `${m} vs ${labels[i]}`);
  let html = `<div class="tbl-wrap"><table><thead><tr><th>Akun</th>${cols.map(c=>`<th>${c}</th>`).join('')}</tr></thead><tbody>`;
  u.waterfall.forEach(row=>{
    const arr = row.values;
    const deltas = arr.slice(1).map((v,i)=> (arr[i]===null||v===null) ? null : (arr[i]===0? 0 : ((v-arr[i])/Math.abs(arr[i]))*100) );
    html += `<tr class="l0"><td>${row.name}</td>${deltas.map(d=> d===null?'<td class="mono">-</td>':`<td class="mono ${d>=0?'pos-delta':'neg-delta'}">${fmtPct(d)}</td>`).join('')}</tr>`;
  });
  html += `</tbody></table></div>`;
  return html;
}

function renderYoY(u){
  // DIPERBAIKI: sebelumnya pakai angka KARANGAN (growth dummy 0.10+(i%5)*0.03).
  // Sekarang membandingkan bulan yg SAMA di tahun berbeda dari data asli.
  // Bulan yg tak punya pasangan tahun sebelumnya ditandai "-" scr jujur,
  // BUKAN diisi angka tebakan.
  const pairs = []; // {idxNow, idxPrev, label}
  PERIODS.forEach((p, i) => {
    const [y, m] = p.split('-');
    const prevKey = (parseInt(y) - 1) + '-' + m;
    const j = PERIODS.indexOf(prevKey);
    if (j !== -1) pairs.push({ idxNow:i, idxPrev:j, label:`${periodLabel(p,true)} vs ${periodLabel(prevKey,true)}` });
  });

  if (!pairs.length) {
    const years = [...new Set(PERIODS.map(p=>p.split('-')[0]))].sort();
    return `<div style="background:#2A1B1B;border:1px solid #FB7185;border-radius:12px;padding:18px 20px;color:#FB7185;font-size:13px;">
      <b>Perbandingan YoY belum bisa ditampilkan.</b><br>
      <span style="color:#C9C3E8;font-size:12px;">Tidak ada satu pun bulan yg punya pasangan di tahun sebelumnya. Tahun yg tersedia di data: ${years.join(', ')}.
      YoY butuh bulan yg sama di 2 tahun berbeda (mis. Jun '26 vs Jun '25).</span>
    </div>`;
  }

  const missing = PERIODS.length - pairs.length;

  // PENJAGA KUALITAS DATA: kalau angka tahun sebelumnya jauh lebih kecil dari
  // level wajar, itu tanda data tahun lalu BELUM LENGKAP -- bukan pertumbuhan
  // fantastis. Tanpa penjaga ini, tabel bisa menampilkan "+71.888%" ke direksi
  // dan itu menyesatkan total.
  const ABSURD_PCT = 500; // di atas ini hampir pasti masalah kelengkapan data, bukan pertumbuhan riil
  const revRow = u.waterfall.find(r=>r.name==='Pendapatan') || u.waterfall[0];
  let absurdCount = 0, comparableCount = 0;
  pairs.forEach(pr => {
    const now = revRow.values[pr.idxNow], prev = revRow.values[pr.idxPrev];
    if (now==null || prev==null || prev===0) return;
    comparableCount++;
    if (Math.abs((now-prev)/Math.abs(prev)*100) > ABSURD_PCT) absurdCount++;
  });
  const dataSuspect = comparableCount>0 && absurdCount/comparableCount >= 0.5;

  let note = '';
  if (dataSuspect) {
    note += `<div style="background:#2A1B1B;border:1px solid #FB7185;border-radius:10px;padding:12px 16px;margin-bottom:12px;font-size:12px;color:#FB7185;">
      <b>⚠ Data tahun pembanding tampaknya BELUM LENGKAP -- angka % di bawah jangan dipakai untuk pengambilan keputusan.</b><br>
      <span style="color:#C9C3E8;font-size:11.5px;">Pendapatan tahun sebelumnya tercatat jauh di bawah level wajar (${absurdCount} dari ${comparableCount} bulan menunjukkan pertumbuhan &gt;${ABSURD_PCT}%, yg secara bisnis tak masuk akal). Ini ciri khas data historis yg baru terisi sebagian, bukan pertumbuhan sesungguhnya. Lengkapi dulu data tahun sebelumnya di spreadsheet.</span>
    </div>`;
  }
  if (missing > 0) {
    note += `<div style="background:#1C1840;border:1px solid #2A2650;border-radius:10px;padding:10px 14px;margin-bottom:12px;font-size:11.5px;color:#9B93C4;">
        Hanya <b>${pairs.length} dari ${PERIODS.length}</b> bulan yg punya pembanding di tahun sebelumnya. ${missing} bulan sisanya sengaja tidak ditampilkan (tak ada data pembandingnya) -- bukan disembunyikan, memang belum ada datanya.
      </div>`;
  }

  let html = note + `<div class="tbl-wrap"><table><thead><tr><th>Akun</th>${pairs.map(p=>`<th>${p.label}</th>`).join('')}</tr></thead><tbody>`;
  u.waterfall.forEach(row=>{
    const cells = pairs.map(pr => {
      const now = row.values[pr.idxNow];
      const prev = row.values[pr.idxPrev];
      if (now===null || prev===null || prev===0) return '<td class="mono" style="color:#4A4568;">-</td>';
      const d = ((now - prev) / Math.abs(prev)) * 100;
      // % di luar nalar ditandai, bukan ditampilkan polos seolah angka sahih
      if (Math.abs(d) > ABSURD_PCT) {
        return `<td class="mono" style="color:#FB7185;" title="Pembanding tahun lalu = ${fmtRp(prev)}, kemungkinan besar data belum lengkap">⚠ ${fmtPct(d)}</td>`;
      }
      return `<td class="mono ${d>=0?'pos-delta':'neg-delta'}">${fmtPct(d)}</td>`;
    }).join('');
    html += `<tr class="l0"><td>${row.name}</td>${cells}</tr>`;
  });
  html += `</tbody></table></div>`;
  return html;
}

function renderYTD(u){
  const labels = currentLabels();
  const rangeLabel = PERIODS.length>1 ? `${labels[0]}-${labels[labels.length-1]}` : labels[0];
  const years = new Set(PERIODS.map(p=>p.split('-')[0]));
  const yearSuffix = years.size===1 ? ` ${[...years][0]}` : '';
  let html = `<div class="tbl-wrap"><table><thead><tr><th>Akun</th><th>${rangeLabel}${yearSuffix}</th></tr></thead><tbody>`;
  u.waterfall.forEach(row=>{
    const sum = row.values.reduce((a,v)=>a+(v===null?0:v),0);
    html += `<tr class="l0"><td>${row.name}</td><td class="mono ${sum<0?'neg':''}">${fmtRp(sum)}</td></tr>`;
  });
  html += `</tbody></table></div>`;
  return html;
}

function periodMeta(p){
  const [y,m] = p.split('-').map(Number);
  return { year:y, month:m, quarter:Math.ceil(m/3), semester: m<=6?1:2 };
}
// kelompokkan PERIODS yg ada SEKARANG (berapapun jumlahnya) ke bucket
// quartal/semester/tahun sesuai kalender asli -- bukan index array tetap.
function groupPeriodsBy(kind){
  const metas = PERIODS.map((p,i)=>({...periodMeta(p), idx:i}));
  const map = {};
  metas.forEach(m=>{
    let key, expected;
    if (kind==='quarter'){ key = `${m.year}-Q${m.quarter}`; expected = 3; }
    else if (kind==='semester'){ key = `${m.year}-S${m.semester}`; expected = 6; }
    else { key = `${m.year}`; expected = 12; }
    if (!map[key]) map[key] = { indices: [], expected };
    map[key].indices.push(m.idx);
  });
  return Object.entries(map).sort(([a],[b])=> a<b?-1:1).map(([key,v])=>({
    key, indices: v.indices, complete: v.indices.length >= v.expected
  }));
}
function bucketLabel(kind, key, indices, complete){
  const labels = currentLabels();
  const firstLast = indices.length>1 ? `${labels[indices[0]]}-${labels[indices[indices.length-1]]}` : labels[indices[0]];
  let base;
  if (kind==='quarter') base = `${key.replace('-Q',' Q')} (${firstLast})`;
  else if (kind==='semester') base = `${key.replace('-S',' Semester ')} (${firstLast})`;
  else base = `Tahun ${key} (${firstLast})`;
  return complete ? base : `${base}, parsial ${indices.length}/${kind==='quarter'?3:kind==='semester'?6:12} bulan`;
}
let qsMode = 'quarter'; // 'quarter' | 'semester' | 'year'
let qsYear = null; // null = semua tahun; angka = fokus 1 tahun
function setQsMode(m){ qsMode = m; render(); }
function setQsYear(y){ qsYear = y; render(); }
function availableYears(){
  return [...new Set(PERIODS.map(p => periodMeta(p).year))].sort();
}

function renderQS(u){
  const years = availableYears();
  // kalau tahun terpilih tidak ada di data (mis. berubah), reset ke null
  if (qsYear !== null && !years.includes(qsYear)) qsYear = null;

  let buckets = groupPeriodsBy(qsMode).map(b=>({kind:qsMode, ...b}));
  // filter ke tahun terpilih (kalau ada). key bucket diawali "YYYY"
  if (qsYear !== null) buckets = buckets.filter(b => parseInt(b.key.slice(0,4)) === qsYear);
  const rev = getLineVals(u,'Pendapatan');

  // toggle mode (Kuartal/Semester/Tahunan)
  const modeBtn = (id,label) => `<button onclick="setQsMode('${id}')" style="cursor:pointer;padding:8px 18px;border-radius:9px;font-family:'Space Grotesk',sans-serif;font-size:13px;font-weight:700;border:1px solid ${qsMode===id?'#FFC93C':'#2A2650'};color:${qsMode===id?'#FFC93C':'#9B93C4'};background:${qsMode===id?'#241F4D':'#171433'};">${label}</button>`;
  const modeToggle = `<div style="display:flex;gap:8px;margin-bottom:12px;">${modeBtn('quarter','Kuartal')}${modeBtn('semester','Semester')}${modeBtn('year','Tahunan')}</div>`;

  // toggle TAHUN -- muncul otomatis sesuai tahun yg ada di data. Disembunyikan
  // di mode 'year' (tabel Tahunan memang lintas-tahun, jadi filter tahun tak relevan).
  let yearToggle = '';
  if (qsMode !== 'year' && years.length > 1) {
    const yBtn = (y,label) => `<button onclick="setQsYear(${y===null?'null':y})" style="cursor:pointer;padding:6px 14px;border-radius:8px;font-family:'Space Grotesk',sans-serif;font-size:12px;font-weight:700;border:1px solid ${qsYear===y?'#4FC3F7':'#2A2650'};color:${qsYear===y?'#4FC3F7':'#726C9C'};background:${qsYear===y?'#1C2A44':'#171433'};">${label}</button>`;
    yearToggle = `<div style="display:flex;gap:7px;margin-bottom:18px;align-items:center;"><span style="font-size:11px;color:#726C9C;font-weight:600;margin-right:2px;">TAHUN:</span>${yBtn(null,'Semua')}${years.map(y=>yBtn(y,String(y))).join('')}</div>`;
  }

  const headers = buckets.map(b => {
    const lbl = bucketLabel(b.kind, b.key, b.indices, b.complete);
    const warn = b.complete ? '' : ` <span style="color:#FFC93C;">⚠</span>`;
    return `<th>${lbl}${warn}</th>`;
  }).join('');

  const revTot = buckets.map(b => rev ? b.indices.reduce((s,i)=>s+(rev[i]||0),0) : 0);

  let rows = '';
  u.waterfall.forEach(row=>{
    const cells = buckets.map((b,bi) => {
      const sum = b.indices.reduce((s,i)=> s + (row.values[i]===null?0:row.values[i]), 0);
      const empty = b.indices.every(i => row.values[i]===null);
      if (empty) return `<td class="mono" style="color:#4A4568;">-</td>`;
      const pct = revTot[bi] ? `<span class="pctTag">${(sum/revTot[bi]*100).toFixed(1)}%</span>` : '';
      return `<td class="mono ${sum<0?'neg':''}">${fmtRp(sum)} ${pct}</td>`;
    }).join('');
    const hl = (row.name==='Pendapatan'||row.name==='Laba Operasional') ? ' row-highlight' : '';
    rows += `<tr class="l0${hl}"><td>${row.name}</td>${cells}</tr>`;
  });

  const emptyMsg = buckets.length===0
    ? `<div style="padding:24px;color:#726C9C;font-size:13px;">Tidak ada data untuk tahun ${qsYear}.</div>` : '';
  const partialNote = buckets.some(b=>!b.complete)
    ? `<div style="font-size:11px;color:#726C9C;margin-top:10px;">⚠ = periode belum lengkap (sebagian bulan belum terisi). Kolom bertanda ini jangan dibandingkan langsung dgn periode penuh.</div>`
    : '';

  // === P&L Franchise ringkas (opsi 2): 1 blok utk SELURUH periode yg tampil
  // di QS (ikut toggle tahun) -- pakai gabungan indeks bulan dari semua bucket.
  let franchiseBlock = '';
  if (state.unit === 'konsolidasi') {
    const allIdx = [...new Set(buckets.flatMap(b => b.indices))].sort((a,b)=>a-b);
    if (allIdx.length) {
      const periodDesc = qsYear !== null ? `Periode: tahun ${qsYear}.` : `Periode: seluruh data (${allIdx.length} bulan).`;
      franchiseBlock = renderOnlineOfflineSplit(u, allIdx, periodDesc) + renderChannelSplit(u, allIdx, periodDesc);
    }
  }

  return modeToggle + yearToggle
    + renderQSAnalysis(u, buckets, revTot)
    + franchiseBlock
    + (buckets.length ? `<div class="tbl-wrap"><table><thead><tr><th>Akun</th>${headers}</tr></thead><tbody>${rows}</tbody></table></div>` : emptyMsg)
    + partialNote;
}

// Analisa antar-bucket -- HANYA membandingkan 2 bucket terakhir yg SAMA-SAMA
// LENGKAP (complete). Kalau salah satu parsial/kosong, analisa perbandingan
// ditahan (transparan ke user) -- mencegah kesimpulan palsu spt "turun 100%"
// yg sebenarnya cuma data belum diisi. Perbandingan antar-TAHUN otomatis ikut
// aturan ini: baru muncul kalau 2 tahun sama2 lengkap.
function renderQSAnalysis(u, buckets, revTot){
  const modeLabel = qsMode==='quarter'?'kuartal':qsMode==='semester'?'semester':'tahun';
  const complete = buckets.filter(b=>b.complete);
  const rev = getLineVals(u,'Pendapatan');
  const hpp = getLineVals(u,'Harga Pokok Penjualan') || getLineVals(u,'HPP');
  const lo = getLineVals(u,'Laba Operasional');
  const sumB = (vals,b) => b.indices.reduce((s,i)=>s+(vals&&vals[i]!=null?vals[i]:0),0);

  let body;
  if (complete.length < 2){
    body = `<div style="font-size:12.5px;color:#9B93C4;">Analisa perbandingan antar-${modeLabel} baru tersedia setelah ada minimal 2 ${modeLabel} yang datanya lengkap. Saat ini baru <b>${complete.length}</b> ${modeLabel} penuh — kolom bertanda ⚠ sengaja tidak dibandingkan agar tidak menyesatkan.</div>`;
  } else {
    const cur = complete[complete.length-1], prev = complete[complete.length-2];
    const curLbl = bucketLabel(cur.kind,cur.key,cur.indices,true).split(' (')[0];
    const prevLbl = bucketLabel(prev.kind,prev.key,prev.indices,true).split(' (')[0];
    const rCur=sumB(rev,cur), rPrev=sumB(rev,prev);
    const loCur=sumB(lo,cur), loPrev=sumB(lo,prev);
    const hCur=sumB(hpp,cur), hPrev=sumB(hpp,prev);
    const dRev = rPrev? ((rCur-rPrev)/Math.abs(rPrev)*100):null;
    const mCur = rCur? loCur/rCur*100:null, mPrev = rPrev? loPrev/rPrev*100:null;
    const hrCur = rCur? hCur/rCur*100:null, hrPrev = rPrev? hPrev/rPrev*100:null;
    let pts=[];
    if(dRev!==null) pts.push(`Pendapatan ${curLbl}: <b>${fmtRp(rCur)}</b>, ${dRev>=0?'naik':'turun'} <b>${Math.abs(dRev).toFixed(1)}%</b> dari ${prevLbl} (${fmtRp(rPrev)}).`);
    if(mCur!==null&&mPrev!==null) pts.push(`Margin Laba Operasional ${mCur>=mPrev?'membaik':'menurun'}: ${mPrev.toFixed(1)}% → <b>${mCur.toFixed(1)}%</b>.`);
    if(hrCur!==null&&hrPrev!==null&&Math.abs(hrCur-hrPrev)>=0.5) pts.push(`Rasio HPP ${hrCur>hrPrev?'naik':'turun'} ${hrPrev.toFixed(1)}% → ${hrCur.toFixed(1)}% — ${hrCur>hrPrev?'menekan':'menopang'} margin.`);
    if(rCur) pts.push(`Sensitivitas: tiap 1 poin % perbaikan rasio HPP ≈ tambahan laba <b>${fmtRp(rCur*0.01)}</b> per ${modeLabel}.`);
    body = `<div style="font-size:12.5px;color:#F5F3FF;line-height:1.5;"><ul style="margin:0;padding-left:18px;">${pts.map(p=>`<li style="margin-bottom:5px;">${p}</li>`).join('')}</ul></div>`;
  }
  return `<div style="background:#171433;border:1px solid #2A2650;border-left:3px solid ${u.color};border-radius:12px;padding:16px 20px;margin-bottom:18px;">
    <div style="font-family:'Space Grotesk',sans-serif;font-size:13px;font-weight:700;color:${u.color};margin-bottom:8px;">ANALISA ANTAR-${modeLabel.toUpperCase()}</div>
    ${body}</div>`;
}

// Filter bulan -- CUMA mempengaruhi tampilan (visibleIdx), TIDAK mengubah
// PERIODS asli. Jadi live-fetch/kalkulasi tetap jalan penuh di semua bulan;
// ini murni soal kolom mana yg dirender ke tabel. Sengaja cuma aktif di tab
// Preview -- tab lain (Month to Month, YoY, YTD, Quartal) py struktur kolom
// berbeda yg belum disambungkan ke filter ini.
function renderMonthFilterBar(){
  const bar = document.getElementById('monthFilterBar');
  if (!bar) return;
  if (state.analysis !== 'preview') { bar.innerHTML = ''; return; }
  ensureYearSelected();
  const years = availableYearsAll();
  const vIdx = getVisibleIdx();

  const yearChips = years.map(y => {
    const on = y === selectedYear;
    return `<div onclick="setSelectedYear(${y})" style="cursor:pointer; padding:6px 14px; border-radius:20px; font-size:12.5px; font-weight:700; font-family:'Space Grotesk',sans-serif; border:1px solid ${on?'#FFC93C':'#2A2650'}; color:${on?'#FFC93C':'#726C9C'}; background:${on?'#FFC93C22':'transparent'};">${y}</div>`;
  }).join('');

  // SELALU 12 bulan (Jan-Des) utk tahun terpilih -- bulan yg blm ada datanya
  // ditampilkan abu2/putus-putus & tak bisa diklik (bukan disembunyikan),
  // supaya user tetap lihat struktur tahun penuh sesuai instruksi.
  const monthChips = Array.from({length:12}, (_,m) => {
    const mm = String(m+1).padStart(2,'0');
    const periodStr = `${selectedYear}-${mm}`;
    const idx = PERIODS.indexOf(periodStr);
    const label = MONTH_NAMES_ID[m];
    if (idx === -1) {
      return `<div style="padding:5px 12px; border-radius:20px; font-size:12px; font-weight:600; font-family:'Space Grotesk',sans-serif; border:1px dashed #2A2650; color:#4A4568;" title="Belum ada data">${label}</div>`;
    }
    const on = vIdx.includes(idx);
    const color = MONTH_COLORS[idx % MONTH_COLORS.length];
    return `<div onclick="toggleMonthFilter(${idx})" style="cursor:pointer; padding:5px 12px; border-radius:20px; font-size:12px; font-weight:600; font-family:'Space Grotesk',sans-serif; border:1px solid ${on?color:'#2A2650'}; color:${on?color:'#726C9C'}; background:${on?color+'22':'transparent'};">${label}</div>`;
  }).join('');
  const allBtn = `<div onclick="selectAllMonthsInYear()" style="cursor:pointer; padding:5px 12px; border-radius:20px; font-size:12px; font-weight:600; font-family:'Space Grotesk',sans-serif; border:1px solid #2A2650; color:#9B93C4;">Semua ${selectedYear}</div>`;

  bar.innerHTML = `
    <div style="margin-bottom:9px; display:flex; align-items:center; flex-wrap:wrap; gap:7px;"><span style="font-size:11px; color:#726C9C; font-weight:600; margin-right:2px;">TAHUN:</span>${yearChips}</div>
    <div style="display:flex; align-items:center; flex-wrap:wrap; gap:7px;"><span style="font-size:11px; color:#726C9C; font-weight:600; margin-right:2px;">BULAN:</span>${allBtn}${monthChips}</div>`;
}
function selectAllMonthsInYear(){ visibleIdx = monthsInYear(selectedYear); render(); }
function toggleMonthFilter(i){
  const current = getVisibleIdx();
  let next = current.includes(i) ? current.filter(x=>x!==i) : [...current, i].sort((a,b)=>a-b);
  if (next.length === 0) next = [i]; // jangan sampai kosong total, minimal 1 bulan tetap tampil
  visibleIdx = next;
  render();
}
function setMonthFilter(idx){
  visibleIdx = idx===null ? monthsInYear(selectedYear) : idx;
  render();
}

// ============ SECTION EKSEKUTIF (KONSOLIDASI) + KARTU ANALISA ============
// getLineVals: ambil values dari baris waterfall by nama (null-safe)
function getLineVals(u, name){
  const r = u.waterfall.find(r => r.name.replace(/<[^>]*>/g,'').trim() === name);
  return r ? r.values : null;
}
// index bulan terakhir & sebelumnya YANG TAMPIL (ikut filter) dan punya data
function lastTwoVisibleIdx(u){
  const vIdx = getVisibleIdx();
  const rev = getLineVals(u, 'Pendapatan') || [];
  const filled = vIdx.filter(i => rev[i] != null);
  return { last: filled[filled.length-1], prev: filled[filled.length-2] };
}
function fmtDeltaPct(cur, prev){
  if (cur==null || prev==null || prev===0) return null;
  return ((cur-prev)/Math.abs(prev))*100;
}

// ============ SPLIT OWNERSHIP vs FRANCHISE (KONSOLIDASI) ============
// PENTING soal kejujuran angka:
// - Pendapatan, Diskon, HPP Konsinyasi, Biaya Operasional outlet = AKTUAL dari data.
// - HPP Produk Langsung Franchise = ESTIMASI 60% x omset kotor (aturan user),
//   BUKAN angka aktual -> ditandai jelas di UI.
// - Laba Operasional di sini = laba setelah biaya OUTLET saja; belum termasuk
//   alokasi biaya Head Office/pabrik bersama -> juga ditandai.
// Semua ikut filter bulan (getVisibleIdx).
const FRANCHISE_HPP_RATE = 0.60; // estimasi HPP produk langsung, dari instruksi user
function sumKonsPath(u, matchFn, idxList){
  // jumlahkan semua leaf di waterfall Konsolidasi yg Path/namanya cocok matchFn,
  // pada bulan-bulan di idxList (default: filter Preview getVisibleIdx)
  const vIdx = idxList || getVisibleIdx();
  let total = 0;
  function walk(node, pathParts){
    if (!node) return;
    if (node.children){
      Object.entries(node.children).forEach(([name,child])=>{
        const arr = Array.isArray(child) ? child : child.vals;
        const newPath = [...pathParts, name];
        if (arr && (!child.children)){
          if (matchFn(newPath, name)) total += vIdx.reduce((s,i)=>s+(arr[i]||0),0);
        }
        if (child.children) walk(child, newPath);
      });
    }
  }
  u.waterfall.forEach(row => {
    if (row.children) walk(row, [row.name.replace(/<[^>]*>/g,'').trim()]);
  });
  return total;
}
// ============ MODE PERIODE BERSAMA (Bulanan/MoM/Kuartal/Semester) ============
// Dipakai di Split, Franchise, & 5 halaman Opex supaya konsisten -- 1 sumber
// kebenaran, bukan implementasi terpisah 7x. Per-halaman punya mode sendiri
// (disimpan by key) supaya tidak saling ganggu.
let periodMode = {}; // {unitKey: 'bulanan'|'mom'|'quarter'|'semester'}
function getPeriodMode(key){ return periodMode[key] || 'bulanan'; }
function setPeriodMode(key, mode){ periodMode[key] = mode; render(); }
function renderPeriodModeTabs(unitKey, accentColor, modes){
  modes = modes || [['bulanan','Bulanan'],['mom','Month to Month'],['quarter','Kuartal'],['semester','Semester']];
  const cur = getPeriodMode(unitKey);
  return `<div style="display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap;">` +
    modes.map(([id,label]) => `<button onclick="setPeriodMode('${unitKey}','${id}')" style="cursor:pointer;padding:7px 14px;border-radius:9px;font-family:'Space Grotesk',sans-serif;font-size:12px;font-weight:700;border:1px solid ${cur===id?accentColor:'#2A2650'};color:${cur===id?accentColor:'#726C9C'};background:${cur===id?'#241F4D':'#171433'};">${label}</button>`).join('') +
    `</div>`;
}
// Tabel MoM generik: rows = [{name, values:[6 bulan]}] -- dipakai utk halaman
// non-entity (Franchise) yg strukturnya beda dari u.waterfall biasa.
function renderGenericMoM(rows){
  const labels = PERIODS.map(p=>periodLabel(p,false));
  if (labels.length < 2) return `<div style="color:#726C9C;font-size:12.5px;padding:12px 0;">Butuh minimal 2 bulan data utk Month to Month.</div>`;
  const cols = labels.slice(1).map((m,i) => `${m} vs ${labels[i]}`);
  let html = `<div class="tbl-wrap"><table><thead><tr><th>Keterangan</th>${cols.map(c=>`<th>${c}</th>`).join('')}</tr></thead><tbody>`;
  rows.forEach(row => {
    const arr = row.values;
    const deltas = arr.slice(1).map((v,i)=> (arr[i]==null||v==null) ? null : (arr[i]===0? null : ((v-arr[i])/Math.abs(arr[i]))*100));
    html += `<tr class="l0"><td>${row.name}</td>${deltas.map(d=> d===null?'<td class="mono">-</td>':`<td class="mono ${d>=0?'pos-delta':'neg-delta'}">${fmtPct(d)}</td>`).join('')}</tr>`;
  });
  html += '</tbody></table></div>';
  return html;
}
// Tabel bucket Kuartal/Semester generik: computeFn(idxList) -> [{name,value}]
function renderGenericBuckets(kind, computeFn){
  const buckets = groupPeriodsBy(kind).map(b=>({kind,...b}));
  if (!buckets.length) return `<div style="color:#726C9C;font-size:12.5px;padding:12px 0;">Belum ada data.</div>`;
  const headers = buckets.map(b => {
    const warn = b.complete ? '' : ` <span style="color:#FFC93C;">⚠</span>`;
    return `<th style="text-align:right;padding:9px 14px;font-size:11px;color:#726C9C;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">${bucketLabel(b.kind,b.key,b.indices,b.complete)}${warn}</th>`;
  }).join('');
  const perBucketRows = buckets.map(b => computeFn(b.indices));
  const rowNames = perBucketRows[0] ? perBucketRows[0].map(r=>r.name) : [];
  let body = '';
  rowNames.forEach((name, ri) => {
    body += `<tr class="l0"><td style="padding:9px 14px;font-size:12.5px;">${name}</td>`;
    perBucketRows.forEach(rows => {
      const v = rows[ri] ? rows[ri].value : null;
      body += `<td class="mono ${v<0?'neg':''}" style="text-align:right;padding:9px 14px;border-left:1px solid #2A2650;">${v==null?'-':fmtRp(v)}</td>`;
    });
    body += '</tr>';
  });
  const partial = buckets.some(b=>!b.complete) ? `<div style="font-size:10.5px;color:#726C9C;margin-top:8px;">⚠ = periode belum lengkap.</div>` : '';
  return `<div class="tbl-wrap"><table><thead><tr><th>Keterangan</th>${headers}</tr></thead><tbody>${body}</tbody></table></div>${partial}`;
}


const FRANCHISE_ROW_SPEC = [
  { name:'Pendapatan (omset kotor)', get:(fra)=>fra.pend },
  { name:'Diskon', get:(fra)=>fra.disk },
  { name:'Pendapatan Bersih', get:(fra)=>fra.pendBersih },
  { name:'HPP Konsinyasi (aktual)', get:(fra)=>fra.hppKons },
  { name:'HPP Produk Langsung (est. 60%)', get:(fra)=>fra.hppLangsung },
  { name:'Laba Kotor', get:(fra)=>fra.labaKotor },
  { name:'Biaya Operasional Outlet', get:(fra)=>fra.biayaOps },
  { name:'Laba Operasional Franchise', get:(fra)=>fra.labaOps },
];
function franchiseRowsForMoM(){
  // hitung fra SEKALI per bulan (bukan per baris) supaya tak boros panggilan
  const fraPerMonth = PERIODS.map((_,i) => computeChannelPnl(UNIT_DATA.konsolidasi, [i]).fra);
  return FRANCHISE_ROW_SPEC.map(spec => ({
    name: spec.name,
    values: fraPerMonth.map(fra => (fra && fra.pend) ? spec.get(fra) : null)
  }));
}
function franchiseRowsForBucket(idxList){
  const { fra } = computeChannelPnl(UNIT_DATA.konsolidasi, idxList);
  if (!fra || !fra.pend) return FRANCHISE_ROW_SPEC.map(spec => ({ name:spec.name, value:null }));
  return FRANCHISE_ROW_SPEC.map(spec => ({ name:spec.name, value:spec.get(fra) }));
}

function computeChannelPnl(u, idxList){
  const pathHas = (parts, kw) => parts.some(p => p.includes(kw));
  // OWNERSHIP
  const pendOwn = sumKonsPath(u, (p,n)=> pathHas(p,'Pendapatan Ownership'), idxList);
  const diskOwn = -Math.abs(sumKonsPath(u, (p)=> p[0]==='Diskon' && pathHas(p,'Ownership'), idxList));
  const hppKonsOwn = sumKonsPath(u, (p,n)=> n==='HPP Konsinyasi Ownership', idxList);
  // FRANCHISE
  const pendFra = sumKonsPath(u, (p)=> pathHas(p,'Pendapatan Franchise'), idxList);
  const diskFra = -Math.abs(sumKonsPath(u, (p)=> p[0]==='Diskon' && pathHas(p,'Franchise'), idxList));
  const hppKonsFra = sumKonsPath(u, (p,n)=> n==='HPP Konsinyasi Franchise', idxList);
  const opsFra = sumKonsPath(u, (p)=> pathHas(p,'Biaya Operasional Franchise'), idxList);

  const hppLangsungFra = FRANCHISE_HPP_RATE * pendFra; // 60% x OMSET KOTOR (1.a)

  const fra = {
    pend: pendFra, disk: diskFra, pendBersih: pendFra+diskFra,
    hppKons: hppKonsFra, hppLangsung: hppLangsungFra, hppTotal: hppKonsFra+hppLangsungFra,
    labaKotor: (pendFra+diskFra)-(hppKonsFra+hppLangsungFra),
    biayaOps: opsFra,
    labaOps: (pendFra+diskFra)-(hppKonsFra+hppLangsungFra)-opsFra,
  };
  const own = {
    pend: pendOwn, disk: diskOwn, pendBersih: pendOwn+diskOwn,
    hppKons: hppKonsOwn,
  };
  return { own, fra };
}
// ============ SPLIT ONLINE vs OFFLINE (KONSOLIDASI) ============
// Aturan (dikonfirmasi user):
// - Pendapatan Online = Harga Normal + Adjustment (dari sheet "Online").
// - HPP Produk Online = 60% x Harga Normal SAJA (adjustment=markup, tak kena HPP). ESTIMASI.
// - Diskon Online = Diskon Online + Komisi (Potongan/Komisi Aplikasi/Merchant).
// - Net Profit Online = Net Pendapatan Online - HPP Online (TANPA opex; semua opex ke offline).
// - OFFLINE = SISA (Konsolidasi total - Online) utk tiap baris -> menjamin
//   Online + Offline = Konsolidasi (rekonsiliasi). Konsinyasi ikut offline.
// Semua ikut daftar bulan (idxList).
// Normalisasi nilai Period APA PUN bentuknya -> "YYYY-MM".
// Perlu krn sel Period di sheet Online tersimpan sbg DATE, sehingga sampai ke
// dashboard sbg ISO datetime ("2026-06-01T07:00:00.000Z"), bukan "2026-06"
// spt di sheet Data. Dibuat toleran supaya tak gampang patah lagi kalau format
// sel berubah di kemudian hari.
function normalizePeriodKey(raw){
  if (raw === null || raw === undefined) return '';
  if (raw instanceof Date) return raw.getUTCFullYear() + '-' + String(raw.getUTCMonth()+1).padStart(2,'0');
  const s = String(raw).trim();
  if (!s) return '';
  if (/^\d{4}-\d{2}$/.test(s)) return s;                       // sudah benar
  const iso = s.match(/^(\d{4})-(\d{2})-(\d{2})/);             // ISO date / datetime
  if (iso) {
    let y = +iso[1], mo = +iso[2];
    const day = +iso[3];
    // Nilai ini mewakili AWAL bulan. Kalau tanggalnya justru di akhir bulan
    // (>=28), itu gejala pergeseran zona waktu (mis. 1 Jul waktu lokal
    // tersimpan sbg 30 Jun 17:00 UTC) -> bulatkan ke bulan berikutnya.
    if (day >= 28) { mo += 1; if (mo > 12) { mo = 1; y += 1; } }
    return y + '-' + String(mo).padStart(2,'0');
  }
  const named = s.match(/^([A-Za-z]+)\s+(\d{4})$/);            // "Jun 2026" / "Juni 2026"
  if (named) {
    const idx = MONTH_NAMES_ID.findIndex(n => named[1].toLowerCase().startsWith(n.toLowerCase().slice(0,3)));
    if (idx >= 0) return named[2] + '-' + String(idx+1).padStart(2,'0');
  }
  const ym = s.match(/^(\d{4})[\/\.](\d{1,2})$/);              // "2026/06"
  if (ym) return ym[1] + '-' + String(+ym[2]).padStart(2,'0');
  const my = s.match(/^(\d{1,2})[\/\.](\d{4})$/);              // "06/2026"
  if (my) return my[2] + '-' + String(+my[1]).padStart(2,'0');
  return s; // tak dikenali -> biarkan, akan terlihat di panel diagnostik
}
function parseOnlineRows(){
  // Sheet "Online" TIDAK punya header (baris pertama kosong), jadi baca per
  // POSISI kolom: [0]Period [1]Entity ... [5]Path ... [7]Amount -- urutan sama
  // dgn sheet Data. Toleran thd 2 bentuk: objek (kalau ada header) atau array.
  const out = {};
  ONLINE_ROWS.forEach(r => {
    let period, path, amountRaw;
    if (Array.isArray(r)) { period=normalizePeriodKey(r[0]); path=r[5]||''; amountRaw=r[7]; }
    else {
      // objek: coba nama kolom dulu, fallback ke nilai per-urutan kalau key kosong
      const vals = Object.values(r);
      period = normalizePeriodKey(r.Period !== undefined ? r.Period : vals[0]);
      path   = (r.Path   || vals[5] || '').toString();
      amountRaw = r.Amount !== undefined ? r.Amount : vals[7];
    }
    if (!period) return;
    const val = parseAmount(amountRaw);
    if (val===null) return;
    if (!out[period]) out[period] = { normal:0, adj:0 };
    if (path.includes('Harga Normal')) out[period].normal += val;
    else if (path.includes('Adjustment Harga')) out[period].adj += val;
  });
  return out;
}
function computeOnlineOffline(u, idxList){
  const vIdx = idxList || getVisibleIdx();
  const online = parseOnlineRows();
  const sumIdx = (fn) => vIdx.reduce((s,i)=> s + fn(i), 0);
  const periodOf = (i) => PERIODS[i];

  // ONLINE dari sheet Online (harga normal & adjustment)
  const onNormal = sumIdx(i => (online[periodOf(i)]?.normal)||0);
  const onAdj    = sumIdx(i => (online[periodOf(i)]?.adj)||0);
  const onGross  = onNormal + onAdj;

  // Diskon Online + Komisi (dari waterfall Konsolidasi, sudah negatif/diabskan)
  const diskOnline = -Math.abs(sumKonsPath(u,(p)=> p[0]==='Diskon' && p.some(x=>x.includes('Diskon Online')), idxList));
  const komisi     = -Math.abs(sumKonsPath(u,(p)=> p[0]==='Diskon' && p.some(x=>x.includes('Potongan/Komisi Aplikasi/Merchant')), idxList));
  const onDiskTotal = diskOnline + komisi;
  const onNet = onGross + onDiskTotal;

  const onHPP = -(0.60 * onNormal); // 60% x harga normal, sbg pengurang. ESTIMASI.
  const onNetProfit = onNet + onHPP;

  // TOTAL konsolidasi (aktual) utk baris terkait
  const totalOf = (name) => { const r = u.waterfall.find(x=>x.name===name); return r ? vIdx.reduce((s,i)=>s+(r.values[i]||0),0) : 0; };
  const tPend = totalOf('Pendapatan');
  const tDisk = totalOf('Diskon');
  const tNet  = totalOf('Pendapatan Bersih');
  const tHPP  = totalOf('Harga Pokok Penjualan');
  const tLK   = totalOf('Laba Kotor');
  const tOpex = totalOf('Biaya Operasional');
  const tLO   = totalOf('Laba Operasional');
  const tLB   = totalOf('Laba Bersih');

  // OFFLINE = sisa (total - online). Konsinyasi & semua opex otomatis masuk sini.
  const offGross = tPend - onGross;
  const offDisk  = tDisk - onDiskTotal;
  const offNet   = tNet - onNet;
  const offHPP   = tHPP - Math.abs(onHPP); // tHPP positif (nilai HPP), onHPP negatif
  const offLK    = tLK - onNetProfit;      // laba kotor offline = total LK - laba kotor online (=net profit online krn tanpa opex)
  const offNetProfit = tLB - onNetProfit;  // offline menyerap seluruh opex, penyusutan, pajak, bunga

  return {
    online: { normal:onNormal, adj:onAdj, gross:onGross, diskon:diskOnline, komisi, net:onNet, hpp:onHPP, labaKotor:onNetProfit, opex:0, labaOp:onNetProfit, netProfit:onNetProfit },
    offline:{ gross:offGross, diskon:offDisk, net:offNet, hpp:-offHPP, labaKotor:offLK, opex:-tOpex, labaOp:tLO-onNetProfit, netProfit:offNetProfit },
    total:  { gross:tPend, diskon:tDisk, net:tNet, hpp:-tHPP, labaKotor:tLK, opex:-tOpex, labaOp:tLO, netProfit:tLB },
    hasOnline: onGross !== 0,
  };
}
// State buka/tutup baris di P&L per kanal (Split Online/Offline).
let splitExpanded = new Set();
function toggleSplitRow(name){
  if (splitExpanded.has(name)) splitExpanded.delete(name); else splitExpanded.add(name);
  render();
}
// Klasifikasi kanal utk SUB-ITEM di bawah Pendapatan & Diskon -- dua grup ini
// SATU-SATUNYA yg buku besarnya memang sudah terpisah per kanal.
// Catatan: "Offline" tidak akan salah ke-match sbg "Online" (huruf beda).
// Komisi aplikasi/merchant dihitung sbg biaya kanal ONLINE (dikonfirmasi user).
function childChannelSplit(parts){
  const j = parts.join(' > ');
  if (/Online/i.test(j)) return 'online';
  if (/Potongan\/Komisi Aplikasi\/Merchant/i.test(j)) return 'online';
  return 'offline';
}
// SATU SUMBER KEBENARAN: nilai ONLINE per baris waterfall Konsolidasi, dlm
// konvensi tanda yg SAMA dgn tabel Konsolidasi (HPP & Opex positif sbg
// magnitude, Diskon negatif). Dipakai mode Bulanan, MoM, Kuartal, & Semester
// -- jangan bikin versi kedua, nanti angkanya bisa beda antar mode.
function splitOnlineByRow(nm, d){
  switch(nm){
    case 'Pendapatan':            return d.online.gross;
    case 'Diskon':                return d.online.diskon + d.online.komisi;
    case 'Pendapatan Bersih':     return d.online.net;
    case 'Harga Pokok Penjualan': return Math.abs(d.online.hpp);
    case 'Laba Kotor':            return d.online.labaKotor;
    case 'Biaya Operasional':     return 0;
    case 'Laba Operasional':      return d.online.labaOp;
    case 'Laba Bersih':           return d.online.netProfit;
    default:                      return 0; // baris lain (pendapatan/beban lain, pajak) -> offline semua
  }
}
// Mode MoM butuh 1 kanal saja per tampilan (kalau 3 kanal x 5 pasang bulan,
// tabelnya jadi 15 kolom -- tak terbaca). Jadi user pilih kanal dulu.
let splitMoMChannel = 'total';
function setSplitMoMChannel(c){ splitMoMChannel = c; render(); }
function renderSplitChannelPicker(){
  const opts = [['online','Online','#4FC3F7'],['offline','Offline','#FFC93C'],['total','Total (gabungan)','#F5F3FF']];
  return `<div style="display:flex;gap:7px;margin-bottom:14px;align-items:center;flex-wrap:wrap;">
    <span style="font-size:11px;color:#726C9C;font-weight:600;">LIHAT KANAL:</span>
    ${opts.map(([id,lbl,col])=>`<button onclick="setSplitMoMChannel('${id}')" style="cursor:pointer;padding:6px 12px;border-radius:8px;font-family:'Space Grotesk',sans-serif;font-size:11.5px;font-weight:700;border:1px solid ${splitMoMChannel===id?col:'#2A2650'};color:${splitMoMChannel===id?col:'#726C9C'};background:${splitMoMChannel===id?'#241F4D':'#171433'};">${lbl}</button>`).join('')}
  </div>`;
}
// MoM: baris waterfall PENUH (pola sama dgn Konsolidasi & mode Bulanan),
// kolom = pasangan bulan, isi = % perubahan pada kanal yg dipilih.
function renderSplitMoM(){
  const u = UNIT_DATA.konsolidasi;
  const dPer = PERIODS.map((_,i) => computeOnlineOffline(u, [i])); // hitung 1x per bulan
  const rows = u.waterfall.map(r => {
    const nm = r.name.replace(/<[^>]*>/g,'').trim();
    return { name: nm, values: PERIODS.map((_,i) => {
      const tot = r.values[i];
      if (tot === null || tot === undefined) return null;
      const on = splitOnlineByRow(nm, dPer[i]);
      return splitMoMChannel === 'online' ? on
           : splitMoMChannel === 'offline' ? (tot - on)
           : tot;
    })};
  });
  return renderGenericMoM(rows);
}
// ============ PNL CABANG (FRANCHISE) -- versi 2, HPP dibatasi 60% ============
// Dibuat krn user ragu dgn HPP yg terisi di menu PNL biasa. Pendapatan &
// Diskon diambil per-KANAL (Online/Offline/Konsinyasi) dari sheet Online;
// HPP Produk Offline & Online dihitung 60% dari pendapatan kanal masing2
// (BUKAN dari sheet Data); HPP Konsinyasi & Pembelian Langsung serta SEMUA
// baris di bawah HPP (Opex, Laba Operasional, dst) tetap dari sheet Data,
// TIDAK diubah.
const FRANCHISE_HPP_RATE_V2 = 0.60;
function computeFranchiseOutlet(outletKey, idxList){
  const entityName = ENTITY_KEY_MAP[outletKey] || UNIT_DATA[outletKey].label;
  const u = UNIT_DATA[outletKey];
  const sf = (path) => sumOnlineSheet(entityName, path, idxList);

  const pendOffline   = sf('Pendapatan > Pendapatan Offline');
  const pendOnlineHN  = sf('Pendapatan > Pendapatan Online Harga Normal');
  const pendOnlineAdj = sf('Pendapatan > Pendapatan Online Adjustment Harga');
  const pendKonsinyasi= sf('Pendapatan > Pendapatan Konsinyasi');
  const hasOnlineData = (pendOffline+pendOnlineHN+pendOnlineAdj+pendKonsinyasi) !== 0;

  const diskonOnline  = -Math.abs(sf('Diskon > Diskon Online'));
  const diskonOffline = -Math.abs(sf('Diskon > Diskon Offline'));

  const sumIdx = (arr) => arr ? idxList.reduce((s,i)=>s+(arr[i]||0),0) : 0;
  const komisiOnline = -Math.abs(sumIdx(getRawSeriesByPath(entityName, 'Potongan Pendapatan > Komisi Online')));
  const hppKonsinyasi = sumIdx(getRawSeriesByPath(entityName, 'Harga Pokok Penjualan > Konsinyasi'));
  const hppPembelianLangsung = sumIdx(getRawSeriesByPath(entityName, 'Harga Pokok Penjualan > Pembelian Langsung'));

  const hppOffline = FRANCHISE_HPP_RATE_V2 * pendOffline;
  const hppOnline  = FRANCHISE_HPP_RATE_V2 * pendOnlineHN;

  const jumlahPendapatan = pendOffline + pendOnlineHN + pendOnlineAdj + pendKonsinyasi;
  const pendapatanBersih = jumlahPendapatan + diskonOnline + diskonOffline + komisiOnline;
  const jumlahHpp = hppOffline + hppOnline + hppKonsinyasi + hppPembelianLangsung;
  const labaKotor = pendapatanBersih - jumlahHpp;

  // Baris di bawah HPP TETAP dari sheet Data, tak diubah -- diambil langsung
  // dari waterfall outlet yg sudah ada.
  const biayaOps = sumIdx(getLineVals(u,'Biaya Operasional'));
  const labaOperasional = labaKotor - biayaOps;
  const biayaPenyusutan = sumIdx(getLineVals(u,'Biaya Penyusutan'));
  const biayaPajak = sumIdx(getLineVals(u,'Biaya Pajak'));
  const costOfMgmt = sumIdx(getLineVals(u,'Cost of Management'));
  const labaBersih = labaOperasional - biayaPenyusutan - biayaPajak - costOfMgmt;

  return {
    hasOnlineData,
    pendOffline, pendOnlineHN, pendOnlineAdj, pendKonsinyasi, jumlahPendapatan,
    diskonOnline, diskonOffline, komisiOnline, pendapatanBersih,
    hppOffline, hppOnline, hppKonsinyasi, hppPembelianLangsung, jumlahHpp,
    labaKotor, biayaOps, labaOperasional, biayaPenyusutan, biayaPajak, costOfMgmt, labaBersih,
  };
}

function renderCabangFranchiseTable(outletKey, idxList, contextNote){
  const d = computeFranchiseOutlet(outletKey, idxList);
  if (!d.hasOnlineData) {
    return `<div style="background:#171433;border:1px solid #2A2650;border-radius:12px;padding:16px 20px;color:#9B93C4;font-size:12.5px;">
      <b>${UNIT_DATA[outletKey].label}</b> belum punya data di sheet Online untuk periode yang dipilih -- halaman ini butuh baris Pendapatan Online/Offline/Konsinyasi & Diskon per outlet di sheet Online.
    </div>`;
  }
  const basis = d.jumlahPendapatan || 0;
  const cell = (val, hl) => {
    if (val === null || val === undefined) return `<td style="text-align:right;padding:8px 14px;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;color:#4A4568;">–</td>`;
    const pct = (basis && val!==0) ? `<span style="display:block;font-size:9.5px;color:#726C9C;">${(val/basis*100).toFixed(1)}%</span>` : '';
    return `<td class="mono ${val<0?'neg':''}" style="text-align:right;padding:8px 14px;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;white-space:nowrap;font-weight:${hl?700:400};">${val===0?'–':fmtRp(val)}${pct}</td>`;
  };
  const row = (label, on, off, kons, tot, opts={}) => `<tr style="${opts.superHl?'background:#122E2B;border-left:3px solid #22D3C5;':(opts.hl?'background:#1C1840;':'')}">
    <td style="padding:8px 14px;font-size:${opts.superHl?13:12.5}px;color:${opts.superHl?'#22D3C5':(opts.hl?'#FFC93C':'#F5F3FF')};font-weight:${opts.superHl||opts.hl?700:400};border-bottom:1px solid #2A2650;">${label}${opts.est?' <span style="color:#FFC93C;">⚠</span>':''}</td>
    ${cell(on,opts.hl||opts.superHl)}${cell(off,opts.hl||opts.superHl)}${cell(kons,opts.hl||opts.superHl)}${cell(tot,opts.hl||opts.superHl)}
  </tr>`;
  const body = `
    ${row('Pendapatan Offline', null, d.pendOffline, null, d.pendOffline)}
    ${row('Pendapatan Online Harga Normal', d.pendOnlineHN, null, null, d.pendOnlineHN)}
    ${row('Pendapatan Online Adjustment Harga', d.pendOnlineAdj, null, null, d.pendOnlineAdj)}
    ${row('Pendapatan Konsinyasi', null, null, d.pendKonsinyasi, d.pendKonsinyasi)}
    ${row('Jumlah Pendapatan', d.pendOnlineHN+d.pendOnlineAdj, d.pendOffline, d.pendKonsinyasi, d.jumlahPendapatan, {superHl:true})}
    ${row('Diskon Online', d.diskonOnline, null, null, d.diskonOnline)}
    ${row('Diskon Offline', null, d.diskonOffline, null, d.diskonOffline)}
    ${row('Komisi Online', d.komisiOnline, null, null, d.komisiOnline)}
    ${row('Pendapatan Bersih', d.pendOnlineHN+d.pendOnlineAdj+d.diskonOnline+d.komisiOnline, d.pendOffline+d.diskonOffline, d.pendKonsinyasi, d.pendapatanBersih, {hl:true})}
    ${row('HPP Produk Online (60% × Hrg Normal)', -d.hppOnline, null, null, -d.hppOnline, {est:true})}
    ${row('HPP Produk Offline (60% × Pendapatan)', null, -d.hppOffline, null, -d.hppOffline, {est:true})}
    ${row('HPP Konsinyasi', null, null, -d.hppKonsinyasi, -d.hppKonsinyasi)}
    ${d.hppPembelianLangsung ? row('HPP Pembelian Langsung', null, null, null, -d.hppPembelianLangsung) : ''}
    ${row('Laba Kotor', null, null, null, d.labaKotor, {hl:true})}
    ${row('Biaya Operasional', null, null, null, -d.biayaOps)}
    ${row('Laba Operasional', null, null, null, d.labaOperasional, {superHl:true})}
    ${row('Biaya Penyusutan', null, null, null, -d.biayaPenyusutan)}
    ${row('Biaya Pajak', null, null, null, -d.biayaPajak)}
    ${d.costOfMgmt ? row('Cost of Management', null, null, null, -d.costOfMgmt) : ''}
    ${row('Laba Bersih', null, null, null, d.labaBersih, {hl:true})}
  `;
  return `
    <div style="background:#171433;border:1px solid #2A2650;border-radius:12px;padding:18px 20px;overflow-x:auto;">
      <div style="font-family:'Space Grotesk',sans-serif;font-size:13px;font-weight:700;color:#22D3C5;margin-bottom:4px;">PNL CABANG (FRANCHISE) — ${UNIT_DATA[outletKey].label}</div>
      <div style="font-size:10.5px;color:#726C9C;margin-bottom:12px;">Versi 2 dgn <b>batas atas HPP 60%</b> (Produk Online & Offline) -- dibuat krn ada keraguan pada HPP yg terisi di menu PNL biasa. HPP Konsinyasi & seluruh baris di bawahnya (Opex, Laba Operasional, dst) TETAP dari sheet Data, tidak diubah. ${contextNote||'Ikut filter bulan.'}</div>
      <table style="width:100%;border-collapse:collapse;min-width:700px;">
        <thead><tr>
          <th style="text-align:left;padding:9px 14px;font-size:11px;color:#726C9C;text-transform:uppercase;border-bottom:1px solid #2A2650;">Keterangan</th>
          <th style="text-align:right;padding:9px 14px;font-size:11px;color:#4FC3F7;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">Online</th>
          <th style="text-align:right;padding:9px 14px;font-size:11px;color:#FFC93C;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">Offline</th>
          <th style="text-align:right;padding:9px 14px;font-size:11px;color:#A78BFA;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">Konsinyasi</th>
          <th style="text-align:right;padding:9px 14px;font-size:11px;color:#F5F3FF;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">Total</th>
        </tr></thead>
        <tbody>${body}</tbody>
      </table>
    </div>`;
}
// Tabel tren bulanan (Omset & Laba Operasional per bulan yg dipilih) --
// muncul TAMBAHAN (bukan pengganti) saat >=2 bulan dipilih di mode Bulanan,
// supaya user tetap lihat pergerakan per bulan meski total-nya digabung.
function renderFranchiseMonthTrend(outletKeys, idxList){
  if (idxList.length < 2) return '';
  const sortedIdx = [...idxList].sort((a,b)=>a-b);
  const cols = sortedIdx.map(i => periodLabel(PERIODS[i], true));
  function buildMetricTable(metricKey, metricLabel, color){
    const rows = outletKeys.map(k => {
      const vals = sortedIdx.map(i => {
        const d = computeFranchiseOutlet(k, [i]);
        return d.hasOnlineData ? d[metricKey] : null;
      });
      return { label: UNIT_DATA[k].label, vals, hasAny: vals.some(v=>v!=null) };
    }).filter(r=>r.hasAny);
    if (!rows.length) return '';
    const header = cols.map(c=>`<th style="text-align:right;padding:8px 12px;font-size:10.5px;color:#726C9C;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">${c}</th>`).join('');
    const body = rows.map(r => `<tr>
      <td style="padding:7px 12px;font-size:12px;border-bottom:1px solid #2A2650;">${r.label}</td>
      ${r.vals.map(v=>`<td class="mono ${v<0?'neg':''}" style="text-align:right;padding:7px 12px;font-size:12px;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;white-space:nowrap;">${v==null?'-':fmtRp(v)}</td>`).join('')}
    </tr>`).join('');
    return `<div style="margin-bottom:16px;">
      <div style="font-size:11.5px;font-weight:700;color:${color};margin-bottom:8px;">Tren ${metricLabel} per Bulan</div>
      <div class="tbl-wrap" style="overflow-x:auto;"><table style="width:100%;border-collapse:collapse;min-width:420px;">
        <thead><tr><th style="text-align:left;padding:8px 12px;font-size:10.5px;color:#726C9C;text-transform:uppercase;border-bottom:1px solid #2A2650;">${outletKeys.length>1?'Outlet':'Keterangan'}</th>${header}</tr></thead>
        <tbody>${body}</tbody>
      </table></div>
    </div>`;
  }
  const omsetTable = buildMetricTable('jumlahPendapatan', 'Omset', '#22D3C5');
  const laoTable = buildMetricTable('labaOperasional', 'Laba Operasional', '#4ADE80');
  if (!omsetTable && !laoTable) return '';
  return `<div style="background:#171433;border:1px solid #2A2650;border-radius:12px;padding:18px 20px;margin-bottom:16px;overflow-x:auto;">
    <div style="font-family:'Space Grotesk',sans-serif;font-size:13px;font-weight:700;color:#F5F3FF;margin-bottom:4px;">TREN BULANAN — ${cols[0]} s.d. ${cols[cols.length-1]}</div>
    <div style="font-size:10.5px;color:#726C9C;margin-bottom:14px;">Rincian per bulan utk memantau pergerakan -- total gabungan tetap ditampilkan di tabel utama di bawah.</div>
    ${omsetTable}${laoTable}
  </div>`;
}
function renderCabangFranchiseCompareBlock(idxList, blockLabel){
  const sorted = [...OUTLET_KEYS].sort((a,b)=>UNIT_DATA[a].label.localeCompare(UNIT_DATA[b].label,'id'));
  const rows = sorted.map(k => {
    const d = computeFranchiseOutlet(k, idxList);
    return { key:k, label:UNIT_DATA[k].label, ...d };
  });
  const withData = rows.filter(r=>r.hasOnlineData).sort((a,b)=>b.labaBersih-a.labaBersih);
  const noData = rows.filter(r=>!r.hasOnlineData);
  if (!withData.length) {
    return `<div style="background:#171433;border:1px solid #2A2650;border-radius:12px;padding:16px 20px;margin-bottom:14px;color:#9B93C4;font-size:12.5px;">${blockLabel?`<b>${blockLabel}</b>: `:''}Belum ada satu pun outlet dgn data sheet Online utk periode ini.</div>`;
  }
  // tiap sel: nilai Rupiah + % thd Omset di bawahnya (kecuali Omset sendiri = 100%)
  const cell = (v, base, hl) => {
    const pct = base ? `<span style="display:block;font-size:9.5px;color:#726C9C;">${(v/base*100).toFixed(1)}%</span>` : '';
    return `<td class="mono ${v<0?'neg':''}" style="text-align:right;padding:9px 14px;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;white-space:nowrap;${hl?'background:#122E2B;font-weight:700;':''}">${fmtRp(v)}${pct}</td>`;
  };
  let body = withData.map(r => {
    const base = r.jumlahPendapatan;
    return `<tr>
      <td style="padding:9px 14px;font-size:12.5px;font-weight:600;border-bottom:1px solid #2A2650;">${r.label}</td>
      ${cell(r.jumlahPendapatan, 0, true)}
      ${cell(r.jumlahHpp, base)}
      ${cell(r.labaKotor, base)}
      ${cell(r.biayaOps, base)}
      ${cell(r.labaOperasional, base, true)}
      ${cell(r.labaBersih, base)}
    </tr>`;
  }).join('');
  const noDataNote = noData.length ? `<div style="font-size:10.5px;color:#726C9C;margin-top:10px;">Belum ada data sheet Online: ${noData.map(r=>r.label).join(', ')}.</div>` : '';
  return `
    <div style="background:#171433;border:1px solid #2A2650;border-radius:12px;padding:18px 20px;margin-bottom:16px;overflow-x:auto;">
      <div style="font-family:'Space Grotesk',sans-serif;font-size:13px;font-weight:700;color:#22D3C5;margin-bottom:4px;">PERBANDINGAN PNL CABANG (FRANCHISE)${blockLabel?` — ${blockLabel}`:''}</div>
      <div style="font-size:10.5px;color:#726C9C;margin-bottom:12px;">Diurutkan dari Laba Bersih tertinggi. % di bawah tiap angka = porsi thd Omset. Kolom <b style="color:#22D3C5;">Omset</b> &amp; <b style="color:#22D3C5;">Laba Operasional</b> disorot. HPP pakai batas atas 60% (Produk Online/Offline); Konsinyasi & baris di bawah HPP tetap dari sheet Data.</div>
      <table style="width:100%;border-collapse:collapse;min-width:820px;">
        <thead><tr>
          <th style="text-align:left;padding:9px 14px;font-size:11px;color:#726C9C;text-transform:uppercase;border-bottom:1px solid #2A2650;">Outlet</th>
          <th style="text-align:right;padding:9px 14px;font-size:11px;color:#22D3C5;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;background:#122E2B;">Omset</th>
          <th style="text-align:right;padding:9px 14px;font-size:11px;color:#A78BFA;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">HPP</th>
          <th style="text-align:right;padding:9px 14px;font-size:11px;color:#FFD93D;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">Laba Kotor</th>
          <th style="text-align:right;padding:9px 14px;font-size:11px;color:#FF9F43;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">Opex</th>
          <th style="text-align:right;padding:9px 14px;font-size:11px;color:#22D3C5;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;background:#122E2B;">Laba Operasional</th>
          <th style="text-align:right;padding:9px 14px;font-size:11px;color:#FFC93C;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">Laba Bersih</th>
        </tr></thead>
        <tbody>${body}</tbody>
      </table>
      ${noDataNote}
    </div>`;
}
function renderCabangFranchiseCompare(periodBlocks){
  // toleran: kalau dipanggil dgn idxList lama (array angka), bungkus jadi 1 blok
  if (Array.isArray(periodBlocks) && typeof periodBlocks[0] === 'number') {
    periodBlocks = [{ indices: periodBlocks, label: '' }];
  }
  return periodBlocks.map(b => renderCabangFranchiseCompareBlock(b.indices, b.label)).join('');
}

function renderOnlineOfflineSplit(u, idxList, contextNote){
  const effIdx = idxList || getVisibleIdx(); // fallback aman -- idxList opsional (mode Bulanan default manggil tanpa argumen ini)
  const d = computeOnlineOffline(u, idxList);
  if (!d.hasOnline) {
    // KASUS 1: fetch memang belum/gagal total -- ONLINE_ROWS kosong.
    if (!ONLINE_ROWS || !ONLINE_ROWS.length) {
      const reason = ONLINE_FETCH_STATUS && ONLINE_FETCH_STATUS!=='ok'
        ? `<br><span style="color:#FFC93C;">Penyebab: ${ONLINE_FETCH_STATUS}</span>` : '';
      return `<div style="background:#171433;border:1px solid #2A2650;border-radius:12px;padding:16px 20px;margin-bottom:18px;font-size:12.5px;color:#9B93C4;">Split Online/Offline butuh data sheet <b>"Online"</b> (Harga Normal & Adjustment). Belum termuat untuk periode ini — pastikan tab <b>Online</b> ter-share "Anyone with link" & namanya persis.${reason}</div>`;
    }
    // KASUS 2 (BARU): ONLINE_ROWS ADA ISINYA (fetch berhasil, ONLINE_FETCH_STATUS='ok'),
    // TAPI tak ada satu pun baris yg ter-agregasi ke periode manapun. Ini
    // beda akar masalah -- kemungkinan besar FORMAT nilai Period di sheet
    // Online tak persis cocok string di PERIODS (mis. "Jun 2026" vs "2026-06",
    // atau Date object yg tak ke-convert). Tampilkan BUKTI langsung supaya
    // ketahuan persis, bukan nebak lagi.
    const parsed = parseOnlineRows();
    const foundPeriods = Object.keys(parsed);
    const sample = ONLINE_ROWS.slice(0,3).map(r => Array.isArray(r) ? JSON.stringify(r.slice(0,2)) : JSON.stringify({Period:r.Period, Path:(r.Path||'').slice(0,40)}));
    return `<div style="background:#171433;border:1px solid #FB7185;border-radius:12px;padding:16px 20px;margin-bottom:18px;font-size:12.5px;color:#9B93C4;">
      <b style="color:#FB7185;">Sheet Online berhasil ter-fetch (${ONLINE_ROWS.length} baris) TAPI tak satu pun cocok dgn periode yg diharapkan.</b><br><br>
      Periode yg TERBACA dari sheet Online: <b style="color:#FFC93C;">${foundPeriods.length ? foundPeriods.join(', ') : '(tidak ada -- kolom Period kosong/tak terbaca)'}</b><br>
      Periode yg DIHARAPKAN (dari sheet Data): <b style="color:#4FC3F7;">${effIdx.map(i=>periodLabel(PERIODS[i],true)).join(', ')}</b><br><br>
      Contoh 3 baris mentah pertama: <code style="font-size:10.5px;color:#726C9C;">${sample.join(' | ')}</code><br><br>
      <span style="color:#726C9C;">Kemungkinan besar format kolom Period di sheet Online (kolom pertama) tidak persis "YYYY-MM" seperti di sheet Data -- cek apakah selnya ke-format sbg Date/format lain di Google Sheets.</span>
    </div>`;
  }
  // ===== STRUKTUR SEPERTI KONSOLIDASI: waterfall penuh, bisa expand, tapi
  //       nilainya dipecah ke kolom ONLINE / OFFLINE / TOTAL.
  const t = d.total;
  const basis = t.gross || 0;
  const vIdx = effIdx;

  // Nilai ONLINE per baris diambil dari splitOnlineByRow() -- fungsi bersama
  // yg juga dipakai mode MoM/Kuartal/Semester. OFFLINE selalu = Total - Online,
  // jadi rekonsiliasi tiap baris dijamin (Online + Offline = Konsolidasi).
  const SUBTOTAL_ROWS = ['Pendapatan','Pendapatan Bersih','Laba Kotor','Laba Operasional','Laba Bersih'];

  const sumVals = (arr) => arr ? vIdx.reduce((s,i)=>s+(arr[i]||0),0) : 0;
  function nodeTotalSplit(child){
    const arr = Array.isArray(child) ? child : child.vals;
    if (arr) return sumVals(arr);
    let x = 0;
    if (child && child.children) Object.values(child.children).forEach(c=>{ x += nodeTotalSplit(c); });
    return x;
  }
  function leavesOfSplit(node){
    const out = [];
    (function walk(n, parts){
      if (!n || !n.children) return;
      Object.entries(n.children).forEach(([nm, child])=>{
        const np = [...parts, nm];
        if (child && child.children) walk(child, np);
        else out.push({ name:nm, parts:np, total: nodeTotalSplit(child) });
      });
    })(node, []);
    return out;
  }

  const cellSplit = (val, hl) => {
    if (val === null) return `<td style="text-align:right;padding:8px 14px;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;color:#4A4568;">–</td>`;
    const pct = (basis && val!==0) ? `<span style="display:block;font-size:9.5px;color:#726C9C;">${(val/basis*100).toFixed(1)}%</span>` : '';
    return `<td class="mono ${val<0?'neg':''}" style="text-align:right;padding:8px 14px;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;white-space:nowrap;font-weight:${hl?700:400};">${val===0?'–':fmtRp(val)}${pct}</td>`;
  };

  let body = '';
  u.waterfall.forEach(row => {
    const nm = row.name.replace(/<[^>]*>/g,'').trim();
    const rowTotal  = sumVals(row.values);
    const rowOnline = splitOnlineByRow(nm, d);
    const rowOffline = rowTotal - rowOnline;
    const hl  = SUBTOTAL_ROWS.includes(nm);
    const est = nm === 'Harga Pokok Penjualan';
    const hasKids = !!row.children;
    const isOpen  = splitExpanded.has(nm);
    const arrow = hasKids
      ? `<span style="display:inline-block;width:14px;color:#726C9C;font-size:10px;transform:rotate(${isOpen?90:0}deg);">&#9654;</span>`
      : `<span style="display:inline-block;width:14px;"></span>`;
    body += `<tr style="${hl?'background:#1C1840;':''}${hasKids?'cursor:pointer;':''}" ${hasKids?`onclick="toggleSplitRow('${nm.replace(/'/g,"\\'")}')"`:''}>
      <td style="padding:8px 14px;font-size:12.5px;color:${hl?'#FFC93C':'#F5F3FF'};font-weight:${hl?700:400};border-bottom:1px solid #2A2650;">${arrow}${nm}${est?' <span style="color:#FFC93C;">⚠</span>':''}</td>
      ${cellSplit(rowOnline,hl)}${cellSplit(rowOffline,hl)}${cellSplit(rowTotal,hl)}
    </tr>`;

    if (!hasKids || !isOpen) return;
    // mode pemecahan anak:
    //  byName    -> buku besar MEMANG terpisah per kanal (Pendapatan, Diskon)
    //  unsplit   -> tak terpisah per kanal & porsi online cuma ESTIMASI (HPP)
    //  allOffline-> seluruhnya offline sesuai aturan (Opex dll)
    const mode = (nm==='Pendapatan'||nm==='Diskon') ? 'byName'
               : (nm==='Harga Pokok Penjualan') ? 'unsplit' : 'allOffline';

    if (nm==='Pendapatan') {
      body += `<tr><td style="padding:6px 14px 6px 34px;font-size:11.5px;color:#4FC3F7;border-bottom:1px solid #2A2650;">Komposisi Online › Harga Normal</td>${cellSplit(d.online.normal)}${cellSplit(null)}${cellSplit(null)}</tr>`;
      body += `<tr><td style="padding:6px 14px 6px 34px;font-size:11.5px;color:#4FC3F7;border-bottom:1px solid #2A2650;">Komposisi Online › Adjustment (Markup)</td>${cellSplit(d.online.adj)}${cellSplit(null)}${cellSplit(null)}</tr>`;
    }
    if (mode==='unsplit') {
      body += `<tr><td colspan="4" style="padding:6px 14px 6px 34px;font-size:10.5px;color:#FFC93C;border-bottom:1px solid #2A2650;">⚠ Item HPP di buku besar TIDAK terpisah per kanal. Porsi Online (${fmtRp(Math.abs(d.online.hpp))}) adalah <b>estimasi 60% × Harga Normal</b> yang dikeluarkan dari total — bukan penjumlahan item di bawah ini.</td></tr>`;
    }
    leavesOfSplit(row).filter(l=>l.total!==0)
      .sort((a,b)=>Math.abs(b.total)-Math.abs(a.total))
      .forEach(l => {
        const lbl = l.parts.length>1 ? `${l.parts[l.parts.length-2]} › ${l.name}` : l.name;
        let onC, offC, totC;
        if (mode==='byName') {
          const ch = childChannelSplit(l.parts);
          onC  = ch==='online'  ? cellSplit(l.total) : cellSplit(null);
          offC = ch==='offline' ? cellSplit(l.total) : cellSplit(null);
          totC = cellSplit(l.total);
        } else if (mode==='unsplit') {
          onC = cellSplit(null); offC = cellSplit(null); totC = cellSplit(l.total);
        } else {
          onC = cellSplit(null); offC = cellSplit(l.total); totC = cellSplit(l.total);
        }
        body += `<tr><td style="padding:6px 14px 6px 34px;font-size:11.5px;color:#9B93C4;border-bottom:1px solid #2A2650;">${lbl}</td>${onC}${offC}${totC}</tr>`;
      });
  });

  return `<div style="background:#171433;border:1px solid #2A2650;border-radius:12px;padding:18px 20px;margin-bottom:18px;overflow-x:auto;">
    <div style="font-family:'Space Grotesk',sans-serif;font-size:13px;font-weight:700;color:#4FC3F7;margin-bottom:4px;">P&amp;L PER KANAL — ONLINE vs OFFLINE</div>
    <div style="font-size:10.5px;color:#726C9C;margin-bottom:12px;">Struktur sama dgn Konsolidasi — <b>klik baris</b> untuk buka/tutup rincian. ⚠ HPP porsi Online = <b>estimasi 60% × omset Harga Normal</b> (adjustment/markup tak kena HPP). Seluruh Opex &amp; Konsinyasi masuk <b>Offline</b>. Offline = Konsolidasi − Online, jadi Online+Offline SELALU = Total. % dihitung terhadap total Pendapatan. ${contextNote||'Ikut filter bulan.'}</div>
    <table style="width:100%;border-collapse:collapse;min-width:640px;">
      <thead><tr>
        <th style="text-align:left;padding:9px 14px;font-size:11px;color:#726C9C;text-transform:uppercase;border-bottom:1px solid #2A2650;">Keterangan</th>
        <th style="text-align:right;padding:9px 14px;font-size:11px;color:#4FC3F7;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">Online</th>
        <th style="text-align:right;padding:9px 14px;font-size:11px;color:#FFC93C;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">Offline</th>
        <th style="text-align:right;padding:9px 14px;font-size:11px;color:#F5F3FF;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">Total</th>
      </tr></thead>
      <tbody>${body}</tbody>
    </table>
  </div>`;
}

function renderChannelSplit(u, idxList, contextNote){
  const { fra } = computeChannelPnl(u, idxList);
  if (!fra.pend) return '';
  const rows = [
    ['Pendapatan (omset kotor)', fra.pend, false],
    ['Diskon', fra.disk, false],
    ['Pendapatan Bersih', fra.pendBersih, true],
    ['HPP Konsinyasi (aktual)', fra.hppKons, false],
    ['HPP Produk Langsung (est. 60%)', fra.hppLangsung, false, true],
    ['Laba Kotor', fra.labaKotor, true],
    ['Biaya Operasional Outlet', fra.biayaOps, false],
    ['Laba Operasional Franchise', fra.labaOps, true],
  ];
  const body = rows.map(([label,val,hl,est])=>{
    const pct = fra.pend ? (val/fra.pend*100).toFixed(1)+'%' : '';
    return `<tr style="${hl?'background:#1C1840;':''}">
      <td style="padding:9px 14px;font-size:12.5px;color:${hl?'#FFC93C':'#F5F3FF'};font-weight:${hl?700:400};border-bottom:1px solid #2A2650;">${label}${est?' <span style="color:#FFC93C;">⚠</span>':''}</td>
      <td class="mono ${val<0?'neg':''}" style="padding:9px 14px;text-align:right;border-bottom:1px solid #2A2650;font-weight:${hl?700:400};">${fmtRp(val)}</td>
      <td class="pctTag" style="padding:9px 14px;text-align:right;border-bottom:1px solid #2A2650;">${pct}</td>
    </tr>`;
  }).join('');
  return `<div style="background:#171433;border:1px solid #2A2650;border-radius:12px;padding:18px 20px;margin-bottom:18px;">
    <div style="font-family:'Space Grotesk',sans-serif;font-size:13px;font-weight:700;color:#B4E61D;margin-bottom:4px;">P&amp;L FRANCHISE (kanal terpisah)</div>
    <div style="font-size:10.5px;color:#726C9C;margin-bottom:12px;">⚠ HPP Produk Langsung = <b>estimasi 60% dari omset kotor</b> (bukan angka aktual). Laba Operasional di sini = setelah biaya outlet Franchise saja, <b>belum termasuk alokasi biaya Head Office/pabrik bersama</b>. ${contextNote || 'Ikut filter bulan.'}</div>
    <table style="width:100%;border-collapse:collapse;"><tbody>${body}</tbody></table>
  </div>`;
}

function renderExecSection(u){
  const rev = getLineVals(u,'Pendapatan');
  if (!rev) return '';
  // ikut filter bulan: kalau multi-bulan dipilih, kartu menampilkan TOTAL
  // rentang terpilih (konsisten dgn kolom Total tabel), bukan cuma bulan akhir
  const vIdx = getVisibleIdx().filter(i => rev[i] != null);
  if (!vIdx.length) return '';
  const last = vIdx[vIdx.length-1];
  const prev = vIdx.length>1 ? vIdx[vIdx.length-2] : undefined;
  const multi = vIdx.length > 1;
  const revTot = vIdx.reduce((s,i)=>s+(rev[i]||0),0);
  const metrics = [
    { name:'Pendapatan', row:'Pendapatan' },
    { name:'HPP', row:'Harga Pokok Penjualan', alt:'HPP' },
    { name:'Biaya Operasional', row:'Biaya Operasional' },
    { name:'Laba Operasional', row:'Laba Operasional' },
  ];
  const cards = metrics.map(m => {
    let vals = getLineVals(u, m.row) || (m.alt ? getLineVals(u, m.alt) : null);
    if (!vals) return '';
    const tot = vIdx.reduce((s,i)=>s+(vals[i]||0),0);
    const headline = multi ? tot : vals[last];
    const pctRev = revTot ? (tot/revTot*100).toFixed(1)+'% dari Pendapatan' : '';
    const avgLine = multi ? `Total ${vIdx.length} bln · rata² ${fmtRp(tot/vIdx.length)}/bln` : '';
    const delta = (prev!==undefined) ? fmtDeltaPct(vals[last], vals[prev]) : null;
    const up = delta!==null && delta>=0;
    const isCost = (m.name==='HPP' || m.name==='Biaya Operasional');
    const deltaColor = delta===null ? '' : (up ? (isCost?'#FFC93C':'#4ADE80') : (isCost?'#4ADE80':'#FFC93C'));
    return `<div style="flex:1;min-width:200px;background:#171433;border:1px solid #2A2650;border-radius:12px;padding:16px 18px;">
      <div style="font-size:11px;color:#726C9C;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">${m.name}</div>
      <div class="mono" style="font-size:20px;font-weight:700;margin:6px 0 2px;">${fmtRp(headline)}</div>
      <div style="font-size:10.5px;color:#726C9C;">${pctRev}</div>
      ${avgLine?`<div style="font-size:10.5px;color:#726C9C;">${avgLine}</div>`:''}
      ${delta!==null?`<div style="font-size:11.5px;font-weight:600;color:${deltaColor};margin-top:6px;">${up?'▲':'▼'} ${Math.abs(delta).toFixed(1)}% ${multi?periodLabel(PERIODS[last],false)+' ':''}vs ${periodLabel(PERIODS[prev],false)}</div>`:''}
    </div>`;
  }).join('');
  return `<div style="margin-bottom:18px;">
    <div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:14px;">${cards}</div>
    <div style="background:#171433;border:1px solid #2A2650;border-radius:12px;padding:14px;height:280px;"><canvas id="execChart"></canvas></div>
  </div>`;
}

// ============ GRAFIK CUSTOM (pilih metrik & rentang bebas) ============
// Nama baris HPP BERBEDA per segmen di data asli -- Manufaktur pakai "Harga
// Pokok Produksi", Store pakai "HPP" singkat, sisanya "Harga Pokok Penjualan".
// Kalau dipukul rata 1 nama, HPP Manufaktur & Store diam2 kosong.
const HPP_ROW_NAME_BY_SEGMENT = { konsolidasi:'Harga Pokok Penjualan', ownership:'Harga Pokok Penjualan', ho:'Harga Pokok Penjualan', store:'HPP', manufaktur:'Harga Pokok Produksi' };
const GRAFIK_METRICS = [
  { key:'Pendapatan',       label:'Pendapatan',        color:'#4FC3F7', isCost:false, get:(u)=> getLineVals(u,'Pendapatan') },
  { key:'Diskon',           label:'Diskon',             color:'#FB7185', isCost:true, flipSign:true, get:(u)=> getLineVals(u,'Diskon') },
  { key:'HPP',              label:'HPP',                color:'#A78BFA', isCost:true,  get:(u)=> getLineVals(u, HPP_ROW_NAME_BY_SEGMENT[grafikSegment] || 'Harga Pokok Penjualan') },
  { key:'HPPBahanBaku',     label:'HPP Bahan Baku',     color:'#F4A6D0', isCost:true,  get:(u)=>{
      // Manufaktur punya "HPP Bahan Baku" LANGSUNG sbg baris waterfall --
      // pakai itu kalau ada (lebih akurat), baru fallback ke raw-lookup
      // (khusus Konsolidasi, yg tak punya baris ini di waterfall-nya).
      const direct = getLineVals(u,'HPP Bahan Baku');
      if (direct && direct.some(v=>v!=null)) return direct;
      return getRawAccountSeries(ENTITY_KEY_MAP[grafikSegment]||'Konsolidasi','HPP Bahan Baku Utama');
    } },
  { key:'LabaKotor',        label:'Laba Kotor',         color:'#FFD93D', isCost:false, get:(u)=> getLineVals(u,'Laba Kotor') },
  { key:'Opex',             label:'Opex',               color:'#FF9F43', isCost:true,  get:(u)=> getLineVals(u,'Biaya Operasional') },
  { key:'LabaOperasional',  label:'Laba Operasional',   color:'#4ADE80', isCost:false, get:(u)=> getLineVals(u,'Laba Operasional') },
];
let grafikSelected = new Set(['Pendapatan','LabaOperasional']); // default: 2 metrik paling sering dilihat -- user bebas ubah
let grafikFromIdx = null; // null = paling lama tersedia
let grafikToIdx = null;   // null = paling baru tersedia
let grafikSegment = 'konsolidasi'; // segmen entity yg sedang dilihat
const GRAFIK_SEGMENTS = [
  { key:'konsolidasi', label:'Konsolidasi' },
  { key:'ownership',   label:'Ownership' },
  { key:'store',        label:'Store & Brand' },
  { key:'manufaktur',  label:'Manufaktur' },
  { key:'ho',           label:'Head Office' },
];
// Series per-bulan utk segmen Franchise (bukan dari waterfall biasa spt
// segmen lain -- dihitung ulang dari computeFranchiseOutlet tiap bulan).
// HPP Bahan Baku TIDAK ADA konsepnya di versi Franchise (HPP-nya cuma 1
// angka gabungan dibatasi 60%) -- return null biar jujur, bukan ditebak.
function franchiseOutletSeries(outletKey, metricKey){
  return PERIODS.map((_,i) => {
    const d = computeFranchiseOutlet(outletKey, [i]);
    if (!d.hasOnlineData) return null;
    switch(metricKey){
      case 'Pendapatan': return d.jumlahPendapatan;
      case 'Diskon': return d.diskonOnline + d.diskonOffline;
      case 'HPP': return d.jumlahHpp;
      case 'LabaKotor': return d.labaKotor;
      case 'Opex': return d.biayaOps;
      case 'LabaOperasional': return d.labaOperasional;
      case 'LabaBersih': return d.labaBersih;
      default: return null;
    }
  });
}
function setGrafikSegment(k){ grafikSegment = k; render(); }
function grafikRangeDefaults(){
  if (grafikFromIdx === null) grafikFromIdx = 0;
  if (grafikToIdx === null) grafikToIdx = PERIODS.length - 1;
  if (grafikFromIdx > grafikToIdx) { const t = grafikFromIdx; grafikFromIdx = grafikToIdx; grafikToIdx = t; }
}
function toggleGrafikMetric(key){
  if (grafikSelected.has(key)) grafikSelected.delete(key); else grafikSelected.add(key);
  if (grafikSelected.size === 0) grafikSelected.add(key); // jangan sampai kosong total
  render();
}
function setGrafikAllMetrics(on){
  grafikSelected = on ? new Set(GRAFIK_METRICS.map(m=>m.key)) : new Set(['Pendapatan']);
  render();
}
function setGrafikFrom(i){ grafikFromIdx = parseInt(i); render(); }
function setGrafikTo(i){ grafikToIdx = parseInt(i); render(); }
// ============ MODUL KEBOCORAN PENDAPATAN (Diskon & Komisi) ============
// Dibuat krn diskon+komisi mencapai ~17,7% pendapatan bruto (lbh besar dari
// SELURUH Opex) tapi sebelumnya tak punya satu pun modul analitik.
// Sumber: baris mentah RAW_ROWS_CACHE, bukan waterfall teragregasi, supaya
// bisa dipilah per jenis & per segmen.
// PENTING: cek SELURUH path, bukan cuma segmen terakhir. Path komisi utama
// berakhiran segmen segmen ("... > Ownership" / "... > Franchise"), jadi kalau
// cuma segmen akhir yg dicek, komisi miliaran salah masuk kategori Diskon.
function isKomisiPath(p){
  return /komisi/i.test(p||'');
}
function isDiskonPath(p){
  const s = (p||'');
  if (isKomisiPath(s)) return false; // komisi punya kategori sendiri
  return /diskon|cashback|potongan/i.test(s);
}
// Kumpulkan kebocoran per periode utk 1 entity. Nilai dikembalikan POSITIF
// (besaran), krn sumber datanya tanda campur -- ada yg negatif, ada positif.
function leakageByPeriod(entityName){
  const diskon = PERIODS.map(()=>0), komisi = PERIODS.map(()=>0), pend = PERIODS.map(()=>0);
  RAW_ROWS_CACHE.forEach(r => {
    if (r.Entity !== entityName) return;
    if ((r.Type||'').trim() !== 'Detail') return;
    const idx = PERIODS.indexOf(normalizePeriod(r.Period));
    if (idx === -1) return;
    const v = parseAmount(r.Amount);
    if (v === null) return;
    const path = r.Path || '';
    if (path.startsWith('Pendapatan')) { pend[idx] += v; return; }
    if (isKomisiPath(path)) { komisi[idx] += Math.abs(v); return; }
    if (isDiskonPath(path)) { diskon[idx] += Math.abs(v); return; }
  });
  return { diskon, komisi, pend };
}
// Rincian per jenis (path lengkap) utk 1 entity, dijumlah pd idxList
function leakageBreakdown(entityName, idxList){
  const map = {};
  RAW_ROWS_CACHE.forEach(r => {
    if (r.Entity !== entityName) return;
    if ((r.Type||'').trim() !== 'Detail') return;
    const idx = PERIODS.indexOf(normalizePeriod(r.Period));
    if (idx === -1 || !idxList.includes(idx)) return;
    const v = parseAmount(r.Amount);
    if (v === null) return;
    const path = (r.Path||'').replace(/\s+/g,' ').trim(); // rapikan spasi ganda yg ada di sumber
    const kind = isKomisiPath(path) ? 'Komisi' : (isDiskonPath(path) ? 'Diskon' : null);
    if (!kind) return;
    const key = kind + '||' + path;
    map[key] = (map[key]||0) + Math.abs(v);
  });
  return Object.entries(map).map(([k,v]) => {
    const [kind, path] = k.split('||');
    return { kind, path, label: path.split('>').slice(1).join(' › ').trim() || path, value: v };
  }).sort((a,b)=>b.value-a.value);
}

function renderLeakRingkas(){
  const L = leakageByPeriod('Konsolidasi');
  const vIdx = getVisibleIdx();
  const sum = (arr) => vIdx.reduce((s,i)=>s+(arr[i]||0),0);
  const pend = sum(L.pend), dis = sum(L.diskon), kom = sum(L.komisi);
  const total = dis + kom, neto = pend - total;
  if (!pend) return `<div style="background:#171433;border:1px solid #2A2650;border-radius:12px;padding:18px;color:#9B93C4;font-size:12.5px;">Tidak ada data Pendapatan Konsolidasi utk periode terpilih.</div>`;
  const pct = (v) => pend ? (v/pend*100).toFixed(1)+'%' : '-';

  // Bridge bruto -> neto
  const bridgeRow = (label, val, colorHex, isSub) => `<tr style="${isSub?'background:#1C1840;':''}">
    <td style="padding:10px 14px;font-size:13px;color:${isSub?'#FFC93C':'#F5F3FF'};font-weight:${isSub?700:400};border-bottom:1px solid #2A2650;">${label}</td>
    <td class="mono" style="text-align:right;padding:10px 14px;border-bottom:1px solid #2A2650;color:${colorHex};font-weight:${isSub?700:400};white-space:nowrap;">${fmtRp(val)}</td>
    <td class="mono" style="text-align:right;padding:10px 14px;border-bottom:1px solid #2A2650;color:#9B93C4;white-space:nowrap;">${pct(val)}</td>
  </tr>`;

  // Tren bulanan
  const trendRows = PERIODS.map((p,i) => {
    if (!L.pend[i]) return '';
    const d = L.diskon[i], k = L.komisi[i], t = d+k;
    const dp = L.pend[i]?d/L.pend[i]*100:0, kp = L.pend[i]?k/L.pend[i]*100:0, tp = L.pend[i]?t/L.pend[i]*100:0;
    return `<tr>
      <td style="padding:8px 14px;font-size:12.5px;border-bottom:1px solid #2A2650;">${periodLabel(p,true)}</td>
      <td class="mono" style="text-align:right;padding:8px 14px;border-bottom:1px solid #2A2650;white-space:nowrap;">${fmtRp(L.pend[i])}</td>
      <td class="mono" style="text-align:right;padding:8px 14px;border-bottom:1px solid #2A2650;white-space:nowrap;">${fmtRp(d)}<span style="display:block;font-size:9.5px;color:#726C9C;">${dp.toFixed(1)}%</span></td>
      <td class="mono" style="text-align:right;padding:8px 14px;border-bottom:1px solid #2A2650;white-space:nowrap;">${fmtRp(k)}<span style="display:block;font-size:9.5px;color:#726C9C;">${kp.toFixed(1)}%</span></td>
      <td class="mono" style="text-align:right;padding:8px 14px;border-bottom:1px solid #2A2650;white-space:nowrap;color:#F97316;font-weight:700;">${fmtRp(t)}<span style="display:block;font-size:9.5px;color:#726C9C;">${tp.toFixed(1)}%</span></td>
    </tr>`;
  }).join('');

  // proyeksi setahun berdasar rata2 bulan yg terpilih
  const bulanTerpilih = vIdx.filter(i=>L.pend[i]).length;
  const annual = bulanTerpilih ? (total/bulanTerpilih*12) : 0;

  return `
    <div style="background:#2A1B0F;border:1px solid #F97316;border-radius:12px;padding:16px 20px;margin-bottom:18px;">
      <div style="font-size:12px;color:#F97316;font-weight:700;margin-bottom:6px;">TOTAL KEBOCORAN PERIODE TERPILIH</div>
      <div class="mono" style="font-size:26px;font-weight:700;color:#F97316;">${fmtRp(total)} <span style="font-size:15px;color:#9B93C4;">(${pct(total)} dari pendapatan bruto)</span></div>
      <div style="font-size:11.5px;color:#9B93C4;margin-top:8px;">Setara <b>${fmtRp(annual)}</b> per tahun kalau laju ${bulanTerpilih} bulan ini berlanjut. Sebagai pembanding: seluruh Biaya Operasional Konsolidasi berjalan di kisaran 14-15% pendapatan.</div>
    </div>

    <div style="background:#171433;border:1px solid #2A2650;border-radius:12px;padding:18px 20px;margin-bottom:18px;overflow-x:auto;">
      <div style="font-family:'Space Grotesk',sans-serif;font-size:13px;font-weight:700;color:#F97316;margin-bottom:12px;">JEMBATAN PENDAPATAN BRUTO → NETO</div>
      <table style="width:100%;border-collapse:collapse;min-width:420px;">
        <thead><tr>
          <th style="text-align:left;padding:9px 14px;font-size:11px;color:#726C9C;text-transform:uppercase;border-bottom:1px solid #2A2650;">Komponen</th>
          <th style="text-align:right;padding:9px 14px;font-size:11px;color:#726C9C;border-bottom:1px solid #2A2650;">Nilai</th>
          <th style="text-align:right;padding:9px 14px;font-size:11px;color:#726C9C;border-bottom:1px solid #2A2650;">% Bruto</th>
        </tr></thead>
        <tbody>
          ${bridgeRow('Pendapatan Bruto', pend, '#4FC3F7', true)}
          ${bridgeRow('− Diskon', -dis, '#FB7185')}
          ${bridgeRow('− Komisi / Potongan Platform', -kom, '#FB7185')}
          ${bridgeRow('Pendapatan Neto (setelah kebocoran)', neto, '#4ADE80', true)}
        </tbody>
      </table>
    </div>

    <div style="background:#171433;border:1px solid #2A2650;border-radius:12px;padding:18px 20px;overflow-x:auto;">
      <div style="font-family:'Space Grotesk',sans-serif;font-size:13px;font-weight:700;color:#F97316;margin-bottom:4px;">TREN BULANAN</div>
      <div style="font-size:10.5px;color:#726C9C;margin-bottom:12px;">Seluruh bulan yg ada datanya ditampilkan (tak terpengaruh filter), supaya arah tren terlihat utuh.</div>
      <table style="width:100%;border-collapse:collapse;min-width:600px;">
        <thead><tr>
          <th style="text-align:left;padding:9px 14px;font-size:11px;color:#726C9C;text-transform:uppercase;border-bottom:1px solid #2A2650;">Bulan</th>
          <th style="text-align:right;padding:9px 14px;font-size:11px;color:#4FC3F7;border-bottom:1px solid #2A2650;">Pendapatan Bruto</th>
          <th style="text-align:right;padding:9px 14px;font-size:11px;color:#FB7185;border-bottom:1px solid #2A2650;">Diskon</th>
          <th style="text-align:right;padding:9px 14px;font-size:11px;color:#FB7185;border-bottom:1px solid #2A2650;">Komisi</th>
          <th style="text-align:right;padding:9px 14px;font-size:11px;color:#F97316;border-bottom:1px solid #2A2650;">Total Kebocoran</th>
        </tr></thead>
        <tbody>${trendRows}</tbody>
      </table>
    </div>`;
}

function renderLeakJenis(){
  const vIdx = getVisibleIdx();
  const items = leakageBreakdown('Konsolidasi', vIdx);
  if (!items.length) return `<div style="background:#171433;border:1px solid #2A2650;border-radius:12px;padding:18px;color:#9B93C4;font-size:12.5px;">Tidak ada data diskon/komisi utk periode terpilih.</div>`;
  const total = items.reduce((s,i)=>s+i.value,0);
  let cum = 0;
  const rows = items.map(it => {
    cum += it.value;
    const share = total ? it.value/total*100 : 0;
    const cumPct = total ? cum/total*100 : 0;
    return `<tr>
      <td style="padding:8px 14px;font-size:12.5px;border-bottom:1px solid #2A2650;">
        <span style="display:inline-block;padding:2px 7px;border-radius:5px;font-size:9.5px;font-weight:700;margin-right:8px;background:${it.kind==='Komisi'?'#A78BFA22':'#FB718522'};color:${it.kind==='Komisi'?'#A78BFA':'#FB7185'};">${it.kind}</span>${it.label}
      </td>
      <td class="mono" style="text-align:right;padding:8px 14px;border-bottom:1px solid #2A2650;white-space:nowrap;">${fmtRp(it.value)}</td>
      <td class="mono" style="text-align:right;padding:8px 14px;border-bottom:1px solid #2A2650;white-space:nowrap;">${share.toFixed(1)}%</td>
      <td class="mono" style="text-align:right;padding:8px 14px;border-bottom:1px solid #2A2650;white-space:nowrap;color:${cumPct<=80?'#F97316':'#726C9C'};">${cumPct.toFixed(1)}%</td>
    </tr>`;
  }).join('');
  const n80 = (() => { let c=0,n=0; for (const it of items){ c+=it.value; n++; if (total && c/total>=0.8) break; } return n; })();
  return `
    <div style="background:#1C1840;border:1px solid #2A2650;border-radius:10px;padding:12px 16px;margin-bottom:16px;font-size:12px;color:#C9C3E8;">
      <b style="color:#F97316;">${n80} dari ${items.length} jenis</b> menyumbang 80% dari total kebocoran ${fmtRp(total)}. Fokuskan perbaikan di situ dulu -- sisanya dampaknya kecil.
    </div>
    <div style="background:#171433;border:1px solid #2A2650;border-radius:12px;padding:18px 20px;overflow-x:auto;">
      <div style="font-family:'Space Grotesk',sans-serif;font-size:13px;font-weight:700;color:#F97316;margin-bottom:12px;">RINCIAN PER JENIS (diurutkan dari terbesar)</div>
      <table style="width:100%;border-collapse:collapse;min-width:560px;">
        <thead><tr>
          <th style="text-align:left;padding:9px 14px;font-size:11px;color:#726C9C;text-transform:uppercase;border-bottom:1px solid #2A2650;">Jenis</th>
          <th style="text-align:right;padding:9px 14px;font-size:11px;color:#726C9C;border-bottom:1px solid #2A2650;">Nilai</th>
          <th style="text-align:right;padding:9px 14px;font-size:11px;color:#726C9C;border-bottom:1px solid #2A2650;">% Total</th>
          <th style="text-align:right;padding:9px 14px;font-size:11px;color:#726C9C;border-bottom:1px solid #2A2650;">Kumulatif</th>
        </tr></thead>
        <tbody>${rows}</tbody>
      </table>
    </div>`;
}

function renderLeakOutlet(){
  const vIdx = getVisibleIdx();
  const sorted = [...OUTLET_KEYS].sort((a,b)=>UNIT_DATA[a].label.localeCompare(UNIT_DATA[b].label,'id'));
  const data = sorted.map(k => {
    const L = leakageByPeriod(ENTITY_KEY_MAP[k] || UNIT_DATA[k].label);
    const sum = (arr)=>vIdx.reduce((s,i)=>s+(arr[i]||0),0);
    const pend = sum(L.pend), dis = sum(L.diskon), kom = sum(L.komisi);
    return { label: UNIT_DATA[k].label, pend, dis, kom, total: dis+kom,
             disPct: pend?dis/pend*100:0, komPct: pend?kom/pend*100:0, totPct: pend?(dis+kom)/pend*100:0 };
  }).filter(d=>d.pend>0).sort((a,b)=>b.totPct-a.totPct);
  if (!data.length) return `<div style="background:#171433;border:1px solid #2A2650;border-radius:12px;padding:18px;color:#9B93C4;font-size:12.5px;">Tidak ada data outlet utk periode terpilih.</div>`;

  const avg = data.reduce((s,d)=>s+d.total,0) / data.reduce((s,d)=>s+d.pend,0) * 100;
  const worst = data[0], best = data[data.length-1];
  // potensi penghematan kalau outlet di atas rata2 turun ke rata2
  const potensi = data.filter(d=>d.totPct>avg).reduce((s,d)=>s+(d.total - d.pend*avg/100), 0);

  const rows = data.map(d => {
    const above = d.totPct > avg;
    return `<tr>
      <td style="padding:8px 14px;font-size:12.5px;font-weight:600;border-bottom:1px solid #2A2650;">${d.label}</td>
      <td class="mono" style="text-align:right;padding:8px 14px;border-bottom:1px solid #2A2650;white-space:nowrap;">${fmtRp(d.pend)}</td>
      <td class="mono" style="text-align:right;padding:8px 14px;border-bottom:1px solid #2A2650;white-space:nowrap;">${d.disPct.toFixed(1)}%</td>
      <td class="mono" style="text-align:right;padding:8px 14px;border-bottom:1px solid #2A2650;white-space:nowrap;">${d.komPct.toFixed(1)}%</td>
      <td class="mono" style="text-align:right;padding:8px 14px;border-bottom:1px solid #2A2650;white-space:nowrap;font-weight:700;color:${above?'#FB7185':'#4ADE80'};">${d.totPct.toFixed(1)}%</td>
      <td class="mono" style="text-align:right;padding:8px 14px;border-bottom:1px solid #2A2650;white-space:nowrap;color:#9B93C4;">${fmtRp(d.total)}</td>
    </tr>`;
  }).join('');

  return `
    <div style="background:#1C1840;border:1px solid #2A2650;border-radius:10px;padding:14px 16px;margin-bottom:16px;font-size:12px;color:#C9C3E8;line-height:1.6;">
      Rata-rata tertimbang kebocoran: <b style="color:#F97316;">${avg.toFixed(1)}%</b> dari pendapatan.
      Terburuk <b style="color:#FB7185;">${worst.label} (${worst.totPct.toFixed(1)}%)</b>, terbaik <b style="color:#4ADE80;">${best.label} (${best.totPct.toFixed(1)}%)</b> -- selisih <b>${(worst.totPct-best.totPct).toFixed(1)} poin</b>.<br>
      Kalau semua outlet di atas rata-rata bisa ditarik ke level rata-rata saja, potensi penghematan periode ini <b style="color:#4ADE80;">${fmtRp(potensi)}</b>.
      <span style="color:#726C9C;">Catatan: sebagian selisih bisa wajar (beda kanal/lokasi/bauran produk) -- angka ini penunjuk arah investigasi, bukan target otomatis.</span>
    </div>
    <div style="background:#171433;border:1px solid #2A2650;border-radius:12px;padding:18px 20px;overflow-x:auto;">
      <div style="font-family:'Space Grotesk',sans-serif;font-size:13px;font-weight:700;color:#F97316;margin-bottom:12px;">PERINGKAT OUTLET -- KEBOCORAN TERBESAR DI ATAS</div>
      <table style="width:100%;border-collapse:collapse;min-width:640px;">
        <thead><tr>
          <th style="text-align:left;padding:9px 14px;font-size:11px;color:#726C9C;text-transform:uppercase;border-bottom:1px solid #2A2650;">Outlet</th>
          <th style="text-align:right;padding:9px 14px;font-size:11px;color:#4FC3F7;border-bottom:1px solid #2A2650;">Pendapatan</th>
          <th style="text-align:right;padding:9px 14px;font-size:11px;color:#FB7185;border-bottom:1px solid #2A2650;">Diskon %</th>
          <th style="text-align:right;padding:9px 14px;font-size:11px;color:#A78BFA;border-bottom:1px solid #2A2650;">Komisi %</th>
          <th style="text-align:right;padding:9px 14px;font-size:11px;color:#F97316;border-bottom:1px solid #2A2650;">Total %</th>
          <th style="text-align:right;padding:9px 14px;font-size:11px;color:#726C9C;border-bottom:1px solid #2A2650;">Nilai Rp</th>
        </tr></thead>
        <tbody>${rows}</tbody>
      </table>
    </div>`;
}

// ============ MODUL REKONSILIASI OTOMATIS ============
// Dibuat krn sepanjang proyek ini kita berulang kali kena bug DIAM-DIAM:
// "Bpjs" vs "BPJS", "Penyusutan konsolidasi" vs "Konsolidasi", pergeseran
// baris Ownership 7 bulan. Semua lolos tanpa peringatan apa pun. Modul ini
// menjalankan pemeriksaan otomatis tiap kali dibuka.
function runRekonsiliasi(){
  const checks = [];
  const add = (status, judul, detail) => checks.push({status, judul, detail});

  // --- 1. Akun mentah yg tak ter-petakan ke skema mana pun ---
  const unmapped = {};
  const REV = entityKeyMapReverse();
  RAW_ROWS_CACHE.forEach(r => {
    if ((r.Type||'').trim()!=='Detail') return;
    const v = parseAmount(r.Amount);
    if (v===null || v===0) return;
    const ent = r.Entity;
    const key = REV[ent];
    if (!key || !UNIT_DATA[key]) return;
    const acc = (r.Account||'').trim();
    let found = false;
    (UNIT_DATA[key].waterfall||[]).forEach(row => {
      if ((row.accountLabel||'')===acc || row.name===acc) found = true;
      const scan = (node) => {
        if (!node || found) return;
        if (node.children) Object.keys(node.children).forEach(k => {
          if (k===acc) { found = true; return; }
          const c = node.children[k];
          if (c && !Array.isArray(c)) scan(c);
        });
      };
      scan(row);
    });
    if (!found) {
      const k = ent + ' › ' + acc;
      unmapped[k] = (unmapped[k]||0) + Math.abs(v);
    }
  });
  const unmappedList = Object.entries(unmapped).sort((a,b)=>b[1]-a[1]);
  if (unmappedList.length===0) add('ok','Semua akun ter-petakan','Tidak ada akun bernilai di data mentah yg gagal dicocokkan ke skema dashboard.');
  else add(unmappedList.length>10?'warn':'info', `${unmappedList.length} akun belum ter-petakan ke skema`,
    `Nilainya tidak akan muncul di P&L manapun. Terbesar:<br>` + unmappedList.slice(0,8).map(([k,v])=>`• ${k} — ${fmtRp(v)}`).join('<br>'));

  // --- 2. Inkonsistensi penamaan (beda huruf besar/kecil / spasi ganda) ---
  const normMap = {};
  RAW_ROWS_CACHE.forEach(r => {
    const acc = (r.Account||'').trim();
    if (!acc) return;
    const norm = acc.toLowerCase().replace(/\s+/g,' ');
    normMap[norm] = normMap[norm] || new Set();
    normMap[norm].add(acc);
  });
  const dupNames = Object.entries(normMap).filter(([,s])=>s.size>1);
  if (dupNames.length===0) add('ok','Penamaan akun konsisten','Tidak ditemukan nama akun yg sama tapi beda penulisan huruf/spasi.');
  else add('warn', `${dupNames.length} nama akun ditulis tidak konsisten`,
    `Beda huruf besar/kecil atau spasi bisa menyebabkan data gagal dicocokkan tanpa peringatan (kita sudah 2x kena ini).<br>` +
    dupNames.slice(0,8).map(([,s])=>'• ' + [...s].map(x=>`"${x}"`).join(' vs ')).join('<br>'));

  // --- 3. Kelengkapan periode per entity ---
  const entPeriods = {};
  RAW_ROWS_CACHE.forEach(r => {
    const p = normalizePeriod(r.Period);
    if (!p || PERIODS.indexOf(p)===-1) return;
    entPeriods[r.Entity] = entPeriods[r.Entity] || new Set();
    entPeriods[r.Entity].add(p);
  });
  const incomplete = Object.entries(entPeriods).filter(([,s])=>s.size < PERIODS.length)
    .map(([e,s])=>({e, ada:s.size, kurang:PERIODS.filter(p=>!s.has(p))}));
  if (incomplete.length===0) add('ok','Semua entity punya data di seluruh periode',`${PERIODS.length} periode terisi lengkap utk semua entity.`);
  else add('info', `${incomplete.length} entity tidak punya data di semua periode`,
    incomplete.slice(0,8).map(x=>`• ${x.e}: ${x.ada}/${PERIODS.length} periode (kosong: ${x.kurang.map(p=>periodLabel(p,true)).join(', ')})`).join('<br>'));

  // --- 4. Pendapatan bertanda negatif / biaya bertanda positif tak wajar ---
  const oddSign = [];
  RAW_ROWS_CACHE.forEach(r => {
    if ((r.Type||'').trim()!=='Detail') return;
    const v = parseAmount(r.Amount);
    if (v===null || v===0) return;
    const path = r.Path||'';
    if (path.startsWith('Pendapatan') && v < 0) oddSign.push(`${r.Entity} › ${r.Account} (${periodLabel(normalizePeriod(r.Period),true)}) = ${fmtRp(v)}`);
  });
  if (oddSign.length===0) add('ok','Tanda nilai Pendapatan wajar','Tidak ada baris Pendapatan bernilai negatif.');
  else add('info', `${oddSign.length} baris Pendapatan bernilai NEGATIF`,
    `Bisa wajar (retur/koreksi), tapi perlu dipastikan bukan salah input:<br>` + oddSign.slice(0,6).map(x=>'• '+x).join('<br>'));

  // --- 5. Konsistensi Konsolidasi: tak boleh memuat baris Internal ---
  const internalInKons = RAW_ROWS_CACHE.filter(r => r.Entity==='Konsolidasi' && (r.Counterparty||'').trim()==='Internal' && parseAmount(r.Amount));
  if (internalInKons.length===0) add('ok','Eliminasi intercompany bersih','Entity Konsolidasi tidak memuat satu pun baris ber-Counterparty "Internal". Transaksi antar-unit sudah tereliminasi dgn benar.');
  else add('warn', `Konsolidasi memuat ${internalInKons.length} baris "Internal"`,
    'Transaksi antar-unit seharusnya dieliminasi dari Konsolidasi, kalau tidak pendapatan jadi dihitung ganda (overstated).');

  // --- 6. Silang: Diskon Ownership vs Franchise di Konsolidasi ---
  const junIdx = PERIODS.length-1;
  const L = leakageByPeriod('Konsolidasi');
  const leakPct = L.pend[junIdx] ? (L.diskon[junIdx]+L.komisi[junIdx])/L.pend[junIdx]*100 : 0;
  if (leakPct > 25) add('warn', `Kebocoran periode terakhir tinggi: ${leakPct.toFixed(1)}%`, 'Diskon + komisi melebihi 25% pendapatan bruto. Cek modul Kebocoran Pendapatan.');
  else if (leakPct > 0) add('ok', `Kebocoran periode terakhir: ${leakPct.toFixed(1)}%`, 'Masih di bawah ambang perhatian 25%. Detail ada di modul Kebocoran Pendapatan.');

  return checks;
}
// peta balik nama entity -> key UNIT_DATA. Dihitung SAAT DIPANGGIL (bukan saat
// file dimuat) krn ENTITY_KEY_MAP didefinisikan jauh di bawah -- const tidak
// di-hoist, jadi kalau dievaluasi di atas akan error "before initialization".
function entityKeyMapReverse(){
  const o = {};
  if (typeof ENTITY_KEY_MAP !== 'undefined') Object.keys(ENTITY_KEY_MAP).forEach(k => { o[ENTITY_KEY_MAP[k]] = k; });
  return o;
}

function renderRekonsiliasi(){
  if (!RAW_ROWS_CACHE || !RAW_ROWS_CACHE.length) {
    return `<div style="background:#171433;border:1px solid #2A2650;border-radius:12px;padding:18px 20px;color:#9B93C4;font-size:12.5px;">
      Belum ada data mentah utk diperiksa. Modul ini bekerja setelah live-fetch berhasil menarik data dari spreadsheet.
    </div>`;
  }
  const checks = runRekonsiliasi();
  const nOk = checks.filter(c=>c.status==='ok').length;
  const nWarn = checks.filter(c=>c.status==='warn').length;
  const nInfo = checks.filter(c=>c.status==='info').length;
  const meta = { ok:{c:'#4ADE80',bg:'#0F2E1A',ic:'✓'}, warn:{c:'#FB7185',bg:'#2A1B1B',ic:'⚠'}, info:{c:'#FFC93C',bg:'#2A2410',ic:'ℹ'} };
  const cards = checks.map(c => {
    const m = meta[c.status];
    return `<div style="background:${m.bg};border:1px solid ${m.c};border-radius:10px;padding:14px 16px;margin-bottom:10px;">
      <div style="font-size:13px;font-weight:700;color:${m.c};margin-bottom:6px;">${m.ic} ${c.judul}</div>
      <div style="font-size:11.5px;color:#C9C3E8;line-height:1.65;">${c.detail}</div>
    </div>`;
  }).join('');
  return `
    <div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:18px;">
      <div style="flex:1;min-width:130px;background:#0F2E1A;border:1px solid #4ADE80;border-radius:12px;padding:14px 18px;">
        <div style="font-size:11px;color:#4ADE80;font-weight:600;">LOLOS</div>
        <div class="mono" style="font-size:24px;font-weight:700;color:#4ADE80;">${nOk}</div>
      </div>
      <div style="flex:1;min-width:130px;background:#2A2410;border:1px solid #FFC93C;border-radius:12px;padding:14px 18px;">
        <div style="font-size:11px;color:#FFC93C;font-weight:600;">PERLU DILIHAT</div>
        <div class="mono" style="font-size:24px;font-weight:700;color:#FFC93C;">${nInfo}</div>
      </div>
      <div style="flex:1;min-width:130px;background:#2A1B1B;border:1px solid #FB7185;border-radius:12px;padding:14px 18px;">
        <div style="font-size:11px;color:#FB7185;font-weight:600;">PERLU TINDAKAN</div>
        <div class="mono" style="font-size:24px;font-weight:700;color:#FB7185;">${nWarn}</div>
      </div>
    </div>
    <div style="font-size:11.5px;color:#726C9C;margin-bottom:14px;">Pemeriksaan dijalankan otomatis setiap halaman ini dibuka, berdasarkan data mentah terakhir yg ditarik dari spreadsheet.</div>
    ${cards}`;
}

// ============ PERBANDINGAN CUSTOM (pilih bakery + bulan) ============
// Dipakai 2 modul: PNL Cabang (data waterfall biasa) & PNL Cabang Versi
// Franchise (perhitungan HPP 60%). Bentuk tabel: baris=outlet, kolom=bulan.
const CMP_METRICS_BIASA = [
  { key:'Pendapatan',      label:'Omset',            row:'Pendapatan' },
  { key:'HPP',             label:'HPP',              row:'Harga Pokok Penjualan', alt:'HPP' },
  { key:'LabaKotor',       label:'Laba Kotor',       row:'Laba Kotor' },
  { key:'Opex',            label:'Opex',             row:'Biaya Operasional' },
  { key:'LabaOperasional', label:'Laba Operasional', row:'Laba Operasional' },
  { key:'LabaBersih',      label:'Laba Bersih',      row:'Laba Bersih' },
];
const CMP_METRICS_FRANCHISE = [
  { key:'Pendapatan',      label:'Omset',            f:'jumlahPendapatan' },
  { key:'HPP',             label:'HPP',              f:'jumlahHpp' },
  { key:'LabaKotor',       label:'Laba Kotor',       f:'labaKotor' },
  { key:'Opex',            label:'Opex',             f:'biayaOps' },
  { key:'LabaOperasional', label:'Laba Operasional', f:'labaOperasional' },
  { key:'LabaBersih',      label:'Laba Bersih',      f:'labaBersih' },
];
// state terpisah utk tiap mode supaya pilihan tak saling timpa
let cmpState = {
  biasa:     { outlets:null, months:null, metric:'Pendapatan', pct:false },
  franchise: { outlets:null, months:null, metric:'Pendapatan', pct:false },
  diff:      { outlets:null, months:null, metric:'HPP', pct:false }, // default HPP -- di situlah kedua versi berbeda
};
function cmpEnsure(mode){
  const s = cmpState[mode];
  if (!s.outlets) {
    const sorted = [...OUTLET_KEYS].sort((a,b)=>UNIT_DATA[a].label.localeCompare(UNIT_DATA[b].label,'id'));
    if (mode==='franchise' || mode==='diff') {
      const withData = sorted.filter(k => parseOnlineRowsRaw().some(r => r.entity === ENTITY_KEY_MAP[k]));
      s.outlets = new Set(withData.length ? withData.slice(0,5) : sorted.slice(0,5));
    } else s.outlets = new Set(sorted.slice(0,5));
  }
  if (!s.months || !s.months.length) {
    // default: sampai 6 bulan terakhir yg tersedia
    s.months = PERIODS.map((_,i)=>i).slice(-6);
  }
  s.months = s.months.filter(i => i>=0 && i<PERIODS.length);
  if (!s.months.length) s.months = [PERIODS.length-1];
}
function cmpToggleOutlet(mode,k){ const s=cmpState[mode]; cmpEnsure(mode); s.outlets.has(k)?s.outlets.delete(k):s.outlets.add(k); render(); }
function cmpToggleMonth(mode,i){ const s=cmpState[mode]; cmpEnsure(mode); const p=s.months.indexOf(i); if(p>=0) s.months.splice(p,1); else s.months.push(i); if(!s.months.length) s.months=[i]; render(); }
function cmpSetMetric(mode,m){ cmpState[mode].metric=m; render(); }
function cmpTogglePct(mode){ cmpState[mode].pct=!cmpState[mode].pct; render(); }
function cmpAllOutlets(mode,on){ cmpEnsure(mode); cmpState[mode].outlets = on ? new Set(OUTLET_KEYS) : new Set([[...OUTLET_KEYS].sort((a,b)=>UNIT_DATA[a].label.localeCompare(UNIT_DATA[b].label,'id'))[0]]); render(); }
function cmpAllMonths(mode,on){ cmpEnsure(mode); cmpState[mode].months = on ? PERIODS.map((_,i)=>i) : [PERIODS.length-1]; render(); }

// ambil nilai 1 outlet, 1 bulan, sesuai mode
function cmpValue(mode, outletKey, idx, metricKey){
  if (mode==='franchise'){
    const meta = CMP_METRICS_FRANCHISE.find(m=>m.key===metricKey);
    const d = computeFranchiseOutlet(outletKey,[idx]);
    if (!d.hasOnlineData) return null;
    return d[meta.f];
  }
  const meta = CMP_METRICS_BIASA.find(m=>m.key===metricKey);
  let vals = getLineVals(UNIT_DATA[outletKey], meta.row);
  if ((!vals || vals[idx]==null) && meta.alt) vals = getLineVals(UNIT_DATA[outletKey], meta.alt);
  return vals ? vals[idx] : null;
}
function cmpOmset(mode, outletKey, idx){
  if (mode==='franchise'){ const d=computeFranchiseOutlet(outletKey,[idx]); return d.hasOnlineData?d.jumlahPendapatan:null; }
  const v = getLineVals(UNIT_DATA[outletKey],'Pendapatan'); return v?v[idx]:null;
}

function renderCompareCustom(mode){
  cmpEnsure(mode);
  const s = cmpState[mode];
  const accent = mode==='franchise' ? '#22D3C5' : '#4ADE80';
  const METRICS = mode==='franchise' ? CMP_METRICS_FRANCHISE : CMP_METRICS_BIASA;
  const sorted = [...OUTLET_KEYS].sort((a,b)=>UNIT_DATA[a].label.localeCompare(UNIT_DATA[b].label,'id'));
  const withData = mode==='franchise' ? new Set(OUTLET_KEYS.filter(k => parseOnlineRowsRaw().some(r => r.entity === ENTITY_KEY_MAP[k]))) : null;

  const metricChips = METRICS.map(m=>{
    const on = s.metric===m.key;
    return `<div onclick="cmpSetMetric('${mode}','${m.key}')" style="cursor:pointer;padding:7px 14px;border-radius:20px;font-size:12.5px;font-weight:700;font-family:'Space Grotesk',sans-serif;border:1px solid ${on?accent:'#2A2650'};color:${on?accent:'#726C9C'};background:${on?accent+'1a':'transparent'};">${m.label}</div>`;
  }).join('');

  const outletChips = sorted.map(k=>{
    const on = s.outlets.has(k);
    const has = withData ? withData.has(k) : true;
    return `<div onclick="cmpToggleOutlet('${mode}','${k}')" style="cursor:pointer;padding:6px 12px;border-radius:20px;font-size:12px;font-weight:600;font-family:'Space Grotesk',sans-serif;border:1px solid ${on?accent:'#2A2650'};color:${on?accent:(has?'#9B93C4':'#4A4568')};background:${on?accent+'1a':'transparent'};">${UNIT_DATA[k].label}${withData&&has?' ●':''}</div>`;
  }).join('');

  const monthChips = PERIODS.map((p,i)=>{
    const on = s.months.includes(i);
    return `<div onclick="cmpToggleMonth('${mode}',${i})" style="cursor:pointer;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:600;font-family:'Space Grotesk',sans-serif;border:1px solid ${on?'#FFC93C':'#2A2650'};color:${on?'#FFC93C':'#726C9C'};background:${on?'#FFC93C22':'transparent'};">${periodLabel(p,true)}</div>`;
  }).join('');

  const selOutlets = sorted.filter(k=>s.outlets.has(k));
  const selMonths = [...s.months].sort((a,b)=>a-b);
  const metricLabel = (METRICS.find(m=>m.key===s.metric)||{}).label || s.metric;

  if (!selOutlets.length || !selMonths.length){
    var body = `<div style="padding:20px;text-align:center;color:#726C9C;font-size:12.5px;">Pilih minimal 1 outlet dan 1 bulan.</div>`;
  } else {
    // hitung matriks
    const rows = selOutlets.map(k=>{
      const cells = selMonths.map(i=>{
        const v = cmpValue(mode,k,i,s.metric);
        const om = cmpOmset(mode,k,i);
        return { v, pct: (om && v!=null) ? v/om*100 : null };
      });
      const vals = cells.map(c=>c.v).filter(v=>v!=null);
      const total = vals.length ? vals.reduce((a,b)=>a+b,0) : null;
      return { k, label:UNIT_DATA[k].label, cells, total, avg: vals.length?total/vals.length:null };
    }).sort((a,b)=> (b.total==null?-Infinity:b.total) - (a.total==null?-Infinity:a.total));

    // total per bulan (kolom)
    const colTotals = selMonths.map((_,ci)=>{
      const vals = rows.map(r=>r.cells[ci].v).filter(v=>v!=null);
      return vals.length ? vals.reduce((a,b)=>a+b,0) : null;
    });
    const grand = colTotals.filter(v=>v!=null).reduce((a,b)=>a+b,0);

    const fmtCell = (c)=>{
      if (c.v==null) return `<td style="text-align:right;padding:8px 12px;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;color:#4A4568;">–</td>`;
      const sub = s.pct && c.pct!=null ? `<span style="display:block;font-size:9.5px;color:#726C9C;">${c.pct.toFixed(1)}%</span>` : '';
      return `<td class="mono ${c.v<0?'neg':''}" style="text-align:right;padding:8px 12px;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;white-space:nowrap;">${fmtRp(c.v)}${sub}</td>`;
    };
    body = `
      <table style="width:100%;border-collapse:collapse;min-width:${260+selMonths.length*130}px;">
        <thead><tr>
          <th style="text-align:left;padding:9px 12px;font-size:11px;color:#726C9C;text-transform:uppercase;border-bottom:1px solid #2A2650;position:sticky;left:0;background:#171433;">Outlet</th>
          ${selMonths.map(i=>`<th style="text-align:right;padding:9px 12px;font-size:11px;color:#FFC93C;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;white-space:nowrap;">${periodLabel(PERIODS[i],true)}</th>`).join('')}
          <th style="text-align:right;padding:9px 12px;font-size:11px;color:${accent};border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;background:#1C1840;">TOTAL</th>
          <th style="text-align:right;padding:9px 12px;font-size:11px;color:#9B93C4;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">Rata2/bln</th>
        </tr></thead>
        <tbody>
          ${rows.map(r=>`<tr>
            <td style="padding:8px 12px;font-size:12.5px;font-weight:600;border-bottom:1px solid #2A2650;position:sticky;left:0;background:#171433;">${r.label}</td>
            ${r.cells.map(fmtCell).join('')}
            <td class="mono ${r.total<0?'neg':''}" style="text-align:right;padding:8px 12px;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;background:#1C1840;font-weight:700;color:${accent};white-space:nowrap;">${r.total==null?'–':fmtRp(r.total)}</td>
            <td class="mono" style="text-align:right;padding:8px 12px;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;color:#9B93C4;white-space:nowrap;">${r.avg==null?'–':fmtRp(r.avg)}</td>
          </tr>`).join('')}
          <tr style="background:#1C1840;">
            <td style="padding:9px 12px;font-size:12.5px;font-weight:700;color:#FFC93C;border-top:2px solid #2A2650;position:sticky;left:0;background:#1C1840;">TOTAL ${selOutlets.length} OUTLET</td>
            ${colTotals.map(v=>`<td class="mono" style="text-align:right;padding:9px 12px;border-top:2px solid #2A2650;border-left:1px solid #2A2650;font-weight:700;color:#FFC93C;white-space:nowrap;">${v==null?'–':fmtRp(v)}</td>`).join('')}
            <td class="mono" style="text-align:right;padding:9px 12px;border-top:2px solid #2A2650;border-left:1px solid #2A2650;font-weight:700;color:#FFC93C;white-space:nowrap;">${fmtRp(grand)}</td>
            <td style="border-top:2px solid #2A2650;border-left:1px solid #2A2650;"></td>
          </tr>
        </tbody>
      </table>`;
  }

  return `
    <div style="margin-bottom:14px;">
      <div style="font-size:11px;color:#726C9C;font-weight:600;margin-bottom:8px;">PILIH METRIK:</div>
      <div style="display:flex;flex-wrap:wrap;gap:8px;">${metricChips}</div>
    </div>
    <div style="margin-bottom:14px;">
      <div style="font-size:11px;color:#726C9C;font-weight:600;margin-bottom:8px;">PILIH BAKERY (${s.outlets.size} dipilih)
        <span onclick="cmpAllOutlets('${mode}',true)" style="cursor:pointer;color:#9B93C4;text-decoration:underline;font-weight:400;margin-left:8px;">Semua</span>
        <span onclick="cmpAllOutlets('${mode}',false)" style="cursor:pointer;color:#9B93C4;text-decoration:underline;font-weight:400;margin-left:10px;">Kosongkan</span>
      </div>
      <div style="display:flex;flex-wrap:wrap;gap:7px;max-width:920px;">${outletChips}</div>
    </div>
    <div style="margin-bottom:14px;">
      <div style="font-size:11px;color:#726C9C;font-weight:600;margin-bottom:8px;">PILIH BULAN (${s.months.length} dipilih)
        <span onclick="cmpAllMonths('${mode}',true)" style="cursor:pointer;color:#9B93C4;text-decoration:underline;font-weight:400;margin-left:8px;">Semua</span>
        <span onclick="cmpAllMonths('${mode}',false)" style="cursor:pointer;color:#9B93C4;text-decoration:underline;font-weight:400;margin-left:10px;">Terakhir saja</span>
      </div>
      <div style="display:flex;flex-wrap:wrap;gap:7px;max-width:920px;">${monthChips}</div>
    </div>
    <div style="margin-bottom:16px;">
      <span onclick="cmpTogglePct('${mode}')" style="cursor:pointer;display:inline-block;padding:6px 13px;border-radius:20px;font-size:12px;font-weight:600;border:1px solid ${s.pct?accent:'#2A2650'};color:${s.pct?accent:'#726C9C'};background:${s.pct?accent+'1a':'transparent'};">${s.pct?'✓ ':''}Tampilkan % thd Omset</span>
    </div>
    <div style="background:#171433;border:1px solid #2A2650;border-radius:12px;padding:18px 20px;overflow-x:auto;">
      <div style="font-family:'Space Grotesk',sans-serif;font-size:13px;font-weight:700;color:${accent};margin-bottom:4px;">PERBANDINGAN CUSTOM — ${metricLabel.toUpperCase()}${mode==='franchise'?' (VERSI FRANCHISE, HPP 60%)':''}</div>
      <div style="font-size:10.5px;color:#726C9C;margin-bottom:12px;">Baris = bakery, kolom = bulan. Diurutkan dari TOTAL terbesar. ${mode==='franchise'?'Outlet bertanda ● sudah punya data sheet Online; tanpa data akan tampil "–".':''}</div>
      ${body}
    </div>`;
}

// ============ KOMPARASI: PNL CABANG BIASA vs VERSI FRANCHISE ============
// Tujuan: menjawab keraguan awal user thd HPP yg tercatat. Modul ini
// menunjukkan PERSIS di bakery & bulan mana kedua versi berbeda, dan seberapa
// besar. Omset seharusnya nyaris identik -- kalau tidak, itu tanda masalah data.
// ============ SANDING: PNL CABANG BIASA vs VERSI FRANCHISE ============
// Ditampilkan BERDAMPINGAN dgn struktur masing2 (bukan tabel selisih), krn
// kedua versi punya baris yg memang berbeda -- versi Franchise memecah
// Pendapatan per kanal & HPP-nya dihitung 60%, versi Biasa memakai baris
// apa adanya dari buku besar.
// Filter bakery bentuk TOGGLE multi-select (bukan single-select spt versi
// lama) -- bisa pilih beberapa bakery sekaligus utk dibandingkan berurutan.
// PENTING: tiap bakery tetap dapat blok ringkasan + panelnya SENDIRI2, angka
// TIDAK pernah digabung/dijumlah jadi 1 total lintas-bakery (skala tiap
// bakery beda2 shg "total gabungan" tak informatif / bisa menyesatkan).
let diffOutlets = null;
function diffEnsure(){
  if (!diffOutlets || !diffOutlets.size) {
    const sorted = [...OUTLET_KEYS].sort((a,b)=>UNIT_DATA[a].label.localeCompare(UNIT_DATA[b].label,'id'));
    const withData = new Set(OUTLET_KEYS.filter(k => parseOnlineRowsRaw().some(r => r.entity === ENTITY_KEY_MAP[k])));
    diffOutlets = new Set([sorted.find(k=>withData.has(k)) || sorted[0]]);
  }
}
function toggleDiffOutlet(k){
  diffEnsure();
  if (diffOutlets.has(k)) { if (diffOutlets.size>1) diffOutlets.delete(k); }
  else diffOutlets.add(k);
  render();
}
function diffSelectAll(on){
  diffEnsure();
  const sorted = [...OUTLET_KEYS].sort((a,b)=>UNIT_DATA[a].label.localeCompare(UNIT_DATA[b].label,'id'));
  diffOutlets = on ? new Set(OUTLET_KEYS) : new Set([sorted[0]]);
  render();
}
// Cuma relevan kalau bakery yg ditoggle >1: pilih mau lihat KEDUA versi
// (blok terpisah per bakery, spt default) atau cuma 1 metode supaya bisa
// bandingkan antar-bakery panel-berdampingan (sejajar, bukan ditumpuk).
let diffMethod = 'both'; // 'both' | 'biasa' | 'franchise'
function setDiffMethod(m){ diffMethod = m; render(); }

// Hitung 1 blok (ringkasan + baris Biasa + baris Franchise) utk SATU bakery.
// Dipanggil berulang per bakery yg ditoggle -- lihat catatan di atas kenapa
// tak pernah dijumlah lintas-bakery.
function computeCabangDiffBlock(outletKey, selMonths){
  const u = UNIT_DATA[outletKey];
  const sumIdx = (arr)=> arr ? selMonths.reduce((t,i)=>t+(arr[i]||0),0) : null;
  const revBiasa = sumIdx(getLineVals(u,'Pendapatan')) || 0;
  const rowsBiasa = (u.waterfall||[]).map(r=>{
    const v = sumIdx(r.values);
    return { name:r.name, v, hl:!!r.highlight, pct: revBiasa? (v/revBiasa*100):null };
  });

  const d = computeFranchiseOutlet(outletKey, selMonths);
  const revFr = d.jumlahPendapatan || 0;
  const rowsFr = !d.hasOnlineData ? null : [
    { name:'Pendapatan Offline', v:d.pendOffline },
    { name:'Pendapatan Online Harga Normal', v:d.pendOnlineHN },
    { name:'Pendapatan Online Adjustment Harga', v:d.pendOnlineAdj },
    { name:'Pendapatan Konsinyasi', v:d.pendKonsinyasi },
    { name:'Jumlah Pendapatan', v:d.jumlahPendapatan, hl:true },
    { name:'Diskon Online', v:d.diskonOnline },
    { name:'Diskon Offline', v:d.diskonOffline },
    { name:'Komisi Online', v:d.komisiOnline },
    { name:'Jumlah Diskon & Komisi', v:(d.diskonOnline + d.diskonOffline + d.komisiOnline), sub:true },
    { name:'Pendapatan Bersih', v:d.pendapatanBersih, hl:true },
    { name:'HPP Produk Online (60%)', v:-d.hppOnline, est:true },
    { name:'HPP Produk Offline (60%)', v:-d.hppOffline, est:true },
    { name:'HPP Konsinyasi', v:-d.hppKonsinyasi },
    ...(d.hppPembelianLangsung ? [{ name:'HPP Pembelian Langsung', v:-d.hppPembelianLangsung }] : []),
    { name:'Jumlah HPP', v:-d.jumlahHpp, sub:true },
    { name:'Laba Kotor', v:d.labaKotor, hl:true },
    { name:'Biaya Operasional', v:-d.biayaOps },
    { name:'Laba Operasional', v:d.labaOperasional, hl:true },
    { name:'Biaya Penyusutan', v:-d.biayaPenyusutan },
    { name:'Biaya Pajak', v:-d.biayaPajak },
    ...(d.costOfMgmt ? [{ name:'Cost of Management', v:-d.costOfMgmt }] : []),
    { name:'Laba Bersih', v:d.labaBersih, hl:true },
  ].map(r=>({ ...r, pct: revFr? (r.v/revFr*100):null }));

  const getB = (nm)=>{ const r=rowsBiasa.find(x=>x.name===nm); return r?r.v:null; };
  const getF = (nm)=>{ if(!rowsFr) return null; const r=rowsFr.find(x=>x.name===nm); return r?r.v:null; };
  const kunci = [
    { label:'Omset / Jumlah Pendapatan', b:getB('Pendapatan'), f:getF('Jumlah Pendapatan') },
    { label:'Laba Kotor',        b:getB('Laba Kotor'),        f:getF('Laba Kotor') },
    { label:'Laba Operasional',  b:getB('Laba Operasional'),  f:getF('Laba Operasional') },
    { label:'Laba Bersih',       b:getB('Laba Bersih'),       f:getF('Laba Bersih') },
  ];
  const ringkas = kunci.map(k=>{
    const diff = (k.b!=null && k.f!=null) ? k.f-k.b : null;
    const pct = (diff!=null && k.b) ? diff/Math.abs(k.b)*100 : null;
    return { ...k, diff, pct, big: pct!=null && Math.abs(pct)>10 };
  });

  return { outletKey, label:u.label, rowsBiasa, rowsFr, ringkas };
}

// Render 1 panel (kartu) versi mockup_pnl_gabungan.html Opsi 2 -- tiap baris
// tetap menampilkan Rp DAN % thd Omset versinya sendiri (kolom paling kanan).
function cdPanelHtml(judul, warna, rows, catatan){
  const body = rows ? rows.map(r=>`<tr style="${r.hl?'background:#1C1840;':(r.sub?'background:#141130;':'')}">
      <td style="padding:7px 12px;font-size:12px;color:${r.hl?'#FFC93C':(r.sub?'#9B93C4':'#F5F3FF')};font-weight:${r.hl?700:(r.sub?600:400)};border-bottom:1px solid #2A2650;${r.sub?'border-top:1px solid #3A3560;padding-left:20px;':''}">${r.sub?'↳ ':''}${r.name}${r.est?'<span class="cd-est-tag">EST</span>':''}</td>
      <td class="mono ${r.v<0?'neg':''}" style="text-align:right;padding:7px 12px;border-bottom:1px solid #2A2650;white-space:nowrap;font-weight:${r.hl||r.sub?700:400};${r.sub?'border-top:1px solid #3A3560;':''}">${r.v==null?'–':fmtRp(r.v)}</td>
      <td class="mono" style="text-align:right;padding:7px 12px;border-bottom:1px solid #2A2650;color:#726C9C;font-size:10.5px;white-space:nowrap;${r.sub?'border-top:1px solid #3A3560;':''}">${r.pct==null||r.v===0?'':r.pct.toFixed(1)+'%'}</td>
    </tr>`).join('')
    : `<tr><td colspan="3" style="padding:20px;text-align:center;color:#9B93C4;font-size:12px;">Belum ada data sheet Online utk bakery/periode ini, jadi versi Franchise tak bisa dihitung.</td></tr>`;
  return `<div class="cd-panel" style="border-color:${warna};">
      <div class="cd-panel-head">
        <div class="tag" style="color:${warna};">${judul}</div>
        <div class="note">${catatan}</div>
      </div>
      <table style="width:100%;border-collapse:collapse;">
        <thead><tr>
          <th style="text-align:left;padding:8px 12px;font-size:10.5px;color:#726C9C;text-transform:uppercase;border-bottom:1px solid #2A2650;">Keterangan</th>
          <th style="text-align:right;padding:8px 12px;font-size:10.5px;color:#726C9C;border-bottom:1px solid #2A2650;">Nilai</th>
          <th style="text-align:right;padding:8px 12px;font-size:10.5px;color:#726C9C;border-bottom:1px solid #2A2650;">% Omset</th>
        </tr></thead>
        <tbody>${body}</tbody>
      </table>
    </div>`;
}

function renderCabangDiff(){
  cmpEnsure('diff');
  diffEnsure();
  const s = cmpState.diff;
  const sorted = [...OUTLET_KEYS].sort((a,b)=>UNIT_DATA[a].label.localeCompare(UNIT_DATA[b].label,'id'));
  const withData = new Set(OUTLET_KEYS.filter(k => parseOnlineRowsRaw().some(r => r.entity === ENTITY_KEY_MAP[k])));
  const selMonths = [...s.months].sort((a,b)=>a-b);
  const periodeTxt = selMonths.map(i=>periodLabel(PERIODS[i],true)).join(', ');

  const outletChips = sorted.map(k=>{
    const on = diffOutlets.has(k), has = withData.has(k);
    return `<div onclick="toggleDiffOutlet('${k}')" style="cursor:pointer;padding:6px 12px;border-radius:20px;font-size:12px;font-weight:600;font-family:'Space Grotesk',sans-serif;border:1px solid ${on?'#E066FF':'#2A2650'};color:${on?'#E066FF':(has?'#9B93C4':'#4A4568')};background:${on?'#E066FF1a':'transparent'};">${UNIT_DATA[k].label}${has?' ●':''}</div>`;
  }).join('');
  const monthChips = PERIODS.map((p,i)=>{
    const on = selMonths.includes(i);
    return `<div onclick="cmpToggleMonth('diff',${i})" style="cursor:pointer;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:600;font-family:'Space Grotesk',sans-serif;border:1px solid ${on?'#FFC93C':'#2A2650'};color:${on?'#FFC93C':'#726C9C'};background:${on?'#FFC93C22':'transparent'};">${periodLabel(p,true)}</div>`;
  }).join('');

  const selOutlets = sorted.filter(k=>diffOutlets.has(k));
  const multiMode = diffMethod !== 'both' && selOutlets.length > 1;

  // ---- Blok default: tiap bakery dpt ringkasan selisih + dual panel sendiri2 ----
  const blocks = selOutlets.map((k,idx)=>{
    const blk = computeCabangDiffBlock(k, selMonths);
    const ringkasRows = blk.ringkas.map(r=>`<tr>
        <td style="padding:8px 12px;font-size:12px;font-weight:600;border-bottom:1px solid #2A2650;">${r.label}</td>
        <td class="mono ${r.b<0?'neg':''}" style="text-align:right;padding:8px 12px;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;white-space:nowrap;color:#4ADE80;">${r.b==null?'–':fmtRp(r.b)}</td>
        <td class="mono ${r.f<0?'neg':''}" style="text-align:right;padding:8px 12px;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;white-space:nowrap;color:#22D3C5;">${r.f==null?'–':fmtRp(r.f)}</td>
        <td class="mono" style="text-align:right;padding:8px 12px;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;white-space:nowrap;font-weight:700;color:${r.diff==null?'#4A4568':(r.big?'#FB7185':'#9B93C4')};">${r.diff==null?'–':fmtRp(r.diff)}</td>
        <td class="mono" style="text-align:right;padding:8px 12px;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;white-space:nowrap;color:${r.diff==null?'#4A4568':(r.big?'#FB7185':'#9B93C4')};">${r.pct==null?'–':(r.big?'⚠ ':'')+r.pct.toFixed(1)+'%'}</td>
      </tr>`).join('');
    return `<div class="cd-block">
      <div class="cd-block-title"><span class="n">${idx+1}</span> ${blk.label}</div>
      <div style="background:#171433;border:1px solid #2A2650;border-radius:12px;padding:16px 18px;margin-bottom:14px;overflow-x:auto;">
        <div style="font-family:'Space Grotesk',sans-serif;font-size:12.5px;font-weight:700;color:#E066FF;margin-bottom:10px;">RINGKASAN SELISIH · ${periodeTxt}</div>
        <table style="width:100%;border-collapse:collapse;min-width:560px;">
          <thead><tr>
            <th style="text-align:left;padding:8px 12px;font-size:10.5px;color:#726C9C;text-transform:uppercase;border-bottom:1px solid #2A2650;">Baris Kunci</th>
            <th style="text-align:right;padding:8px 12px;font-size:10.5px;color:#4ADE80;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">Biasa</th>
            <th style="text-align:right;padding:8px 12px;font-size:10.5px;color:#22D3C5;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">Franchise</th>
            <th style="text-align:right;padding:8px 12px;font-size:10.5px;color:#E066FF;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">Selisih</th>
            <th style="text-align:right;padding:8px 12px;font-size:10.5px;color:#E066FF;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">Selisih %</th>
          </tr></thead>
          <tbody>${ringkasRows}</tbody>
        </table>
      </div>
      <div class="cd-dual-grid">
        ${cdPanelHtml('PNL CABANG (BIASA)', '#4ADE80', blk.rowsBiasa, 'Struktur buku besar apa adanya. HPP memakai angka tercatat.')}
        ${cdPanelHtml('VERSI FRANCHISE', '#22D3C5', blk.rowsFr, 'Pendapatan dipecah per kanal. HPP Produk dibatasi 60% (badge EST = estimasi).')}
      </div>
    </div>`;
  }).join('');

  // ---- Opsi (muncul kalau bakery yg ditoggle >1): pilih 1 metode saja spy
  // panel tiap bakery bisa ditaruh SEJAJAR (bandingkan antar-bakery), bukan
  // ditumpuk per blok spt default di atas. ----
  const methodChips = diffOutlets.size > 1 ? `
    <div style="margin-bottom:20px;">
      <div style="font-size:11px;color:#726C9C;font-weight:600;margin-bottom:8px;">METODE UTK BANDINGKAN ANTAR-BAKERY</div>
      <div style="display:flex;flex-wrap:wrap;gap:7px;">
        ${[['both','Kedua Versi (blok per bakery)'],['biasa','Metode Biasa saja (sejajar)'],['franchise','Metode Franchise saja (sejajar)']].map(([val,lbl])=>{
          const on = diffMethod===val;
          return `<div onclick="setDiffMethod('${val}')" style="cursor:pointer;padding:6px 14px;border-radius:20px;font-size:12px;font-weight:600;font-family:'Space Grotesk',sans-serif;border:1px solid ${on?'#FFC93C':'#2A2650'};color:${on?'#FFC93C':'#726C9C'};background:${on?'#FFC93C22':'transparent'};">${lbl}</div>`;
        }).join('')}
      </div>
    </div>` : '';

  let bodyHtml;
  if (multiMode) {
    const warna = diffMethod==='biasa' ? '#4ADE80' : '#22D3C5';
    const catatanM = diffMethod==='biasa'
      ? 'Struktur buku besar apa adanya. HPP memakai angka tercatat.'
      : 'Pendapatan dipecah per kanal. HPP Produk dibatasi 60% (badge EST = estimasi).';
    const panelsHtml = selOutlets.map(k=>{
      const blk = computeCabangDiffBlock(k, selMonths);
      const rows = diffMethod==='biasa' ? blk.rowsBiasa : blk.rowsFr;
      return cdPanelHtml(blk.label, warna, rows, catatanM);
    }).join('');
    bodyHtml = `<div style="font-size:10.5px;color:#726C9C;margin-bottom:14px;">Metode <b>${diffMethod==='biasa'?'Biasa':'Franchise'}</b> dipakai utk semua bakery di bawah, ditampilkan sejajar spy gampang dibandingkan (geser ke samping kalau bakery-nya banyak). % dihitung thd Omset bakery itu sendiri, bukan thd total gabungan.</div>
      <div class="cd-multi-grid">${panelsHtml}</div>`;
  } else {
    bodyHtml = blocks || '<div style="padding:20px;text-align:center;color:#726C9C;font-size:12.5px;">Pilih minimal 1 bakery.</div>';
  }

  return `
    <div style="margin-bottom:14px;">
      <div style="font-size:11px;color:#726C9C;font-weight:600;margin-bottom:8px;">PILIH BAKERY (${diffOutlets.size} dipilih, ● = ada data sheet Online)
        <span onclick="diffSelectAll(true)" style="cursor:pointer;color:#9B93C4;text-decoration:underline;font-weight:400;margin-left:8px;">Semua</span>
        <span onclick="diffSelectAll(false)" style="cursor:pointer;color:#9B93C4;text-decoration:underline;font-weight:400;margin-left:10px;">Kosongkan</span>
      </div>
      <div style="display:flex;flex-wrap:wrap;gap:7px;max-width:920px;">${outletChips}</div>
    </div>
    <div style="margin-bottom:20px;">
      <div style="font-size:11px;color:#726C9C;font-weight:600;margin-bottom:8px;">PILIH BULAN (${selMonths.length} dipilih, digabung)
        <span onclick="cmpAllMonths('diff',true)" style="cursor:pointer;color:#9B93C4;text-decoration:underline;font-weight:400;margin-left:8px;">Semua</span>
        <span onclick="cmpAllMonths('diff',false)" style="cursor:pointer;color:#9B93C4;text-decoration:underline;font-weight:400;margin-left:10px;">Terakhir saja</span>
      </div>
      <div style="display:flex;flex-wrap:wrap;gap:7px;max-width:920px;">${monthChips}</div>
    </div>
    ${methodChips}
    ${multiMode ? '' : `<div style="font-size:10.5px;color:#726C9C;margin-bottom:18px;">Tiap bakery tampil sbg blok terpisah (ringkasan + panel Biasa | Franchise sendiri2) -- angkanya <b>tidak digabung/dijumlah</b> antar bakery, krn skala tiap bakery beda2 shg total gabungan tak bermakna. Per bakery, Omset "Biasa" vs "Jumlah Pendapatan" Franchise seharusnya nyaris identik; kalau selisihnya besar, itu tanda masalah data.</div>`}
    ${bodyHtml}`;
}
function renderGrafikCustom(){
  grafikRangeDefaults();
  const chartOk = typeof Chart !== 'undefined';
  const diagBox = chartOk ? '' : `<div id="grafikDiagBox" style="background:#2A1B1B;border:1px solid #FB7185;border-radius:10px;padding:10px 14px;margin-bottom:16px;font-family:monospace;font-size:11px;color:#FB7185;">
    ⚠️ Chart.js gagal dimuat -- grafik tak bisa ditampilkan. Build ${GRAFIK_BUILD_VERSION}.
  </div>`;
  const segChips = GRAFIK_SEGMENTS.map(s => {
    const on = grafikSegment === s.key;
    return `<div onclick="setGrafikSegment('${s.key}')" style="cursor:pointer;padding:7px 14px;border-radius:20px;font-size:12.5px;font-weight:700;font-family:'Space Grotesk',sans-serif;border:1px solid ${on?'#4FC3F7':'#2A2650'};color:${on?'#4FC3F7':'#726C9C'};background:${on?'#4FC3F71a':'transparent'};">${s.label}</div>`;
  }).join('');
  const allOn = GRAFIK_METRICS.every(m=>grafikSelected.has(m.key));
  const chips = GRAFIK_METRICS.map(m => {
    const on = grafikSelected.has(m.key);
    return `<div onclick="toggleGrafikMetric('${m.key}')" style="cursor:pointer;padding:7px 14px;border-radius:20px;font-size:12.5px;font-weight:600;font-family:'Space Grotesk',sans-serif;border:1px solid ${on?m.color:'#2A2650'};color:${on?m.color:'#726C9C'};background:${on?m.color+'1a':'transparent'};">${m.label}</div>`;
  }).join('');
  const rangeOpts = PERIODS.map((p,i) => `<option value="${i}">${periodLabel(p,true)}</option>`);
  const segLabel = (GRAFIK_SEGMENTS.find(s=>s.key===grafikSegment)||{}).label || 'Konsolidasi';
  return `
    ${diagBox}
    <div style="margin-bottom:16px;">
      <div style="font-size:11px;color:#726C9C;font-weight:600;margin-bottom:8px;">PILIH SEGMEN:</div>
      <div style="display:flex;flex-wrap:wrap;gap:8px;">${segChips}</div>
    </div>
    <div style="margin-bottom:16px;">
      <div style="font-size:11px;color:#726C9C;font-weight:600;margin-bottom:8px;">PILIH METRIK:</div>
      <div style="display:flex;flex-wrap:wrap;gap:8px;align-items:center;">
        ${chips}
        <div onclick="setGrafikAllMetrics(${!allOn})" style="cursor:pointer;padding:7px 14px;border-radius:20px;font-size:12.5px;font-weight:700;font-family:'Space Grotesk',sans-serif;border:1px solid #E066FF;color:#E066FF;background:${allOn?'#E066FF1a':'transparent'};">${allOn?'✓ Semua Dipilih':'Pilih Semua'}</div>
      </div>
    </div>
    <div style="display:flex;gap:14px;align-items:center;margin-bottom:20px;flex-wrap:wrap;">
      <div>
        <div style="font-size:11px;color:#726C9C;font-weight:600;margin-bottom:6px;">DARI:</div>
        <select onchange="setGrafikFrom(this.value)" style="background:#171433;border:1px solid #2A2650;color:#F5F3FF;border-radius:8px;padding:7px 12px;font-size:12.5px;">
          ${PERIODS.map((p,i)=>`<option value="${i}" ${grafikFromIdx===i?'selected':''}>${periodLabel(p,true)}</option>`).join('')}
        </select>
      </div>
      <div style="color:#726C9C;margin-top:18px;">→</div>
      <div>
        <div style="font-size:11px;color:#726C9C;font-weight:600;margin-bottom:6px;">SAMPAI:</div>
        <select onchange="setGrafikTo(this.value)" style="background:#171433;border:1px solid #2A2650;color:#F5F3FF;border-radius:8px;padding:7px 12px;font-size:12.5px;">
          ${PERIODS.map((p,i)=>`<option value="${i}" ${grafikToIdx===i?'selected':''}>${periodLabel(p,true)}</option>`).join('')}
        </select>
      </div>
      <div style="font-size:10.5px;color:#726C9C;margin-top:20px;">Rentang mencakup ${PERIODS.length} bulan data yg tersedia (${periodLabel(PERIODS[0],true)} – ${periodLabel(PERIODS[PERIODS.length-1],true)}).</div>
    </div>
    <div style="background:#171433;border:1px solid #2A2650;border-radius:12px;padding:16px;height:380px;position:relative;">
      <div id="grafikCustomChart" style="width:100%;height:100%;"></div>
    </div>
    <div style="font-size:10.5px;color:#726C9C;margin-top:10px;">Diskon, HPP, HPP Bahan Baku &amp; Opex ditampilkan sbg <b>% dari Pendapatan bulan yg sama</b> (sumbu kanan) -- Pendapatan, Laba Kotor &amp; Laba Operasional tetap Rupiah (sumbu kiri), krn skalanya jauh lebih besar &amp; tak sebanding kalau digabung. Diskon ditampilkan <b>positif</b> (besaran diskon thd Pendapatan) supaya tren naik terbaca naik, bukan turun -- di tabel P&amp;L lain tetap negatif spt biasa (pengurang).</div>
    <div style="font-size:10.5px;color:#726C9C;margin-top:6px;">⚠ HPP Bahan Baku: dipakai langsung dari baris waterfall segmen ini kalau ada (mis. Manufaktur); kalau tidak ada (mis. Konsolidasi), diambil dari data mentah -- mungkin kosong kalau segmen <b>${segLabel}</b> tak punya rincian ini.</div>
  `;
}
let grafikChartInstance = null;
// Tambahkan info diagnostik DETAIL (jumlah dataset, contoh nilai) ke banner --
// dipanggil dari tiap fungsi drawXXXChart supaya user tinggal copy-paste 1
// blok teks ke saya, tanpa perlu screenshot.
function appendGrafikDiag(label, ds){
  const el = document.getElementById('grafikDiagDetail');
  if (!el) return;
  const lines = ds.map(d => {
    const nonNull = d.data.filter(v=>v!=null);
    const sample = nonNull.slice(0,2).map(v=>Math.round(v)).join(', ');
    return `${d.label}: ${d.data.length} titik, ${nonNull.length} terisi${nonNull.length?' (contoh: '+sample+')':' (SEMUA KOSONG/NULL)'}`;
  });
  el.innerHTML += `<div style="margin-top:6px;padding-top:6px;border-top:1px solid #2A2650;"><b>${label}</b> -- ${ds.length} dataset:<br>${lines.map(l=>'&nbsp;&nbsp;'+l).join('<br>')}</div>`;
}
// Paksa ukuran BUFFER GAMBAR canvas secara eksplisit (bukan cuma CSS) --
// dipakai SEBELUM new Chart(), lalu Chart dibuat dgn responsive:false supaya
// tak bergantung pada mesin auto-sizing Chart.js yg mungkin bermasalah di
// browser/environment tertentu (Safari iPad khususnya).
// ============ RENDERER GRAFIK SVG (TANPA Chart.js) ============
// Dibuat setelah Chart.js berulang kali gagal render (data & ukuran sudah
// terbukti benar, tapi canvas tetap kosong tanpa error). SVG murni tak
// bergantung library eksternal apa pun -- kalau ini jg kosong, masalahnya
// pasti bukan soal Chart.js, tapi sesuatu yg lebih dasar (mis. CSS/browser).
// Scatter SVG -- utk Grafik #4 (Sebaran Profitabilitas Outlet). Beda dari
// line chart: sumbu X & Y sama2 kontinu (bukan kategori bulan), dan tiap
// titik dilabeli NAMA langsung (bukan angka), sesuai permintaan user.
function renderSvgScatterChart(containerId, points, opts={}){
  const container = document.getElementById(containerId);
  if (!container) return;
  const W = opts.width || container.clientWidth || 700;
  const H = opts.height || container.clientHeight || 380;
  const padL = 70, padR = 30, padT = 30, padB = 50;
  const plotW = W - padL - padR, plotH = H - padT - padB;

  if (!points.length) {
    container.innerHTML = `<div style="display:flex;align-items:center;justify-content:center;height:100%;color:#726C9C;font-size:12.5px;text-align:center;padding:16px;">${opts.emptyMsg || 'Tidak ada data utk periode ini.'}</div>`;
    return;
  }
  const xs = points.map(p=>p.x), ys = points.map(p=>p.y);
  let xMin = Math.min(...xs, 0), xMax = Math.max(...xs, 0);
  let yMin = Math.min(...ys, 0), yMax = Math.max(...ys, 0);
  if (xMin===xMax) { xMin-=1; xMax+=1; }
  if (yMin===yMax) { yMin-=1; yMax+=1; }
  const xPad = (xMax-xMin)*0.08, yPad = (yMax-yMin)*0.12;
  xMin-=xPad; xMax+=xPad; yMin-=yPad; yMax+=yPad;
  const xAt = (v) => padL + ((v-xMin)/(xMax-xMin)) * plotW;
  const yAt = (v) => padT + plotH - ((v-yMin)/(yMax-yMin)) * plotH;

  let svg = `<svg viewBox="0 0 ${W} ${H}" style="width:100%;height:100%;display:block;font-family:'Space Grotesk',sans-serif;">`;
  for (let g=0; g<=4; g++){
    const y = padT + plotH*g/4;
    svg += `<line x1="${padL}" y1="${y}" x2="${W-padR}" y2="${y}" stroke="#2A2650" stroke-width="1"/>`;
    const yv = yMax - (yMax-yMin)*g/4;
    svg += `<text x="${padL-8}" y="${y+3}" fill="#9B93C4" font-size="9.5" text-anchor="end">${(yv/1e6).toFixed(0)} Jt</text>`;
  }
  for (let g=0; g<=4; g++){
    const x = padL + plotW*g/4;
    const xv = xMin + (xMax-xMin)*g/4;
    svg += `<text x="${x}" y="${H-padB+18}" fill="#9B93C4" font-size="9.5" text-anchor="middle">${(xv/1e6).toFixed(0)} Jt</text>`;
  }
  // garis 0 (sumbu Y=0) ditebalkan kalau ada nilai negatif
  if (yMin < 0 && yMax > 0) svg += `<line x1="${padL}" y1="${yAt(0)}" x2="${W-padR}" y2="${yAt(0)}" stroke="#4A4568" stroke-width="1.5"/>`;

  // Titik-titik yg berdekatan (omset mirip) bikin label numpuk kalau ditaruh
  // rata di atas semua titik. Di sini label digeser NAIK BERTAHAP (kayak
  // tangga) tiap kali posisi defaultnya nabrak label lain yg sudah ditaruh,
  // diurutkan dari kiri ke kanan spy pola tangganya konsisten & enak dibaca.
  const positioned = points.map(p => ({ ...p, px: xAt(p.x), py: yAt(p.y) }));
  positioned.sort((a,b) => a.px - b.px);
  const placedBoxes = [];
  const charW = 5.6, labelH = 13;
  positioned.forEach(p => {
    const textW = p.n.length * charW;
    let offset = 12, tries = 0, placed = false;
    while (!placed && tries < 25) {
      const lx1 = p.px - textW/2, lx2 = p.px + textW/2;
      const ly1 = p.py - offset - labelH, ly2 = p.py - offset;
      const overlap = placedBoxes.some(b => !(lx2 < b.x1-2 || lx1 > b.x2+2 || ly2 < b.y1-2 || ly1 > b.y2+2));
      if (!overlap) { placed = true; placedBoxes.push({x1:lx1,y1:ly1,x2:lx2,y2:ly2}); p.labelOffset = offset; }
      else { offset += labelH + 3; tries++; }
    }
    if (!placed) p.labelOffset = offset;
  });
  points.forEach(p => {
    svg += `<circle cx="${xAt(p.x)}" cy="${yAt(p.y)}" r="7" fill="${p.color}" stroke="#171433" stroke-width="1.5"><title>${p.n}: Omset ${fmtRp(p.x)}, ${opts.yLabel||'Nilai'} ${fmtRp(p.y)}</title></circle>`;
  });
  positioned.forEach(p => {
    const ly = p.py - p.labelOffset;
    // kalau label digeser cukup jauh dari titiknya, kasih garis penghubung tipis biar tetap jelas ini punya titik mana
    if (p.labelOffset > 16) svg += `<line x1="${p.px}" y1="${p.py-6}" x2="${p.px}" y2="${ly+3}" stroke="${p.color}" stroke-width="1" stroke-dasharray="2,2" opacity="0.5"/>`;
    svg += `<text x="${p.px}" y="${ly}" fill="#C9C3E8" font-size="10" text-anchor="middle">${p.n}</text>`;
  });
  svg += `<text x="${padL+plotW/2}" y="${H-10}" fill="#9B93C4" font-size="10.5" text-anchor="middle">Omset</text>`;
  svg += `<text x="16" y="${padT+plotH/2}" fill="#9B93C4" font-size="10.5" text-anchor="middle" transform="rotate(-90 16 ${padT+plotH/2})">${opts.yLabel||'Nilai'}</text>`;
  svg += `</svg>`;
  container.innerHTML = svg;
}
function renderSvgLineChart(containerId, datasets, labels, opts={}){
  const container = document.getElementById(containerId);
  if (!container) return;
  const W = opts.width || container.clientWidth || 700;
  const H = opts.height || container.clientHeight || 340;
  const padL = 60, padR = opts.dualAxis ? 55 : 20, padT = 30, padB = 40;
  const plotW = W - padL - padR, plotH = H - padT - padB;

  const valDs = datasets.filter(d => !d.isCost);
  const costDs = datasets.filter(d => d.isCost);
  const allValNums = valDs.flatMap(d=>d.data).filter(v=>v!=null);
  const allCostNums = costDs.flatMap(d=>d.data).filter(v=>v!=null);
  const valMax = allValNums.length ? Math.max(...allValNums, 0) : 1;
  const valMin = allValNums.length ? Math.min(...allValNums, 0) : 0;
  const costMax = allCostNums.length ? Math.max(...allCostNums, 0) : 1;
  const costMin = allCostNums.length ? Math.min(...allCostNums, 0) : 0;

  if (!datasets.length || (!allValNums.length && !allCostNums.length)) {
    container.innerHTML = `<div style="display:flex;align-items:center;justify-content:center;height:100%;color:#726C9C;font-size:12.5px;">Tidak ada data utk ditampilkan pada pilihan saat ini.</div>`;
    return;
  }

  const n = labels.length;
  const xAt = (i) => padL + (n<=1 ? plotW/2 : (plotW * i/(n-1)));
  const yAtVal = (v) => padT + plotH - ((v-valMin)/((valMax-valMin)||1)) * plotH;
  const yAtCost = (v) => padT + plotH - ((v-costMin)/((costMax-costMin)||1)) * plotH;

  let svg = `<svg viewBox="0 0 ${W} ${H}" style="width:100%;height:100%;display:block;font-family:'Space Grotesk',sans-serif;">`;
  // grid horizontal (5 garis)
  for (let g=0; g<=4; g++){
    const y = padT + plotH*g/4;
    svg += `<line x1="${padL}" y1="${y}" x2="${W-padR}" y2="${y}" stroke="#2A2650" stroke-width="1"/>`;
  }
  // label sumbu X (bulan)
  labels.forEach((lb,i) => {
    if (n>8 && i%2!==0 && i!==n-1) return; // biar tak numpuk kalau bulan banyak
    svg += `<text x="${xAt(i)}" y="${H-14}" fill="#9B93C4" font-size="10" text-anchor="middle">${lb}</text>`;
  });
  // label sumbu Y kiri (value)
  if (allValNums.length) {
    for (let g=0; g<=4; g++){
      const v = valMax - (valMax-valMin)*g/4;
      const y = padT + plotH*g/4;
      svg += `<text x="${padL-8}" y="${y+3}" fill="#9B93C4" font-size="9.5" text-anchor="end">${(v/1e9).toFixed(1)} M</text>`;
    }
  }
  // label sumbu Y kanan (%, kalau ada dual axis)
  if (opts.dualAxis && allCostNums.length) {
    for (let g=0; g<=4; g++){
      const v = costMax - (costMax-costMin)*g/4;
      const y = padT + plotH*g/4;
      svg += `<text x="${W-padR+8}" y="${y+3}" fill="#726C9C" font-size="9.5" text-anchor="start">${v.toFixed(0)}%</text>`;
    }
  }
  // garis tiap dataset
  datasets.forEach(ds => {
    const yFn = ds.isCost ? yAtCost : yAtVal;
    const pts = ds.data.map((v,i) => v==null ? null : `${xAt(i)},${yFn(v)}`).filter(Boolean);
    if (pts.length >= 2) {
      svg += `<polyline points="${pts.join(' ')}" fill="none" stroke="${ds.color}" stroke-width="2.2" stroke-linejoin="round" stroke-linecap="round"/>`;
    }
    ds.data.forEach((v,i) => {
      if (v==null) return;
      svg += `<circle cx="${xAt(i)}" cy="${yFn(v)}" r="3" fill="${ds.color}"><title>${ds.label}: ${labels[i]} = ${opts.dualAxis && ds.isCost ? v.toFixed(1)+'%' : fmtRp(v)}</title></circle>`;
    });
  });
  svg += `</svg>`;

  // legenda di bawah SVG
  const legend = datasets.map(d => `<span style="display:inline-flex;align-items:center;gap:5px;margin-right:14px;font-size:11px;color:#C9C3E8;"><span style="width:9px;height:9px;border-radius:50%;background:${d.color};display:inline-block;"></span>${d.label}</span>`).join('');
  container.innerHTML = `<div style="display:flex;flex-direction:column;height:100%;">
    <div style="flex:1;min-height:0;">${svg}</div>
    <div style="padding-top:8px;flex-shrink:0;">${legend}</div>
  </div>`;
}
function forceCanvasSize(canvas){
  const rect = canvas.getBoundingClientRect();
  const dpr = window.devicePixelRatio || 1;
  const w = Math.max(Math.round(rect.width) || 700, 300);
  const h = Math.max(Math.round(rect.height) || 340, 200);
  canvas.width = Math.round(w * dpr);
  canvas.height = Math.round(h * dpr);
  canvas.style.width = w + 'px';
  canvas.style.height = h + 'px';
  const c2d = canvas.getContext('2d');
  if (c2d && c2d.scale) c2d.setTransform(dpr, 0, 0, dpr, 0, 0);
  return { width: w, height: h };
}
function drawGrafikCustomChart(){
  const ctx = document.getElementById('grafikCustomChart');
  if (!ctx) return;
  try {
    grafikRangeDefaults();
    const idxRange = [];
    for (let i = grafikFromIdx; i <= grafikToIdx; i++) idxRange.push(i);
    const u = UNIT_DATA[grafikSegment] || UNIT_DATA.konsolidasi;
    const labels = idxRange.map(i => periodLabel(PERIODS[i], true));
    const pendSeries = getLineVals(u,'Pendapatan') || [];
    // Item BIAYA (isCost) ditampilkan sbg % dari Pendapatan bulan yg sama,
    // di sumbu KANAN terpisah -- supaya tak campur skala dgn Pendapatan/Laba
    // yg nilainya Rupiah utuh (skala jauh lebih besar & tak sebanding).
    const ds = GRAFIK_METRICS.filter(m => grafikSelected.has(m.key)).map(m => {
      const series = m.get(u) || [];
      const data = idxRange.map(i => {
        const v = series[i];
        if (v == null) return null;
        if (!m.isCost) return v;
        const base = pendSeries[i];
        if (!base) return null;
        const pct = v/base*100;
        return m.flipSign ? -pct : pct; // Diskon: tampilkan sbg % POSITIF (besaran diskon), bukan negatif -- trennya memang naik, biar tak terbaca "turun"
      });
      return { label: m.label, data, color: m.color, isCost: m.isCost };
    });
    renderSvgLineChart('grafikCustomChart', ds, labels, { dualAxis:true });
  } catch(e){ console.error('drawGrafikCustomChart error:', e); if(ctx) ctx.innerHTML = `<div style="color:#FB7185;font-size:12px;padding:16px;">⚠️ Grafik gagal digambar: ${e.message}<br><span style="color:#726C9C;font-size:10.5px;">Screenshot pesan ini & kirim ke saya.</span></div>`; }
}

let execChartInstance = null;
function drawExecChart(u){
  const ctx = document.getElementById('execChart');
  if (!ctx) return;
  if (typeof Chart === 'undefined') {
    ctx.parentElement.innerHTML = `<div style="color:#FB7185;font-size:12px;padding:16px;">⚠️ Library Chart.js gagal dimuat (kemungkinan CDN diblokir jaringan Anda) -- grafik tak bisa digambar.<br><span style="color:#726C9C;font-size:10.5px;">Coba refresh, atau cek koneksi/pengaturan jaringan.</span></div>`;
    return;
  }
  try {
    if (execChartInstance) execChartInstance.destroy();
    forceCanvasSize(ctx);
    const vIdx = getVisibleIdx();
    const labels = currentLabels();
    const pick = (name, alt) => { const v = getLineVals(u,name) || (alt?getLineVals(u,alt):null); return v ? vIdx.map(i=>v[i]) : null; };
    const ds = [];
    const rev = pick('Pendapatan'); if (rev) ds.push({ label:'Pendapatan', data:rev, borderColor:'#4FC3F7', backgroundColor:'#4FC3F733', fill:true, tension:.3 });
    const hpp = pick('Harga Pokok Penjualan','HPP'); if (hpp) ds.push({ label:'HPP', data:hpp, borderColor:'#A78BFA', fill:false, tension:.3 });
    const opex = pick('Biaya Operasional'); if (opex) ds.push({ label:'Biaya Operasional', data:opex, borderColor:'#FFC93C', fill:false, tension:.3 });
    const lo = pick('Laba Operasional'); if (lo) ds.push({ label:'Laba Operasional', data:lo, borderColor:'#4ADE80', borderWidth:2.5, fill:false, tension:.3 });
    execChartInstance = new Chart(ctx, {
      type:'line',
      data:{ labels: vIdx.map(i=>labels[i]), datasets: ds },
      options:{ responsive:false, maintainAspectRatio:false,
        plugins:{ legend:{ labels:{ color:'#C9C3E8', font:{size:11} } },
          tooltip:{ callbacks:{ label:(c)=> c.dataset.label+': '+fmtRp(c.parsed.y) } } },
        scales:{ x:{ type:'category', ticks:{color:'#9B93C4'}, grid:{color:'#2A2650'} },
          y:{ type:'linear', ticks:{color:'#9B93C4', callback:(v)=>(v/1e9).toFixed(1)+' M'}, grid:{color:'#2A2650'} } } }
    });
  } catch(e){ console.error('drawExecChart error:', e); if(ctx&&ctx.parentElement) ctx.parentElement.innerHTML = `<div style="color:#FB7185;font-size:12px;padding:16px;">⚠️ Grafik gagal digambar: ${e.message}<br><span style="color:#726C9C;font-size:10.5px;">Screenshot pesan ini & kirim ke saya.</span></div>`; }
}

// Kartu Analisa: SEPENUHNYA dihitung dari data (delta, rasio, komponen
// penggerak terbesar) -- BUKAN penilaian manusia. "Mengapa" di sini terbatas
// pada KOMPONEN MANA yg bergerak (yg bisa dibuktikan dari angka), bukan
// sebab-akibat bisnis di lapangan yg tidak terlihat dari laporan.
function childValsOf(row){
  const out = [];
  if (!row || !row.children) return out;
  Object.entries(row.children).forEach(([name, v]) => {
    const arr = Array.isArray(v) ? v : v.vals;
    if (arr) out.push({ name, arr });
  });
  return out;
}
function renderAnalysisCard(u){
  const rev = getLineVals(u,'Pendapatan');
  if (!rev) return '';
  // vIdx = SEMUA bulan yg dipilih user di filter DAN punya data -- seluruh
  // analisa di bawah dihitung ulang dari rentang ini setiap filter berubah
  const vIdx = getVisibleIdx().filter(i => rev[i] != null);
  if (!vIdx.length) return '';
  const first = vIdx[0], last = vIdx[vIdx.length-1];
  const prev = vIdx.length>1 ? vIdx[vIdx.length-2] : undefined;
  const multi = vIdx.length > 1;

  const hpp = getLineVals(u,'Harga Pokok Penjualan') || getLineVals(u,'HPP');
  const opexRow = u.waterfall.find(r=>r.name==='Biaya Operasional');
  const opex = opexRow ? opexRow.values : null;
  const lo = getLineVals(u,'Laba Operasional');
  const lb = getLineVals(u,'Laba Bersih');
  const mL = (i) => periodLabel(PERIODS[i], false);

  const rangeLabel = multi
    ? `${mL(first)} – ${periodLabel(PERIODS[last],true)} · ${vIdx.length} bulan dipilih`
    : periodLabel(PERIODS[last], true);

  let apa=[], mengapa=[], kedepan=[];

  // === APA YANG TERJADI (rentang penuh) ===
  if (multi) {
    const totRev = vIdx.reduce((s,i)=>s+(rev[i]||0),0);
    apa.push(`Total Pendapatan periode terpilih: <b>${fmtRp(totRev)}</b> (rata-rata ${fmtRp(totRev/vIdx.length)}/bulan).`);
    const g = fmtDeltaPct(rev[last], rev[first]);
    if (g!==null) apa.push(`Pendapatan ${mL(last)} ${g>=0?'lebih tinggi':'lebih rendah'} <b>${Math.abs(g).toFixed(1)}%</b> dibanding ${mL(first)} (awal periode terpilih).`);
    if (lo) {
      const margins = vIdx.filter(i=>lo[i]!=null && rev[i]).map(i=>({i, m: lo[i]/rev[i]*100}));
      if (margins.length>1) {
        const best = margins.reduce((a,b)=>b.m>a.m?b:a);
        const worst = margins.reduce((a,b)=>b.m<a.m?b:a);
        apa.push(`Margin Laba Operasional terbaik: <b>${mL(best.i)}</b> (${best.m.toFixed(1)}%); terlemah: ${mL(worst.i)} (${worst.m.toFixed(1)}%).`);
      }
    }
    if (prev!==undefined && lo && lo[last]!=null && lo[prev]!=null) {
      const d = fmtDeltaPct(lo[last], lo[prev]);
      if (d!==null) apa.push(`Bulan terakhir terpilih (${mL(last)}): Laba Operasional <b>${fmtRp(lo[last])}</b>, ${d>=0?'naik':'turun'} ${Math.abs(d).toFixed(1)}% vs ${mL(prev)}.`);
    }
  } else {
    apa.push(`Pendapatan ${rangeLabel}: <b>${fmtRp(rev[last])}</b>.`);
    if (lo && lo[last]!=null && rev[last]) apa.push(`Laba Operasional: <b>${fmtRp(lo[last])}</b> (margin ${(lo[last]/rev[last]*100).toFixed(1)}%).`);
  }
  if (lb) {
    const negMonths = vIdx.filter(i=>lb[i]!=null && lb[i]<0);
    if (negMonths.length) apa.push(`<b>Perhatian:</b> Laba Bersih NEGATIF di ${negMonths.map(mL).join(', ')}.`);
  }

  // === MENGAPA (komponen penggerak, terukur dari data rentang) ===
  if (hpp) {
    const ratios = vIdx.filter(i=>hpp[i]!=null && rev[i]).map(i=>({i, r: hpp[i]/rev[i]*100}));
    if (ratios.length>1) {
      const rMin = ratios.reduce((a,b)=>b.r<a.r?b:a), rMax = ratios.reduce((a,b)=>b.r>a.r?b:a);
      if (rMax.r - rMin.r >= 0.5) {
        mengapa.push(`Rasio HPP bergerak antara <b>${rMin.r.toFixed(1)}%</b> (${mL(rMin.i)}) s/d <b>${rMax.r.toFixed(1)}%</b> (${mL(rMax.i)}) -- rentang ${(rMax.r-rMin.r).toFixed(1)} poin ini penggerak margin terbesar antar bulan terpilih.`);
      } else {
        mengapa.push(`Rasio HPP relatif stabil (${rMin.r.toFixed(1)}%–${rMax.r.toFixed(1)}%) di seluruh periode terpilih.`);
      }
    } else if (ratios.length===1) {
      mengapa.push(`Rasio HPP: ${ratios[0].r.toFixed(1)}% dari pendapatan.`);
    }
  }
  if (opexRow && prev!==undefined) {
    const movers = childValsOf(opexRow)
      .map(c => ({ name:c.name, d: (c.arr[last]!=null && c.arr[prev]!=null) ? c.arr[last]-c.arr[prev] : null }))
      .filter(c => c.d!==null && Math.abs(c.d) > 0)
      .sort((a,b) => Math.abs(b.d)-Math.abs(a.d))
      .slice(0,2);
    movers.forEach(mv => {
      mengapa.push(`Penggerak biaya terbesar di bulan terakhir: <b>${mv.name}</b> ${mv.d>0?'bertambah':'berkurang'} ${fmtRp(Math.abs(mv.d))} vs ${mL(prev)}.`);
    });
  }
  if (!mengapa.length) mengapa.push(`Hanya 1 bulan terpilih -- pilih 2 bulan atau lebih di filter utk melihat pergerakan komponen antar bulan.`);

  // === KE DEPAN (berlabuh ke angka rentang terpilih) ===
  if (rev[last] && hpp && hpp[last]!=null) {
    kedepan.push(`Setiap perbaikan 1 poin persen rasio HPP (skrg ${(hpp[last]/rev[last]*100).toFixed(1)}%) setara tambahan laba ± <b>${fmtRp(rev[last]*0.01)}</b>/bulan pada tingkat pendapatan ${mL(last)}.`);
  }
  if (multi && opex) {
    const oRatios = vIdx.filter(i=>opex[i]!=null && rev[i]).map(i=>({i, r: opex[i]/rev[i]*100}));
    if (oRatios.length>1) {
      const eff = oRatios.reduce((a,b)=>b.r<a.r?b:a);
      kedepan.push(`Bulan paling efisien di periode terpilih: <b>${mL(eff.i)}</b> (Biaya Operasional ${eff.r.toFixed(1)}% dari pendapatan) -- bisa dijadikan acuan target utk bulan lain.`);
    }
  }
  kedepan.push(`Rekomendasi spesifik (harga, mix produk, sewa, SDM) perlu konteks di luar laporan ini -- angka di atas menunjukkan DI MANA tekanannya, bukan tindakan persisnya.`);

  const li = (arr) => arr.map(x=>`<li style="margin-bottom:5px;">${x}</li>`).join('');
  return `<div style="background:#171433;border:1px solid #2A2650;border-left:3px solid ${u.color};border-radius:12px;padding:18px 20px;margin-bottom:18px;">
    <div style="font-family:'Space Grotesk',sans-serif;font-size:13px;font-weight:700;color:${u.color};margin-bottom:4px;">ANALISA OTOMATIS · ${rangeLabel}</div>
    <div style="font-size:10.5px;color:#726C9C;margin-bottom:12px;">Dihitung ulang setiap filter bulan berubah -- murni dari pergerakan angka pada bulan yang dipilih, bukan penilaian manusia. "Mengapa" terbatas pada komponen yang terukur di laporan.</div>
    <div style="font-size:12.5px;color:#F5F3FF;line-height:1.5;">
      <div style="font-weight:700;color:#FFC93C;margin-bottom:4px;">Apa yang terjadi</div><ul style="margin:0 0 12px;padding-left:18px;">${li(apa)}</ul>
      <div style="font-weight:700;color:#FFC93C;margin-bottom:4px;">Mengapa (komponen penggerak)</div><ul style="margin:0 0 12px;padding-left:18px;">${li(mengapa)}</ul>
      <div style="font-weight:700;color:#FFC93C;margin-bottom:4px;">Ke depan</div><ul style="margin:0;padding-left:18px;">${li(kedepan)}</ul>
    </div>
  </div>`;
}

// ============ MODUL OPEX (5 fitur, konfirmasi user 2026-07-21) ============
// flattenOpexLeaves: tembus SEMUA level nested di 1 baris waterfall (bukan
// cuma 1 level spt childValsOf), kembalikan daftar item leaf {name,path,vals}.
function flattenOpexLeaves(row){
  const out = [];
  function walk(node, pathParts){
    if (!node || !node.children) return;
    Object.entries(node.children).forEach(([name, child]) => {
      const arr = Array.isArray(child) ? child : child.vals;
      const hasChildren = child && child.children;
      if (hasChildren) walk(child, [...pathParts, name]);
      else if (arr) out.push({ name, path: [...pathParts, name].join(' > '), vals: arr });
    });
  }
  walk(row, [row.name]);
  return out;
}

// Kategorisasi berbasis kata kunci -- BEST EFFORT, bukan sempurna. Urutan
// penting: kategori lebih spesifik dicek duluan (mis. "Sales" sblm "Insentif"
// generik) supaya item spt "Biaya Insentif Sales" masuk Marketing, bukan SDM.
const OPEX_CATEGORIES = [
  { name:'Marketing & Sales', kw:['Marketing','Activity','Sales Tools','Sales Event','Entertain','Jamuan','Insentif Sales','Sales'] },
  { name:'Gaji', kw:['Gaji'] },
  { name:'Insentif', kw:['Insentif','Bonus','Fee','Freelance'] },
  { name:'Tunjangan & Benefit', kw:['Tunjangan Hari Raya','THR','Tunjangan','BPJS','Training'] },
  { name:'Sewa & Aset', kw:['Sewa','Maintenance','Aset'] },
  { name:'Utilitas', kw:['Listrik','Air','Internet','Telepon'] },
  { name:'Transport & Perjalanan', kw:['Transport','Perjalanan Dinas','Akomodasi'] },
  { name:'Administrasi & Sistem', kw:['Administrasi','System POS','IT System','Sistem Keuangan','Alat Tulis','Konsultan','Pajak','POS'] },
];
function categorizeOpexItem(name){
  const nameLower = (name||'').toLowerCase();
  for (const cat of OPEX_CATEGORIES) {
    if (cat.kw.some(k => nameLower.includes(k.toLowerCase()))) return cat.name;
  }
  return 'Lain-lain';
}

function opexModuleWrap(title, note, bodyHtml){
  return `<div style="background:#171433;border:1px solid #2A2650;border-radius:12px;padding:20px 22px;">
    <div style="font-family:'Space Grotesk',sans-serif;font-size:14px;font-weight:700;color:#FFC93C;margin-bottom:4px;">${title}</div>
    <div style="font-size:11px;color:#726C9C;margin-bottom:16px;max-width:720px;">${note}</div>
    ${bodyHtml}
  </div>`;
}

// --- FITUR 1: Tren Opex sbg % Pendapatan, per kategori, per bulan ---
function renderOpexTrend(mode){
  const u = UNIT_DATA.konsolidasi;
  const opexRow = u.waterfall.find(r=>r.name==='Biaya Operasional');
  const rev = getLineVals(u,'Pendapatan');
  if (!opexRow || !rev) return opexModuleWrap('Tren Opex % Pendapatan','Data tidak tersedia.','');
  const leaves = flattenOpexLeaves(opexRow);

  // "columns" generik: mode bulanan = 1 kolom/bulan (dari filter chip); mode
  // kuartal/semester = 1 kolom/bucket (dari groupPeriodsBy). Struktur sama,
  // cuma sumber & label kolomnya beda -- supaya 1 fungsi render melayani semua.
  let columns;
  if (mode === 'quarter' || mode === 'semester') {
    columns = groupPeriodsBy(mode).map(b => ({ label: bucketLabel(mode,b.key,b.indices,b.complete), indices: b.indices, warn: !b.complete }));
  } else {
    columns = getVisibleIdx().map(i => ({ label: periodLabel(PERIODS[i],false), indices:[i], warn:false }));
  }
  if (!columns.length) return opexModuleWrap('Tren Opex % Pendapatan','Belum ada periode.','');

  const catTotals = {}; // {cat: [val per kolom]}
  leaves.forEach(l => {
    const cat = categorizeOpexItem(l.name);
    if (!catTotals[cat]) catTotals[cat] = columns.map(()=>0);
    columns.forEach((c,ci) => { c.indices.forEach(i => { catTotals[cat][ci] += (l.vals[i]||0); }); });
  });
  const cats = Object.keys(catTotals).sort((a,b)=> {
    const sa = catTotals[a].reduce((s,v)=>s+v,0), sb = catTotals[b].reduce((s,v)=>s+v,0);
    return sb-sa;
  });
  const revByCol = columns.map(c => c.indices.reduce((s,i)=>s+(rev[i]||0),0));

  let head = `<th style="text-align:left;padding:9px 14px;color:#726C9C;font-size:11px;text-transform:uppercase;border-bottom:1px solid #2A2650;">Kategori</th>`;
  columns.forEach(c => head += `<th style="text-align:right;padding:9px 14px;color:#726C9C;font-size:11px;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">${c.label}${c.warn?' <span style="color:#FFC93C;">⚠</span>':''}</th>`);
  let rows = '';
  cats.forEach(cat => {
    rows += `<tr><td style="padding:9px 14px;font-size:12.5px;border-bottom:1px solid #2A2650;">${cat}</td>`;
    catTotals[cat].forEach((v,ci) => {
      const pct = revByCol[ci] ? (v/revByCol[ci]*100).toFixed(1)+'%' : '-';
      rows += `<td class="mono" style="text-align:right;padding:9px 14px;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;font-size:12px;">${pct}<span style="display:block;font-size:9.5px;color:#726C9C;">${fmtRp(v)}</span></td>`;
    });
    rows += '</tr>';
  });
  rows += `<tr style="background:#1C1840;"><td style="padding:9px 14px;font-size:12.5px;font-weight:700;color:#FFC93C;border-bottom:1px solid #2A2650;">TOTAL OPEX</td>`;
  columns.forEach((c,ci) => {
    const tot = cats.reduce((s,cc)=>s+catTotals[cc][ci],0);
    const pct = revByCol[ci] ? (tot/revByCol[ci]*100).toFixed(1)+'%' : '-';
    rows += `<td class="mono" style="text-align:right;padding:9px 14px;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;font-size:12px;font-weight:700;color:#FFC93C;">${pct}</td>`;
  });
  rows += '</tr>';

  const table = `<div class="tbl-wrap"><table><thead><tr>${head}</tr></thead><tbody>${rows}</tbody></table></div>`;
  return opexModuleWrap('Tren Opex sebagai % Pendapatan (per kategori)',
    'Item biaya dikelompokkan otomatis berbasis kata kunci nama akun -- BEST EFFORT, bukan klasifikasi akuntansi resmi. Kategori "Lain-lain" menampung item yang tak cocok kata kunci mana pun; kalau jumlahnya besar, kategorisasi ini perlu disempurnakan bersama Anda.',
    table);
}

// --- FITUR 2: Pareto biaya (20% item mana yg menyumbang 80% biaya) ---
function renderOpexPareto(){
  return renderOpexParetoImpl(getVisibleIdx());
}
let paretoGroupMode = 'item'; // 'item' | 'kategori' | 'header'
let paretoExpanded = new Set(); // grup mana yg sedang dibuka (nama grup)
function setParetoGroupMode(m){ paretoGroupMode = m; paretoExpanded = new Set(); render(); }
function toggleParetoGroup(name){
  if (paretoExpanded.has(name)) paretoExpanded.delete(name); else paretoExpanded.add(name);
  render();
}
// Header/divisi = segmen KEDUA-DARI-AKHIR pada path (mis. "Biaya Operasional
// > Divisi HR > Biaya Perjalanan Dinas HR" -> header = "Divisi HR"). Kalau
// item langsung anak "Biaya Operasional" (tak ada grup antara), diberi label
// "Langsung di Biaya Operasional" -- best effort, bukan struktur akuntansi resmi.
function getOpexHeader(path){
  const parts = path.split(' > ');
  return parts.length >= 3 ? parts[parts.length-2] : 'Langsung di Biaya Operasional';
}
function renderOpexParetoImpl(vIdx){
  const u = UNIT_DATA.konsolidasi;
  const opexRow = u.waterfall.find(r=>r.name==='Biaya Operasional');
  if (!opexRow) return opexModuleWrap('Pareto Biaya','Data tidak tersedia.','');
  const leaves = flattenOpexLeaves(opexRow)
    .map(l => ({ name:l.name, path:l.path, total: vIdx.reduce((s,i)=>s+(l.vals[i]||0),0) }))
    .filter(l => l.total > 0);
  const grandTotal = leaves.reduce((s,l)=>s+l.total,0);

  const modeBtn = (id,label) => `<button onclick="setParetoGroupMode('${id}')" style="cursor:pointer;padding:7px 14px;border-radius:9px;font-family:'Space Grotesk',sans-serif;font-size:12px;font-weight:700;border:1px solid ${paretoGroupMode===id?'#FFC93C':'#2A2650'};color:${paretoGroupMode===id?'#FFC93C':'#726C9C'};background:${paretoGroupMode===id?'#241F4D':'#171433'};">${label}</button>`;
  const modeToggle = `<div style="display:flex;gap:8px;margin-bottom:16px;">${modeBtn('item','Per Item')}${modeBtn('kategori','Per Kategori')}${modeBtn('header','Per Divisi/Header')}</div>`;

  let rowsData; // [{key, total, items:[{name,total}]}]
  if (paretoGroupMode === 'item') {
    rowsData = leaves.map(l => ({ key:l.name, total:l.total, items:null }));
  } else {
    const groupFn = paretoGroupMode === 'kategori' ? (l=>categorizeOpexItem(l.name)) : (l=>getOpexHeader(l.path));
    const groups = {};
    leaves.forEach(l => {
      const k = groupFn(l);
      if (!groups[k]) groups[k] = { total:0, items:[] };
      groups[k].total += l.total;
      groups[k].items.push({ name:l.name, total:l.total });
    });
    rowsData = Object.entries(groups).map(([key,g]) => ({ key, total:g.total, items:g.items.sort((a,b)=>b.total-a.total) }));
  }
  rowsData.sort((a,b)=>b.total-a.total);

  let cum = 0, cutoffIdx = -1;
  const rows = rowsData.map((r,idx) => {
    cum += r.total;
    const pct = grandTotal ? (r.total/grandTotal*100) : 0;
    const cumPct = grandTotal ? (cum/grandTotal*100) : 0;
    if (cutoffIdx===-1 && cumPct>=80) cutoffIdx = idx;
    return { ...r, pct, cumPct };
  });
  const n80 = cutoffIdx>=0 ? cutoffIdx+1 : rows.length;
  const unitWord = paretoGroupMode==='item' ? 'item' : (paretoGroupMode==='kategori' ? 'kategori' : 'divisi/header');
  const pctItems80 = rows.length ? (n80/rows.length*100).toFixed(0) : 0;

  const summary = `<div style="background:#1C1840;border-radius:10px;padding:14px 18px;margin-bottom:16px;">
    <span style="font-size:13px;color:#F5F3FF;">Cuma <b style="color:#FFC93C;">${n80} dari ${rows.length} ${unitWord}</b> (${pctItems80}%) sudah menyumbang <b style="color:#FFC93C;">80%</b> total Opex.</span>
  </div>`;
  let body = `<div class="tbl-wrap"><table><thead><tr>
    <th style="text-align:left;padding:9px 14px;color:#726C9C;font-size:11px;text-transform:uppercase;border-bottom:1px solid #2A2650;">${paretoGroupMode==='item'?'Item Biaya':(paretoGroupMode==='kategori'?'Kategori':'Divisi / Header')}</th>
    <th style="text-align:right;padding:9px 14px;color:#726C9C;font-size:11px;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">Total</th>
    <th style="text-align:right;padding:9px 14px;color:#726C9C;font-size:11px;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">%</th>
    <th style="text-align:right;padding:9px 14px;color:#726C9C;font-size:11px;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">Kumulatif</th>
  </tr></thead><tbody>`;
  rows.forEach((r,idx) => {
    const hl = idx < n80;
    const expandable = r.items && r.items.length > 1;
    const isOpen = paretoExpanded.has(r.key);
    const arrow = expandable ? `<span onclick="toggleParetoGroup('${r.key.replace(/'/g,"\\'")}')" style="cursor:pointer;color:#726C9C;margin-right:6px;font-size:10px;display:inline-block;transform:rotate(${isOpen?90:0}deg);transition:transform .15s;">&#9654;</span>` : '<span style="display:inline-block;width:16px;"></span>';
    body += `<tr style="${hl?'background:#1C1840;':''}${expandable?'cursor:pointer;':''}" ${expandable?`onclick="toggleParetoGroup('${r.key.replace(/'/g,"\\'")}')"`:''}>
      <td style="padding:8px 14px;font-size:12px;color:${hl?'#FFC93C':'#C9C3E8'};border-bottom:1px solid #2A2650;">${arrow}${r.key}${expandable?` <span style="color:#726C9C;font-size:10.5px;">(${r.items.length} item)</span>`:''}</td>
      <td class="mono" style="text-align:right;padding:8px 14px;font-size:12px;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">${fmtRp(r.total)}</td>
      <td class="mono" style="text-align:right;padding:8px 14px;font-size:12px;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">${r.pct.toFixed(1)}%</td>
      <td class="mono" style="text-align:right;padding:8px 14px;font-size:12px;color:#726C9C;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">${r.cumPct.toFixed(1)}%</td>
    </tr>`;
    if (expandable && isOpen) {
      r.items.forEach(it => {
        const itPct = r.total ? (it.total/r.total*100) : 0;
        body += `<tr><td style="padding:6px 14px 6px 38px;font-size:11.5px;color:#9B93C4;border-bottom:1px solid #2A2650;">${it.name}</td>
          <td class="mono" style="text-align:right;padding:6px 14px;font-size:11.5px;color:#9B93C4;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">${fmtRp(it.total)}</td>
          <td class="mono" style="text-align:right;padding:6px 14px;font-size:11.5px;color:#726C9C;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">${itPct.toFixed(1)}%<span style="color:#4A4568;"> dari grup</span></td>
          <td style="border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;"></td></tr>`;
      });
    }
  });
  body += '</tbody></table></div>';
  const noteExtra = paretoGroupMode==='kategori' ? ' Kategori dari kata kunci nama akun -- BEST EFFORT, bukan klasifikasi resmi.'
    : paretoGroupMode==='header' ? ' Divisi/header dari struktur Path (grup induk tiap item) -- item tanpa grup antara ditandai "Langsung di Biaya Operasional".' : '';
  return opexModuleWrap('Pareto Biaya Operasional', `Diurutkan dari ${unitWord} terbesar. Baris kuning = bagian dari 80% biaya teratas. Berdasarkan total periode yang sedang difilter.${noteExtra}`, modeToggle+summary+body);
}

// --- FITUR 3: Efisiensi Opex per outlet (rasio Biaya Ops / Pendapatan) ---
function renderOpexOutlet(){
  return renderOpexOutletImpl(getVisibleIdx());
}
let outletEffExpanded = new Set(); // outlet key mana yg sedang dibuka rinciannya
function toggleOutletEffRow(key){
  if (outletEffExpanded.has(key)) outletEffExpanded.delete(key); else outletEffExpanded.add(key);
  render();
}
function renderOpexOutletImpl(vIdx){
  const rows = OUTLET_KEYS.map(k => {
    const u = UNIT_DATA[k];
    const rev = getLineVals(u,'Pendapatan');
    const opexRow = u.waterfall.find(r=>r.name==='Biaya Operasional');
    if (!rev || !opexRow) return null;
    const revTot = vIdx.reduce((s,i)=>s+(rev[i]||0),0);
    const opexTot = vIdx.reduce((s,i)=>s+(opexRow.values[i]||0),0);
    if (!revTot) return null;
    return { key:k, label:u.label, revTot, opexTot, ratio: opexTot/revTot*100, opexRow };
  }).filter(Boolean).sort((a,b)=>b.ratio-a.ratio);

  if (!rows.length) return opexModuleWrap('Efisiensi Opex per Outlet','Belum ada outlet dengan data Pendapatan & Biaya Operasional pada periode ini.','');

  const avgRatio = rows.reduce((s,r)=>s+r.ratio,0)/rows.length;
  let body = `<div class="tbl-wrap"><table><thead><tr>
    <th style="text-align:left;padding:9px 14px;color:#726C9C;font-size:11px;text-transform:uppercase;border-bottom:1px solid #2A2650;">Outlet</th>
    <th style="text-align:right;padding:9px 14px;color:#726C9C;font-size:11px;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">Pendapatan</th>
    <th style="text-align:right;padding:9px 14px;color:#726C9C;font-size:11px;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">Biaya Operasional</th>
    <th style="text-align:right;padding:9px 14px;color:#726C9C;font-size:11px;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">Opex/Pendapatan</th>
  </tr></thead><tbody>`;
  rows.forEach(r => {
    const worse = r.ratio > avgRatio;
    const isOpen = outletEffExpanded.has(r.key);
    const arrow = `<span style="cursor:pointer;color:#726C9C;margin-right:6px;font-size:10px;display:inline-block;transform:rotate(${isOpen?90:0}deg);transition:transform .15s;">&#9654;</span>`;
    body += `<tr style="cursor:pointer;" onclick="toggleOutletEffRow('${r.key}')">
      <td style="padding:8px 14px;font-size:12.5px;font-weight:600;border-bottom:1px solid #2A2650;">${arrow}${r.label}</td>
      <td class="mono" style="text-align:right;padding:8px 14px;font-size:12px;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">${fmtRp(r.revTot)}</td>
      <td class="mono" style="text-align:right;padding:8px 14px;font-size:12px;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">${fmtRp(r.opexTot)}</td>
      <td class="mono" style="text-align:right;padding:8px 14px;font-size:12px;font-weight:700;color:${worse?'#FFC93C':'#4ADE80'};border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">${r.ratio.toFixed(1)}%</td>
    </tr>`;
    if (isOpen) {
      const items = flattenOpexLeaves(r.opexRow)
        .map(l => ({ name:l.name, total: vIdx.reduce((s,i)=>s+(l.vals[i]||0),0) }))
        .filter(l => l.total !== 0)
        .sort((a,b)=>b.total-a.total);
      if (!items.length) {
        body += `<tr><td colspan="4" style="padding:8px 14px 8px 38px;font-size:11.5px;color:#726C9C;border-bottom:1px solid #2A2650;">Tidak ada rincian item biaya pada periode ini.</td></tr>`;
      } else {
        items.forEach(it => {
          const pctOfOpex = r.opexTot ? (it.total/r.opexTot*100) : 0;
          body += `<tr><td style="padding:6px 14px 6px 38px;font-size:11.5px;color:#9B93C4;border-bottom:1px solid #2A2650;">${it.name}</td>
            <td style="border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;"></td>
            <td class="mono" style="text-align:right;padding:6px 14px;font-size:11.5px;color:#9B93C4;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">${fmtRp(it.total)}</td>
            <td class="mono" style="text-align:right;padding:6px 14px;font-size:11px;color:#726C9C;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">${pctOfOpex.toFixed(1)}% dari opex outlet</td>
          </tr>`;
        });
      }
    }
  });
  body += `</tbody></table></div>
  <div style="font-size:11px;color:#726C9C;margin-top:10px;">Rata-rata semua outlet: <b>${avgRatio.toFixed(1)}%</b>. Kuning = di atas rata-rata (opex relatif lebih besar terhadap pendapatannya), hijau = di bawah rata-rata. Klik baris outlet untuk lihat rincian item biayanya.</div>`;
  return opexModuleWrap('Efisiensi Opex per Outlet', 'Outlet diurutkan dari rasio Biaya Operasional/Pendapatan tertinggi -- makin tinggi, makin besar porsi pendapatan outlet itu terserap biaya operasional. Berdasarkan total periode yang difilter.', body);
}

// --- FITUR 4: Deteksi lonjakan (item biaya naik tajam vs bulan sebelumnya) ---
function renderOpexAnomaly(){
  const vIdx = getVisibleIdx();
  if (vIdx.length < 2) return opexModuleWrap('Deteksi Lonjakan Biaya','Pilih minimal 2 bulan di filter untuk mendeteksi lonjakan (perlu bulan pembanding).','');
  return renderOpexAnomalyImpl([vIdx[vIdx.length-2]], [vIdx[vIdx.length-1]], periodLabel(PERIODS[vIdx[vIdx.length-2]],false), periodLabel(PERIODS[vIdx[vIdx.length-1]],false));
}
let anomalyTraceExpanded = new Set(); // nama item yg rincian tracing-nya sedang dibuka
function toggleAnomalyTrace(name){
  if (anomalyTraceExpanded.has(name)) anomalyTraceExpanded.delete(name); else anomalyTraceExpanded.add(name);
  render();
}
function renderOpexAnomalyImpl(prevIdx, lastIdx, mPrev, mLast){
  const u = UNIT_DATA.konsolidasi;
  const opexRow = u.waterfall.find(r=>r.name==='Biaya Operasional');
  if (!opexRow) return opexModuleWrap('Deteksi Lonjakan Biaya','Data tidak tersedia.','');
  const leaves = flattenOpexLeaves(opexRow);
  const sumIdx = (vals, idxArr) => idxArr.reduce((s,i)=> vals[i]==null ? s : s+vals[i], 0);
  const hasAnyIdx = (vals, idxArr) => idxArr.some(i=>vals[i]!=null);
  const THRESHOLD_PCT = 50; // lonjakan didefinisikan: naik >=50% vs periode sebelumnya
  const THRESHOLD_ABS = 2000000; // DAN kenaikan absolut >= Rp2jt (biar item kecil tak berisik)
  const flagged = leaves.map(l => {
    if (!hasAnyIdx(l.vals,prevIdx) || !hasAnyIdx(l.vals,lastIdx)) return null;
    const v0 = sumIdx(l.vals, prevIdx), v1 = sumIdx(l.vals, lastIdx);
    if (v0===0) return null;
    const deltaPct = (v1-v0)/Math.abs(v0)*100;
    const deltaAbs = v1-v0;
    if (deltaPct >= THRESHOLD_PCT && deltaAbs >= THRESHOLD_ABS) return { name:l.name, path:l.path, vals:l.vals, v0, v1, deltaPct, deltaAbs };
    return null;
  }).filter(Boolean).sort((a,b)=>b.deltaAbs-a.deltaAbs);

  if (!flagged.length) {
    return opexModuleWrap('Deteksi Lonjakan Biaya', `Membandingkan ${mLast} vs ${mPrev}. Ambang: naik ≥50% DAN ≥Rp2.000.000.`,
      `<div style="color:#4ADE80;font-size:13px;padding:12px 0;">✓ Tidak ada item biaya yang melonjak melebihi ambang antara ${mPrev} dan ${mLast}.</div>`);
  }
  let body = `<div class="tbl-wrap"><table><thead><tr>
    <th style="text-align:left;padding:9px 14px;color:#726C9C;font-size:11px;text-transform:uppercase;border-bottom:1px solid #2A2650;">Item Biaya</th>
    <th style="text-align:right;padding:9px 14px;color:#726C9C;font-size:11px;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">${mPrev}</th>
    <th style="text-align:right;padding:9px 14px;color:#726C9C;font-size:11px;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">${mLast}</th>
    <th style="text-align:right;padding:9px 14px;color:#726C9C;font-size:11px;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">Kenaikan</th>
  </tr></thead><tbody>`;
  flagged.forEach(f => {
    const isOpen = anomalyTraceExpanded.has(f.name);
    const arrow = `<span style="display:inline-block;font-size:10px;margin-right:6px;transform:rotate(${isOpen?90:0}deg);transition:transform .15s;">&#9654;</span>`;
    body += `<tr style="background:#241F1F;cursor:pointer;" onclick="toggleAnomalyTrace('${f.name.replace(/'/g,"\\'")}')">
      <td style="padding:8px 14px;font-size:12px;color:#FB7185;border-bottom:1px solid #2A2650;">${arrow}⚠ ${f.name}</td>
      <td class="mono" style="text-align:right;padding:8px 14px;font-size:12px;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">${fmtRp(f.v0)}</td>
      <td class="mono" style="text-align:right;padding:8px 14px;font-size:12px;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">${fmtRp(f.v1)}</td>
      <td class="mono" style="text-align:right;padding:8px 14px;font-size:12px;font-weight:700;color:#FB7185;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">+${f.deltaPct.toFixed(0)}% (${fmtRp(f.deltaAbs)})</td>
    </tr>`;
    if (isOpen) {
      const pathParts = f.path.split(' > ');
      const asalUsul = pathParts.length>1 ? pathParts.slice(0,-1).join(' › ') : '(langsung di Biaya Operasional)';
      const histCells = PERIODS.map((p,i) => {
        const v = f.vals[i];
        const isFlagMonth = lastIdx.includes(i);
        return `<td class="mono" style="text-align:right;padding:7px 12px;font-size:11.5px;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;${isFlagMonth?'color:#FB7185;font-weight:700;':'color:#9B93C4;'}">${v==null?'-':fmtRp(v)}</td>`;
      }).join('');
      const histHead = PERIODS.map(p=>`<th style="text-align:right;padding:7px 12px;font-size:10px;color:#726C9C;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">${periodLabel(p,false)}</th>`).join('');
      body += `<tr><td colspan="4" style="padding:12px 14px 16px 34px;border-bottom:1px solid #2A2650;">
        <div style="font-size:11px;color:#726C9C;margin-bottom:8px;">Asal: <b style="color:#9B93C4;">${asalUsul}</b></div>
        <div style="overflow-x:auto;"><table style="border-collapse:collapse;"><thead><tr><th style="text-align:left;padding:7px 12px;font-size:10px;color:#726C9C;">Riwayat bulanan</th>${histHead}</tr></thead>
        <tbody><tr><td style="padding:7px 12px;font-size:11px;color:#726C9C;">Rp</td>${histCells}</tr></tbody></table></div>
      </div></td></tr>`;
    }
  });
  body += '</tbody></table></div>';
  return opexModuleWrap('Deteksi Lonjakan Biaya', `Membandingkan ${mLast} vs ${mPrev}. Ambang sederhana: naik ≥50% DAN ≥Rp2.000.000 -- ini heuristik dasar (2 periode saja), BUKAN model statistik. Makin panjang histori data, deteksi ini bisa dibuat lebih presisi (rata-rata bergerak, deviasi).`, body);
}

// --- FITUR 5: Operating leverage (pertumbuhan Pendapatan vs Opex) ---
function renderOpexLeverage(mode){
  const u = UNIT_DATA.konsolidasi;
  const rev = getLineVals(u,'Pendapatan');
  const opexRow = u.waterfall.find(r=>r.name==='Biaya Operasional');

  let columns;
  if (mode === 'quarter' || mode === 'semester') {
    columns = groupPeriodsBy(mode).map(b => ({ label: bucketLabel(mode,b.key,b.indices,b.complete), r: b.indices.reduce((s,i)=>s+(rev[i]||0),0), o: b.indices.reduce((s,i)=>s+(opexRow.values[i]||0),0) }));
  } else {
    const vIdx = getVisibleIdx().filter(i => rev[i]!=null && opexRow.values[i]!=null);
    columns = vIdx.map(i => ({ label: periodLabel(PERIODS[i],false), r: rev[i], o: opexRow.values[i] }));
  }
  if (columns.length < 2) return opexModuleWrap('Operating Leverage','Pilih/tersedia minimal 2 periode untuk melihat pertumbuhan.','');
  const first = columns[0], last = columns[columns.length-1];
  const revGrowth = fmtDeltaPct(last.r, first.r);
  const opexGrowth = fmtDeltaPct(last.o, first.o);
  const leverage = (revGrowth!==null && opexGrowth!==null) ? (revGrowth - opexGrowth) : null;

  let rowsHtml = '';
  columns.forEach((c,idx) => {
    const rG = idx>0 ? fmtDeltaPct(c.r, columns[idx-1].r) : null;
    const oG = idx>0 ? fmtDeltaPct(c.o, columns[idx-1].o) : null;
    rowsHtml += `<tr>
      <td style="padding:8px 14px;font-size:12px;border-bottom:1px solid #2A2650;">${c.label}</td>
      <td class="mono" style="text-align:right;padding:8px 14px;font-size:12px;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">${fmtRp(c.r)}${rG!==null?`<span style="display:block;font-size:9.5px;color:${rG>=0?'#4ADE80':'#FFC93C'};">${rG>=0?'▲':'▼'}${Math.abs(rG).toFixed(1)}%</span>`:''}</td>
      <td class="mono" style="text-align:right;padding:8px 14px;font-size:12px;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">${fmtRp(c.o)}${oG!==null?`<span style="display:block;font-size:9.5px;color:${oG>=0?'#FFC93C':'#4ADE80'};">${oG>=0?'▲':'▼'}${Math.abs(oG).toFixed(1)}%</span>`:''}</td>
    </tr>`;
  });
  const summary = leverage===null ? '' : `<div style="background:#1C1840;border-radius:10px;padding:14px 18px;margin-bottom:16px;font-size:13px;color:#F5F3FF;">
    Dari ${first.label} ke ${last.label}: Pendapatan tumbuh <b style="color:#4ADE80;">${revGrowth.toFixed(1)}%</b>, Opex tumbuh <b style="color:#FFC93C;">${opexGrowth.toFixed(1)}%</b>.
    ${leverage>0 ? `<br>Selisih <b style="color:#4ADE80;">+${leverage.toFixed(1)} poin</b> menguntungkan -- pendapatan tumbuh lebih cepat drpd biaya (tanda skala ekonomi mulai bekerja).` : `<br>Selisih <b style="color:#FB7185;">${leverage.toFixed(1)} poin</b> -- biaya tumbuh LEBIH CEPAT drpd pendapatan, perlu perhatian.`}
  </div>`;
  const table = `<div class="tbl-wrap"><table><thead><tr>
    <th style="text-align:left;padding:9px 14px;color:#726C9C;font-size:11px;text-transform:uppercase;border-bottom:1px solid #2A2650;">Periode</th>
    <th style="text-align:right;padding:9px 14px;color:#726C9C;font-size:11px;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">Pendapatan</th>
    <th style="text-align:right;padding:9px 14px;color:#726C9C;font-size:11px;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">Biaya Operasional</th>
  </tr></thead><tbody>${rowsHtml}</tbody></table></div>`;
  return opexModuleWrap('Operating Leverage', 'Membandingkan laju pertumbuhan Pendapatan vs Biaya Operasional pada rentang periode. Idealnya Pendapatan tumbuh lebih cepat dari Opex.', summary+table);
}

// Notice lonjakan biaya -- ditampilkan MENCOLOK, bukan cuma ditemukan kalau
// user buka tab "Deteksi Lonjakan" secara sengaja. Dipakai jg utk badge sidebar.
function countOpexAnomalies(){
  const vIdx = getVisibleIdx();
  if (vIdx.length < 2) return { count:0, items:[] };
  const u = UNIT_DATA.konsolidasi;
  const opexRow = u.waterfall.find(r=>r.name==='Biaya Operasional');
  if (!opexRow) return { count:0, items:[] };
  const last = vIdx[vIdx.length-1], prev = vIdx[vIdx.length-2];
  const leaves = flattenOpexLeaves(opexRow);
  const items = leaves.map(l => {
    const v1 = l.vals[last], v0 = l.vals[prev];
    if (v1==null || v0==null || v0===0) return null;
    const deltaPct = (v1-v0)/Math.abs(v0)*100;
    const deltaAbs = v1-v0;
    if (deltaPct >= 50 && deltaAbs >= 2000000) return { name:l.name, deltaPct, deltaAbs };
    return null;
  }).filter(Boolean).sort((a,b)=>b.deltaAbs-a.deltaAbs);
  return { count: items.length, items, mLast: PERIODS[last]?periodLabel(PERIODS[last],false):'', mPrev: PERIODS[prev]?periodLabel(PERIODS[prev],false):'' };
}
function renderOpexAnomalyNotice(currentKey){
  if (currentKey === 'opexAnomaly') return ''; // halaman detailnya sendiri, tak perlu notice ringkasan lagi
  const { count, items, mLast, mPrev } = countOpexAnomalies();
  if (!count) return '';
  const top3 = items.slice(0,3).map(i=>i.name).join(', ');
  return `<div onclick="navigateTo('opexAnomaly')" style="cursor:pointer;background:#2A1B1B;border:1px solid #FB7185;border-radius:12px;padding:14px 18px;margin-bottom:16px;display:flex;align-items:center;gap:12px;">
    <span style="font-size:20px;">🔴</span>
    <div style="flex:1;">
      <div style="font-size:13px;font-weight:700;color:#FB7185;">${count} item biaya melonjak (${mPrev}→${mLast})</div>
      <div style="font-size:11.5px;color:#C9A0A6;margin-top:2px;">${top3}${items.length>3?`, +${items.length-3} lainnya`:''} -- klik untuk lihat detail & putuskan pos mana yang perlu dievaluasi.</div>
    </div>
    <span style="color:#FB7185;font-size:18px;">→</span>
  </div>`;
}

let opexBucketPick = {}; // {unitKey: bucketKey} -- utk Pareto/Outlet mode kuartal/semester (ranking 1 periode, butuh PILIH bucket mana, bukan kolom banyak)
function setOpexBucket(unitKey, bucketKey){ opexBucketPick[unitKey] = bucketKey; render(); }
let multiBucketPick = {}; // {unitKey: Set(bucketKey)} -- versi MULTI-pilih dari opexBucketPick, khusus halaman perbandingan
function toggleMultiBucket(unitKey, mode, bucketKey){
  if (!multiBucketPick[unitKey]) multiBucketPick[unitKey] = new Set();
  const s = multiBucketPick[unitKey];
  if (s.has(bucketKey)) s.delete(bucketKey); else s.add(bucketKey);
  if (s.size === 0) s.add(bucketKey); // jangan sampai kosong total
  render();
}
function renderMultiBucketPicker(unitKey, mode, accentColor){
  const buckets = groupPeriodsBy(mode).map(b=>({kind:mode,...b}));
  if (!buckets.length) return { html:'', blocks:[] };
  if (!multiBucketPick[unitKey] || multiBucketPick[unitKey].size===0) {
    const completeB = buckets.filter(b=>b.complete);
    const defaultKey = (completeB.length ? completeB[completeB.length-1] : buckets[buckets.length-1]).key;
    multiBucketPick[unitKey] = new Set([defaultKey]);
  }
  const sel = multiBucketPick[unitKey];
  const html = `<div style="display:flex;gap:7px;margin-bottom:14px;flex-wrap:wrap;align-items:center;">
    <span style="font-size:11px;color:#726C9C;font-weight:600;">PILIH ${mode==='quarter'?'KUARTAL':'SEMESTER'} (bisa lebih dari 1 utk dibandingkan):</span>
    ${buckets.map(b => `<button onclick="toggleMultiBucket('${unitKey}','${mode}','${b.key}')" style="cursor:pointer;padding:6px 12px;border-radius:8px;font-family:'Space Grotesk',sans-serif;font-size:11.5px;font-weight:700;border:1px solid ${sel.has(b.key)?accentColor:'#2A2650'};color:${sel.has(b.key)?accentColor:'#726C9C'};background:${sel.has(b.key)?'#241F4D':'#171433'};">${bucketLabel(mode,b.key,b.indices,b.complete)}${!b.complete?' ⚠':''}</button>`).join('')}
  </div>`;
  const blocks = buckets.filter(b=>sel.has(b.key)).map(b => ({ indices:b.indices, label: bucketLabel(mode,b.key,b.indices,b.complete), complete:b.complete }));
  return { html, blocks };
}
function renderBucketPicker(unitKey, mode, accentColor){
  const buckets = groupPeriodsBy(mode).map(b=>({kind:mode,...b}));
  if (!buckets.length) return { html:'', indices:[] };
  const completeB = buckets.filter(b=>b.complete);
  const defaultKey = (completeB.length ? completeB[completeB.length-1] : buckets[buckets.length-1]).key;
  const curKey = opexBucketPick[unitKey] || defaultKey;
  const cur = buckets.find(b=>b.key===curKey) || buckets[buckets.length-1];
  const html = `<div style="display:flex;gap:7px;margin-bottom:14px;flex-wrap:wrap;align-items:center;">
    <span style="font-size:11px;color:#726C9C;font-weight:600;">PILIH ${mode==='quarter'?'KUARTAL':'SEMESTER'}:</span>
    ${buckets.map(b => `<button onclick="setOpexBucket('${unitKey}','${b.key}')" style="cursor:pointer;padding:6px 12px;border-radius:8px;font-family:'Space Grotesk',sans-serif;font-size:11.5px;font-weight:700;border:1px solid ${curKey===b.key?accentColor:'#2A2650'};color:${curKey===b.key?accentColor:'#726C9C'};background:${curKey===b.key?'#241F4D':'#171433'};">${bucketLabel(mode,b.key,b.indices,b.complete)}${!b.complete?' ⚠':''}</button>`).join('')}
  </div>`;
  return { html, indices: cur.indices, label: bucketLabel(mode, cur.key, cur.indices, cur.complete), complete: cur.complete };
}
function renderOpexModule(key, mode){
  mode = mode || 'bulanan';
  if (key==='opexTrend') return renderOpexTrend(mode);
  if (key==='opexLeverage') return renderOpexLeverage(mode);
  if (key==='opexPareto') {
    if (mode==='bulanan') return renderOpexParetoImpl(getVisibleIdx());
    const { html, indices } = renderBucketPicker('opexPareto', mode, '#FFC93C');
    return html + renderOpexParetoImpl(indices);
  }
  if (key==='opexOutlet') {
    if (mode==='bulanan') return renderOpexOutletImpl(getVisibleIdx());
    const { html, indices } = renderBucketPicker('opexOutlet', mode, '#FFC93C');
    return html + renderOpexOutletImpl(indices);
  }
  if (key==='opexAnomaly') {
    if (mode==='bulanan') return renderOpexAnomaly();
    const buckets = groupPeriodsBy(mode).map(b=>({kind:mode,...b})).filter(b=>b.complete);
    if (buckets.length < 2) return opexModuleWrap('Deteksi Lonjakan Biaya', '', `<div style="color:#726C9C;font-size:12.5px;padding:12px 0;">Butuh minimal 2 ${mode==='quarter'?'kuartal':'semester'} lengkap utk membandingkan.</div>`);
    const prev = buckets[buckets.length-2], last = buckets[buckets.length-1];
    return renderOpexAnomalyImpl(prev.indices, last.indices, bucketLabel(mode,prev.key,prev.indices,true), bucketLabel(mode,last.key,last.indices,true));
  }
  return '';
}

// ============ HPP BAHAN BAKU (fokus akun raw material, level Manufaktur/Konsolidasi) ============
// Beda dgn Opex: bahan baku dibeli PABRIK, bukan per-cabang, jadi TAK ADA
// varian "per outlet". Datanya jg 1 akun flat (blm ada breakdown per jenis
// bahan spt tepung/gula/dll), jadi TAK ADA varian "Pareto" -- 3 fitur yg
// relevan: Ringkasan, Tren % Pendapatan, Deteksi Lonjakan.
function getHppBahanBakuSeries(){
  const u = UNIT_DATA.konsolidasi;
  const direct = getLineVals(u, 'HPP Bahan Baku'); // ada langsung kalau segmennya Manufaktur
  if (direct && direct.some(v=>v!=null)) return direct;
  return getRawAccountSeries(ENTITY_KEY_MAP.konsolidasi || 'Konsolidasi', 'HPP Bahan Baku Utama'); // fallback: Konsolidasi (nama akun hilang strukturnya di waterfall teragregasi)
}
function hppModuleWrap(title, note, bodyHtml){
  return `<div style="background:#171433;border:1px solid #2A2650;border-radius:12px;padding:20px 22px;">
    <div style="font-family:'Space Grotesk',sans-serif;font-size:14px;font-weight:700;color:#F4A6D0;margin-bottom:4px;">${title}</div>
    <div style="font-size:11px;color:#726C9C;margin-bottom:16px;max-width:720px;">${note}</div>
    ${bodyHtml}
  </div>`;
}

// --- FITUR 1: Ringkasan (bulan terakhir vs sebelumnya vs rata-rata) ---
function renderHppRingkasan(){
  const u = UNIT_DATA.konsolidasi;
  const rev = getLineVals(u,'Pendapatan');
  const hpp = getHppBahanBakuSeries();
  const vIdx = getVisibleIdx().filter(i=>hpp[i]!=null);
  if (!vIdx.length) return hppModuleWrap('Ringkasan HPP Bahan Baku','Data tidak tersedia utk periode yg dipilih.','');

  const last = vIdx[vIdx.length-1];
  const prev = vIdx.length>1 ? vIdx[vIdx.length-2] : null;
  const histIdx = vIdx.slice(0,-1).length ? vIdx.slice(0,-1) : vIdx; // bulan2 SEBELUM bulan terakhir, biar rata2 tak ikut bandingkan diri sendiri
  const avgHpp = histIdx.reduce((s,i)=>s+hpp[i],0) / histIdx.length;
  const avgRev = histIdx.reduce((s,i)=>s+(rev[i]||0),0) / histIdx.length;
  const avgPct = avgRev ? avgHpp/avgRev*100 : null;
  const pctLast = rev[last] ? hpp[last]/rev[last]*100 : null;
  const pctPrev = (prev!=null && rev[prev]) ? hpp[prev]/rev[prev]*100 : null;

  const cardHtml = (label, val, pct) => `<div style="flex:1;min-width:200px;background:#171433;border:1px solid #2A2650;border-radius:14px;padding:18px 20px;">
    <div style="font-size:11px;color:#726C9C;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">${label}</div>
    <div class="mono" style="font-size:20px;font-weight:700;color:#F4A6D0;margin-top:6px;">${fmtRp(val)}</div>
    <div style="font-size:11.5px;color:#9B93C4;margin-top:3px;">${pct!=null?pct.toFixed(1)+'% thd Omset':'–'}</div>
  </div>`;
  const cards = [
    cardHtml(`Bulan Terakhir · ${periodLabel(PERIODS[last],false)}`, hpp[last], pctLast),
    prev!=null ? cardHtml(`Bulan Sebelumnya · ${periodLabel(PERIODS[prev],false)}`, hpp[prev], pctPrev) : '',
    cardHtml(`Rata-rata ${histIdx.length} Bulan Sebelumnya`, avgHpp, avgPct),
  ].join('');

  const deltaPrev = prev!=null ? fmtDeltaPct(hpp[last], hpp[prev]) : null;
  const deltaAvg = avgHpp ? (hpp[last]-avgHpp)/Math.abs(avgHpp)*100 : null;
  const poinPrev = (pctLast!=null && pctPrev!=null) ? pctLast-pctPrev : null;
  const poinAvg = (pctLast!=null && avgPct!=null) ? pctLast-avgPct : null;
  const deltaHtml = `<div style="background:#1C1840;border-radius:10px;padding:14px 18px;margin-top:16px;font-size:13px;color:#F5F3FF;line-height:1.9;">
    ${deltaPrev!==null ? `Rp vs bulan sebelumnya: <b style="color:${deltaPrev>=0?'#FB7185':'#4ADE80'};">${deltaPrev>=0?'▲':'▼'}${Math.abs(deltaPrev).toFixed(1)}%</b>${poinPrev!=null?` (rasio thd Omset ${poinPrev>=0?'naik':'turun'} ${Math.abs(poinPrev).toFixed(1)} poin)`:''}<br>` : ''}
    ${deltaAvg!==null ? `Rp vs rata-rata ${histIdx.length} bulan: <b style="color:${deltaAvg>=0?'#FB7185':'#4ADE80'};">${deltaAvg>=0?'▲':'▼'}${Math.abs(deltaAvg).toFixed(1)}%</b>${poinAvg!=null?` (rasio thd Omset ${poinAvg>=0?'naik':'turun'} ${Math.abs(poinAvg).toFixed(1)} poin)`:''}` : ''}
  </div>`;

  return hppModuleWrap('Ringkasan HPP Bahan Baku',
    'Data diambil dari level Konsolidasi (fallback ke akun Manufaktur kalau Konsolidasi kosong) -- akun ini cuma ada di level pabrik, bukan per-cabang, krn bahan baku dibeli terpusat.',
    `<div style="display:flex;gap:14px;flex-wrap:wrap;">${cards}</div>${deltaHtml}`);
}

// --- FITUR 2: Tren HPP Bahan Baku (Rp & % thd Omset per periode) ---
function renderHppTren(mode){
  const u = UNIT_DATA.konsolidasi;
  const rev = getLineVals(u,'Pendapatan');
  const hpp = getHppBahanBakuSeries();
  if (!hpp.some(v=>v!=null)) return hppModuleWrap('Tren HPP Bahan Baku','Data tidak tersedia.','');

  let columns;
  if (mode === 'quarter' || mode === 'semester') {
    columns = groupPeriodsBy(mode).map(b => ({ label: bucketLabel(mode,b.key,b.indices,b.complete), indices: b.indices, warn: !b.complete }));
  } else {
    columns = getVisibleIdx().map(i => ({ label: periodLabel(PERIODS[i],false), indices:[i], warn:false }));
  }
  if (!columns.length) return hppModuleWrap('Tren HPP Bahan Baku','Belum ada periode.','');

  const hppByCol = columns.map(c => c.indices.reduce((s,i)=> hpp[i]!=null ? s+hpp[i] : s, 0));
  const revByCol = columns.map(c => c.indices.reduce((s,i)=>s+(rev[i]||0),0));

  let head = `<th style="text-align:left;padding:9px 14px;color:#726C9C;font-size:11px;text-transform:uppercase;border-bottom:1px solid #2A2650;">Keterangan</th>`;
  columns.forEach(c => head += `<th style="text-align:right;padding:9px 14px;color:#726C9C;font-size:11px;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">${c.label}${c.warn?' <span style="color:#FFC93C;">⚠</span>':''}</th>`);
  let rowRp = `<tr><td style="padding:9px 14px;font-size:12.5px;border-bottom:1px solid #2A2650;">HPP Bahan Baku (Rp)</td>`;
  let rowPct = `<tr style="background:#1C1840;"><td style="padding:9px 14px;font-size:12.5px;font-weight:700;color:#F4A6D0;border-bottom:1px solid #2A2650;">% thd Omset</td>`;
  columns.forEach((c,ci)=>{
    rowRp += `<td class="mono" style="text-align:right;padding:9px 14px;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;font-size:12px;">${fmtRp(hppByCol[ci])}</td>`;
    const pct = revByCol[ci] ? (hppByCol[ci]/revByCol[ci]*100) : null;
    rowPct += `<td class="mono" style="text-align:right;padding:9px 14px;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;font-size:12px;font-weight:700;color:#F4A6D0;">${pct!=null?pct.toFixed(1)+'%':'-'}</td>`;
  });
  rowRp += '</tr>'; rowPct += '</tr>';
  const table = `<div class="tbl-wrap"><table><thead><tr>${head}</tr></thead><tbody>${rowRp}${rowPct}</tbody></table></div>`;
  return hppModuleWrap('Tren HPP Bahan Baku sbg % Pendapatan',
    'HPP Bahan Baku dibandingkan Omset Konsolidasi pada periode yg sama. Krn cuma 1 akun (blm ada breakdown per jenis bahan), tabelnya polos 2 baris -- beda dgn Opex yg dipecah per kategori.',
    table);
}

// --- FITUR 3: Deteksi Lonjakan -- yg dipantau RASIO thd Omset, bukan Rp
// mentah, krn Rp wajar naik-turun ngikutin volume produksi. Lonjakan
// beneran = rasionya melebar (harga bahan naik / makin boros / dll). ---
const HPP_ANOMALY_POIN = 2; // ambang: rasio thd Omset berubah >= 2 poin persentase
function computeHppAnomaly(prevIdx, lastIdx, mPrev, mLast){
  const u = UNIT_DATA.konsolidasi;
  const rev = getLineVals(u,'Pendapatan');
  const hpp = getHppBahanBakuSeries();
  const sumIdx = (vals, idxArr) => idxArr.reduce((s,i)=> vals[i]==null ? s : s+vals[i], 0);
  const hasAnyIdx = (vals, idxArr) => idxArr.some(i=>vals[i]!=null);
  if (!hasAnyIdx(hpp,prevIdx) || !hasAnyIdx(hpp,lastIdx)) return null;
  const h0 = sumIdx(hpp,prevIdx), h1 = sumIdx(hpp,lastIdx);
  const r0 = sumIdx(rev,prevIdx), r1 = sumIdx(rev,lastIdx);
  if (!r0 || !r1) return null;
  const pct0 = h0/r0*100, pct1 = h1/r1*100;
  const poin = pct1 - pct0;
  if (Math.abs(poin) < HPP_ANOMALY_POIN) return null;
  return { h0,h1,r0,r1,pct0,pct1,poin,mPrev,mLast };
}
function renderHppAnomaly(){
  const vIdx = getVisibleIdx();
  if (vIdx.length < 2) return hppModuleWrap('Deteksi Lonjakan HPP Bahan Baku','Pilih minimal 2 bulan di filter utk mendeteksi lonjakan (perlu bulan pembanding).','');
  return renderHppAnomalyImpl([vIdx[vIdx.length-2]],[vIdx[vIdx.length-1]], periodLabel(PERIODS[vIdx[vIdx.length-2]],false), periodLabel(PERIODS[vIdx[vIdx.length-1]],false));
}
function renderHppAnomalyImpl(prevIdx, lastIdx, mPrev, mLast){
  const r = computeHppAnomaly(prevIdx, lastIdx, mPrev, mLast);
  const note = `Membandingkan ${mLast} vs ${mPrev}. Yg dibandingkan RASIO HPP Bahan Baku thd Omset (bukan Rp mentah), krn Rp wajar naik-turun ngikutin volume produksi -- sinyal masalah beneran adalah kalau RASIO-nya melebar. Ambang: berubah ≥${HPP_ANOMALY_POIN} poin persentase.`;
  if (!r) {
    return hppModuleWrap('Deteksi Lonjakan HPP Bahan Baku', note,
      `<div style="color:#4ADE80;font-size:13px;padding:12px 0;">✓ Rasio HPP Bahan Baku thd Omset stabil antara ${mPrev} dan ${mLast} (blm melewati ambang ${HPP_ANOMALY_POIN} poin), atau data tak tersedia.</div>`);
  }
  const naik = r.poin >= 0;
  const body = `<div class="tbl-wrap"><table><thead><tr>
    <th style="text-align:left;padding:9px 14px;color:#726C9C;font-size:11px;text-transform:uppercase;border-bottom:1px solid #2A2650;">Periode</th>
    <th style="text-align:right;padding:9px 14px;color:#726C9C;font-size:11px;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">HPP Bahan Baku</th>
    <th style="text-align:right;padding:9px 14px;color:#726C9C;font-size:11px;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">Omset</th>
    <th style="text-align:right;padding:9px 14px;color:#726C9C;font-size:11px;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">Rasio</th>
  </tr></thead><tbody>
    <tr><td style="padding:8px 14px;font-size:12px;border-bottom:1px solid #2A2650;">${mPrev}</td>
      <td class="mono" style="text-align:right;padding:8px 14px;font-size:12px;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">${fmtRp(r.h0)}</td>
      <td class="mono" style="text-align:right;padding:8px 14px;font-size:12px;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">${fmtRp(r.r0)}</td>
      <td class="mono" style="text-align:right;padding:8px 14px;font-size:12px;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">${r.pct0.toFixed(1)}%</td></tr>
    <tr style="background:#241F1F;"><td style="padding:8px 14px;font-size:12px;color:#FB7185;border-bottom:1px solid #2A2650;">⚠ ${mLast}</td>
      <td class="mono" style="text-align:right;padding:8px 14px;font-size:12px;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">${fmtRp(r.h1)}</td>
      <td class="mono" style="text-align:right;padding:8px 14px;font-size:12px;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">${fmtRp(r.r1)}</td>
      <td class="mono" style="text-align:right;padding:8px 14px;font-size:12px;font-weight:700;color:#FB7185;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;">${r.pct1.toFixed(1)}% (${naik?'▲':'▼'}${Math.abs(r.poin).toFixed(1)} poin)</td></tr>
  </tbody></table></div>`;
  return hppModuleWrap('Deteksi Lonjakan HPP Bahan Baku', note, body);
}
function countHppAnomalies(){
  const vIdx = getVisibleIdx();
  if (vIdx.length < 2) return { count:0 };
  const r = computeHppAnomaly([vIdx[vIdx.length-2]],[vIdx[vIdx.length-1]], periodLabel(PERIODS[vIdx[vIdx.length-2]],false), periodLabel(PERIODS[vIdx[vIdx.length-1]],false));
  return r ? { count:1, ...r } : { count:0 };
}
function renderHppAnomalyNotice(currentKey){
  if (currentKey === 'hppAnomaly') return ''; // halaman detailnya sendiri, tak perlu notice ringkasan lagi
  const r = countHppAnomalies();
  if (!r.count) return '';
  const naik = r.poin >= 0;
  return `<div onclick="navigateTo('hppAnomaly')" style="cursor:pointer;background:#2A1B1B;border:1px solid #FB7185;border-radius:12px;padding:14px 18px;margin-bottom:16px;display:flex;align-items:center;gap:12px;">
    <span style="font-size:20px;">🔴</span>
    <div style="flex:1;">
      <div style="font-size:13px;font-weight:700;color:#FB7185;">Rasio HPP Bahan Baku thd Omset ${naik?'naik':'turun'} ${Math.abs(r.poin).toFixed(1)} poin (${r.mPrev}→${r.mLast})</div>
      <div style="font-size:11.5px;color:#C9A0A6;margin-top:2px;">${r.pct0.toFixed(1)}% → ${r.pct1.toFixed(1)}% -- klik utk detail.</div>
    </div>
    <span style="color:#FB7185;font-size:18px;">→</span>
  </div>`;
}
function renderHppModule(key, mode){
  mode = mode || 'bulanan';
  if (key==='hppRingkasan') return renderHppRingkasan();
  if (key==='hppTren') return renderHppTren(mode);
  if (key==='hppAnomaly') {
    if (mode==='bulanan') return renderHppAnomaly();
    const buckets = groupPeriodsBy(mode).map(b=>({kind:mode,...b})).filter(b=>b.complete);
    if (buckets.length < 2) return hppModuleWrap('Deteksi Lonjakan HPP Bahan Baku', '', `<div style="color:#726C9C;font-size:12.5px;padding:12px 0;">Butuh minimal 2 ${mode==='quarter'?'kuartal':'semester'} lengkap utk membandingkan.</div>`);
    const prev = buckets[buckets.length-2], last = buckets[buckets.length-1];
    return renderHppAnomalyImpl(prev.indices, last.indices, bucketLabel(mode,prev.key,prev.indices,true), bucketLabel(mode,last.key,last.indices,true));
  }
  return '';
}

let cabangRinciSelected = 'sudirman'; // residu dari navigasi lama (dropdown Cabang Rinci) -- tidak lagi dipakai sidebar baru, dibiarkan supaya fungsi setCabangRinci tak error kalau msh terpanggil dari tempat lain


function setCabangRinci(k){ cabangRinciSelected = k; render(); }

// ============ HALAMAN BERANDA (landing, sebelum pilih menu) ============
function renderHomePage(){
  ensureYearSelected();
  const yearIdx = monthsInYear(selectedYear);
  const u = UNIT_DATA.konsolidasi;
  const rev = getLineVals(u,'Pendapatan');
  const lo = getLineVals(u,'Laba Operasional');
  const lb = getLineVals(u,'Laba Bersih');
  const sumY = (vals) => vals ? yearIdx.reduce((s,i)=>s+(vals[i]||0),0) : 0;
  const totalPend = sumY(rev);
  const kpis = [
    { label:'Pendapatan', val: sumY(rev), color:'#4FC3F7', pct: null },
    { label:'Laba Operasional', val: sumY(lo), color:'#4ADE80', pct: totalPend ? sumY(lo)/totalPend*100 : null },
    { label:'Laba Bersih', val: sumY(lb), color:'#FFC93C', pct: totalPend ? sumY(lb)/totalPend*100 : null },
  ];
  const kpiHtml = kpis.map(k => `<div style="flex:1;min-width:180px;background:#171433;border:1px solid #2A2650;border-radius:14px;padding:18px 20px;">
    <div style="font-size:11px;color:#726C9C;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">${k.label} · ${selectedYear}</div>
    <div class="mono" style="font-size:22px;font-weight:700;color:${k.color};margin-top:6px;">${fmtRp(k.val)}${k.pct!==null?` <span style="font-size:13px;font-weight:600;color:#726C9C;">(${k.pct.toFixed(1)}%)</span>`:''}</div>
  </div>`).join('');

  const GROUP_META = {
    financialAnalysis: { color:'#4ADE80', desc:'Management Snapshot, Health Score, Trend, Profit Drivers, Anomali, Outlet, Forecast', svg:`<path d="M3 3v18h18"/><path d="M7 16l3-4 3 2 4-6"/><circle cx="17" cy="8" r="1.4"/>`, target:'faOverview' },
    pnlUtama:  { color:'#FFC93C', desc:'Konsolidasi, Ownership, Split Online/Offline', svg:`<path d="M3 8l9-5 9 5-9 5-9-5z"/><path d="M3 12l9 5 9-5"/><path d="M3 16l9 5 9-5"/>`, target:'konsolidasi' },
    pnlUnit:   { color:'#A78BFA', desc:'Store & Brand, Manufaktur, Head Office, Franchise', svg:`<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>`, target:'store' },
    pnlCabang: { color:'#4ADE80', desc:'Sanding P&L Biasa vs Franchise, multi-bakery', svg:`<path d="M12 21s-7-6.5-7-11.5A7 7 0 0 1 19 9.5C19 14.5 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.3"/>`, target:'cabangDiff' },
    opex:      { color:'#FB7185', desc:'Tren, Pareto, Efisiensi, Lonjakan, Leverage', svg:`<path d="M12 2s-5 5.5-5 10a5 5 0 0 0 10 0c0-1.5-.7-2.8-1.5-4 .1 1.5-.6 2.5-1.6 2.5.6-3-1-6.5-1.9-8.5z"/>`, target:'opexTrend' },
    hpp:       { color:'#F4A6D0', desc:'Ringkasan, tren & deteksi lonjakan akun HPP Bahan Baku', svg:`<path d="M21 8l-9-5-9 5 9 5 9-5z"/><path d="M3 8v8l9 5 9-5V8"/><path d="M12 13v8"/>`, target:'hppRingkasan' },
    kebocoran: { color:'#F97316', desc:'Diskon & komisi -- analisis kebocoran pendapatan', svg:`<path d="M12 3s5 6 5 10a5 5 0 0 1-10 0c0-1.2.4-2.4 1-3.5"/><path d="M4 20l16-16"/>`, target:'leakRingkas' },
    grafik:    { color:'#E066FF', desc:'Grafik custom -- pilih metrik & rentang', svg:`<path d="M3 3v18h18"/><path d="M7 15l4-5 3 3 5-7"/>`, target:'grafikCustom' },
    validasi:  { color:'#38BDF8', desc:'Cek integritas data otomatis', svg:`<path d="M9 12l2 2 4-4"/><path d="M12 3l7 3v6c0 4-3 7.5-7 9-4-1.5-7-5-7-9V6z"/>`, target:'rekonsiliasi' },
    bank:      { color:'#4FC3F7', desc:'Belum tersedia', svg:`<path d="M3 10l9-6 9 6"/><path d="M5 10v9M9 10v9M15 10v9M19 10v9"/><path d="M3 21h18"/>`, target:'bankPlaceholder' },
  };
  const cardsHtml = SIDEBAR_GROUPS.map(g => {
    const m = GROUP_META[g.key];
    return `<div onclick="navigateTo('${m.target}')" style="cursor:pointer;flex:1;min-width:200px;background:#171433;border:1px solid #2A2650;border-radius:14px;padding:20px;transition:border-color .15s;" onmouseover="this.style.borderColor='${m.color}'" onmouseout="this.style.borderColor='#2A2650'">
      <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="${m.color}" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom:10px;">${m.svg}</svg>
      <div style="font-family:'Space Grotesk',sans-serif;font-size:15px;font-weight:700;color:#F5F3FF;margin-bottom:4px;">${g.label}</div>
      <div style="font-size:11.5px;color:#726C9C;line-height:1.4;">${m.desc}</div>
    </div>`;
  }).join('');

  const anomaly = countOpexAnomalies();
  const anomalyHtml = anomaly.count ? `<div onclick="navigateTo('opexAnomaly')" style="cursor:pointer;background:#2A1B1B;border:1px solid #FB7185;border-radius:12px;padding:14px 18px;margin-bottom:22px;display:flex;align-items:center;gap:12px;">
    <span style="font-size:20px;">🔴</span>
    <div style="flex:1;"><div style="font-size:13px;font-weight:700;color:#FB7185;">${anomaly.count} item biaya melonjak (${anomaly.mPrev}→${anomaly.mLast})</div>
    <div style="font-size:11.5px;color:#C9A0A6;margin-top:2px;">Klik untuk lihat detail di modul Opex.</div></div>
    <span style="color:#FB7185;font-size:18px;">→</span>
  </div>` : '';

  return `
    <div style="margin-bottom:6px;font-family:'Space Grotesk',sans-serif;font-size:13px;font-weight:700;color:#FFC93C;text-transform:uppercase;letter-spacing:.05em;">Selamat datang</div>
    <div style="font-family:'Space Grotesk',sans-serif;font-size:30px;font-weight:800;color:#F5F3FF;margin-bottom:22px;">PT Inovasi Sukses Persada</div>
    <div style="display:flex;gap:14px;flex-wrap:wrap;margin-bottom:22px;">${kpiHtml}</div>
    ${anomalyHtml}
    <div style="font-size:11px;color:#726C9C;font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-bottom:10px;">Pilih menu</div>
    <div style="display:flex;gap:14px;flex-wrap:wrap;">${cardsHtml}</div>
  `;
}

// ============ GRAFIK PER CABANG (multi-outlet, multi-periode) ============
const GRAFIK_CABANG_METRICS = [
  { key:'Pendapatan',       label:'Pendapatan',        rowName:'Pendapatan' },
  { key:'Opex',             label:'Opex',              rowName:'Biaya Operasional' },
  { key:'LabaOperasional',  label:'Laba Operasional',  rowName:'Laba Operasional' },
  { key:'LabaBersih',       label:'Laba Bersih',       rowName:'Laba Bersih' },
];
const GRAFIK_CABANG_COLORS = ['#4FC3F7','#FFC93C','#B4E61D','#22D3C5','#A78BFA','#F4A6D0','#FF9F43','#54A0FF','#A0E86A','#E066FF','#FB7185','#FFD93D','#6BCB77','#4ADE80'];
let grafikCabangMetric = 'Pendapatan'; // single-select toggle
let grafikCabangSelected = new Set([...OUTLET_KEYS].sort((a,b)=>UNIT_DATA[a].label.localeCompare(UNIT_DATA[b].label,'id')).slice(0,4)); // default 4 outlet pertama (abjad)
let grafikCabangFromIdx = null;
let grafikCabangToIdx = null;
function grafikCabangRangeDefaults(){
  if (grafikCabangFromIdx === null) grafikCabangFromIdx = 0;
  if (grafikCabangToIdx === null) grafikCabangToIdx = PERIODS.length - 1;
  if (grafikCabangFromIdx > grafikCabangToIdx) { const t=grafikCabangFromIdx; grafikCabangFromIdx=grafikCabangToIdx; grafikCabangToIdx=t; }
}
function setGrafikCabangMetric(k){ grafikCabangMetric = k; render(); }
function toggleGrafikCabangOutlet(k){
  if (grafikCabangSelected.has(k)) grafikCabangSelected.delete(k); else grafikCabangSelected.add(k);
  render();
}
function grafikCabangSelectAll(){ grafikCabangSelected = new Set(OUTLET_KEYS); render(); }
function grafikCabangClearAll(){ grafikCabangSelected = new Set(); render(); }
function setGrafikCabangFrom(i){ grafikCabangFromIdx = parseInt(i); render(); }
function setGrafikCabangTo(i){ grafikCabangToIdx = parseInt(i); render(); }
function renderGrafikCabang(){
  grafikCabangRangeDefaults();
  const metricChips = GRAFIK_CABANG_METRICS.map(m => {
    const on = grafikCabangMetric === m.key;
    return `<div onclick="setGrafikCabangMetric('${m.key}')" style="cursor:pointer;padding:7px 14px;border-radius:20px;font-size:12.5px;font-weight:700;font-family:'Space Grotesk',sans-serif;border:1px solid ${on?'#4ADE80':'#2A2650'};color:${on?'#4ADE80':'#726C9C'};background:${on?'#4ADE801a':'transparent'};">${m.label}</div>`;
  }).join('');
  const sortedOutlets = [...OUTLET_KEYS].sort((a,b)=>UNIT_DATA[a].label.localeCompare(UNIT_DATA[b].label,'id'));
  const outletChips = sortedOutlets.map((k,i) => {
    const on = grafikCabangSelected.has(k);
    const color = GRAFIK_CABANG_COLORS[i % GRAFIK_CABANG_COLORS.length];
    return `<div onclick="toggleGrafikCabangOutlet('${k}')" style="cursor:pointer;padding:6px 12px;border-radius:20px;font-size:12px;font-weight:600;font-family:'Space Grotesk',sans-serif;border:1px solid ${on?color:'#2A2650'};color:${on?color:'#726C9C'};background:${on?color+'1a':'transparent'};">${UNIT_DATA[k].label}</div>`;
  }).join('');
  return `
    <div style="margin-top:36px;padding-top:28px;border-top:1px solid #2A2650;">
      <div style="font-family:'Space Grotesk',sans-serif;font-size:18px;font-weight:700;color:#F5F3FF;margin-bottom:4px;">Grafik PNL Cabang</div>
      <div style="font-size:11.5px;color:#726C9C;margin-bottom:18px;">Bandingkan beberapa outlet sekaligus, 1 metrik, rentang bebas.</div>
      <div style="margin-bottom:14px;">
        <div style="font-size:11px;color:#726C9C;font-weight:600;margin-bottom:8px;">PILIH METRIK:</div>
        <div style="display:flex;flex-wrap:wrap;gap:8px;">${metricChips}</div>
      </div>
      <div style="margin-bottom:16px;">
        <div style="font-size:11px;color:#726C9C;font-weight:600;margin-bottom:8px;">PILIH OUTLET (${grafikCabangSelected.size} dipilih):
          <span onclick="grafikCabangSelectAll()" style="cursor:pointer;color:#9B93C4;text-decoration:underline;font-weight:400;margin-left:8px;">Pilih Semua</span>
          <span onclick="grafikCabangClearAll()" style="cursor:pointer;color:#9B93C4;text-decoration:underline;font-weight:400;margin-left:10px;">Kosongkan</span>
        </div>
        <div style="display:flex;flex-wrap:wrap;gap:7px;max-width:900px;">${outletChips}</div>
      </div>
      <div style="display:flex;gap:14px;align-items:center;margin-bottom:20px;flex-wrap:wrap;">
        <div>
          <div style="font-size:11px;color:#726C9C;font-weight:600;margin-bottom:6px;">DARI:</div>
          <select onchange="setGrafikCabangFrom(this.value)" style="background:#171433;border:1px solid #2A2650;color:#F5F3FF;border-radius:8px;padding:7px 12px;font-size:12.5px;">
            ${PERIODS.map((p,i)=>`<option value="${i}" ${grafikCabangFromIdx===i?'selected':''}>${periodLabel(p,true)}</option>`).join('')}
          </select>
        </div>
        <div style="color:#726C9C;margin-top:18px;">→</div>
        <div>
          <div style="font-size:11px;color:#726C9C;font-weight:600;margin-bottom:6px;">SAMPAI:</div>
          <select onchange="setGrafikCabangTo(this.value)" style="background:#171433;border:1px solid #2A2650;color:#F5F3FF;border-radius:8px;padding:7px 12px;font-size:12.5px;">
            ${PERIODS.map((p,i)=>`<option value="${i}" ${grafikCabangToIdx===i?'selected':''}>${periodLabel(p,true)}</option>`).join('')}
          </select>
        </div>
      </div>
      ${grafikCabangSelected.size === 0
        ? `<div style="color:#726C9C;font-size:12.5px;padding:20px;text-align:center;">Pilih minimal 1 outlet utk menampilkan grafik.</div>`
        : `<div style="background:#171433;border:1px solid #2A2650;border-radius:12px;padding:16px;height:380px;position:relative;"><div id="grafikCabangChart" style="width:100%;height:100%;"></div></div>`}
    </div>
  `;
}
let grafikCabangChartInstance = null;
function drawGrafikCabangChart(){
  const ctx = document.getElementById('grafikCabangChart');
  if (!ctx) return;
  if (grafikCabangSelected.size === 0) return; // pesan "pilih outlet" sudah ditangani di renderGrafikCabang
  try {
    grafikCabangRangeDefaults();
    const idxRange = [];
    for (let i = grafikCabangFromIdx; i <= grafikCabangToIdx; i++) idxRange.push(i);
    const labels = idxRange.map(i => periodLabel(PERIODS[i], true));
    const metricMeta = GRAFIK_CABANG_METRICS.find(m=>m.key===grafikCabangMetric);
    const sortedOutlets = [...OUTLET_KEYS].sort((a,b)=>UNIT_DATA[a].label.localeCompare(UNIT_DATA[b].label,'id'));
    const ds = sortedOutlets.filter(k=>grafikCabangSelected.has(k)).map(k => {
      const idx = sortedOutlets.indexOf(k);
      const color = GRAFIK_CABANG_COLORS[idx % GRAFIK_CABANG_COLORS.length];
      const series = getLineVals(UNIT_DATA[k], metricMeta.rowName) || [];
      return { label: UNIT_DATA[k].label, data: idxRange.map(i=> series[i]!=null ? series[i] : null), color, isCost:false };
    });
    renderSvgLineChart('grafikCabangChart', ds, labels, { dualAxis:false });
  } catch(e){ console.error('drawGrafikCabangChart error:', e); if(ctx) ctx.innerHTML = `<div style="color:#FB7185;font-size:12px;padding:16px;">⚠️ Grafik gagal digambar: ${e.message}<br><span style="color:#726C9C;font-size:10.5px;">Screenshot pesan ini & kirim ke saya.</span></div>`; }
}

// ============ GRAFIK #3: PNL CABANG VERSI FRANCHISE (multi-outlet) ============
// Struktur PERSIS sama dgn Grafik PNL Cabang biasa di atas, tapi sumber data
// pakai franchiseOutletSeries() (HPP dibatasi 60%) bukan waterfall biasa.
// Sengaja DIPISAH (bukan digabung ke picker segmen di atas) sesuai instruksi user.
const GRAFIK_CABANG_FR_METRICS = [
  { key:'Pendapatan',       label:'Pendapatan',        fKey:'Pendapatan' },
  { key:'Opex',             label:'Opex',              fKey:'Opex' },
  { key:'LabaOperasional',  label:'Laba Operasional',  fKey:'LabaOperasional' },
  { key:'LabaBersih',       label:'Laba Bersih',       fKey:'LabaBersih' },
];
let grafikCabangFrMetric = 'Pendapatan';
let grafikCabangFrSelected = new Set(); // default kosong -- diisi otomatis ke outlet yg PUNYA data saat pertama render
let grafikCabangFrFromIdx = null;
let grafikCabangFrToIdx = null;
function grafikCabangFrRangeDefaults(){
  if (grafikCabangFrFromIdx === null) grafikCabangFrFromIdx = 0;
  if (grafikCabangFrToIdx === null) grafikCabangFrToIdx = PERIODS.length - 1;
  if (grafikCabangFrFromIdx > grafikCabangFrToIdx) { const t=grafikCabangFrFromIdx; grafikCabangFrFromIdx=grafikCabangFrToIdx; grafikCabangFrToIdx=t; }
}
function ensureGrafikCabangFrSelected(){
  if (grafikCabangFrSelected.size > 0) return;
  const withData = OUTLET_KEYS.filter(k => parseOnlineRowsRaw().some(r => r.entity === ENTITY_KEY_MAP[k]));
  grafikCabangFrSelected = new Set(withData.length ? withData : []);
}
function setGrafikCabangFrMetric(k){ grafikCabangFrMetric = k; render(); }
function toggleGrafikCabangFrOutlet(k){
  if (grafikCabangFrSelected.has(k)) grafikCabangFrSelected.delete(k); else grafikCabangFrSelected.add(k);
  render();
}
function grafikCabangFrSelectAll(){ grafikCabangFrSelected = new Set(OUTLET_KEYS); render(); }
function grafikCabangFrClearAll(){ grafikCabangFrSelected = new Set(); render(); }
function setGrafikCabangFrFrom(i){ grafikCabangFrFromIdx = parseInt(i); render(); }
function setGrafikCabangFrTo(i){ grafikCabangFrToIdx = parseInt(i); render(); }
function renderGrafikCabangFranchise(){
  grafikCabangFrRangeDefaults();
  ensureGrafikCabangFrSelected();
  const metricChips = GRAFIK_CABANG_FR_METRICS.map(m => {
    const on = grafikCabangFrMetric === m.key;
    return `<div onclick="setGrafikCabangFrMetric('${m.key}')" style="cursor:pointer;padding:7px 14px;border-radius:20px;font-size:12.5px;font-weight:700;font-family:'Space Grotesk',sans-serif;border:1px solid ${on?'#22D3C5':'#2A2650'};color:${on?'#22D3C5':'#726C9C'};background:${on?'#22D3C51a':'transparent'};">${m.label}</div>`;
  }).join('');
  const sortedOutlets = [...OUTLET_KEYS].sort((a,b)=>UNIT_DATA[a].label.localeCompare(UNIT_DATA[b].label,'id'));
  const withData = new Set(OUTLET_KEYS.filter(k => parseOnlineRowsRaw().some(r => r.entity === ENTITY_KEY_MAP[k])));
  const outletChips = sortedOutlets.map((k,i) => {
    const on = grafikCabangFrSelected.has(k);
    const hasData = withData.has(k);
    const color = GRAFIK_CABANG_COLORS[i % GRAFIK_CABANG_COLORS.length];
    return `<div onclick="toggleGrafikCabangFrOutlet('${k}')" style="cursor:pointer;padding:6px 12px;border-radius:20px;font-size:12px;font-weight:600;font-family:'Space Grotesk',sans-serif;border:1px solid ${on?color:'#2A2650'};color:${on?color:(hasData?'#9B93C4':'#4A4568')};background:${on?color+'1a':'transparent'};">${UNIT_DATA[k].label}${hasData?' ●':''}</div>`;
  }).join('');
  return `
    <div style="margin-top:36px;padding-top:28px;border-top:1px solid #2A2650;">
      <div style="font-family:'Space Grotesk',sans-serif;font-size:18px;font-weight:700;color:#22D3C5;margin-bottom:4px;">Grafik PNL Cabang Versi Franchise</div>
      <div style="font-size:11.5px;color:#726C9C;margin-bottom:18px;">Sama spt Grafik PNL Cabang di atas, tapi HPP dibatasi 60% (versi Franchise) -- ● = outlet sudah ada data sheet Online.</div>
      <div style="margin-bottom:14px;">
        <div style="font-size:11px;color:#726C9C;font-weight:600;margin-bottom:8px;">PILIH METRIK:</div>
        <div style="display:flex;flex-wrap:wrap;gap:8px;">${metricChips}</div>
      </div>
      <div style="margin-bottom:16px;">
        <div style="font-size:11px;color:#726C9C;font-weight:600;margin-bottom:8px;">PILIH OUTLET (${grafikCabangFrSelected.size} dipilih):
          <span onclick="grafikCabangFrSelectAll()" style="cursor:pointer;color:#9B93C4;text-decoration:underline;font-weight:400;margin-left:8px;">Pilih Semua</span>
          <span onclick="grafikCabangFrClearAll()" style="cursor:pointer;color:#9B93C4;text-decoration:underline;font-weight:400;margin-left:10px;">Kosongkan</span>
        </div>
        <div style="display:flex;flex-wrap:wrap;gap:7px;max-width:900px;">${outletChips}</div>
      </div>
      <div style="display:flex;gap:14px;align-items:center;margin-bottom:20px;flex-wrap:wrap;">
        <div>
          <div style="font-size:11px;color:#726C9C;font-weight:600;margin-bottom:6px;">DARI:</div>
          <select onchange="setGrafikCabangFrFrom(this.value)" style="background:#171433;border:1px solid #2A2650;color:#F5F3FF;border-radius:8px;padding:7px 12px;font-size:12.5px;">
            ${PERIODS.map((p,i)=>`<option value="${i}" ${grafikCabangFrFromIdx===i?'selected':''}>${periodLabel(p,true)}</option>`).join('')}
          </select>
        </div>
        <div style="color:#726C9C;margin-top:18px;">→</div>
        <div>
          <div style="font-size:11px;color:#726C9C;font-weight:600;margin-bottom:6px;">SAMPAI:</div>
          <select onchange="setGrafikCabangFrTo(this.value)" style="background:#171433;border:1px solid #2A2650;color:#F5F3FF;border-radius:8px;padding:7px 12px;font-size:12.5px;">
            ${PERIODS.map((p,i)=>`<option value="${i}" ${grafikCabangFrToIdx===i?'selected':''}>${periodLabel(p,true)}</option>`).join('')}
          </select>
        </div>
      </div>
      ${grafikCabangFrSelected.size === 0
        ? `<div style="color:#726C9C;font-size:12.5px;padding:20px;text-align:center;">Pilih minimal 1 outlet utk menampilkan grafik.</div>`
        : `<div style="background:#171433;border:1px solid #2A2650;border-radius:12px;padding:16px;height:380px;position:relative;"><div id="grafikCabangFrChart" style="width:100%;height:100%;"></div></div>`}
    </div>
  `;
}
let grafikCabangFrChartInstance = null;
function drawGrafikCabangFranchiseChart(){
  const ctx = document.getElementById('grafikCabangFrChart');
  if (!ctx) return;
  if (grafikCabangFrSelected.size === 0) return; // pesan "pilih outlet" sudah ditangani di renderGrafikCabangFranchise
  try {
    grafikCabangFrRangeDefaults();
    const idxRange = [];
    for (let i = grafikCabangFrFromIdx; i <= grafikCabangFrToIdx; i++) idxRange.push(i);
    const labels = idxRange.map(i => periodLabel(PERIODS[i], true));
    const metricMeta = GRAFIK_CABANG_FR_METRICS.find(m=>m.key===grafikCabangFrMetric);
    const sortedOutlets = [...OUTLET_KEYS].sort((a,b)=>UNIT_DATA[a].label.localeCompare(UNIT_DATA[b].label,'id'));
    const ds = sortedOutlets.filter(k=>grafikCabangFrSelected.has(k)).map(k => {
      const idx = sortedOutlets.indexOf(k);
      const color = GRAFIK_CABANG_COLORS[idx % GRAFIK_CABANG_COLORS.length];
      const series = franchiseOutletSeries(k, metricMeta.fKey) || [];
      return { label: UNIT_DATA[k].label, data: idxRange.map(i=> series[i]!=null ? series[i] : null), color, isCost:false };
    });
    renderSvgLineChart('grafikCabangFrChart', ds, labels, { dualAxis:false });
  } catch(e){ console.error('drawGrafikCabangFranchiseChart error:', e); if(ctx) ctx.innerHTML = `<div style="color:#FB7185;font-size:12px;padding:16px;">⚠️ Grafik gagal digambar: ${e.message}<br><span style="color:#726C9C;font-size:10.5px;">Screenshot pesan ini & kirim ke saya.</span></div>`; }
}

// ============ GRAFIK #4: SEBARAN PROFITABILITAS OUTLET (scatter) ============
// Omset (X) vs Laba Operasional (Y), 1 titik per outlet, dilabeli nama bakery
// langsung (bukan angka) -- warna titik = kesehatan margin.
let grafikScatterYear = null;
let grafikScatterIdx = null; // array index bulan terpilih -- null = belum diinisialisasi
let grafikScatterVersion = 'biasa'; // 'biasa' | 'franchise'
function ensureGrafikScatterPeriod(){
  const years = availableYearsAll();
  if (!years.length) return;
  if (grafikScatterYear===null || !years.includes(grafikScatterYear)) {
    grafikScatterYear = years[years.length-1];
    grafikScatterIdx = latestMonthOfYear(grafikScatterYear);
  }
}
function setGrafikScatterYear(y){ grafikScatterYear=y; grafikScatterIdx=latestMonthOfYear(y); render(); }
function toggleGrafikScatterMonth(i){
  if (!grafikScatterIdx || !grafikScatterIdx.length) grafikScatterIdx=[];
  const pos = grafikScatterIdx.indexOf(i);
  if (pos>=0) grafikScatterIdx.splice(pos,1); else grafikScatterIdx.push(i);
  if (grafikScatterIdx.length===0) grafikScatterIdx=[i];
  render();
}
function setGrafikScatterVersion(v){ grafikScatterVersion=v; render(); }
function computeScatterPoint(outletKey, idxList){
  if (grafikScatterVersion === 'franchise') {
    const d = computeFranchiseOutlet(outletKey, idxList);
    if (!d.hasOnlineData) return null;
    return { omset:d.jumlahPendapatan, laba:d.labaOperasional };
  }
  const pend = getLineVals(UNIT_DATA[outletKey],'Pendapatan');
  const lo = getLineVals(UNIT_DATA[outletKey],'Laba Operasional');
  if (!pend || !lo) return null;
  const omset = idxList.reduce((s,i)=>s+(pend[i]||0),0);
  const laba = idxList.reduce((s,i)=>s+(lo[i]!=null?lo[i]:0),0);
  if (!omset) return null;
  return { omset, laba };
}
function renderGrafikScatter(){
  ensureGrafikScatterPeriod();
  const years = availableYearsAll();
  const yearChips = years.map(y => `<div onclick="setGrafikScatterYear(${y})" style="cursor:pointer;padding:6px 14px;border-radius:20px;font-size:12.5px;font-weight:700;font-family:'Space Grotesk',sans-serif;border:1px solid ${grafikScatterYear===y?'#FFC93C':'#2A2650'};color:${grafikScatterYear===y?'#FFC93C':'#726C9C'};background:${grafikScatterYear===y?'#FFC93C22':'transparent'};">${y}</div>`).join('');
  const monthChips = Array.from({length:12},(_,m) => {
    const mm = String(m+1).padStart(2,'0');
    const idx = PERIODS.indexOf(`${grafikScatterYear}-${mm}`);
    const label = MONTH_NAMES_ID[m];
    if (idx===-1) return `<div style="padding:5px 12px;border-radius:20px;font-size:12px;font-weight:600;font-family:'Space Grotesk',sans-serif;border:1px dashed #2A2650;color:#4A4568;">${label}</div>`;
    const on = grafikScatterIdx && grafikScatterIdx.includes(idx);
    return `<div onclick="toggleGrafikScatterMonth(${idx})" style="cursor:pointer;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:600;font-family:'Space Grotesk',sans-serif;border:1px solid ${on?'#4ADE80':'#2A2650'};color:${on?'#4ADE80':'#726C9C'};background:${on?'#4ADE8022':'transparent'};">${label}</div>`;
  }).join('');
  const versionToggle = ['biasa','franchise'].map(v => {
    const lbl = v==='biasa' ? 'Biasa' : 'Versi Franchise (HPP 60%)';
    return `<div onclick="setGrafikScatterVersion('${v}')" style="cursor:pointer;padding:7px 14px;border-radius:20px;font-size:12.5px;font-weight:700;font-family:'Space Grotesk',sans-serif;border:1px solid ${grafikScatterVersion===v?'#22D3C5':'#2A2650'};color:${grafikScatterVersion===v?'#22D3C5':'#726C9C'};background:${grafikScatterVersion===v?'#22D3C51a':'transparent'};">${lbl}</div>`;
  }).join('');
  return `
    <div style="margin-top:36px;padding-top:28px;border-top:1px solid #2A2650;">
      <div style="font-family:'Space Grotesk',sans-serif;font-size:18px;font-weight:700;color:#F5F3FF;margin-bottom:4px;">Sebaran Profitabilitas Outlet</div>
      <div style="font-size:11.5px;color:#726C9C;margin-bottom:18px;">1 titik = 1 outlet. Sumbu X = Omset, sumbu Y = Laba Operasional. Warna titik = kesehatan margin (hijau &gt;15%, kuning 5-15%, merah di bawah itu/rugi). Pilih beberapa bulan sekaligus utk lihat performa gabungan periode itu.</div>
      <div style="margin-bottom:14px;">
        <div style="font-size:11px;color:#726C9C;font-weight:600;margin-bottom:8px;">VERSI DATA:</div>
        <div style="display:flex;flex-wrap:wrap;gap:8px;">${versionToggle}</div>
      </div>
      <div style="margin-bottom:8px;">
        <div style="font-size:11px;color:#726C9C;font-weight:600;margin-bottom:8px;">TAHUN:</div>
        <div style="display:flex;flex-wrap:wrap;gap:7px;">${yearChips}</div>
      </div>
      <div style="margin-bottom:18px;">
        <div style="font-size:11px;color:#726C9C;font-weight:600;margin-bottom:8px;">BULAN (bisa lebih dari 1, digabung):</div>
        <div style="display:flex;flex-wrap:wrap;gap:7px;">${monthChips}</div>
      </div>
      <div style="background:#171433;border:1px solid #2A2650;border-radius:12px;padding:16px;height:420px;position:relative;">
        <div id="grafikScatterChart" style="width:100%;height:100%;"></div>
      </div>
    </div>
  `;
}
let grafikScatterChartInstance = null;
// Ukur ukuran PIKSEL SUNGGUHAN canvas grafik setelah semua digambar --
// kalau tinggi/lebar 0, itu bukti kuat masalahnya soal sizing CSS
// (container tak punya position:relative, dsb), BUKAN soal data/library.
function reportGrafikCanvasSizes(){
  const span = document.getElementById('grafikDiagCanvasSize');
  if (!span) return;
  try {
    const ids = ['grafikCustomChart','grafikCabangChart','grafikCabangFrChart','grafikScatterChart'];
    const sizes = ids.map(id => {
      const el = document.getElementById(id);
      if (!el || typeof el.getBoundingClientRect !== 'function') return `${id}: (elemen tak ada)`;
      const r = el.getBoundingClientRect();
      return `${id}: ${Math.round(r.width)}×${Math.round(r.height)}px`;
    });
    span.innerHTML = 'Ukuran canvas sungguhan -- ' + sizes.join(' | ');
  } catch(e) {
    span.innerHTML = 'Ukuran canvas: gagal diukur (' + e.message + ')';
  }
}
function drawGrafikScatterChart(){
  const ctx = document.getElementById('grafikScatterChart');
  if (!ctx) return;
  try {
    ensureGrafikScatterPeriod();
    const idxList = (grafikScatterIdx && grafikScatterIdx.length) ? grafikScatterIdx : latestMonthOfYear(grafikScatterYear);
    const points = OUTLET_KEYS.map(k => {
      const p = computeScatterPoint(k, idxList);
      if (!p) return null;
      const margin = p.omset ? p.laba/p.omset : 0;
      const color = p.laba<0 ? '#d03b3b' : (margin>=0.15 ? '#0ca30c' : '#fab219');
      return { x:p.omset, y:p.laba, n:UNIT_DATA[k].label, color };
    }).filter(Boolean);
    const periodTxt = idxList.map(i=>periodLabel(PERIODS[i],true)).join(', ');
    renderSvgScatterChart('grafikScatterChart', points, {
      yLabel: 'Laba Operasional',
      emptyMsg: `Tidak ada outlet dgn data Pendapatan utk periode ${periodTxt} (versi: ${grafikScatterVersion==='franchise'?'Franchise':'Biasa'}).\nCoba ganti bulan/tahun, atau kalau versi Franchise, pastikan sheet Online sudah terisi.`,
    });
  } catch(e){ console.error('drawGrafikScatterChart error:', e); if(ctx) ctx.innerHTML = `<div style="color:#FB7185;font-size:12px;padding:16px;">⚠️ Grafik gagal digambar: ${e.message}<br><span style="color:#726C9C;font-size:10.5px;">Screenshot pesan ini & kirim ke saya.</span></div>`; }
}

function render(){
  try {
    renderInner();
  } catch(err) {
    // JARING PENGAMAN TERAKHIR: kalau ADA error tak terduga di mana pun dalam
    // proses render (mis. index PERIODS tak valid, data belum siap, dll),
    // JANGAN biarkan halaman diam2 menampilkan konten LAMA yg membingungkan
    // (gejala yg pernah terjadi: judul berubah tapi isi tak ikut berubah).
    // Tampilkan pesan error yg JELAS di #content supaya user tahu ada
    // masalah, dan console.error utk saya bisa diagnosis dari pesan itu.
    console.error('render() gagal:', err);
    const c = document.getElementById('content');
    if (c) c.innerHTML = `<div style="background:#2A1B1B;border:1px solid #FB7185;border-radius:12px;padding:18px 20px;color:#FB7185;font-size:13px;">⚠️ Terjadi error saat menampilkan halaman ini: <b>${err.message}</b><br><span style="color:#C9A0A6;font-size:11.5px;">Coba klik menu lain lalu kembali, atau Refresh. Kalau berulang, screenshot pesan ini.</span></div>`;
  }
}
function renderInner(){
  renderTabs();

  // ===== Financial Analysis (Phase 1 -- Financial Intelligence Dashboard) =====
  // Dispatch DULUAN sebelum `UNIT_DATA[state.unit]` di bawah, krn key2 ini
  // BUKAN entity di UNIT_DATA (akan undefined -> u.label crash kalau tidak
  // di-guard di sini). Semua kalkulasi & render ada di financial-analysis.js.
  if (['faOverview','faTrend','faExpense','faOutlet','faForecast'].includes(state.unit)) {
    const faTitles = { faOverview:'Overview', faTrend:'Trend', faExpense:'Expense', faOutlet:'Outlet Performance', faForecast:'Forecast' };
    document.getElementById('analysisTabs').innerHTML = '';
    document.getElementById('monthFilterBar').innerHTML = '';
    document.getElementById('bannerArea').innerHTML = '';
    document.getElementById('secEyebrow').textContent = 'Financial Analysis';
    document.getElementById('secEyebrow').style.color = '#4ADE80';
    document.getElementById('secTitle').textContent = faTitles[state.unit];
    renderFinancialAnalysis();
    return;
  }

  const u = UNIT_DATA[state.unit];
  document.getElementById('secEyebrow').textContent = u.label;
  document.getElementById('secEyebrow').style.color = u.color;

  const content = document.getElementById('content');
  if (state.unit === 'home') {
    document.getElementById('analysisTabs').innerHTML = '';
    document.getElementById('monthFilterBar').innerHTML = '';
    document.getElementById('bannerArea').innerHTML = '';
    document.getElementById('secTitle').textContent = '';
    document.getElementById('secEyebrow').textContent = '';
    content.innerHTML = renderHomePage();
    return;
  }
  if (state.unit === 'outletPerf') {
    // Tab spesial: bukan waterfall 1 entity, tapi perbandingan 14 outlet.
    // Sub-tabs analysis biasa (Preview/MoM/YoY/dst) & filter bulan bawaan
    // TIDAK relevan di sini -- tab ini punya kontrol sendiri (Top N / Filter
    // Manual), disembunyikan supaya tidak membingungkan.
    document.getElementById('analysisTabs').innerHTML = '';
    document.getElementById('monthFilterBar').innerHTML = '';
    document.getElementById('bannerArea').innerHTML = '';
    document.getElementById('secTitle').textContent = 'Perbandingan Outlet';
    content.innerHTML = renderOutletPerf();
    initOutletPerf();
    return;
  }

  if (state.unit === 'franchiseOutletPerf') {
    state.analysis = 'preview';
    document.getElementById('analysisTabs').innerHTML = '';
    document.getElementById('bannerArea').innerHTML = '';
    document.getElementById('secTitle').textContent = 'Perbandingan PNL Cabang (Franchise)';
    const mode = getPeriodMode('franchiseOutletPerf');
    const modeTabs = renderPeriodModeTabs('franchiseOutletPerf', '#22D3C5', [['bulanan','Bulanan'],['kuartal','Kuartal'],['semester','Semester']]);
    if (mode === 'bulanan') {
      // Bulan yg dipilih di filter chip DIGABUNG (dijumlah) -- konsisten dgn
      // SEMUA halaman lain di dashboard ini yg pakai month-filter-bar. Pilih
      // Mei+Juni -> tampil performa GABUNGAN 2 bulan itu, bukan dipisah.
      // TAMBAHAN: kalau >=2 bulan dipilih, tampilkan jg tabel tren per-bulan
      // (Omset & Laba Operasional) supaya pergerakannya tetap kelihatan.
      renderMonthFilterBar();
      const vIdx = getVisibleIdx();
      const trendOutlets = [...OUTLET_KEYS].sort((a,b)=>UNIT_DATA[a].label.localeCompare(UNIT_DATA[b].label,'id')).filter(k => parseOnlineRowsRaw().some(r => r.entity === ENTITY_KEY_MAP[k]));
      const trendHtml = renderFranchiseMonthTrend(trendOutlets, vIdx);
      content.innerHTML = modeTabs + trendHtml + renderCabangFranchiseCompare(vIdx);
    } else {
      document.getElementById('monthFilterBar').innerHTML = '';
      const bucketMode = mode === 'kuartal' ? 'quarter' : 'semester';
      const pick = renderMultiBucketPicker('franchiseOutletPerf', bucketMode, '#22D3C5');
      content.innerHTML = modeTabs + pick.html + renderCabangFranchiseCompare(pick.blocks);
    }
    return;
  }

  if (state.unit.startsWith('franchiseOutlet_')) {
    state.analysis = 'preview';
    const outletKey = state.unit.replace('franchiseOutlet_', '');
    document.getElementById('analysisTabs').innerHTML = '';
    document.getElementById('bannerArea').innerHTML = '';
    document.getElementById('secTitle').textContent = 'PNL Cabang (Franchise) — ' + (UNIT_DATA[outletKey]?.label || outletKey);
    const mode = getPeriodMode('franchiseOutlet');
    const modeTabs = renderPeriodModeTabs('franchiseOutlet', '#22D3C5', [['bulanan','Bulanan'],['kuartal','Kuartal'],['semester','Semester']]);
    if (mode === 'bulanan') {
      renderMonthFilterBar();
      const vIdx = getVisibleIdx();
      const trendHtml = renderFranchiseMonthTrend([outletKey], vIdx);
      content.innerHTML = modeTabs + trendHtml + renderCabangFranchiseTable(outletKey, vIdx);
    } else {
      document.getElementById('monthFilterBar').innerHTML = '';
      const bucketMode = mode === 'kuartal' ? 'quarter' : 'semester';
      const pick = renderBucketPicker('franchiseOutlet', bucketMode, '#22D3C5');
      const note = pick.label ? `Periode: ${pick.label}${pick.complete?'':' (belum lengkap)'}.` : '';
      content.innerHTML = modeTabs + pick.html + renderCabangFranchiseTable(outletKey, pick.indices, note);
    }
    return;
  }

  if (state.unit === 'cabangRinci') {
    state.analysis = 'preview'; // hanya Preview yg didukung di tab ini -- paksa supaya filter bulan & konten tidak salah tampil krn state lama dari tab lain
    // Tab spesial KEDUA: 1 outlet dipilih dari dropdown, lalu PAKAI ULANG
    // renderTree/fillTree/filter-bulan yg SAMA persis dgn tab Store dkk --
    // bukan kode baru, cuma `u` diarahkan ke UNIT_DATA[outlet yg dipilih].
    document.getElementById('secTitle').textContent = 'Cabang Rinci';
    const outletU = UNIT_DATA[cabangRinciSelected];
    document.getElementById('secEyebrow').textContent = outletU.label;
    document.getElementById('secEyebrow').style.color = outletU.color;
    document.getElementById('bannerArea').innerHTML = `<div style="margin-bottom:14px;"><select id="cabangRinciSelect" onchange="setCabangRinci(this.value)" style="background:#171433;border:1px solid #2A2650;color:#F5F3FF;border-radius:8px;padding:8px 14px;font-size:13px;font-family:'Space Grotesk',sans-serif;font-weight:600;">${[...OUTLET_KEYS].sort((a,b)=>UNIT_DATA[a].label.localeCompare(UNIT_DATA[b].label,'id')).map(k=>`<option value="${k}" ${k===cabangRinciSelected?'selected':''}>${UNIT_DATA[k].label}</option>`).join('')}</select></div>`;
    document.getElementById('analysisTabs').innerHTML = '';
    renderMonthFilterBar();
    if (state.analysis==='preview') {
      content.innerHTML = renderExecSection(outletU) + renderAnalysisCard(outletU) + renderTree(outletU);
      fillTree(outletU);
      drawExecChart(outletU);
    } else {
      content.innerHTML = `<div style="color:#726C9C;font-size:13px;padding:20px;">Tab analisa ini (Month to Month/YoY/dst) belum tersedia utk Cabang Rinci -- baru Preview.</div>`;
    }
    return;
  }

// Baris RINGKAS (bukan full 9 baris) utk mode MoM/Kuartal/Semester Split --
// supaya tabel tak terlalu lebar (Split py 2 kanal x banyak baris). Mode
// "Bulanan" (default) tetap tampilkan breakdown penuh via renderOnlineOfflineSplit.

  if (state.unit === 'splitOnline') {
    // Tab spesial: HANYA kartu Online/Offline, tanpa exec/analisa/tree entity
    // biasa -- ini pandangan turunan dari Konsolidasi, bukan entity sendiri.
    document.getElementById('analysisTabs').innerHTML = '';
    document.getElementById('bannerArea').innerHTML = '';
    document.getElementById('secTitle').textContent = 'Split Online vs Offline';
    const sMode = getPeriodMode('splitOnline');
    const sModeTabs = renderPeriodModeTabs('splitOnline', '#4FC3F7');
    if (sMode === 'bulanan') {
      renderMonthFilterBar();
      content.innerHTML = sModeTabs + renderOnlineOfflineSplit(UNIT_DATA.konsolidasi);
    } else if (sMode === 'mom') {
      document.getElementById('monthFilterBar').innerHTML = '';
      content.innerHTML = sModeTabs + renderSplitChannelPicker()
        + `<div style="font-size:10.5px;color:#726C9C;margin-bottom:10px;">Baris sama persis dgn struktur Konsolidasi. Angka = <b>% perubahan</b> bulan ke bulan pada kanal terpilih. Rincian per item tidak dibuka di mode ini — untuk itu pakai mode <b>Bulanan</b>/<b>Kuartal</b>/<b>Semester</b> yang barisnya bisa diklik.</div>`
        + renderSplitMoM();
    } else {
      // Kuartal/Semester: POLA SAMA dgn Bulanan (waterfall penuh, bisa diklik),
      // cuma rentang bulannya diambil dari bucket yg dipilih.
      document.getElementById('monthFilterBar').innerHTML = '';
      const pick = renderBucketPicker('splitOnline', sMode, '#4FC3F7');
      const note = pick.label ? `Periode: ${pick.label}${pick.complete?'':' (belum lengkap)'}.` : '';
      content.innerHTML = sModeTabs + pick.html
        + renderOnlineOfflineSplit(UNIT_DATA.konsolidasi, pick.indices, note);
    }
    return;
  }

  if (state.unit === 'franchiseUnit') {
    // Tab spesial: P&L Franchise (estimasi, turunan dari Konsolidasi) --
    // ditampilkan penuh 1 halaman, bukan kartu kecil spt sebelumnya di dalam
    // tab Konsolidasi. Tetap pakai computeChannelPnl yg sudah tervalidasi.
    document.getElementById('analysisTabs').innerHTML = '';
    document.getElementById('bannerArea').innerHTML = `<div style="background:#241F4D;border:1px solid #FFC93C;border-radius:10px;padding:12px 16px;font-size:12px;color:#9B93C4;margin-bottom:14px;">⚠️ <b>Franchise BUKAN entity dengan buku besar sendiri</b> -- ini pandangan turunan dari Konsolidasi (HPP Produk Langsung = estimasi 60% × omset kotor). Sedang dibangun bertahap sesuai arahan Anda.</div>`;
    document.getElementById('secTitle').textContent = 'PNL Franchise';
    const fMode = getPeriodMode('franchiseUnit');
    const fModeTabs = renderPeriodModeTabs('franchiseUnit', '#B4E61D');
    if (fMode === 'bulanan') {
      renderMonthFilterBar();
      content.innerHTML = fModeTabs + renderChannelSplit(UNIT_DATA.konsolidasi);
    } else if (fMode === 'mom') {
      document.getElementById('monthFilterBar').innerHTML = '';
      content.innerHTML = fModeTabs + renderGenericMoM(franchiseRowsForMoM());
    } else {
      document.getElementById('monthFilterBar').innerHTML = '';
      content.innerHTML = fModeTabs + renderGenericBuckets(fMode, franchiseRowsForBucket);
    }
    return;
  }

  if (state.unit === 'cabangDiff') {
    state.analysis = 'preview';
    document.getElementById('analysisTabs').innerHTML = '';
    document.getElementById('bannerArea').innerHTML = '';
    document.getElementById('monthFilterBar').innerHTML = ''; // punya pemilih bulan sendiri
    document.getElementById('secTitle').textContent = 'Biasa vs Versi Franchise';
    content.innerHTML = renderCabangDiff();
    return;
  }

  if (state.unit === 'outletCompare' || state.unit === 'franchiseCompare') {
    state.analysis = 'preview';
    const mode = state.unit==='franchiseCompare' ? 'franchise' : 'biasa';
    document.getElementById('analysisTabs').innerHTML = '';
    document.getElementById('bannerArea').innerHTML = '';
    document.getElementById('monthFilterBar').innerHTML = ''; // punya pemilih bulan sendiri
    document.getElementById('secTitle').textContent = mode==='franchise' ? 'Perbandingan Custom (Franchise)' : 'Perbandingan Custom Outlet';
    content.innerHTML = renderCompareCustom(mode);
    return;
  }

  if (state.unit === 'leakRingkas' || state.unit === 'leakJenis' || state.unit === 'leakOutlet') {
    state.analysis = 'preview';
    document.getElementById('analysisTabs').innerHTML = '';
    document.getElementById('bannerArea').innerHTML = '';
    const titles = { leakRingkas:'Kebocoran — Ringkasan & Tren', leakJenis:'Kebocoran — Rincian per Jenis', leakOutlet:'Kebocoran — Peringkat per Outlet' };
    document.getElementById('secTitle').textContent = titles[state.unit];
    renderMonthFilterBar();
    content.innerHTML = state.unit==='leakRingkas' ? renderLeakRingkas()
                      : state.unit==='leakJenis'   ? renderLeakJenis()
                      : renderLeakOutlet();
    return;
  }

  if (state.unit === 'rekonsiliasi') {
    state.analysis = 'preview';
    document.getElementById('analysisTabs').innerHTML = '';
    document.getElementById('bannerArea').innerHTML = '';
    document.getElementById('monthFilterBar').innerHTML = '';
    document.getElementById('secTitle').textContent = 'Rekonsiliasi Otomatis';
    content.innerHTML = renderRekonsiliasi();
    return;
  }

  if (state.unit === 'grafikCustom') {
    document.getElementById('analysisTabs').innerHTML = '';
    document.getElementById('bannerArea').innerHTML = '';
    document.getElementById('monthFilterBar').innerHTML = '';
    document.getElementById('secTitle').textContent = 'Grafik Custom';
    content.innerHTML = renderGrafikCustom() + renderGrafikCabang() + renderGrafikCabangFranchise() + renderGrafikScatter();
    drawGrafikCustomChart();
    drawGrafikCabangChart();
    drawGrafikCabangFranchiseChart();
    drawGrafikScatterChart();
    return;
  }

  if (state.unit === 'bankPlaceholder') {
    document.getElementById('analysisTabs').innerHTML = '';
    document.getElementById('bannerArea').innerHTML = '';
    document.getElementById('monthFilterBar').innerHTML = '';
    document.getElementById('secTitle').textContent = 'Bank';
    content.innerHTML = `<div style="padding:40px 20px;text-align:center;color:#726C9C;font-size:13px;">Menu Bank sengaja dikosongkan dulu sesuai arahan Anda. Belum ada tampilan di sini.</div>`;
    return;
  }

  if (['opexTrend','opexPareto','opexOutlet','opexAnomaly','opexLeverage'].includes(state.unit)) {
    document.getElementById('analysisTabs').innerHTML = '';
    document.getElementById('bannerArea').innerHTML = '';
    document.getElementById('secTitle').textContent = UNIT_DATA[state.unit].label;
    const oMode = getPeriodMode(state.unit);
    const oModeTabs = renderPeriodModeTabs(state.unit, '#FFC93C', [['bulanan','Bulanan'],['quarter','Kuartal'],['semester','Semester']]);
    if (oMode === 'bulanan') renderMonthFilterBar();
    else document.getElementById('monthFilterBar').innerHTML = '';
    content.innerHTML = oModeTabs + renderOpexAnomalyNotice(state.unit) + renderOpexModule(state.unit, oMode);
    return;
  }

  if (state.unit === 'hppRingkasan') {
    document.getElementById('analysisTabs').innerHTML = '';
    document.getElementById('bannerArea').innerHTML = '';
    document.getElementById('secTitle').textContent = UNIT_DATA[state.unit].label;
    renderMonthFilterBar(); // Ringkasan cuma bulanan (bulan terakhir vs sebelumnya) -- tak ada varian kuartal/semester
    content.innerHTML = renderHppAnomalyNotice(state.unit) + renderHppModule(state.unit, 'bulanan');
    return;
  }
  if (['hppTren','hppAnomaly'].includes(state.unit)) {
    document.getElementById('analysisTabs').innerHTML = '';
    document.getElementById('bannerArea').innerHTML = '';
    document.getElementById('secTitle').textContent = UNIT_DATA[state.unit].label;
    const hMode = getPeriodMode(state.unit);
    const hModeTabs = renderPeriodModeTabs(state.unit, '#F4A6D0', [['bulanan','Bulanan'],['quarter','Kuartal'],['semester','Semester']]);
    if (hMode === 'bulanan') renderMonthFilterBar();
    else document.getElementById('monthFilterBar').innerHTML = '';
    content.innerHTML = hModeTabs + renderHppAnomalyNotice(state.unit) + renderHppModule(state.unit, hMode);
    return;
  }


  renderMonthFilterBar();
  document.getElementById('secTitle').textContent = ANALYSIS_TABS.find(t=>t.id===state.analysis).label;

  if (state.analysis==='preview') {
    const execHtml = renderExecSection(u); // semua entity, bukan cuma konsolidasi (permintaan user 2026-07-09)
    const analysisHtml = renderAnalysisCard(u);
    content.innerHTML = execHtml + analysisHtml + renderTree(u);
    fillTree(u);
    drawExecChart(u);
  } else if (state.analysis==='mom') {
    content.innerHTML = renderMoM(u);
  } else if (state.analysis==='yoy') {
    content.innerHTML = renderYoY(u);
  } else if (state.analysis==='ytd') {
    content.innerHTML = renderYTD(u);
  } else if (state.analysis==='qs') {
    content.innerHTML = renderQS(u);
  }
}

// ============ TAB PERFORMA OUTLET ============
// Pakai bulan TERAKHIR yg ada datanya (default Juni/index 5 saat ini) --
// BUKAN multi-bulan spt tab lain, krn kolom di sini sudah dipakai utk outlet.
// Soal toggle-bulan single-select utk tab ini masih BELUM dikonfirmasi user
// (lihat catatan chat) -- sengaja belum dibangun sampai ada konfirmasi.
const OUTLET_ROWS = ['Pendapatan','Diskon','Pendapatan Bersih','HPP','Laba Kotor','Biaya Operasional','Laba Operasional','Biaya Penyusutan','Biaya Pajak','Cost of Management','Laba Bersih'];
const OUTLET_HIGHLIGHT = new Set(['Pendapatan','Laba Operasional']);
let outletTab = 'topn'; // 'topn' atau 'filter'
let outletMode = 'top10';
let outletSortKey = 'Pendapatan';
let outletSelected = new Set(OUTLET_KEYS);
let outletExpandedRows = new Set(); // baris yg sedang di-expand (dropdown)
let outletMonthIdx = null; // null = otomatis ikut bulan terakhir yg ada data; kalau diisi angka, user override manual
// Mode periode (Bulanan/Kuartal/Semester) pakai mekanisme GENERIK yg sama dgn
// Franchise/Split/Opex (getPeriodMode/setPeriodMode, key='outletPerf'). MoM
// sengaja tak disediakan -- alasan sama spt Pareto/Efisiensi Outlet Opex: ini
// tabel RANKING 1 periode, bukan seri waktu per-baris.
let outletBucketIdx = []; // index bulan dari bucket kuartal/semester terpilih (dipakai renderOutletTable saat mode!=bulanan)

function outletLatestIdx(){
  for (let i = PERIODS.length-1; i>=0; i--) {
    if (OUTLET_KEYS.some(k => { const r = UNIT_DATA[k].waterfall.find(r=>r.name==='Pendapatan'); return r && r.values[i]!=null; })) return i;
  }
  return PERIODS.length-1;
}
function outletCurrentIdx(){ return outletMonthIdx !== null ? outletMonthIdx : outletLatestIdx(); }
function setOutletMonth(i){ outletMonthIdx = (i==='' ? null : parseInt(i)); renderOutletTable(); }
function toggleOutletRowExpand(rowName){
  if (outletExpandedRows.has(rowName)) outletExpandedRows.delete(rowName);
  else outletExpandedRows.add(rowName);
  renderOutletTable();
}
function getChildVal(row, childKey, idx){
  const c = row.children ? row.children[childKey] : null;
  if (!c) return null;
  const arr = Array.isArray(c) ? c : c.vals;
  return arr ? arr[idx] : null;
}

function renderOutletPerf(){
  const oMode = getPeriodMode('outletPerf');
  const modeTabs = renderPeriodModeTabs('outletPerf', '#4ADE80', [['bulanan','Bulanan'],['quarter','Kuartal'],['semester','Semester']]);
  const bucketArea = oMode==='bulanan' ? '' : `<div id="outletBucketArea" style="margin-bottom:16px;"></div>`;
  return `
    ${modeTabs}
    <div style="display:flex; gap:10px; align-items:center; margin-bottom:16px; flex-wrap:wrap; ${oMode==='bulanan'?'':'display:none'}">
      <span style="font-size:11px;color:#726C9C;font-weight:600;">BULAN:</span>
      <select id="outletMonthSelect" onchange="setOutletMonth(this.value)" style="background:#171433;border:1px solid #2A2650;color:#F5F3FF;border-radius:8px;padding:7px 12px;font-size:12.5px;">
        <option value="" ${outletMonthIdx===null?'selected':''}>Otomatis (bulan terakhir terisi)</option>
        ${PERIODS.map((p,i) => `<option value="${i}" ${outletMonthIdx===i?'selected':''}>${periodLabel(p,false)}</option>`).join('')}
      </select>
    </div>
    ${bucketArea}
    <div style="display:flex; gap:8px; margin-bottom:20px;">
      <button class="tab-btn ${outletTab==='topn'?'active':''}" style="${outletTab==='topn'?'border-color:#FFC93C;color:#FFC93C;background:#241F4D':''}" onclick="setOutletTab('topn')">Perbandingan (Top N)</button>
      <button class="tab-btn ${outletTab==='filter'?'active':''}" style="${outletTab==='filter'?'border-color:#FFC93C;color:#FFC93C;background:#241F4D':''}" onclick="setOutletTab('filter')">Filter Manual</button>
    </div>
    <div id="outletTopnCtrl" style="${outletTab==='topn'?'':'display:none'}; display:flex; gap:10px; align-items:center; margin-bottom:16px; flex-wrap:wrap;">
      <span style="font-size:11px;color:#726C9C;font-weight:600;">URUTKAN:</span>
      <select id="outletSortSelect" onchange="setOutletSort(this.value)" style="background:#171433;border:1px solid #2A2650;color:#F5F3FF;border-radius:8px;padding:7px 12px;font-size:12.5px;">
        <option value="Pendapatan">Pendapatan</option>
        <option value="Laba Kotor">Laba Kotor</option>
        <option value="Laba Operasional">Laba Operasional</option>
        <option value="Laba Bersih">Laba Bersih</option>
      </select>
      <span style="font-size:11px;color:#726C9C;font-weight:600;margin-left:10px;">TAMPILKAN:</span>
      <button class="tab-btn ${outletMode==='top10'?'active':''}" style="${outletMode==='top10'?'border-color:#FFC93C;color:#FFC93C':''}" onclick="setOutletMode('top10')">10 Teratas</button>
      <button class="tab-btn ${outletMode==='all'?'active':''}" style="${outletMode==='all'?'border-color:#FFC93C;color:#FFC93C':''}" onclick="setOutletMode('all')">Semua (${OUTLET_KEYS.length})</button>
    </div>
    <div id="outletFilterCtrl" style="${outletTab==='filter'?'':'display:none'}; margin-bottom:14px;">
      <span onclick="outletSelectAll()" style="font-size:11.5px;color:#9B93C4;cursor:pointer;text-decoration:underline;margin-right:14px;">Pilih Semua</span>
      <span onclick="outletClearAll()" style="font-size:11.5px;color:#9B93C4;cursor:pointer;text-decoration:underline;">Kosongkan</span>
      <div id="outletChipGrid" style="display:flex;flex-wrap:wrap;gap:7px;margin-top:12px;max-width:900px;"></div>
    </div>
    <div class="tbl-wrap" style="overflow-x:auto;">
      <table id="outletPerfTable"></table>
    </div>`;
}

function initOutletPerf(){
  const oMode = getPeriodMode('outletPerf');
  if (oMode !== 'bulanan') {
    const pick = renderBucketPicker('outletPerf', oMode, '#4ADE80');
    const area = document.getElementById('outletBucketArea');
    if (area) area.innerHTML = pick.html;
    outletBucketIdx = pick.indices;
  }
  renderOutletChips();
  renderOutletTable();
}
function setOutletTab(t){ outletTab=t; render(); }
function setOutletMode(m){ outletMode=m; render(); }
function setOutletSort(v){ outletSortKey=v; renderOutletTable(); }

function renderOutletChips(){
  const grid = document.getElementById('outletChipGrid');
  if (!grid) return;
  // urutan ABJAD sesuai instruksi user -- pakai label ('Nusa Indah' dst), bukan urutan OUTLET_KEYS asli
  const sorted = [...OUTLET_KEYS].sort((a,b) => UNIT_DATA[a].label.localeCompare(UNIT_DATA[b].label, 'id'));
  grid.innerHTML = sorted.map(k => {
    const on = outletSelected.has(k);
    return `<div onclick="toggleOutletChip('${k}')" style="cursor:pointer;padding:7px 14px;border-radius:20px;font-size:12px;font-weight:600;font-family:'Space Grotesk',sans-serif;border:1px solid ${on?'#FFC93C':'#2A2650'};color:${on?'#FFC93C':'#726C9C'};background:${on?'#FFC93C1a':'transparent'};">${UNIT_DATA[k].label}</div>`;
  }).join('');
}
function toggleOutletChip(k){
  if (outletSelected.has(k)) outletSelected.delete(k); else outletSelected.add(k);
  if (outletSelected.size===0) outletSelected.add(k);
  renderOutletChips();
  renderOutletTable();
}
function outletSelectAll(){ OUTLET_KEYS.forEach(k=>outletSelected.add(k)); renderOutletChips(); renderOutletTable(); }
function outletClearAll(){ outletSelected = new Set([OUTLET_KEYS[0]]); renderOutletChips(); renderOutletTable(); }

function renderOutletTable(){
  const table = document.getElementById('outletPerfTable');
  if (!table) return;
  // "idx" DULU 1 angka (1 bulan). Sekarang idxArr = ARRAY bulan yg DIJUMLAH --
  // utk mode Bulanan isinya 1 elemen (perilaku SAMA persis spt sebelumnya),
  // utk Kuartal/Semester isinya 3/6 elemen (dijumlah, bkn dirata2 -- konsisten
  // dgn cara modul Opex/Franchise/Split menghitung total periode).
  const idxArr = getPeriodMode('outletPerf')==='bulanan' ? [outletCurrentIdx()] : outletBucketIdx;
  const sumAt = (arr) => !arr ? null : idxArr.reduce((s,i)=> arr[i]!=null ? s+arr[i] : s, (idxArr.some(i=>arr[i]!=null)?0:null));
  let shown;
  if (outletTab === 'filter') {
    shown = OUTLET_KEYS.filter(k => outletSelected.has(k));
  } else {
    const getVal = (k, rowName) => { const r = UNIT_DATA[k].waterfall.find(r=>r.name===rowName); return r ? sumAt(r.values) : null; };
    const sorted = [...OUTLET_KEYS].sort((a,b) => (getVal(b,outletSortKey)||0) - (getVal(a,outletSortKey)||0));
    shown = outletMode==='top10' ? sorted.slice(0,10) : sorted;
  }

  let thead = `<thead><tr><th style="text-align:left;padding:12px 16px;color:#726C9C;font-family:'Space Grotesk',sans-serif;font-size:11px;text-transform:uppercase;border-bottom:1px solid #2A2650;">Akun</th>`;
  shown.forEach(k => { thead += `<th style="padding:12px 16px;text-align:right;border-bottom:1px solid #2A2650;border-left:1px solid #2A2650;font-family:'Space Grotesk',sans-serif;font-size:12px;font-weight:700;">${UNIT_DATA[k].label}</th>`; });
  thead += '</tr></thead>';

  // pakai outlet PERTAMA yg tampil sbg acuan struktur children (semua outlet
  // punya struktur identik meski datanya blm terisi semua)
  const refKey = shown[0] || OUTLET_KEYS[0];

  let tbody = '<tbody>';
  OUTLET_ROWS.forEach(rowName => {
    const hl = OUTLET_HIGHLIGHT.has(rowName);
    const refRow = UNIT_DATA[refKey].waterfall.find(r=>r.name===rowName);
    const hasChildren = refRow && refRow.children && Object.keys(refRow.children).length > 0;
    const expanded = outletExpandedRows.has(rowName);
    const arrow = hasChildren ? `<span onclick="toggleOutletRowExpand('${rowName}')" style="cursor:pointer;color:#726C9C;margin-right:6px;font-size:10px;display:inline-block;transform:rotate(${expanded?90:0}deg);transition:transform .15s;">&#9654;</span>` : '';
    const rowStyle = hasChildren ? 'cursor:pointer;' : '';
    tbody += `<tr style="${hl?'background:#1C1840;':''}${rowStyle}" ${hasChildren?`onclick="toggleOutletRowExpand('${rowName}')"`:''}><td style="padding:12px 16px;font-size:13px;font-weight:${hl?700:500};color:${hl?'#FFC93C':'#F5F3FF'};border-bottom:1px solid #2A2650;white-space:nowrap;">${arrow}${rowName}</td>`;
    shown.forEach(k => {
      const r = UNIT_DATA[k].waterfall.find(r=>r.name===rowName);
      const v = r ? sumAt(r.values) : null;
      const pend = UNIT_DATA[k].waterfall.find(r=>r.name==='Pendapatan');
      const base = pend ? sumAt(pend.values) : null;
      const txt = v==null ? '-' : v.toLocaleString('id-ID');
      const pctTxt = (v!=null && base) ? `<span style="font-size:9.5px;color:#726C9C;display:block;">${(v/base*100).toFixed(1)}%</span>` : '';
      const negStyle = (v!=null && v<0) ? 'color:#9B93C4;' : '';
      tbody += `<td style="padding:12px 16px;text-align:right;border-left:1px solid #2A2650;border-bottom:1px solid #2A2650;white-space:nowrap;font-family:'Plus Jakarta Sans',sans-serif;font-variant-numeric:tabular-nums;font-size:13px;${negStyle}">${txt}${pctTxt}</td>`;
    });
    tbody += '</tr>';

    if (hasChildren && expanded) {
      Object.keys(refRow.children).forEach(childKey => {
        tbody += `<tr><td style="padding:10px 16px 10px 38px;font-size:12.5px;color:#9B93C4;border-bottom:1px solid #2A2650;white-space:nowrap;">${childKey}</td>`;
        shown.forEach(k => {
          const kRow = UNIT_DATA[k].waterfall.find(r=>r.name===rowName);
          const v = kRow ? sumAt((kRow.children && kRow.children[childKey]) ? (Array.isArray(kRow.children[childKey]) ? kRow.children[childKey] : kRow.children[childKey].vals) : null) : null;
          const txt = v==null ? '-' : v.toLocaleString('id-ID');
          const negStyle = (v!=null && v<0) ? 'color:#726C9C;' : 'color:#9B93C4;';
          tbody += `<td style="padding:10px 16px;text-align:right;border-left:1px solid #2A2650;border-bottom:1px solid #2A2650;white-space:nowrap;font-family:'Plus Jakarta Sans',sans-serif;font-variant-numeric:tabular-nums;font-size:12.5px;${negStyle}">${txt}</td>`;
        });
        tbody += '</tr>';
      });
    }
  });
  tbody += '</tbody>';
  table.innerHTML = thead + tbody;
}

function initDashboard(){
  render();
  // Live-fetch Apps Script DINONAKTIFKAN sesuai permintaan sebelumnya ("cancel
  // dulu script"). Fungsi loadLiveData() masih ada kalau nanti mau diaktifkan
  // lagi -- tinggal un-comment baris di bawah. Untuk sekarang, satu-satunya
  // cara update data adalah tombol "Upload CSV" di atas.
  loadLiveData().catch(e => console.error('loadLiveData gagal total:', e));
}

/* ============================================================
   LIVE FETCH dari Google Apps Script Web App. Sekarang mencocokkan
   SETIAP baris waterfall (bukan cuma 5 metrik lama) via accountLabel
   masing-masing baris. BELUM DIUJI dari sandbox chat ini.

   CARA AKTIFKAN:
   1. Deploy Code.gs sbg Web App (lihat instruksi di file Code.gs)
   2. Isi APPS_SCRIPT_URL di bawah dgn URL hasil deploy

   PERINGATAN: endpoint dgn "Anyone" access bersifat PUBLIK tanpa login.
   ============================================================ */

const APPS_SCRIPT_URL = 'https://script.google.com/macros/s/AKfycby7NsLDUS8Poxq0lLBstRcYOpocSa3_0XDbdJvsRFvUQV4C_lclKDiEeEd8vM_vkfjG/exec';

const ENTITY_KEY_MAP = {
  konsolidasi: 'Konsolidasi', store: 'Store & Brand', manufaktur: 'Manufaktur', ho: 'Head Office', ownership: 'Ownership',
  sudirman: 'Sudirman', cikole: 'Cikole', dramaga: 'Dramaga', bangbarung: 'Bangbarung', cibubur: 'Cibubur',
  mekarsari: 'Mekarsari', pekapuran: 'Pekapuran', jatimelati: 'Jatimelati', nusaindah: 'Nusa Indah',
  cibinong: 'Cibinong', bantargebang: 'Bantargebang', abdulgani: 'Abdul Gani', pangleseran: 'Pangleseran', outletsby: 'Outlet',
};

// cek apakah CSV yg baru diupload membawa periode (bulan) yang belum ada di
// PERIODS -- kalau ada, gabungkan (union, urut kronologis) & normalisasi
// semua array data supaya kolom baru otomatis muncul di seluruh dashboard
// (tabel, chart, KPI, MoM/YoY/YTD/QS) tanpa perlu ubah kode lain.
// return true kalau PERIODS berubah (ada bulan baru).
// Normalisasi Period APAPUN bentuknya jadi "YYYY-MM" bersih. Perlu ini krn
// terkonfirmasi LANGSUNG lewat Console (bukan dugaan): salah satu baris di
// live sheet punya Period bertipe TANGGAL asli (bukan teks), yg oleh Apps
// Script di-serialize jadi ISO datetime spt "2026-01T08:00:00.000Z" -- bukan
// "2026-01" polos. String itu dianggap periode BARU yg berbeda krn perbandingan
// string apa adanya, padahal scr kalender itu bulan yg SAMA. Fungsi ini
// menangkap prefix YYYY-MM dari string apa pun (baik "2026-01" maupun
// "2026-01T08:00:00.000Z" atau variasi ISO lainnya) sblm dibandingkan/disimpan.
function normalizePeriod(raw){
  const s = (raw||'').toString().trim();
  const m = s.match(/^(\d{4})-(\d{2})/);
  return m ? `${m[1]}-${m[2]}` : s;
}

// Remap SEMUA array nilai di UNIT_DATA ke posisi index BARU saat PERIODS
// berubah (mis. live-fetch menemukan periode LEBIH AWAL, spt 2025, yg masuk
// ke DEPAN array setelah di-sort). BUKAN cuma tambah panjang di ujung --
// setiap nilai lama harus pindah ke index yg sesuai dgn periodenya SENDIRI,
// bukan tetap di posisi lama (yg sekarang berarti bulan lain).
function remapPeriodsArray(arr, oldPeriods, newPeriods){
  if (!Array.isArray(arr)) return arr;
  const out = newPeriods.map(()=>null);
  oldPeriods.forEach((p,i) => {
    const newIdx = newPeriods.indexOf(p);
    if (newIdx !== -1 && arr[i] !== undefined) out[newIdx] = arr[i];
  });
  return out;
}
function remapNode(node, oldPeriods, newPeriods){
  if (!node) return;
  if (Array.isArray(node.values)) node.values = remapPeriodsArray(node.values, oldPeriods, newPeriods);
  if (node.children) {
    Object.keys(node.children).forEach(k => {
      const child = node.children[k];
      if (Array.isArray(child)) node.children[k] = remapPeriodsArray(child, oldPeriods, newPeriods);
      else remapNode(child, oldPeriods, newPeriods);
    });
  }
}
function remapAllDataForNewPeriods(oldPeriods, newPeriods){
  Object.values(UNIT_DATA).forEach(u => {
    if (u.waterfall) u.waterfall.forEach(row => remapNode(row, oldPeriods, newPeriods));
    if (u.revChildrenRaw) Object.keys(u.revChildrenRaw).forEach(k => {
      u.revChildrenRaw[k] = remapPeriodsArray(u.revChildrenRaw[k], oldPeriods, newPeriods);
    });
  });
}
function mergePeriodsFromRows(allRows){
  const found = [...new Set(allRows.map(r => normalizePeriod(r.Period)).filter(Boolean))];
  const merged = Array.from(new Set([...PERIODS, ...found])).sort();
  const changed = merged.length !== PERIODS.length || merged.some((p,i) => p !== PERIODS[i]);
  if (changed) {
    const oldPeriods = PERIODS;
    PERIODS = merged;
    remapAllDataForNewPeriods(oldPeriods, merged); // BUKAN normalizeAllData() -- itu cuma pad di ujung, tak memindah data ke posisi yg benar
    normalizeAllData(); // tetap panggil, jaga2 ada array yg msh salah panjang (mis. belum pernah di-normalize sebelumnya)
  }
  return changed;
}

function parseAmount(raw){
  if (raw === '' || raw === null || raw === undefined) return null;
  if (typeof raw === 'number') return raw;
  let s = String(raw).trim();
  let negative = false;
  if (s.startsWith('(') && s.endsWith(')')) { negative = true; s = s.slice(1,-1); }
  const n = (negative?-1:1) * Number(s.replace(/,/g, ''));
  return isNaN(n) ? null : n;
}

// dipakai bersama oleh loadLiveData (fetch Apps Script) DAN handleCSVUpload
// (baca file lokal) -- satu logika pencocokan, dua sumber data.
// return: { matched: n, warnings: [...] } supaya bisa ditampilkan ke UI,
// bukan cuma console.warn yang gampang tidak kelihatan.
// Cache baris mentah (SEMUA row dari CSV/live-fetch) -- dipakai utk akun yg
// TIDAK tersimpan sbg children di waterfall teragregasi (mis. HPP Konsolidasi
// disimpan RATA/flat, "HPP Bahan Baku Utama" hilang strukturnya). TIDAK
// menyentuh logika agregasi waterfall yg sudah ada -- murni kemampuan baru.
let RAW_ROWS_CACHE = [];
function getRawAccountSeries(entityName, accountName){
  // -> array SEPANJANG PERIODS, index selaras, null kalau tak ketemu di bulan itu
  const out = PERIODS.map(()=>null);
  RAW_ROWS_CACHE.forEach(r => {
    if (r.Entity !== entityName) return;
    if ((r.Account||'').trim() !== accountName.trim()) return;
    const p = normalizePeriod(r.Period);
    const idx = PERIODS.indexOf(p);
    if (idx === -1) return;
    const v = parseAmount(r.Amount);
    if (v === null) return;
    out[idx] = (out[idx]||0) + v;
  });
  return out;
}
// Versi PATH-PERSIS -- perlu krn ada nama Account yg SAMA di 2 path berbeda
// (mis. "Konsinyasi" muncul di bawah Pendapatan DAN di bawah Harga Pokok
// Penjualan) -- kalau cuma cocok nama Account, dua-duanya ketumpuk kejumlah.
function getRawSeriesByPath(entityName, exactPath){
  const out = PERIODS.map(()=>null);
  RAW_ROWS_CACHE.forEach(r => {
    if (r.Entity !== entityName) return;
    if ((r.Path||'').trim() !== exactPath.trim()) return;
    const p = normalizePeriod(r.Period);
    const idx = PERIODS.indexOf(p);
    if (idx === -1) return;
    const v = parseAmount(r.Amount);
    if (v === null) return;
    out[idx] = (out[idx]||0) + v;
  });
  return out;
}
// Baris mentah sheet Online, TANPA agregasi -- {period, entity, path, amount}.
// Perlu krn parseOnlineRows() lama HANYA utk Konsolidasi (path Harga
// Normal/Adjustment tanpa peduli Entity); di sini kita perlu filter per
// OUTLET, jadi entity ikut disimpan.
function parseOnlineRowsRaw(){
  const out = [];
  ONLINE_ROWS.forEach(r => {
    let period, entity, path, amountRaw;
    if (Array.isArray(r)) { period=normalizePeriodKey(r[0]); entity=(r[1]||'').toString().trim(); path=r[5]||''; amountRaw=r[7]; }
    else {
      const vals = Object.values(r);
      period = normalizePeriodKey(r.Period !== undefined ? r.Period : vals[0]);
      entity = (r.Entity !== undefined ? r.Entity : vals[1] || '').toString().trim();
      path   = (r.Path   || vals[5] || '').toString();
      amountRaw = r.Amount !== undefined ? r.Amount : vals[7];
    }
    const amt = parseAmount(amountRaw);
    if (amt === null || !period) return;
    out.push({ period, entity, path, amount: amt });
  });
  return out;
}
function sumOnlineSheet(entityName, pathExact, idxList){
  const rows = parseOnlineRowsRaw();
  return idxList.reduce((s,i) => {
    const p = PERIODS[i];
    return s + rows.filter(r => r.entity===entityName && r.period===p && r.path.trim()===pathExact.trim()).reduce((ss,r)=>ss+r.amount,0);
  }, 0);
}
function applyRowsToUnitData(allRows){
  RAW_ROWS_CACHE = allRows;
  let matched = 0;
  let added = 0;
  const warnings = [];

  // coba cocokkan satu node (row waterfall ATAU child-group) by accountLabel/name;
  // kalau gagal & node itu punya children serta computeFromChildren, jumlahkan
  // leaf children-nya (masing2 dicocokkan by nama) sbg fallback.
  // cari SEMUA baris yg cocok Entity+Account+Period, lalu pilih yg PUNYA nilai
  // (bukan sekadar kemunculan pertama) -- perlu krn ada pola akun bernama sama
  // dipakai utk header (kosong) DAN detail (berisi), spt "Beban Depresiasi",
  // "EAT", "Cost of Management" yg sudah ditemukan sebelumnya.
  // Perbandingan "longgar": spasi berlebih dirapikan jadi satu spasi & huruf
  // disamakan jadi kecil semua -- dipakai HANYA sbg fallback kalau match
  // PERSIS gagal. Ditambahkan krn kasus nyata: "Biaya Kang Ridwan Official"
  // (Ownership) dilaporkan user datanya ADA di sheet tapi tidak pernah
  // muncul di dashboard, padahal akun tetangganya (Biaya Konsultan Pajak dkk,
  // di path yg sama) cocok normal -- ciri khas selisih teks kecil (spasi
  // ganda/kapitalisasi beda) antara isi sel Account di sheet vs label yg
  // di-hardcode di sini, bukan live-fetch yg gagal total.
  function looseNormalize(s){
    return (s||'').toString().trim().replace(/\s+/g, ' ').toLowerCase();
  }
  function findValueRow(entityName, account, period, pathHint){
    let candidates = allRows.filter(r => r.Entity === entityName && (r.Account||'').trim() === account.trim() && normalizePeriod(r.Period) === period);
    // FALLBACK longgar: kalau match PERSIS nihil, coba lagi pakai
    // looseNormalize (baik utk Account maupun Entity) SEBELUM menyerah total.
    // Match persis tetap dicoba lebih dulu (di atas) supaya tidak mengubah
    // perilaku existing yg sudah benar/teruji.
    if (candidates.length === 0) {
      const targetAcc = looseNormalize(account);
      const targetEntity = looseNormalize(entityName);
      candidates = allRows.filter(r => looseNormalize(r.Entity) === targetEntity && looseNormalize(r.Account) === targetAcc && normalizePeriod(r.Period) === period);
    }
    // kalau ada pathHint (utk disambiguasi nama duplikat lintas konteks, spt
    // "Bakery Pagelaran" yg muncul di Management Fee DAN HPP Franchise) --
    // saring dulu yg Path-nya mengandung hint itu, sebelum fallback ke semua.
    if (pathHint && candidates.length > 1) {
      const filtered = candidates.filter(r => (r.Path||'').includes(pathHint));
      if (filtered.length) candidates = filtered;
    }
    return candidates.find(r => r.Amount !== '' && r.Amount !== null && r.Amount !== undefined) || candidates[0];
  }

  // MERGE per-sel: cuma menimpa bulan yang benar2 ada datanya di CSV ini,
  // bulan lain (yg tidak disebut di CSV upload ini) TETAP dipertahankan dari
  // currentValues -- supaya upload CSV berisi bulan baru saja tidak menghapus
  // histori bulan lama. currentValues HARUS sudah sepanjang PERIODS.length
  // (dinormalisasi sebelum dipanggil).
  function resolveNode(entityName, label, currentValues, children, treatMissingAsZero, signRule, forceFormula){
    const merged = currentValues.slice();
    let anyDirect = false;
    if (!forceFormula) {
      PERIODS.forEach((period, i) => {
        const hit = findValueRow(entityName, label, period);
        if (!hit) return;
        const amt = parseAmount(hit.Amount);
        if (amt !== null) { merged[i] = amt; anyDirect = true; }
      });
    }
    if (anyDirect) return merged;
    if (!children) return null;
    // fallback: jumlahkan SEMUA child (array biasa ATAU grup bersarang {vals,children})
    // yg match by nama, per-sel juga. Grup bersarang diresolve REKURSIF dulu (spt
    // "HPP Bahan Baku (bersih)" yg sendiri = HPP Bahan Baku - Cashback Supplier)
    // sebelum ikut dijumlahkan ke level ini -- supaya nilai bersihnya yg dipakai,
    // bukan salah satu komponen mentahnya saja.
    // signRule='absSubtractExceptPendapatan': nilai source diabaikan tandanya
    // (diambil abs), lalu DIKURANGKAN kecuali nama item mengandung "Pendapatan"
    // (yg DITAMBAHKAN) -- sesuai instruksi eksplisit user utk section Pajak & Bunga,
    // krn source-nya tidak konsisten tanda (bbrp item pajak kadang tercatat negatif).
    const entries = Object.entries(children);
    if (entries.length === 0) return null;
    const sums = currentValues.slice();
    const touched = PERIODS.map(() => false);
    let anyFound = false;
    entries.forEach(([name, val]) => {
      const dir = (signRule === 'absSubtractExceptPendapatan') ? (name.includes('Pendapatan') ? 1 : -1)
                : (signRule === 'subtractPendapatanFromCosts') ? (name.includes('Pendapatan') ? -1 : 1)
                : 1;
      let childVals;
      // leaf bisa berbentuk array biasa [...], ATAU {vals:[...], pathHint:'...'}
      // utk item yg nama akunnya AMBIGU di CSV (sama persis tapi beda konteks,
      // spt "Bakery Pagelaran" di Management Fee vs HPP Franchise) -- pathHint
      // memaksa pencocokan live cuma ambil baris yg Path-nya mengandung teks itu.
      const isLeafWithHint = !Array.isArray(val) && val && Array.isArray(val.vals) && !val.children;
      if (Array.isArray(val) || isLeafWithHint) {
        const staticArr = isLeafWithHint ? val.vals : val;
        const hint = isLeafWithHint ? val.pathHint : undefined;
        childVals = PERIODS.map(period => {
          const hit = findValueRow(entityName, name, period, hint);
          if (!hit) return null;
          return parseAmount(hit.Amount);
        });
        childVals = childVals.map((v,i) => v !== null ? v : (staticArr[i] != null ? staticArr[i] : null));
      } else {
        // grup bersarang: resolve REKURSIF (leaf datar ATAU forceFormula-nya sendiri kalau ada)
        childVals = resolveNode(entityName, name, val.vals, val.children, val.treatMissingAsZero, val.signRule, true);
        if (!childVals) childVals = val.vals; // fallback ke statis kalau resolve rekursif gagal total
      }
      // BUG FIX PENTING: simpan childVals balik ke struktur children -- tanpa
      // ini, total induk benar (krn dihitung langsung ke variabel lokal) TAPI
      // dropdown-nya tetap menampilkan data lama/kosong krn UI membaca dari
      // `children[name]`/`val.vals` yg TIDAK PERNAH diperbarui. Ditemukan dari
      // laporan user: total Pendapatan Manufaktur muncul benar tapi semua
      // child (Factory/Aksesoris/dst) tetap "-" di dropdown.
      if (Array.isArray(val) || isLeafWithHint) {
        if (isLeafWithHint) val.vals = childVals; else children[name] = childVals;
      } else {
        val.vals = childVals;
      }
      PERIODS.forEach((period, i) => {
        const v = childVals[i];
        if (v === null || v === undefined) return;
        const amt = (signRule === 'absSubtractExceptPendapatan' || signRule === 'subtractPendapatanFromCosts') ? dir * Math.abs(v) : v;
        if (!touched[i]) { sums[i] = 0; touched[i] = true; }
        sums[i] += amt;
        anyFound = true;
      });
    });
    return anyFound ? sums : (treatMissingAsZero ? null : null);
  }

  Object.entries(ENTITY_KEY_MAP).forEach(([unitKey, entityName]) => {
    const u = UNIT_DATA[unitKey];
    if (!u) return;
    u.waterfall.forEach(row => {
      if (!row.accountLabel) return; // baris wrapper/toggle (mis. "Item Tambahan", "Laba Bersih" hasil hitung) dilewati, tidak dicocokkan ke CSV
      const current = normalizeLength(row.values || PERIODS.map(() => null));
      const resolved = resolveNode(entityName, row.accountLabel, current, row.children, row.treatMissingAsZero, row.signRule, row.forceFormula);
      if (resolved) { row.values = resolved; matched++; }
      else warnings.push(`${u.label} > "${row.accountLabel}"`);
    });
  });

  // ============================================================
  // AKUN BARU DARI CSV -- kalau CSV punya kombinasi Entity+Account yang
  // BELUM ada dimanapun di waterfall entity itu (baik sbg baris top-level
  // maupun anak/cucu bersarang), baris itu otomatis ditambahkan sbg baris
  // waterfall baru (bukan cuma nge-warn "tidak ketemu" spt akun yg memang
  // typo/salah nama). Baris baru disisipkan tepat SEBELUM baris computed
  // (mis. "Laba Bersih" di Store yg dihitung dari baris2 lain) supaya baris
  // hasil kalkulasi tetap di paling bawah; kalau entity itu tidak punya
  // baris computed, baris baru ditaruh di akhir. Ditandai badge "CSV BARU"
  // di UI (lihat .csv-new-tag) supaya kelihatan beda dari baris asli.
  function collectKnownAccounts(u){
    const known = new Set();
    function walk(node){
      if (!node) return;
      if (node.accountLabel) known.add(node.accountLabel);
      // baris computed (spt "Laba Bersih" hasil hitungan) kadang accountLabel-nya
      // kosong (''), sehingga nama aslinya tidak pernah terdaftar "dikenal" --
      // itu menyebabkan versi mentah dari CSV (yg bisa jadi masih bug/basi)
      // lolos masuk lagi sbg "akun baru" dgn NAMA SAMA tapi ANGKA BEDA,
      // membingungkan. Daftarkan juga row.name (bukan cuma accountLabel).
      if (node.name) known.add(node.name.replace(/<[^>]*>/g, '').trim());
      if (node.children) {
        Object.entries(node.children).forEach(([childName, childVal]) => {
          known.add(childName.trim());
          if (!Array.isArray(childVal)) walk(childVal); // group bersarang {vals, children}
        });
      }
    }
    u.waterfall.forEach(row => walk(row));
    return known;
  }

  Object.entries(ENTITY_KEY_MAP).forEach(([unitKey, entityName]) => {
    const u = UNIT_DATA[unitKey];
    if (!u || !u.waterfall) return;
    const known = collectKnownAccounts(u);
    const accountsInCSV = [...new Set(
      allRows.filter(r => r.Entity === entityName && r.Account && r.Account.trim() !== '').map(r => r.Account.trim())
    )];
    const newAccounts = accountsInCSV.filter(acc => !known.has(acc));
    // Kumpulkan SEMUA akun baru jadi satu grup dropdown ("Item Tambahan dari
    // CSV") -- bukan dibeberkan sbg baris top-level terpisah satu-satu spt
    // sebelumnya. Konsepnya sama dgn baris lain: rincian ada di dalam
    // dropdown, bukan berserakan di tabel utama.
    const groupChildren = {};
    newAccounts.forEach(acc => {
      const values = PERIODS.map(period => {
        const hit = findValueRow(entityName, acc, period);
        return hit ? parseAmount(hit.Amount) : null;
      });
      // lewati kalau SEMUA nilai kosong di semua periode -- baris begini
      // biasanya cuma Header kosong di source (spt "Biaya Operasional" yg
      // jadi judul section, bukan angka sendiri), tidak ada informasi apa
      // pun buat ditampilkan, cuma bikin ramai tanpa guna.
      if (values.every(v => v === null)) return;
      groupChildren[acc] = values;
      added++;
    });
    if (Object.keys(groupChildren).length > 0) {
      // TIDAK ditampilkan sbg baris di tabel (sesuai instruksi user: hanya 11
      // baris tetap yg boleh tampil). Tapi TETAP dicatat ke console -- supaya
      // ada jejak kalau ternyata ada uang riil yg belum ter-petakan ke
      // struktur manapun, bukan hilang tanpa jejak sama sekali.
      console.warn(`[${u.label}] ${Object.keys(groupChildren).length} akun blm ter-petakan ke dropdown manapun (TIDAK ditampilkan di tabel, cek di sini):`, groupChildren);
    }
  });

  rebuildStoreComputed();
  rebuildManufakturComputed();
  rebuildHOComputed();
  rebuildOwnershipComputed();
  rebuildAllOutletsComputed();
  rebuildKonsolidasiComputed();
  return { matched, warnings, added };
}

// ISI DUA VARIABEL INI dari spreadsheet Anda:
// - GVIZ_SPREADSHEET_ID: bagian URL antara '/d/' dan '/edit'
// - GVIZ_SHEET_NAME: nama tab yang berisi data long-format (kemungkinan "Data")
// Sheet HARUS di-share "Anyone with the link can view" supaya gviz bisa diakses.
const GVIZ_SPREADSHEET_ID = '1B15PT_Q_oAubKcZMazIGDdznXK5v3rlkxIsR28AIcwA'; // dbpnl
const GVIZ_SHEET_NAME = 'Data';
const GVIZ_ONLINE_SHEET = 'Online'; // nama tab (fallback)
const GVIZ_ONLINE_GID = '2067229002'; // gid tab Online (dari URL spreadsheet). Lebih andal drpd nama.
let ONLINE_ROWS = []; // baris mentah dari sheet Online (diisi live-fetch atau upload)
let ONLINE_FETCH_STATUS = ''; // '' = belum dicoba, 'ok' = berhasil, selain itu = pesan alasan gagal (utk ditampilkan di UI)

function csvRowsToObjects(csvText){
  const rowsRaw = parseCSVText(csvText); // parser yg sama dgn fitur upload CSV
  if (rowsRaw.length < 2) return [];
  const headers = rowsRaw[0].map(h => h.trim());
  return rowsRaw.slice(1).filter(r => r.length > 1).map(r => {
    const obj = {};
    headers.forEach((h, i) => obj[h] = r[i] !== undefined ? r[i] : '');
    return obj;
  });
}

// Diagnostik: coba beberapa varian URL utk tab Online, laporkan mana yg tembus.
// Hasilnya memberi tahu persis apakah masalahnya gid, sharing, atau CORS.
async function diagnoseOnline(){
  const el = document.getElementById('liveStatus');
  const say = (msg, color) => { if (el){ el.innerHTML = msg; el.style.color = color||'#9B93C4'; } };
  const base = `https://docs.google.com/spreadsheets/d/${GVIZ_SPREADSHEET_ID}`;
  const sep = APPS_SCRIPT_URL.includes('?') ? '&' : '?';
  const variants = [
    ['Apps Script ?sheet=Online', `${APPS_SCRIPT_URL}${sep}sheet=${encodeURIComponent(GVIZ_ONLINE_SHEET)}&t=${Date.now()}`, 'json'],
    ['gviz + gid',      `${base}/gviz/tq?tqx=out:csv&gid=${GVIZ_ONLINE_GID}&t=${Date.now()}`, 'csv'],
    ['gviz + nama',     `${base}/gviz/tq?tqx=out:csv&sheet=${encodeURIComponent(GVIZ_ONLINE_SHEET)}&t=${Date.now()}`, 'csv'],
    ['export CSV + gid',`${base}/export?format=csv&gid=${GVIZ_ONLINE_GID}&t=${Date.now()}`, 'csv'],
  ];
  let out = '<b>Hasil tes tab Online:</b><br>';
  for (const [label, url, kind] of variants){
    try {
      const r = await fetch(url);
      if (!r.ok){ out += `• ${label}: HTTP ${r.status} ✗<br>`; continue; }
      let txt, isOnline;
      if (kind === 'json') {
        const j = await r.json();
        txt = JSON.stringify(j);
        if (j && j.error) { out += `• ${label}: Apps Script error: "${j.error}" ✗<br>`; continue; }
        isOnline = Array.isArray(j) && /Harga Normal|Adjustment Harga/i.test(txt);
        const rowCount = Array.isArray(j) ? j.length : 0;
        if (isOnline) out += `• ${label}: <span style="color:#4ADE80;">BERHASIL — ${rowCount} baris ✓ (INI YANG DIPAKAI DASHBOARD)</span><br>`;
        else if (Array.isArray(j) && rowCount>0) out += `• ${label}: dapat ${rowCount} baris tapi BUKAN sheet Online -- cek Code.gs sudah versi terbaru & di-redeploy ✗<br>`;
        else out += `• ${label}: balasan kosong/tak terduga ✗<br>`;
        continue;
      }
      txt = await r.text();
      if (txt.trim().startsWith('<')){ out += `• ${label}: dapat HTML (sharing/format) ✗<br>`; continue; }
      isOnline = /Harga Normal|Adjustment Harga/i.test(txt);
      const isData = /SourceBatch|Pendapatan Offline/i.test(txt) && !isOnline;
      const rowCount = txt.split('\n').filter(l=>l.trim()).length;
      if (isOnline) out += `• ${label}: <span style="color:#4ADE80;">BERHASIL — sheet Online, ${rowCount} baris ✓</span><br>`;
      else if (isData) out += `• ${label}: dapat sheet DATA (bukan Online) ✗<br>`;
      else out += `• ${label}: dapat CSV tapi isi tak dikenali (${rowCount} baris) ?<br>`;
      await new Promise(res=>setTimeout(res,400));
    } catch(e){
      out += `• ${label}: <span style="color:#FB7185;">Load failed / CORS ✗</span> (${e.message})<br>`;
      await new Promise(res=>setTimeout(res,400));
    }
  }
  out += '<span style="color:#726C9C;font-size:10.5px;">Dashboard SEKARANG pakai jalur "Apps Script ?sheet=Online" sbg utama (gviz cuma cadangan). Kalau baris itu ✗ dgn pesan error Code.gs -> pastikan Code.gs sudah di-tempel versi terbaru & di-Deploy ulang sbg "New version".</span>';
  say(out, '#F5F3FF');
}

async function loadLiveData(){
  const statusEl = document.getElementById('liveStatus');
  const setStatus = (text, color) => { if (statusEl){ statusEl.textContent = text; statusEl.style.color = color; } };
  const dot = document.getElementById('liveDot');
  const badgeText = document.getElementById('liveBadgeText');
  const meta = document.getElementById('liveMeta');
  const setBadge = (dotColor, text) => {
    if (dot) dot.style.background = dotColor;
    if (badgeText) badgeText.textContent = text;
  };

  let allRows = null;
  let sourceUsed = '';

  // --- Coba gviz/tq dulu (CSV bawaan Google, tanpa deploy, tanpa risiko Date-object) ---
  if (GVIZ_SPREADSHEET_ID) {
    setStatus('🔄 Menghubungkan via gviz (Google Sheets bawaan)...', '#9B93C4');
    setBadge('#F5C147', 'Menghubungkan...');
    try {
      const gvizUrl = `https://docs.google.com/spreadsheets/d/${GVIZ_SPREADSHEET_ID}/gviz/tq?tqx=out:csv&sheet=${encodeURIComponent(GVIZ_SHEET_NAME)}&t=${Date.now()}`;
      const res = await fetch(gvizUrl);
      if (!res.ok) throw new Error('HTTP ' + res.status);
      const text = await res.text();
      if (text.trim().startsWith('<!') || text.trim().startsWith('<html')) {
        throw new Error('Google mengembalikan HTML, bukan CSV -- kemungkinan sheet belum di-share "Anyone with link" atau nama tab salah');
      }
      allRows = csvRowsToObjects(text);
      if (!allRows.length) throw new Error('CSV terbaca tapi 0 baris -- cek header kolom di tab "' + GVIZ_SHEET_NAME + '"');
      sourceUsed = 'gviz';
    } catch (e) {
      console.warn('gviz/tq gagal, coba fallback ke Apps Script:', e.message);
    }
  }

  // --- Fallback ke Apps Script kalau gviz tidak diisi/gagal ---
  if (!allRows && APPS_SCRIPT_URL) {
    setStatus('🔄 Menghubungkan via Apps Script...', '#9B93C4');
    setBadge('#F5C147', 'Menghubungkan...');
    try {
      const res = await fetch(APPS_SCRIPT_URL);
      if (!res.ok) throw new Error('HTTP ' + res.status);
      const json = await res.json();
      if (!Array.isArray(json)) throw new Error('Response bukan array JSON');
      allRows = json;
      sourceUsed = 'apps-script';
    } catch (e) {
      console.error('Live fetch GAGAL total (gviz & Apps Script keduanya gagal/tidak diisi):', e);
      setStatus('🔴 Live-fetch GAGAL (' + e.message + '). Pakai data statis bawaan.', '#FB7185');
      setBadge('#FB7185', 'Gagal terhubung');
      if (meta) meta.textContent = 'Percobaan terakhir: ' + new Date().toLocaleTimeString('id-ID') + ' (gagal)';
      return;
    }
  }

  if (!allRows){
    setStatus('⚪ Live-fetch tidak aktif (GVIZ_SPREADSHEET_ID & APPS_SCRIPT_URL kosong) -- pakai data statis bawaan.', '#726C9C');
    setBadge('#726C9C', 'Tidak aktif');
    return;
  }

  mergePeriodsFromRows(allRows);
  const { matched, warnings, added } = applyRowsToUnitData(allRows);

  // --- Sheet kedua "Online": detail Pendapatan Online (Harga Normal vs
  //     Adjustment) utk split kanal Online/Offline. Sheet TERPISAH dari "Data"
  //     (keputusan user), jadi perlu fetch sendiri. Kegagalan sheet ini TIDAK
  //     menggagalkan dashboard -- split online cuma tidak tampil.
  if (GVIZ_SPREADSHEET_ID || APPS_SCRIPT_URL) {
    // Ambil baris Online dari respons apa pun -> normalisasi ke bentuk objek
    // {Period, Path, Amount, ...}. parseOnlineRows() sudah toleran objek/array.
    function acceptOnline(rows, via){
      // verifikasi ini benar sheet Online (ada Harga Normal / Adjustment)
      const asText = JSON.stringify(rows);
      if (!/Harga Normal|Adjustment Harga/i.test(asText)) {
        ONLINE_FETCH_STATUS = `Data ter-ambil (${via}) tapi BUKAN sheet Online (tak ada "Harga Normal"/"Adjustment").`;
        return false;
      }
      ONLINE_ROWS = rows;
      ONLINE_FETCH_STATUS = rows.length ? 'ok' : `Sheet Online kosong.`;
      console.log(`Sheet Online: ${rows.length} baris dimuat (via ${via}).`);
      return true;
    }

    // 1) UTAMA: Apps Script ?sheet=Online -- jalur yg TERBUKTI bekerja di
    //    environment user (gviz/docs.google.com diblokir CORS di localhost).
    let done = false;
    if (APPS_SCRIPT_URL) {
      try {
        const sep = APPS_SCRIPT_URL.includes('?') ? '&' : '?';
        const res2 = await fetch(`${APPS_SCRIPT_URL}${sep}sheet=${encodeURIComponent(GVIZ_ONLINE_SHEET)}&t=${Date.now()}`);
        if (res2.ok) {
          const j = await res2.json();
          if (Array.isArray(j)) done = acceptOnline(j, 'apps-script');
          else ONLINE_FETCH_STATUS = 'Apps Script balas non-array (cek Code.gs sudah versi multi-sheet & di-redeploy).';
        } else ONLINE_FETCH_STATUS = `Apps Script HTTP ${res2.status}.`;
      } catch(e){ ONLINE_FETCH_STATUS = 'Apps Script gagal: ' + e.message; }
    }

    // 2) CADANGAN: gviz (kalau Apps Script tak tersedia/gagal & gviz bisa)
    if (!done && GVIZ_SPREADSHEET_ID) {
      try {
        const sheetParam = GVIZ_ONLINE_GID ? `gid=${encodeURIComponent(GVIZ_ONLINE_GID)}` : `sheet=${encodeURIComponent(GVIZ_ONLINE_SHEET)}`;
        const res3 = await fetch(`https://docs.google.com/spreadsheets/d/${GVIZ_SPREADSHEET_ID}/gviz/tq?tqx=out:csv&${sheetParam}&t=${Date.now()}`);
        if (res3.ok) {
          const t3 = await res3.text();
          if (!t3.trim().startsWith('<')) {
            const rows = parseCSVText(t3).filter(r => r.length > 1 && r[0] && r[0].toString().trim());
            done = acceptOnline(rows, 'gviz');
          } else {
            ONLINE_FETCH_STATUS = 'gviz balas HTML (bukan CSV) -- kemungkinan sharing/gid Online salah.';
          }
        } else {
          ONLINE_FETCH_STATUS = `gviz HTTP ${res3.status} saat ambil tab Online.`;
        }
      } catch(e){ ONLINE_FETCH_STATUS = 'gviz gagal: ' + e.message; }
    }
    // Jaring pengaman TERAKHIR: kalau sampai sini masih belum ada status SAMA
    // SEKALI (mis. APPS_SCRIPT_URL kosong DAN GVIZ_SPREADSHEET_ID kosong,
    // atau kombinasi kondisi lain yg tak terduga), jangan biarkan banner
    // kosong tanpa penjelasan -- itu yg bikin sulit didiagnosis sebelumnya.
    if (!done && !ONLINE_FETCH_STATUS) {
      ONLINE_FETCH_STATUS = !APPS_SCRIPT_URL && !GVIZ_SPREADSHEET_ID
        ? 'APPS_SCRIPT_URL & GVIZ_SPREADSHEET_ID dua-duanya kosong -- tak ada jalur fetch yg dicoba sama sekali.'
        : 'Tak ada baris valid diterima dari kedua jalur (apps-script & gviz), penyebab pasti tak terdeteksi -- cek Console.';
    }
  }

  console.log(`Live fetch (${sourceUsed}): ${matched} baris cocok, ${added} baris akun baru ditambahkan, ${warnings.length} tidak ketemu.`);
  setStatus('', '#726C9C'); // bersihkan pesan "Menghubungkan..." -- status sukses cukup lewat badge+meta, tak perlu baris terpisah lagi
  const lastPeriod = PERIODS.length ? periodLabel(PERIODS[PERIODS.length-1], true) : '-';
  setBadge('#4ADE80', `Live · ${lastPeriod}`);
  if (meta) meta.textContent = `Data s/d: ${lastPeriod} · Diperbarui: ${new Date().toLocaleTimeString('id-ID')} (via ${sourceUsed})`;
  render();
}

/* ============================================================
   UPLOAD CSV LOKAL -- dibaca langsung di browser (FileReader), TIDAK
   ada fetch ke server manapun, jadi TIDAK ada isu CORS sama sekali.
   Format CSV harus persis: header Period,Entity,Counterparty,Account,
   Level,Path,Type,Amount,SourceBatch (urutan kolom bebas, nama header
   harus persis sama -- case-sensitive).
   ============================================================ */

// parser CSV RFC4180-ish: menangani field ber-kutip yg berisi koma/newline
function parseCSVText(text){
  const rows = [];
  let row = [], field = '', inQuotes = false;
  for (let i=0; i<text.length; i++){
    const c = text[i], next = text[i+1];
    if (inQuotes){
      if (c === '"' && next === '"'){ field += '"'; i++; }
      else if (c === '"'){ inQuotes = false; }
      else field += c;
    } else {
      if (c === '"') inQuotes = true;
      else if (c === ','){ row.push(field); field=''; }
      else if (c === '\r'){ /* skip, biar \r\n atau \n dua-duanya jalan */ }
      else if (c === '\n'){ row.push(field); rows.push(row); row=[]; field=''; }
      else field += c;
    }
  }
  if (field.length || row.length){ row.push(field); rows.push(row); }
  return rows;
}

function handleCSVUpload(event){
  const file = event.target.files[0];
  const statusEl = document.getElementById('csvStatus');
  if (!file) return;
  statusEl.textContent = `Membaca ${file.name}...`;
  statusEl.style.color = '#9B93C4';

  const reader = new FileReader();
  reader.onerror = () => {
    statusEl.textContent = `Gagal baca file ${file.name}.`;
    statusEl.style.color = '#FB7185';
  };
  reader.onload = (e) => {
    try {
      const rowsRaw = parseCSVText(e.target.result);
      if (rowsRaw.length < 2) throw new Error('File kosong atau cuma ada header.');
      const headers = rowsRaw[0].map(h => h.trim());
      const required = ['Period','Entity','Account','Amount'];
      const missing = required.filter(r => !headers.includes(r));
      if (missing.length) throw new Error(`Header wajib tidak ketemu: ${missing.join(', ')}. Header yang ada: ${headers.join(', ')}`);

      const allRows = rowsRaw.slice(1).filter(r => r.length > 1).map(r => {
        const obj = {};
        headers.forEach((h, i) => obj[h] = r[i] !== undefined ? r[i] : '');
        return obj;
      });

      const periodsBefore = PERIODS.length;
      const periodsChanged = mergePeriodsFromRows(allRows);
      const newPeriodsCount = PERIODS.length - periodsBefore;
      const { matched, warnings, added } = applyRowsToUnitData(allRows);
      render();

      statusEl.style.color = (matched > 0 || added > 0) ? '#4ADE80' : '#FB7185';
      statusEl.textContent = `${file.name}: ${allRows.length} baris dibaca, ${matched} baris akun ter-update` +
        (added ? `, ${added} baris akun baru otomatis ditambahkan` : '') +
        (periodsChanged ? `, ${newPeriodsCount} bulan baru otomatis ditambahkan ke dashboard` : '') +
        (warnings.length ? `, ${warnings.length} akun tidak ketemu (cek Console).` : '.');
      if (warnings.length) console.warn('Akun yang tidak ketemu di CSV upload:', warnings);
      if (added) console.log(`Baris akun baru ditambahkan otomatis dari CSV upload (ditandai badge "CSV BARU" di tabel):`, added);
      if (matched === 0 && added === 0) console.error('TIDAK ADA satu pun baris akun yang cocok/baru -- cek apakah nama Entity/Account di CSV persis sama dengan yang diharapkan dashboard (case-sensitive).');
    } catch (err) {
      statusEl.textContent = `Error: ${err.message}`;
      statusEl.style.color = '#FB7185';
      console.error('CSV upload error:', err);
    }
  };
  reader.readAsText(file, 'UTF-8');
}
</script>
<!-- Financial Analysis (Phase 1 -- Financial Intelligence Dashboard). Dimuat
     SETELAH script di atas supaya bisa memakai UNIT_DATA/PERIODS/OUTLET_KEYS/
     fmtRp/getLineVals/dst sbg global, tanpa mengubah satu baris pun kode di
     atas. Read-only analytics -- tidak pernah menulis balik ke UNIT_DATA. -->
<script src="financial-analysis.js"></script>
</body>
</html>
