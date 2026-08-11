<?php
// Fichier: abys-ai/api/leads.php
header('Content-Type: application/json');

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); die(json_encode(['error' => 'Method not allowed']));
}

$input  = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? 'create';
$db     = get_db();

if ($action === 'create') {
    $url     = substr(trim($input['url']    ?? ''), 0, 500);
    $email   = filter_var(trim($input['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $secteur = substr(trim($input['sector'] ?? $input['secteur'] ?? ''), 0, 100);
    $source  = $input['source'] ?? 'url';

    $stmt = $db->prepare("
        INSERT INTO leads (url, email, secteur, source)
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE email = IF(email = '' OR email IS NULL, VALUES(email), email)
    ");
    $stmt->execute([$url, $email ?: null, $secteur, $source]);
    $lead_id = $db->lastInsertId() ?: get_lead_id_by_url($db, $url);

    echo json_encode(['success' => true, 'lead_id' => $lead_id]);
}

function get_lead_id_by_url(PDO $db, string $url): int {
    $stmt = $db->prepare("SELECT id FROM leads WHERE url = ? ORDER BY created_at DESC LIMIT 1");
    $stmt->execute([$url]);
    return (int)($stmt->fetchColumn() ?: 0);
}
