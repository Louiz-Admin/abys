<?php
// Fichier: abys-ai/api/gen-test.php
// DÉSACTIVÉ. Cet endpoint d'auto-test a servi à prouver la génération premium
// (5 opportunités, tutoriels, totaux non nuls, ~60 s) puis a été neutralisé.
// Il ne fait plus aucun appel API et ne consomme plus rien.
header('Content-Type: application/json; charset=utf-8');
http_response_code(410);
echo json_encode(['status' => 'disabled', 'message' => 'Endpoint de test désactivé.']);
