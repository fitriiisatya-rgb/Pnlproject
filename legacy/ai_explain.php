<?php
/**
 * AI Explanation Engine (Phase 1 — Financial Intelligence Dashboard).
 *
 * Purpose: the ONLY server-side code this project gains. It exists purely
 * so the AI API key never has to sit in the browser. It does NOT compute any
 * financial figure — the frontend calculation engine (financial-analysis.js)
 * has already computed every KPI/ratio/trend/anomaly/forecast; this endpoint
 * only asks an LLM to *narrate* the structured numbers it's given.
 *
 * Contract: POST a JSON body (see financial-analysis.js: faBuildAIContext())
 * -> { ok:true, summary: string[] } on success, or { ok:false, reason } on
 * any failure. The dashboard is fully functional without this endpoint ever
 * succeeding (see AI FAILURE HANDLING in the project spec) -- so failures
 * here must never surface as a 5xx that breaks the page; always return 200
 * with an { ok:false } body the frontend already knows how to handle.
 *
 * Security posture (QA review point 26): the request body is NOT forwarded
 * to the LLM as-is. Only a fixed, whitelisted set of fields is extracted and
 * re-serialized server-side (fa_extract_context() below) -- any extra field,
 * or a body that isn't shaped like the expected financial context, is
 * ignored/rejected. This keeps the endpoint from being repurposed as a
 * general-purpose LLM proxy paid for by the site owner's API key, and keeps
 * arbitrary browser-supplied text out of the prompt.
 *
 * Known limitation (same posture as the existing lock screen, which already
 * documents itself as cosmetic-only): this dashboard has no real server-side
 * authentication, so this endpoint is reachable by anyone who can load the
 * page. The schema whitelist above is the mitigation for that -- it cannot
 * turn into a free-form chat proxy even without login.
 */

// Poin 26 QA review: PHP error/warning/stack trace TIDAK BOLEH pernah bocor
// ke response (path filesystem, versi PHP, dll). Semua kegagalan sudah
// ditangani eksplisit lewat fa_fail() di bawah; ini cuma jaring pengaman
// terakhir kalau ada error tak terduga (mis. TypeError).
error_reporting(0);
ini_set('display_errors', '0');
header('Content-Type: application/json; charset=utf-8');

// Poin 27 QA review: log kegagalan server-side (AI gagal, config hilang,
// schema tak valid) SECUKUPNYA utk diagnosis -- TIDAK PERNAH log API key,
// dan TIDAK log seluruh payload finansial (cukup entity+period+reason).
function fa_log($reason, $context = []) {
    error_log('[ai_explain] ' . $reason . ' ' . json_encode($context, JSON_UNESCAPED_UNICODE));
}
function fa_fail($reason, $logContext = []) {
    fa_log($reason, $logContext);
    http_response_code(200);
    echo json_encode(['ok' => false, 'reason' => $reason]);
    exit;
}

/**
 * Whitelist eksplisit: HANYA field ini yg diteruskan ke LLM, apapun yg
 * dikirim browser. Field tak dikenal dibuang diam2 (bukan error) -- ini
 * bukan validasi ketat ala API publik, tujuannya semata memastikan isi
 * prompt SELALU berbentuk konteks finansial terstruktur, bukan teks bebas.
 */
