<?php
// Fichier: abys-ai/api/db.php

require_once __DIR__ . '/config.php';

// Guard : AES-256 exige exactement 32 octets
if (strlen(ENCRYPTION_KEY) !== 32) {
    throw new \RuntimeException('ENCRYPTION_KEY must be exactly 32 bytes.');
}

function get_db(): PDO {
    static $pdo = null;
    if ($pdo !== null) return $pdo;

    $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=%s',
        DB_HOST, DB_NAME, DB_CHARSET
    );

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $e) {
        if (DEBUG) {
            throw $e;
        }
        http_response_code(500);
        die(json_encode(['error' => 'Erreur de connexion BDD']));
    }

    return $pdo;
}

// Utilitaire : chiffrement des clés API
function encrypt_value(string $value): string {
    $iv = random_bytes(16);
    $encrypted = openssl_encrypt($value, 'AES-256-CBC', ENCRYPTION_KEY, 0, $iv);
    return base64_encode($iv . $encrypted);
}

function decrypt_value(string $encrypted): string|false {
    $data = base64_decode($encrypted, true);
    if ($data === false || strlen($data) < 17) {
        return false;
    }
    $iv = mb_substr($data, 0, 16, '8bit');
    $ciphertext = mb_substr($data, 16, null, '8bit');
    return openssl_decrypt($ciphertext, 'AES-256-CBC', ENCRYPTION_KEY, 0, $iv);
}
