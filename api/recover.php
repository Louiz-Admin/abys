<?php
// Recuperation d'urgence : lit la config actuellement en memoire (opcache).
// A SUPPRIMER immediatement apres usage.
$T = 'abys-rec-9k2f7q';
if (($_GET['k'] ?? '') !== $T) { http_response_code(404); die('{}'); }
header('Content-Type: application/json');
$out = ['opcache_actif' => function_exists('opcache_get_status')];
try {
    require __DIR__ . '/config.php';
    $out['db_host'] = DB_HOST;
    $out['db_name'] = DB_NAME;
    $out['db_user'] = DB_USER;
    $out['db_pass'] = DB_PASS;
    $out['enc_key'] = ENCRYPTION_KEY;
    $out['site_url'] = SITE_URL;
} catch (Throwable $e) {
    $out['err'] = $e->getMessage();
}
echo json_encode($out, JSON_PRETTY_PRINT);
