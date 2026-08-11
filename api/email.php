<?php
// Fichier: abys-ai/api/email.php
// Envoi d'emails via Brevo SMTP (STARTTLS, port 587) avec fallback mail()

// ── Client SMTP minimal (sans dépendance externe) ────────────────────────────

/**
 * Envoie un email via SMTP avec STARTTLS.
 * Utilise les identifiants stockés dans la table settings (smtp_host, smtp_port, smtp_user, smtp_pass).
 * Fallback sur mail() si SMTP non configuré ou échec.
 */
function send_email(string $to, string $subject, string $html, string $from = ''): bool {
    // Récupère config SMTP
    try {
        $db       = get_db();
        $settings = [];
        foreach ($db->query("SELECT `key`, value FROM settings WHERE `key` LIKE 'smtp%' OR `key` = 'contact_email'")->fetchAll() as $r) {
            $settings[$r['key']] = $r['value'];
        }
    } catch (Exception $e) { $settings = []; }

    $smtp_host = $settings['smtp_host'] ?? '';
    $smtp_port = (int)($settings['smtp_port'] ?? 587);
    $smtp_user = $settings['smtp_user'] ?? '';
    $smtp_pass = !empty($settings['smtp_pass']) ? (decrypt_value($settings['smtp_pass']) ?: '') : '';
    $smtp_from = $settings['smtp_from'] ?? 'ABYS AI <noreply@abys.ai>';

    if (!$from) $from = $smtp_from;

    $boundary = uniqid('abys_', true);
    $plain    = strip_tags(preg_replace('/<br\s*\/?>/i', "\n", $html));
    $body_mime = "--{$boundary}\r\n"
               . "Content-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n"
               . chunk_split(base64_encode($plain)) . "\r\n"
               . "--{$boundary}\r\n"
               . "Content-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n"
               . chunk_split(base64_encode(email_wrap($html))) . "\r\n"
               . "--{$boundary}--";

    // Essai via SMTP Brevo si configuré
    if ($smtp_host && $smtp_user && $smtp_pass) {
        $sent = smtp_send($smtp_host, $smtp_port, $smtp_user, $smtp_pass, $from, $to, $subject, $boundary, $body_mime);
        if ($sent) return true;
        // sinon on tombe dans le fallback
        error_log("[ABYS email] SMTP failed for {$to}, falling back to mail()");
    }

    // Fallback : mail() natif
    $headers = implode("\r\n", [
        "MIME-Version: 1.0",
        "Content-Type: multipart/alternative; boundary=\"{$boundary}\"",
        "From: {$from}",
        "Reply-To: contact@abys.ai",
        "X-Mailer: ABYS-AI/1.0",
    ]);
    return @mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $body_mime, $headers);
}

/**
 * Envoie via SMTP STARTTLS (port 587).
 * Retourne true si succès, false sinon.
 */