function fa_extract_context(array $raw): array {
    $str = fn($v) => is_string($v) ? $v : (is_scalar($v) ? (string)$v : null);
    $num = fn($v) => is_numeric($v) ? $v + 0 : null;
    $bool = fn($v) => is_bool($v) ? $v : null;
    $arr = fn($v) => is_array($v) ? $v : [];

    $kpiFields = ['revenue','cogs','gross_profit','gross_margin_pct','opex','opex_ratio_pct','operating_profit','operating_margin_pct','net_profit','net_margin_pct'];
    $extractKpis = function($v) use ($arr, $num, $kpiFields) {
        $v = $arr($v); $out = [];
        foreach ($kpiFields as $f) $out[$f] = isset($v[$f]) ? $num($v[$f]) : null;
        return $out;
    };

    $out = [
        'entity' => $str($raw['entity'] ?? null),
        'current_period' => $str($raw['current_period'] ?? null),
        'is_open_month' => $bool($raw['is_open_month'] ?? null),
        'days_elapsed' => $num($raw['days_elapsed'] ?? null),
        'days_in_month' => $num($raw['days_in_month'] ?? null),
        'comparison_period' => $str($raw['comparison_period'] ?? null),
        'kpis' => $extractKpis($raw['kpis'] ?? []),
        'comparison_kpis' => $extractKpis($raw['comparison_kpis'] ?? []),
        'historical_trends' => [
            'revenue' => $str($arr($raw['historical_trends'] ?? [])['revenue'] ?? null),
            'net_profit' => $str($arr($raw['historical_trends'] ?? [])['net_profit'] ?? null),
        ],
        'financial_health_score' => $num($raw['financial_health_score'] ?? null),
        'data_completeness' => [
            'overall_pct' => $num($arr($raw['data_completeness'] ?? [])['overall_pct'] ?? null),
            'confidence' => $str($arr($raw['data_completeness'] ?? [])['confidence'] ?? null),
        ],
    ];

    $out['expense_variances'] = array_map(function($r) use ($str, $num) {
        $r = is_array($r) ? $r : [];
        return [
            'category' => $str($r['category'] ?? null), 'current' => $num($r['current'] ?? null),
            'previous_month' => $num($r['previous_month'] ?? null), 'variance_rp' => $num($r['variance_rp'] ?? null),
            'variance_pct' => $num($r['variance_pct'] ?? null), 'pct_revenue' => $num($r['pct_revenue'] ?? null),
            'status' => $str($r['status'] ?? null),
        ];
    }, array_slice($arr($raw['expense_variances'] ?? []), 0, 10));

    $out['anomalies'] = array_map(function($r) use ($str, $num) {
        $r = is_array($r) ? $r : [];
        return [
            'account' => $str($r['account'] ?? null), 'outlet' => $str($r['outlet'] ?? null),
            'variance_rp' => $num($r['variance_rp'] ?? null), 'variance_pct' => $num($r['variance_pct'] ?? null),
            'profit_impact' => $num($r['profit_impact'] ?? null), 'severity' => $str($r['severity'] ?? null),
        ];
    }, array_slice($arr($raw['anomalies'] ?? []), 0, 10));

    $driverItem = function($r) use ($str, $num) {
        $r = is_array($r) ? $r : [];
        return ['name' => $str($r['name'] ?? null), 'impact' => $num($r['impact'] ?? null)];
    };
    $pd = $arr($raw['profit_drivers'] ?? []);
    $out['profit_drivers'] = [
        'positive' => array_map($driverItem, array_slice($arr($pd['positive'] ?? []), 0, 6)),
        'negative' => array_map($driverItem, array_slice($arr($pd['negative'] ?? []), 0, 6)),
    ];

    $outletPerf = $raw['outlet_performance'] ?? null;
    $out['outlet_performance'] = is_array($outletPerf) ? array_map(function($r) use ($str, $num) {
        $r = is_array($r) ? $r : [];
        return ['outlet' => $str($r['outlet'] ?? null), 'revenue' => $num($r['revenue'] ?? null),
            'net_margin_pct' => $num($r['net_margin_pct'] ?? null), 'classification' => $str($r['classification'] ?? null)];
    }, array_slice($outletPerf, 0, 30)) : null;

    $fc = $arr($raw['forecast'] ?? []);
    $out['forecast'] = isset($fc['actual']) ? ['actual' => true] : [
        'projected_revenue' => $num($fc['projected_revenue'] ?? null),
        'projected_net_profit' => $num($fc['projected_net_profit'] ?? null),
        'confidence' => $str($fc['confidence'] ?? null),
    ];

    return $out;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    fa_fail('method_not_allowed');
}

$raw = file_get_contents('php://input', false, null, 0, 262144); // cap 256KB
if ($raw === false || $raw === '') {
    fa_fail('empty_body');
}

$rawContext = json_decode($raw, true);
if (!is_array($rawContext)) {
    fa_fail('invalid_json');
}
$context = fa_extract_context($rawContext);

// API key lives ONLY in the server environment -- never in this file, never
// sent to the browser, never logged. Configure it on the host (e.g. Apache
// SetEnv / php-fpm pool env / .env loaded by the hosting panel).
$apiKey = getenv('ANTHROPIC_API_KEY');
if (!$apiKey) {
    fa_fail('not_configured');
}
$model = getenv('FA_AI_MODEL') ?: 'claude-sonnet-4-5';

