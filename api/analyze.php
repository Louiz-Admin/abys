<?php
// Fichier: abys-ai/api/analyze.php
set_time_limit(90);
header('Content-Type: application/json');

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/ai-helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); die(json_encode(['error' => 'Method not allowed']));
}

$input   = json_decode(file_get_contents('php://input'), true);
$domain  = trim($input['domain']   ?? '');
$scrape  = $input['scrape_data']   ?? [];
$answers = $input['answers']       ?? [];
$lead_id = intval($input['lead_id'] ?? 0);

if (!$domain && !$answers) {
    http_response_code(400); die(json_encode(['error' => 'Données manquantes']));
}

$db       = get_db();

// ── Garde-fou de coût ────────────────────────────────────────────────────────
// Chaque appel ici consomme du budget IA. Sans limite, n'importe qui peut vider
// la cagnotte en boucle. Deux verrous : par visiteur, et global sur la journée.
$ip_key    = md5($_SERVER['REMOTE_ADDR'] ?? 'inconnu');
$rate_file = sys_get_temp_dir() . '/abys_ia_' . $ip_key;
$rate      = is_file($rate_file) ? (json_decode(file_get_contents($rate_file), true) ?: []) : [];
if (!isset($rate['reset']) || time() > $rate['reset']) $rate = ['count' => 0, 'reset' => time() + 3600];
if ($rate['count'] >= 6) {
    http_response_code(429);
    die(json_encode(['error' => "Vous avez lancé plusieurs audits d'affilée. Réessayez dans une heure."]));
}
$rate['count']++;
@file_put_contents($rate_file, json_encode($rate));

try {
    $plafond = (int) ($db->query("SELECT value FROM settings WHERE `key`='audits_max_jour'")->fetchColumn() ?: 120);
    $aujourd = (int) $db->query("SELECT COUNT(*) FROM audits WHERE created_at >= CURDATE()")->fetchColumn();
    if ($aujourd >= $plafond) {
        http_response_code(429);
        die(json_encode(['error' => "Le nombre d'audits du jour est atteint. Revenez demain, ou écrivez à contact@abys.ai."]));
    }
} catch (Throwable $e) { /* la limite ne doit jamais bloquer un audit legitime */ }

$settings = get_settings($db);
$provider = $settings['ai_provider'] ?? 'claude';
$prompt   = build_audit_prompt($domain, $scrape, $answers, true); // fast=true → Haiku

try {
    $result = call_ai($provider, $prompt, $settings, true); // fast=true → Haiku 3-5s
} catch (Exception $e) {
    http_response_code(500);
    die(json_encode(['error' => 'Erreur IA : ' . $e->getMessage()]));
}

$audit_id = 0;
if ($lead_id) {
    $audit_id = save_audit($db, $lead_id, $result, $provider, !empty($scrape));
}

echo json_encode(['success' => true, 'audit' => $result, 'audit_id' => $audit_id]);
