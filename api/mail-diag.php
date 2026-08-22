<?php
// Fichier: abys-ai/api/mail-diag.php
// DIAGNOSTIC DES EN-TÊTES D'ENVOI.
// Lit dans la boîte contact@abys.ai la dernière copie d'un email parti d'ABYS
// et renvoie ses en-têtes bruts. C'est le seul moyen de savoir ce que le relais
// ajoute réellement (List-Unsubscribe, Precedence, etc.) au lieu de le supposer.
//
// URL : https://abys.ai/api/mail-diag.php?key=<imap_cron_key>[&n=1]

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/ai-helpers.php';
require_once __DIR__ . '/imap-lite.php';

header('Content-Type: application/json; charset=utf-8');

$db       = get_db();
$settings = get_settings($db);

$cle = $settings['imap_cron_key'] ?? 'abys_cron_2026_x7k9p';
if (($_GET['key'] ?? '') !== $cle) { http_response_code(403); exit('Forbidden'); }

$out = [
    'smtp_from'    => $settings['smtp_from']    ?? '(non defini)',
    'smtp_host'    => $settings['smtp_host']    ?? '(non defini)',
    'smtp_user'    => $settings['smtp_user']    ?? '(non defini)',
    'contact_email'=> $settings['contact_email'] ?? '(non defini)',
    'ts'           => date('c'),
];

// Envoi d'un message temoin vers la boite lue juste apres : c'est le seul moyen
// de constater les en-tetes reellement poses par le relais.
// Essai direct du canal personnel, avec l'erreur SMTP exacte s'il echoue
if (isset($_GET['perso'])) {
    require_once __DIR__ . '/email.php';
    $cfg = [];
    foreach ($db->query("SELECT `key`, value FROM settings WHERE `key` IN
        ('imap_user','imap_pass','perso_smtp_host','perso_smtp_port','perso_smtp_user','perso_smtp_pass')")->fetchAll() as $r) {
        $cfg[$r['key']] = $r['value'];
    }
    $host = $cfg['perso_smtp_host'] ?? 'smtp.ionos.fr';
    $port = (int) ($cfg['perso_smtp_port'] ?? 587);
    $user = $cfg['perso_smtp_user'] ?? ($cfg['imap_user'] ?? '');
    $brut = $cfg['perso_smtp_pass'] ?? ($cfg['imap_pass'] ?? '');
    $pass = $brut ? (decrypt_value($brut) ?: '') : '';

    $out['perso'] = ['host' => $host, 'port' => $port, 'user' => $user,
                     'mot_de_passe' => $pass ? 'present (' . strlen($pass) . ' car.)' : 'ABSENT'];

    if ($pass) {
        $b = uniqid('t_', true);
        $mime = "--{$b}\r\nContent-Type: text/plain; charset=UTF-8\r\n\r\nTemoin canal personnel.\r\n--{$b}--";
        $GLOBALS['abys_smtp_erreur'] = null;
        $ok = smtp_send($host, $port, $user, $pass, MILO_FROM,
                        $cfg['imap_user'] ?? 'contact@abys.ai',
                        'Temoin en-tetes PERSO ' . date('H:i:s'), $b, $mime);
        $out['perso']['envoi'] = $ok;
        $out['perso']['erreur'] = $GLOBALS['abys_smtp_erreur'] ?? null;
    }
    exit(json_encode($out, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}

if (isset($_GET['envoi'])) {
    require_once __DIR__ . '/email.php';
    $dest = $settings['imap_user'] ?? 'contact@abys.ai';
    $perso = !isset($_GET['relais']);
    $ok = $perso
        ? send_email_perso($dest, 'Temoin en-tetes ' . date('H:i:s'),
            '<p>Message temoin envoye par le diagnostic. Aucune action requise.</p>')
        : send_email($dest, 'Temoin en-tetes ' . date('H:i:s'),
            '<p>Message temoin envoye par le diagnostic. Aucune action requise.</p>', MILO_FROM);
    $out['canal'] = $perso ? 'personnel' : 'relais';
    $out['temoin_envoye'] = $ok;
    $out['temoin_vers']   = $dest;
    exit(json_encode($out, JSON_UNESCAPED_UNICODE));
}

$imap_host = $settings['imap_host'] ?? 'imap.ionos.fr';
$imap_port = (int) ($settings['imap_port'] ?? 993);
$imap_user = $settings['imap_user'] ?? 'contact@abys.ai';
$imap_pass = !empty($settings['imap_pass']) ? (decrypt_value($settings['imap_pass']) ?: '') : '';

if (!$imap_pass) { exit(json_encode(array_merge($out, ['erreur' => 'mot de passe IMAP absent']), JSON_UNESCAPED_UNICODE)); }

try {
    $imap = new ImapLite($imap_host, $imap_port, $imap_user, $imap_pass);
    $imap->selectInbox();

    // Les copies admin des envois de Milo viennent de l'adresse d'expedition
    $ids = [];
    $critere = isset($_GET['temoin']) ? 'SEARCH SUBJECT "Temoin en-tetes"' : 'SEARCH FROM "abys.ai"';
    foreach ($imap->cmd($critere) as $l) {
        if (preg_match('/^\*\s+SEARCH\s*(.*)$/i', trim($l), $m)) {
            $ids = array_values(array_filter(array_map('intval', preg_split('/\s+/', trim($m[1])))));
        }
    }
    $out['messages_trouves'] = count($ids);
    if (!$ids) { $imap->close(); exit(json_encode($out, JSON_UNESCAPED_UNICODE)); }

    $combien = max(1, min(3, (int) ($_GET['n'] ?? 1)));
    $derniers = array_slice($ids, -$combien);

    $out['entetes'] = [];
    foreach ($derniers as $num) {
        $raw = $imap->fetchRaw($num, 24576);
        $tete = explode("\r\n\r\n", $raw, 2)[0];
        $tete = explode("\n\n", $tete, 2)[0];

        $lignes = preg_split('/\r?\n(?![ \t])/', $tete);
        $garde  = [];
        foreach ($lignes as $ligne) {
            if (preg_match('/^(From|To|Reply-To|Subject|Date|Return-Path|Sender|List-[A-Za-z-]+|Precedence|X-[A-Za-z0-9-]+|Message-ID|Feedback-ID|Auto-Submitted|DKIM-Signature|Authentication-Results)\s*:/i', $ligne)) {
                $garde[] = mb_substr(preg_replace('/\s+/', ' ', trim($ligne)), 0, 240);
            }
        }
        $out['entetes'][] = $garde;
    }

    // Verdict lisible
    $tout = strtolower(json_encode($out['entetes']));
    $out['verdict'] = [
        'list_unsubscribe' => strpos($tout, 'list-unsubscribe') !== false,
        'precedence_bulk'  => strpos($tout, 'precedence') !== false,
        'feedback_id'      => strpos($tout, 'feedback-id') !== false,
    ];

    $imap->close();
} catch (Throwable $e) {
    $out['erreur'] = $e->getMessage();
}

echo json_encode($out, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