// Poin 17/18 QA review: instruksi anti-halusinasi eksplisit + larangan
// sekadar membeo angka kartu. AI di sini HANYA interpretasi -- backend sudah
// menghitung & memfilter semua angka berdasarkan materialitas.
$systemPrompt = <<<PROMPT
Anda adalah Senior Financial Analyst dan Finance Business Partner yang berpengalaman menangani bisnis F&B multi-outlet.

Analisis HANYA berdasarkan data keuangan terstruktur yang diberikan aplikasi ini (JSON di bawah). Aturan mutlak:
- JANGAN PERNAH menghitung atau mengoreksi angka finansial sendiri -- semua angka sudah final dari calculation engine.
- JANGAN PERNAH mengarang angka, akun, nama outlet, periode historis, atau budget yang tidak ada di data.
- JANGAN mengklaim hubungan sebab-akibat kecuali didukung eksplisit oleh data (mis. profit_drivers/anomalies). Kalau penyebab tidak bisa diverifikasi dari data yang tersedia, gunakan frasa "Possible contributor", "Likely contributor", atau nyatakan tegas "Needs Investigation" -- jangan menyimpulkan penyebab pasti.
- JANGAN sekadar mengulang angka kartu (contoh BURUK: "Revenue adalah Rp500 juta"). Setiap poin HARUS berupa insight: tren, risiko, dampak profit, atau rekomendasi (contoh BAIK: "Revenue daily run rate 9% di atas bulan lalu, tapi sebagian tergerus penurunan GP margin 2,1pp").

Prioritaskan isu keuangan yang material (lihat profit_drivers, anomalies, expense_variances -- semua sudah difilter berdasarkan materialitas oleh backend, bukan daftar lengkap semua akun).

Bandingkan periode berjalan dengan tren historis (historical_trends). Bedakan antara: fluktuasi satu-off, pergerakan musiman, perbaikan konsisten, penurunan konsisten, dan masalah struktural -- HANYA jika data historis yang diberikan mendukung pembedaan itu.

Gunakan Bahasa Indonesia yang ringkas dan profesional, sesuai untuk owner, direktur, dan management. Jangan gunakan markdown heading -- cukup kalimat lugas.

Output WAJIB berupa JSON array of strings (maksimal 8 butir), setiap string 1 kalimat lengkap. Jangan sertakan teks lain di luar JSON array itu.
PROMPT;

$userMessage = "Data keuangan terstruktur (JSON):\n" . json_encode($context, JSON_UNESCAPED_UNICODE);

$payload = json_encode([
    'model' => $model,
    'max_tokens' => 1024,
    'system' => $systemPrompt,
    'messages' => [
        ['role' => 'user', 'content' => $userMessage],
    ],
]);

$logCtx = ['entity' => $context['entity'], 'period' => $context['current_period']];

$ch = curl_init('https://api.anthropic.com/v1/messages');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $payload,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'x-api-key: ' . $apiKey,
        'anthropic-version: 2023-06-01',
    ],
    CURLOPT_TIMEOUT => 25,
    CURLOPT_CONNECTTIMEOUT => 8,
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErr = curl_errno($ch);
curl_close($ch);

if ($curlErr || $response === false) {
    fa_fail('network', $logCtx);
}
if ($httpCode < 200 || $httpCode >= 300) {
    fa_fail('upstream_error', $logCtx + ['http_code' => $httpCode]);
}

$data = json_decode($response, true);
$text = $data['content'][0]['text'] ?? null;
if (!$text) {
    fa_fail('empty_response', $logCtx);
}

// Model diminta menjawab JSON array of strings -- coba parse langsung; kalau
// gagal (mis. model membungkusnya dgn teks lain), fallback pecah per baris.
$summary = json_decode(trim($text), true);
if (!is_array($summary)) {
    $lines = preg_split('/\r?\n/', trim($text));
    $summary = [];
    foreach ($lines as $line) {
        $line = trim($line, " \t-*•\r\n");
        if ($line !== '') $summary[] = $line;
    }
}
$summary = array_values(array_filter(array_map('strval', $summary), fn($s) => trim($s) !== ''));
$summary = array_slice($summary, 0, 8);

if (!count($summary)) {
    fa_fail('empty_summary', $logCtx);
}

echo json_encode(['ok' => true, 'summary' => $summary], JSON_UNESCAPED_UNICODE);
