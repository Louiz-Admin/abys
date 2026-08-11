<?php
// Enregistrement securise des cles Stripe (CLI uniquement, jamais via le web).
// Usage sur le VPS :
//   docker compose -f /opt/abys/compose.yml exec php php /var/www/html/api/update-stripe-keys.php
// Les cles sont saisies au clavier, chiffrees avec ENCRYPTION_KEY, stockees en base.
// Rien ne transite par le chat, par git, ni par une URL.

if (PHP_SAPI !== 'cli') { http_response_code(404); die('{}'); }

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

function ask(string $label): string {
    echo $label;
    $v = trim(fgets(STDIN) ?: '');
    return $v;
}

echo "=== ABYS : enregistrement des cles Stripe (compte micro-entreprise) ===\n\n";

$sk = ask("Cle secrete (sk_live_...) : ");
if (!str_starts_with($sk, 'sk_')) { die("Refus : la cle secrete doit commencer par sk_. Abandon.\n"); }

$pk = ask("Cle publique (pk_live_...) : ");
if (!str_starts_with($pk, 'pk_')) { die("Refus : la cle publique doit commencer par pk_. Abandon.\n"); }

$wh = ask("Secret du webhook (whsec_...) : ");
if (!str_starts_with($wh, 'whsec_')) { die("Refus : le secret webhook doit commencer par whsec_. Abandon.\n"); }

// Verification reelle de la cle aupres de Stripe avant enregistrement
require_once __DIR__ . '/../lib/stripe/init.php';
try {
    \Stripe\Stripe::setApiKey($sk);
    $acct = \Stripe\Account::retrieve();
    echo "\nCompte Stripe verifie : " . ($acct->business_profile->name ?? $acct->id) . "\n";
    echo "Encaissements actifs : " . (($acct->charges_enabled ?? false) ? "OUI" : "NON (a activer dans Stripe)") . "\n";
} catch (Throwable $e) {
    die("La cle est invalide aupres de Stripe : " . $e->getMessage() . "\nAbandon, rien n'a ete enregistre.\n");
}

$db = get_db();
$up = $db->prepare("INSERT INTO settings (`key`, value) VALUES (?, ?) ON DUPLICATE KEY UPDATE value = VALUES(value)");
$up->execute(['stripe_sk', encrypt_value($sk)]);
$up->execute(['stripe_pk', $pk]);
$up->execute(['stripe_webhook', encrypt_value($wh)]);
$up->execute(['payments_enabled', '1']);

echo "\nCles enregistrees (chiffrees) et paiements REACTIVES.\n";
echo "Rappel : URL du webhook a configurer dans Stripe -> https://abys.ai/api/stripe-webhook.php\n";
echo "Evenements : checkout.session.completed, customer.subscription.deleted\n";
