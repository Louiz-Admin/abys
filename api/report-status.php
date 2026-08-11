<?php
// Fichier: abys-ai/api/report-status.php
// Endpoint LÉGER (pas d'appel IA) : dit si le rapport premium est prêt.
// Utilisé par la page de succès pour faire du polling sans garder une connexion
// ouverte pendant toute la génération (évite les 504).
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

$token = trim($_GET['token'] ?? '');
if (!$token) { http_response_code(400); die(json_encode(['error' => 'Token manquant'])); }

try {
    $stmt = get_db()->prepare("SELECT content FROM reports WHERE token = ? AND paid_at IS NOT NULL LIMIT 1");
    $stmt->execute([$token]);
    $row = $stmt->fetch();
    if (!$row) { echo json_encode(['ready' => false, 'found' => false]); exit; }
    echo json_encode(['ready' => !empty($row['content']), 'found' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['ready' => false, 'error' => 'db']);
}