function smtp_send(
    string $host, int $port,
    string $user, string $pass,
    string $from_header, string $to,
    string $subject, string $boundary, string $body_mime
): bool {
    // Parse adresse expéditeur
    preg_match('/<([^>]+)>/', $from_header, $m);
    $from_addr = $m[1] ?? $from_header;

    try {
        // 1. Connexion TCP
        $socket = @stream_socket_client(
            "tcp://{$host}:{$port}", $errno, $errstr, 15,
            STREAM_CLIENT_CONNECT
        );
        if (!$socket) throw new Exception("Connect failed: {$errstr}");
        stream_set_timeout($socket, 15);

        // Helper : lire réponse + vérifier code attendu
        $read = function(string $expect = '') use ($socket): string {
            $buf = '';
            while (!feof($socket)) {
                $line = fgets($socket, 512);
                $buf .= $line;
                // Multi-line SMTP: dernier ligne n'a pas de tiret après le code
                if (strlen($line) >= 4 && $line[3] === ' ') break;
            }
            if ($expect && substr($buf, 0, strlen($expect)) !== $expect) {
                throw new Exception("SMTP unexpected: expected {$expect}, got: " . trim($buf));
            }
            return $buf;
        };
        $send = function(string $cmd) use ($socket) { fwrite($socket, $cmd . "\r\n"); };

        // 2. Greeting
        $read('220');

        // 3. EHLO
        $send('EHLO abys.ai');
        $ehlo = $read('250');

        // 4. STARTTLS si supporté
        if (str_contains($ehlo, 'STARTTLS')) {
            $send('STARTTLS');
            $read('220');
            if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                throw new Exception("TLS handshake failed");
            }
            // EHLO de nouveau après TLS
            $send('EHLO abys.ai');
            $read('250');
        }

        // 5. AUTH LOGIN
        $send('AUTH LOGIN');
        $read('334');
        $send(base64_encode($user));
        $read('334');
        $send(base64_encode($pass));
        $read('235');

        // 6. Enveloppe
        $send("MAIL FROM:<{$from_addr}>");
        $read('250');

        // Plusieurs destinataires séparés par virgule
        $recipients = array_map('trim', explode(',', $to));
        foreach ($recipients as $rcpt) {
            preg_match('/<([^>]+)>/', $rcpt, $rm);
            $rcpt_addr = $rm[1] ?? $rcpt;
            $send("RCPT TO:<{$rcpt_addr}>");
            $read('250');
        }

        // 7. Corps du message
        $send('DATA');
        $read('354');

        $date    = date('r');
        $subj_b64 = '=?UTF-8?B?' . base64_encode($subject) . '?=';
        $headers = "Date: {$date}\r\n"
                 . "From: {$from_header}\r\n"
                 . "To: {$to}\r\n"
                 . "Reply-To: contact@abys.ai\r\n"
                 . "Subject: {$subj_b64}\r\n"
                 . "MIME-Version: 1.0\r\n"
                 . "Content-Type: multipart/alternative; boundary=\"{$boundary}\"\r\n"
                 . "X-Mailer: ABYS-AI/2.0\r\n";

        fwrite($socket, $headers . "\r\n" . $body_mime . "\r\n.\r\n");
        $read('250');

        // 8. Fin
        $send('QUIT');
        fclose($socket);
        return true;

    } catch (Exception $e) {
        error_log("[ABYS SMTP] " . $e->getMessage());
        if (isset($socket) && is_resource($socket)) fclose($socket);
        return false;
    }
}

// ── Utilitaires ───────────────────────────────────────────────────────────────

/**
 * Notifie l'admin d'un événement
 */
function notify_admin(string $subject, string $html): void {
    try {
        $db    = get_db();
        $email = $db->query("SELECT value FROM settings WHERE `key`='contact_email'")->fetchColumn();
        if ($email) send_email($email, "[ABYS] {$subject}", $html);
    } catch (Exception $e) { /* silently fail */ }
}

/**
 * Wraps HTML dans un template email premium
 */
