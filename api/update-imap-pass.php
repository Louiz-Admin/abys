<?php
// Enregistrement securise du mot de passe IMAP de contact@abys.ai (CLI uniquement).
// Usage sur le VPS :
//   docker compose -f /opt/abys/compose.yml exec php php /var/www/html/api/update-imap-pass.php
// Le mot de passe est saisi au clavier, la connexion IMAP est TESTEE en reel,
// puis il est chiffre (ENCRYPTION_KEY) et stocke en base. Rien ne transite par le chat ni git.

if (PHP_SAPI !== 'cli') { http_response_code(404); die('{}'); }

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/imap-lite.php';

function ask(string $label, string $default = ''): string {
    echo $label . ($default !== '' ? " [{$default}]" : '') . ' : ';
    $v = trim(fgets(STDIN) ?: '');
    return $v !== '' ? $v : $default;
}

echo "=== ABYS : activation du canal email de Milo (contact@abys.ai) ===\n\n";

$host = ask('Serveur IMAP', 'imap.ionos.fr');
$port = (int) ask('Port IMAP', '993');
$user = ask('Adresse email', 'contact@abys.ai');
$pass = ask('Mot de passe IMAP (saisie visible)');
if ($pass === '') die("Mot de passe vide. Abandon.\n");

echo "\nTest de connexion IMAP reelle...\n";
try {
    $imap = new ImapLite($host, $port, $user, $pass);
    $imap->selectInbox();
    $unseen = count($imap->searchUnseen());
    $imap->close();
    echo "Connexion OK. Messages non lus actuellement : {$unseen}\n";
} catch (Throwable $e) {
    die("Echec de connexion IMAP : " . $e->getMessage() . "\nAbandon, rien n'a ete enregistre.\n");
}

$db = get_db();
$up = $db->prepare("INSERT INTO settings (`key`, value) VALUES (?, ?) ON DUPLICATE KEY UPDATE value = VALUES(value)");
$up->execute(['imap_host', $host]);
$up->execute(['imap_port', (string)$port]);
$up->execute(['imap_user', $user]);
$up->execute(['imap_pass', encrypt_value($pass)]);

echo "\nEnregistre et chiffre. Milo repondra desormais aux emails de {$user}.\n";
echo "Plafond de securite : 40 reponses auto/jour (settings.milo_email_daily_cap pour changer).\n";
echo "Verification manuelle possible : https://abys.ai/api/email-check.php?key=<imap_cron_key>\n";
