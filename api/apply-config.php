<?php
// Reconstruction d'urgence de api/config.php (Chemin 2).
// Lit api/config-values.json, ecrit config.php, reenregistre les secrets
// chiffres avec une cle neuve, puis supprime le json ET ce script.
header('Content-Type: application/json');

$T = 'abys-fix-5m2v8c';
if (($_GET['k'] ?? '') !== $T) { http_response_code(404); die('{}'); }

$values_file = __DIR__ . '/config-values.json';
if (!file_exists($values_file)) { die(json_encode(['etape' => 'attente', 'msg' => 'config-values.json absent'])); }

$v = json_decode(file_get_contents($values_file), true);
if (!$v || empty($v['db_pass'])) { die(json_encode(['erreur' => 'json invalide ou db_pass manquant'])); }

// 1. Generer une cle de chiffrement neuve (32 octets exactement)
$new_key = substr(bin2hex(random_bytes(32)), 0, 32);

// 2. Ecrire le nouveau config.php
$db_host = $v['db_host'] ?? 'localhost';
$db_name = $v['db_name'] ?? 'abysai_db';
$db_user = $v['db_user'] ?? 'abysai_user';
$conf = "<?php\n"
      . "// Fichier: abys-ai/api/config.php (reconstruit le " . date('Y-m-d H:i') . ")\n"
      . "define('DB_HOST', " . var_export($db_host, true) . ");\n"
      . "define('DB_NAME', " . var_export($db_name, true) . ");\n"
      . "define('DB_USER', " . var_export($db_user, true) . ");\n"
      . "define('DB_PASS', " . var_export($v['db_pass'], true) . ");\n"
      . "define('DB_CHARSET', 'utf8mb4');\n"
      . "define('ENCRYPTION_KEY', " . var_export($new_key, true) . ");\n"
      . "define('SITE_URL', 'https://abys.ai');\n"
      . "define('APP_ENV', 'production');\n"
      . "define('DEBUG', false);\n";

if (file_put_contents(__DIR__ . '/config.php', $conf) === false) {
    die(json_encode(['erreur' => 'ecriture config.php impossible']));
}

// 3. Tester la connexion et reenregistrer les secrets avec la cle neuve
$out = ['config_ecrit' => true];
try {
    $pdo = new PDO("mysql:host={$db_host};dbname={$db_name};charset=utf8mb4", $db_user, $v['db_pass'],
                   [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $out['db'] = 'ok';

    $enc = function (string $val) use ($new_key): string {
        $iv = random_bytes(16);
        return base64_encode($iv . openssl_encrypt($val, 'AES-256-CBC', $new_key, 0, $iv));
    };

    $stmt = $pdo->prepare("INSERT INTO settings (`key`, value) VALUES (?, ?) ON DUPLICATE KEY UPDATE value = VALUES(value)");
    $maj = [];
    foreach (['stripe_sk', 'stripe_webhook', 'smtp_pass', 'claude_key', 'imap_pass'] as $k) {
        if (!empty($v[$k])) { $stmt->execute([$k, $enc($v[$k])]); $maj[] = $k; }
    }
    // stripe_pk est public, stocke en clair s'il est fourni
    if (!empty($v['stripe_pk'])) { $stmt->execute(['stripe_pk', $v['stripe_pk']]); $maj[] = 'stripe_pk'; }
    $out['secrets_reenregistres'] = $maj;
} catch (Throwable $e) {
    $out['db'] = 'FAIL: ' . substr($e->getMessage(), 0, 150);
}

// 4. Nettoyage
@unlink($values_file);
$out['json_supprime'] = !file_exists($values_file);
$out['script_supprime'] = @unlink(__FILE__);

echo json_encode($out, JSON_PRETTY_PRINT);