function email_wrap(string $content): string {
    return <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<style>
  body{margin:0;padding:0;background:#F3F4F6;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif}
  .wrap{max-width:560px;margin:32px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.06)}
  .header{background:linear-gradient(135deg,#0A1F1A,#064E3B);padding:28px 32px;text-align:center}
  .logo{font-size:20px;font-weight:200;letter-spacing:.18em;text-transform:uppercase;color:#fff}
  .logo sup{font-size:9px;opacity:.5;font-weight:400}
  .body{padding:32px}
  .body h2{font-size:22px;font-weight:600;color:#111827;margin:0 0 12px}
  .body p{font-size:15px;color:#4B5563;line-height:1.65;margin:0 0 16px}
  .btn{display:inline-block;padding:12px 28px;background:#10B981;color:#fff;border-radius:8px;text-decoration:none;font-weight:600;font-size:15px;margin:8px 0 16px}
  .info-box{background:#F9FAFB;border:1px solid #E5E7EB;border-radius:8px;padding:16px;margin:16px 0;font-size:14px;color:#374151;line-height:1.6}
  .footer{background:#F9FAFB;border-top:1px solid #E5E7EB;padding:20px 32px;text-align:center;font-size:12px;color:#9CA3AF}
  .footer a{color:#6B7280}
</style></head>
<body>
<div class="wrap">
  <div class="header">
    <div class="logo">ABYS<sup>AI</sup></div>
  </div>
  <div class="body">{$content}</div>
  <div class="footer">
    ABYS AI · <a href="https://abys.ai">abys.ai</a><br>
    Questions : <a href="mailto:contact@abys.ai">contact@abys.ai</a>
  </div>
</div>
</body></html>
HTML;
}

// ── Templates d'emails ────────────────────────────────────────────────────────

/**
 * Email de confirmation rapport premium (249€)
 */
function email_report_paid(string $to, string $url, string $token): void {
    $report_url = 'https://abys.ai/rapport.php?token=' . urlencode($token);
    $body = <<<HTML
<h2>Votre rapport IA est prêt ✅</h2>
<p>Merci pour votre confiance. Votre plan d'action IA personnalisé pour <strong>{$url}</strong> est en cours de génération.</p>
<a class="btn" href="{$report_url}">Accéder à mon rapport →</a>
<div class="info-box">
  <strong>Ce que contient votre rapport :</strong><br>
  • 7+ opportunités IA avec tutoriels pas-à-pas<br>
  • Plan d'action sur 12 mois priorisé<br>
  • Simulation ROI interactive<br>
  • Analyse concurrentielle de votre secteur
</div>
<p style="font-size:13px;color:#6B7280">Conservez ce lien, il vous donne accès à vie à votre rapport.<br>En cas de problème : <a href="mailto:contact@abys.ai">contact@abys.ai</a></p>
HTML;
    send_email($to, 'Votre rapport IA ABYS est prêt', $body);
}

/**
 * Email de bienvenue abonnement assistant (29€/mois)
 */
function email_subscription_welcome(string $to, string $plan = 'assistant'): void {
    $plan_label = $plan === 'seo' ? 'SEO & Visibilité IA' : 'Assistant IA';
    $price      = $plan === 'seo' ? '49€' : '29€';
    $body = <<<HTML
<h2>Bienvenue dans votre abonnement {$plan_label} 🎉</h2>
<p>Votre abonnement <strong>{$plan_label} · {$price}/mois</strong> est actif. Votre espace personnel est prêt.</p>
<div class="info-box">
  <strong>Prochaines étapes :</strong><br>
  • Connectez-vous à votre espace : <a href="https://abys.ai/compte/">abys.ai/compte</a><br>
  • Posez votre première question à votre assistant IA<br>
  • Résiliable à tout moment · sans condition
</div>
<a class="btn" href="https://abys.ai/compte/">Accéder à mon espace →</a>
<p style="font-size:13px;color:#6B7280">Questions immédiates : <a href="mailto:contact@abys.ai">contact@abys.ai</a></p>
HTML;
    send_email($to, "Votre abonnement ABYS {$plan_label} est actif", $body);
}

/**
 * Email de confirmation Pack 499€
 */
function email_pack_confirm(string $to, string $name = ''): void {
    $greeting = $name ? "Bonjour {$name}," : "Bonjour,";
    $body = <<<HTML
<h2>{$greeting}</h2>
<h2>Votre Forfait Intégral est activé ✅</h2>
<p>Merci pour votre confiance. <strong>Milo, votre copilote IA</strong>, vous attend dès maintenant dans votre espace pour démarrer le déploiement de vos outils, un par un.</p>
<div class="info-box">
  <strong>Ce qui vous attend :</strong><br>
  • Rapport Premium complet (7+ opportunités)<br>
  • Toutes vos missions de lancement · chaque outil installé et actif<br>
  • Guidage pas à pas par Milo, jusqu'au premier résultat<br>
  • 6 mois d'assistance complète, 24h/24
</div>
<a class="btn" href="https://abys.ai/compte/">Démarrer avec Milo →</a>
<p style="font-size:13px;color:#6B7280">Questions : <a href="mailto:contact@abys.ai">contact@abys.ai</a></p>
HTML;
    send_email($to, 'Votre Pack IA Accompagné ABYS · Confirmation', $body);
}
