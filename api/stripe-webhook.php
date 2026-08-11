<?php
// Fichier: abys-ai/api/stripe-webhook.php
// Configurer dans Dashboard Stripe → Développeurs → Webhooks
// URL : https://abys.ai/api/stripe-webhook.php
// Événements : checkout.session.completed, customer.subscription.deleted

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/email.php';

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} elseif (file_exists(__DIR__ . '/../lib/stripe/init.php')) {
    require_once __DIR__ . '/../lib/stripe/init.php';
}

$payload   = file_get_contents('php://input');
$sig       = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
$db        = get_db();
$wh_secret = decrypt_value($db->query("SELECT value FROM settings WHERE `key`='stripe_webhook'")->fetchColumn() ?: '');

try {
    $event = \Stripe\Webhook::constructEvent($payload, $sig, $wh_secret);
} catch (\Exception $e) {
    http_response_code(400); die('Webhook invalide');
}

if ($event->type === 'checkout.session.completed') {
    $session = $event->data->object;
    $meta    = $session->metadata;
    $mode    = $session->mode;

    // Carte enregistrée pour le paiement 1 clic des missions (tout paiement avec lead_id)
    if ($mode === 'payment' && !empty($meta->lead_id)) {
        save_card_for_lead($db, (int)$meta->lead_id, $session);
    }

    if ($mode === 'payment' && isset($meta->report_id)) {
        $db->prepare("UPDATE reports SET stripe_payment_id=?, paid_at=NOW(), expires_at=DATE_ADD(NOW(), INTERVAL 90 DAY) WHERE id=?")
           ->execute([$session->payment_intent, $meta->report_id]);

        $db->prepare("INSERT INTO payments (lead_id, stripe_payment_intent, amount, type, reference_id, status) VALUES (?,?,?,?,?,'succeeded')")
           ->execute([$meta->lead_id, $session->payment_intent, $session->amount_total / 100, 'report', $meta->report_id]);

        // Déclenche la génération du rapport côté serveur, sans attendre le navigateur.
        // L'email "rapport prêt" est envoyé par generate-report.php À LA FIN,
        // une fois le contenu réellement généré (plus d'email prématuré/contradictoire).
        $customer_email = $session->customer_details->email ?? $session->customer_email ?? '';
        $report_row = $db->prepare("SELECT r.token, l.url FROM reports r JOIN leads l ON r.lead_id=l.id WHERE r.id=?");
        $report_row->execute([$meta->report_id]);
        $rdata = $report_row->fetch();
        if ($rdata) {
            trigger_report_generation($rdata['token']);
        }
        // Notif admin
        notify_admin("Nouveau paiement rapport " . ($session->amount_total / 100) . "€ · {$customer_email}", "
            <p>Nouveau paiement rapport premium reçu.</p>
            <div class='info-box'>
              Email : <strong>{$customer_email}</strong><br>
              Site : {$rdata['url']}<br>
              Montant : " . ($session->amount_total / 100) . "€
            </div>
        ");
    }

    // Missions de lancement (79€ / 199€) · accompagnement Milo
    if ($mode === 'payment' && isset($meta->plan) && in_array($meta->plan, ['mission', 'lancement'], true)) {
        $customer_email = $session->customer_details->email ?? ($session->customer_email ?? '');
        $tool = $meta->mission_tool ?? '';
        $db->prepare("INSERT INTO payments (lead_id, stripe_payment_intent, amount, type, status) VALUES (?,?,?,?,'succeeded')")
           ->execute([intval($meta->lead_id ?? 0), $session->payment_intent, $session->amount_total / 100, 'mission']);
        notify_admin("Nouvelle mission " . ($session->amount_total / 100) . "€ · {$customer_email}", "
            <p>Nouvelle mission de lancement payée.</p>
            <div class='info-box'>
              Email : <strong>{$customer_email}</strong><br>
              Formule : {$meta->plan}" . ($tool ? "<br>Outil : {$tool}" : "") . "<br>
              Montant : " . ($session->amount_total / 100) . "€
            </div>
        ");
        if ($customer_email) {
            send_email($customer_email, 'Votre mission de lancement ABYS est activée', "
                <div style='text-align:center;margin:0 0 16px'><img src='https://abys.ai/assets/img/milo-avatar.jpg' alt='Milo' width='76' height='76' style='width:76px;height:76px;border-radius:50%;border:2px solid #10B981;object-fit:cover'></div>
                <h2>Votre mission est activée</h2>
                <p>Merci pour votre confiance. Milo, votre copilote IA, vous attend dans votre espace pour démarrer la mise en action" . ($tool ? " de <strong>" . htmlspecialchars($tool) . "</strong>" : " de vos outils") . ".</p>
                <a class='btn' href='https://abys.ai/compte/'>Démarrer avec Milo</a>
                <p style='font-size:13px;color:#6B7280'>Objectif : outil installé, paramétré, premier résultat. Satisfait ou remboursé.</p>
            ");
        }
    }

    if ($mode === 'subscription' && isset($meta->lead_id)) {
        $plan = $meta->plan ?? 'assistant';
        $db->prepare("
            INSERT INTO subscriptions (lead_id, stripe_subscription_id, stripe_customer_id, plan, price, status)
            VALUES (?, ?, ?, ?, ?, 'active')
            ON DUPLICATE KEY UPDATE status='active', stripe_subscription_id=VALUES(stripe_subscription_id)
        ")->execute([$meta->lead_id, $session->subscription, $session->customer, $plan, $session->amount_total / 100]);

        $db->prepare("INSERT INTO payments (lead_id, amount, type, status) VALUES (?,?,'subscription','succeeded')")
           ->execute([$meta->lead_id, $session->amount_total / 100]);

        // Email client · bienvenue abonnement
        $customer_email = $session->customer_details->email ?? $session->customer_email ?? '';
        if ($customer_email) {
            email_subscription_welcome($customer_email, $plan);
        }
        // Notif admin
        $plan_label = $plan === 'seo' ? 'SEO 49€/mois' : 'Assistant 29€/mois';
        notify_admin("Nouvel abonnement {$plan_label} · {$customer_email}", "
            <p>Nouvel abonnement activé.</p>
            <div class='info-box'>
              Email : <strong>{$customer_email}</strong><br>
              Plan : {$plan_label}<br>
              lead_id : {$meta->lead_id}
            </div>
            <p><strong>Action requise :</strong> contacter ce client sous 24h.</p>
        ");
    }
}

if ($event->type === 'customer.subscription.deleted') {
    $sub = $event->data->object;
    $db->prepare("UPDATE subscriptions SET status='cancelled', cancelled_at=NOW() WHERE stripe_subscription_id=?")
       ->execute([$sub->id]);
}

http_response_code(200);
echo json_encode(['received' => true]);

/**
 * Enregistre le client Stripe + la carte du lead pour le paiement 1 clic.
 * La carte reste chez Stripe ; on ne stocke que des identifiants + les 4 derniers chiffres.
 */
function save_card_for_lead(PDO $db, int $lead_id, $session): void {
    if (!$lead_id || empty($session->customer) || empty($session->payment_intent)) return;
    try {
        // Migration auto (MariaDB : IF NOT EXISTS supporté)
        $db->exec("ALTER TABLE leads
            ADD COLUMN IF NOT EXISTS stripe_customer_id VARCHAR(64) NULL,
            ADD COLUMN IF NOT EXISTS stripe_pm_id VARCHAR(64) NULL,
            ADD COLUMN IF NOT EXISTS card_last4 VARCHAR(4) NULL,
            ADD COLUMN IF NOT EXISTS card_brand VARCHAR(20) NULL");

        $pi = \Stripe\PaymentIntent::retrieve($session->payment_intent);
        $pm_id = is_object($pi->payment_method ?? null) ? $pi->payment_method->id : ($pi->payment_method ?? '');
        if (!$pm_id) return;
        $pm = \Stripe\PaymentMethod::retrieve($pm_id);
        $last4 = $pm->card->last4 ?? '';
        $brand = $pm->card->brand ?? '';

        $db->prepare("UPDATE leads SET stripe_customer_id=?, stripe_pm_id=?, card_last4=?, card_brand=? WHERE id=?")
           ->execute([$session->customer, $pm_id, $last4, $brand, $lead_id]);
        error_log("[ABYS webhook] Carte enregistrée lead {$lead_id} · {$brand} •••• {$last4}");
    } catch (\Exception $e) {
        error_log('[ABYS webhook] save_card_for_lead: ' . $e->getMessage());
    }
}

/**
 * Déclenche la génération du rapport côté serveur en « fire-and-forget ».
 * On ouvre une requête HTTP vers generate-report.php avec un timeout très court :
 * PHP-FPM démarre le traitement, et generate-report.php (ignore_user_abort)
 * poursuit jusqu'au bout même après la déconnexion. Le webhook n'est pas bloqué.
 */
function trigger_report_generation(string $token): void {
    $ch = curl_init('https://abys.ai/api/generate-report.php');
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode(['token' => $token]),
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        CURLOPT_TIMEOUT_MS     => 900,   // fire-and-forget : on ne bloque pas la réponse au webhook
        CURLOPT_NOSIGNAL       => 1,
        CURLOPT_RETURNTRANSFER => true,
    ]);
    curl_exec($ch);
    curl_close($ch);
}
