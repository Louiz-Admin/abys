<?php
// Fichier: abys-ai/api/logo.php
// Proxy + cache de logos officiels, servi depuis le domaine ABYS (même origine).
// Le serveur va chercher le logo une fois (Clearbit -> favicon Google -> unavatar),
// le met en cache disque, puis le sert. Retourne TOUJOURS une image
// (à défaut, une pastille avec l'initiale). Aucune dépendance côté navigateur.
//
//   <img src="/api/logo.php?d=axonaut.com">

$d = strtolower(preg_replace('/[^a-zA-Z0-9.\-]/', '', $_GET['d'] ?? ''));

function svg_initial(string $host): string {
    $l = strtoupper(substr($host !== '' ? $host : '?', 0, 1));
    $l = htmlspecialchars($l, ENT_QUOTES);
    return '<svg xmlns="http://www.w3.org/2000/svg" width="128" height="128" viewBox="0 0 128 128">'
         . '<defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1">'
         . '<stop offset="0" stop-color="#10B981"/><stop offset="1" stop-color="#0EA5E9"/></linearGradient></defs>'
         . '<rect width="128" height="128" rx="28" fill="url(#g)"/>'
         . '<text x="64" y="86" font-family="Arial,Helvetica,sans-serif" font-size="64" font-weight="800" '
         . 'fill="#ffffff" text-anchor="middle">' . $l . '</text></svg>';
}

function serve_svg(string $host): void {
    header('Content-Type: image/svg+xml');
    header('Cache-Control: public, max-age=86400');
    echo svg_initial($host);
    exit;
}

if ($d === '' || !str_contains($d, '.')) { serve_svg($d); }

// ── Cache disque ────────────────────────────────────────────────
$cacheDir = __DIR__ . '/../assets/logos';
if (!is_dir($cacheDir)) { @mkdir($cacheDir, 0775, true); }
$base = $cacheDir . '/' . $d;
$imgFile = $base . '.img';
$typeFile = $base . '.type';

if (is_file($imgFile) && filesize($imgFile) > 60) {
    $ct = is_file($typeFile) ? trim(file_get_contents($typeFile)) : 'image/png';
    header('Content-Type: ' . $ct);
    header('Cache-Control: public, max-age=2592000');
    readfile($imgFile);
    exit;
}

// Marqueur d'échec récent (évite de re-tenter à chaque hit pendant 6h)
$failFile = $base . '.fail';
if (is_file($failFile) && (time() - filemtime($failFile)) < 21600) {
    serve_svg($d);
}

// ── Récupération côté serveur ───────────────────────────────────
function fetch_img(string $url): ?array {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 3,
        CURLOPT_TIMEOUT        => 6,
        CURLOPT_CONNECTTIMEOUT => 4,
        CURLOPT_USERAGENT      => 'ABYS-logo/1.0 (+https://abys.ai)',
        CURLOPT_SSL_VERIFYPEER => true,
    ]);
    $body = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $ct   = (string) curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    curl_close($ch);
    if ($code >= 200 && $code < 300 && $body && strlen($body) > 100 && stripos($ct, 'image') !== false) {
        return ['body' => $body, 'type' => strtok($ct, ';') ?: 'image/png'];
    }
    return null;
}

$sources = [
    'https://logo.clearbit.com/' . $d . '?size=128',
    'https://www.google.com/s2/favicons?domain=' . $d . '&sz=128',
    'https://unavatar.io/' . $d . '?fallback=false',
];

foreach ($sources as $u) {
    $img = fetch_img($u);
    if ($img) {
        @file_put_contents($imgFile, $img['body']);
        @file_put_contents($typeFile, $img['type']);
        @unlink($failFile);
        header('Content-Type: ' . $img['type']);
        header('Cache-Control: public, max-age=2592000');
        echo $img['body'];
        exit;
    }
}

// Rien trouvé : on note l'échec et on sert l'initiale
@file_put_contents($failFile, '1');
serve_svg($d);
