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
 * Known limitation (same posture as the existing lock screen, which already
 * documents itself as cosmetic-only): this dashboard has no real server-side
 * authentication, so this endpoint is reachable by anyone who can load the
 * page. It is deliberately narrow (fixed system prompt, JSON-only, size
 * capped) so it can't be repurposed as a general chatbot proxy.
 */

header('Content-Type: application/json; charset=utf-8');

function fa_fail($reason) {
    http_response_code(200);
    echo json_encode(['ok' => false, 'reason' => $reason]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    fa_fail('method_not_allowed');
}

$raw = file_get_contents('php://input', false, null, 0, 262144); // cap 256KB
if ($raw === false || $raw === '') {
    fa_fail('empty_body');
}

$context = json_decode($raw, true);
if (!is_array($context)) {
    fa_fail('invalid_json');
}

// API key lives ONLY in the server environment -- never in this file, never
// sent to the browser, never logged. Configure it on the host (e.g. Apache
// SetEnv / php-fpm pool env / .env loaded by the hosting panel).
$apiKey = getenv('ANTHROPIC_API_KEY');
if (!$apiKey) {
    fa_fail('not_configured');
}
$model = getenv('FA_AI_MODEL') ?: 'claude-sonnet-4-5';

$systemPrompt = <<<PROMPT
Anda adalah Senior Financial Analyst dan Finance Business Partner yang berpengalaman menangani bisnis F&B multi-outlet.

Analisis HANYA berdasarkan data keuangan terstruktur yang diberikan aplikasi ini. JANGAN PERNAH mengarang angka keuangan. JANGAN PERNAH mengarang root cause.

Prioritaskan isu keuangan yang material (lihat profit_drivers, anomalies, expense_variances -- semua sudah difilter berdasarkan materialitas oleh backend). Jangan hanya mendeskripsikan angka.

Untuk setiap insight penting, jelaskan: apa yang terjadi, kenapa ini penting, kemungkinan penyebab, apa yang perlu dicek management, dan rekomendasi tindakan.

Bandingkan periode berjalan dengan tren historis (historical_trends). Bedakan antara: fluktuasi satu-off, pergerakan musiman, perbaikan konsisten, penurunan konsisten, dan masalah struktural.

Jika root cause tidak bisa diverifikasi dari data yang tersedia, nyatakan dengan jelas: "Needs Investigation".

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
    fa_fail('network');
}
if ($httpCode < 200 || $httpCode >= 300) {
    fa_fail('upstream_error');
}

$data = json_decode($response, true);
$text = $data['content'][0]['text'] ?? null;
if (!$text) {
    fa_fail('empty_response');
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
    fa_fail('empty_summary');
}

echo json_encode(['ok' => true, 'summary' => $summary], JSON_UNESCAPED_UNICODE);
