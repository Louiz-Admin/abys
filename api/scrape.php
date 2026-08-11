<?php
// Fichier: abys-ai/api/scrape.php
header('Content-Type: application/json');

require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); die(json_encode(['error' => 'Method not allowed']));
}

$input = json_decode(file_get_contents('php://input'), true);
$url   = trim($input['url'] ?? '');

if (!$url) {
    http_response_code(400); die(json_encode(['error' => 'URL manquante']));
}

$url = preg_replace('/^https?:\/\//', '', $url);
$url = explode('/', $url)[0];
$url = strtolower(trim($url));

if (!preg_match('/^[a-zA-Z0-9][a-zA-Z0-9\-\.]{1,61}[a-zA-Z0-9]\.[a-zA-Z]{2,}$/', $url)) {
    http_response_code(400); die(json_encode(['error' => 'URL invalide']));
}

$target = 'https://' . $url;

// Rate limiting simple
$ip_key   = md5($_SERVER['REMOTE_ADDR'] ?? 'unknown');
$rate_file = sys_get_temp_dir() . '/abys_rate_' . $ip_key;
$rate_data = file_exists($rate_file) ? json_decode(file_get_contents($rate_file), true) : ['count' => 0, 'reset' => time() + 3600];
if (time() > $rate_data['reset']) { $rate_data = ['count' => 0, 'reset' => time() + 3600]; }
if ($rate_data['count'] >= 10) {
    http_response_code(429); die(json_encode(['error' => 'Trop de requêtes, réessayez dans 1h']));
}
$rate_data['count']++;
file_put_contents($rate_file, json_encode($rate_data));

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL            => $target,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_MAXREDIRS      => 3,
    CURLOPT_TIMEOUT        => 10,
    CURLOPT_CONNECTTIMEOUT => 5,
    CURLOPT_USERAGENT      => 'Mozilla/5.0 (compatible; ABYSBot/1.0; +https://abys.ai)',
    CURLOPT_HTTPHEADER     => ['Accept-Language: fr-FR,fr;q=0.9'],
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_ENCODING       => 'gzip,deflate',
]);
$html   = curl_exec($ch);
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error  = curl_error($ch);
curl_close($ch);

if ($error || !$html || $status >= 400) {
    echo json_encode(['success' => false, 'domain' => $url, 'reason' => $error ?: "HTTP $status"]);
    exit;
}

$data = extract_site_data($html, $url);
$data['success'] = true;
$data['domain']  = $url;
echo json_encode($data);

function extract_site_data(string $html, string $domain): array {
    preg_match('/<title[^>]*>(.*?)<\/title>/si', $html, $m);
    $title = strip_tags($m[1] ?? '');

    preg_match('/<meta[^>]*name=["\']description["\'][^>]*content=["\']([^"\']+)/si', $html, $m2);
    $desc = $m2[1] ?? '';

    preg_match('/<h1[^>]*>(.*?)<\/h1>/si', $html, $m3);
    $h1 = strip_tags($m3[1] ?? '');

    $clean = preg_replace('/<(script|style|nav|footer|header)[^>]*>.*?<\/\1>/si', '', $html);
    $text  = strip_tags($clean);
    $text  = preg_replace('/\s+/', ' ', $text);
    $text  = substr(trim($text), 0, 3000);

    $sector_hints = detect_sector($domain . ' ' . $title . ' ' . $h1 . ' ' . substr($text, 0, 500));

    return [
        'title'        => mb_substr($title, 0, 200),
        'description'  => mb_substr($desc,  0, 500),
        'h1'           => mb_substr($h1,    0, 200),
        'text_excerpt' => $text,
        'sector_hint'  => $sector_hints,
    ];
}

function detect_sector(string $content): string {
    $content = mb_strtolower($content);
    $sectors = [
        'restauration' => ['restaurant','brasserie','pizz','food','chef','menu','cuisine'],
        'artisan'      => ['artisan','plomb','electr','maçon','peintr','menuisi','btp','charpent'],
        'commerce'     => ['boutique','shop','magasin','vente','soldes','livraison'],
        'sante'        => ['médec','docteur','cabinet','clinique','kiné','dentiste','soin'],
        'immobilier'   => ['immobilier','agence','appartement','maison','location'],
        'juridique'    => ['avocat','notaire','juriste','cabinet juridique','droit'],
        'transport'    => ['transport','livraison','logistique','chauffeur','déménage'],
        'beaute'       => ['coiffure','esthétique','beauté','salon','massage'],
        'services'     => ['conseil','consulting','formation','agence','prestation'],
    ];
    foreach ($sectors as $name => $keywords) {
        foreach ($keywords as $kw) {
            if (str_contains($content, $kw)) return $name;
        }
    }
    return 'services';
}
