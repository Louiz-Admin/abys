<?php
// Fichier: abys-ai/api/track.php
// MESURE DU PARCOURS. Sans ça, on discute d'impressions.
//
// Écriture  : POST {etape, cle, meta?, lead_id?}   · aucune donnée personnelle
// Lecture   : GET  ?stats=1&key=<imap_cron_key>[&jours=30]
//
// La clé de session est générée par le navigateur et vit le temps de l'onglet.
// Aucun cookie, aucune adresse IP, aucun identifiant publicitaire.

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/ai-helpers.php';

header('Content-Type: application/json; charset=utf-8');

$db = get_db();

// ── Étapes autorisées : liste fermée, rien d'autre n'entre en base ──────────
const ETAPES = [
    'accueil_url',        // une URL est saisie sur la page d'accueil
    'audit_lance',        // l'analyse du site démarre
    'tunnel_ouvert',      // le questionnaire s'affiche
    'tunnel_q1', 'tunnel_q2', 'tunnel_q3', 'tunnel_q4', 'tunnel_q5',
    'tunnel_q6', 'tunnel_q7', 'tunnel_q8', 'tunnel_q9',
    'tunnel_contact',     // l'écran final du tunnel est atteint
    'tunnel_email',       // l'email est donné dans le tunnel
    'resultats_vus',      // la page de résultats s'affiche
    'porte_vue',          // la porte email entre dans l'écran
    'porte_ouverte',      // l'email est donné sur la page de résultats
    'offres_vues',        // les offres entrent dans l'écran
    'offre_cliquee',      // un bouton d'offre est cliqué
];

try {
    $db->exec("CREATE TABLE IF NOT EXISTS funnel_events (
        id INT AUTO_INCREMENT PRIMARY KEY,
        cle VARCHAR(32) NOT NULL,
        etape VARCHAR(40) NOT NULL,
        lead_id INT DEFAULT NULL,
        meta VARCHAR(190) DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY uniq_passage (cle, etape),
        INDEX idx_etape (etape),
        INDEX idx_created (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
} catch (Throwable $e) { error_log('[ABYS track] ' . $e->getMessage()); }

// Autotest : ecrit un evenement puis le relit. Prouve la table et l'endpoint.
if (isset($_GET['autotest'])) {
    $settings = get_settings($db);
    if (($_GET['key'] ?? '') !== ($settings['imap_cron_key'] ?? 'abys_cron_2026_x7k9p')) { http_response_code(403); exit('Forbidden'); }
    $cle = 'autotest' . substr(bin2hex(random_bytes(6)), 0, 10);
    $r = ['cle' => $cle];
    try {
        $db->prepare("INSERT IGNORE INTO funnel_events (cle, etape, meta) VALUES (?, 'tunnel_ouvert', 'autotest')")->execute([$cle]);
        $r['ecrit'] = (int) $db->query("SELECT COUNT(*) FROM funnel_events WHERE cle = " . $db->quote($cle))->fetchColumn();
        $db->exec("DELETE FROM funnel_events WHERE meta = 'autotest'");
        $r['nettoye'] = true;
        $r['total_table'] = (int) $db->query("SELECT COUNT(*) FROM funnel_events")->fetchColumn();
    } catch (Throwable $e) { $r['erreur'] = $e->getMessage(); }
    exit(json_encode($r, JSON_UNESCAPED_UNICODE));
}

// ══════════════════════════════════════════════════════════════════
// LECTURE : l'entonnoir, en clair
// ══════════════════════════════════════════════════════════════════
if (isset($_GET['stats'])) {
    $settings = get_settings($db);
    $cle = $settings['imap_cron_key'] ?? 'abys_cron_2026_x7k9p';
    if (($_GET['key'] ?? '') !== $cle) { http_response_code(403); exit('Forbidden'); }

    $jours = max(1, min(365, (int) ($_GET['jours'] ?? 30)));
    $st = $db->prepare("
        SELECT etape, COUNT(DISTINCT cle) AS sessions, COUNT(*) AS evenements
        FROM funnel_events
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
        GROUP BY etape
    ");
    $st->execute([$jours]);
    $brut = [];
    foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) $brut[$r['etape']] = (int) $r['sessions'];

    $entonnoir = [];
    foreach (ETAPES as $e) $entonnoir[$e] = $brut[$e] ?? 0;

    // Taux de survie d'une étape à la suivante, uniquement sur les étapes vues
    $chaine = [];
    $prec = null;
    foreach ($entonnoir as $e => $n) {
        if ($n === 0 && $prec === null) continue;
        $chaine[] = [
            'etape'    => $e,
            'sessions' => $n,
            'survie'   => ($prec !== null && $prec > 0) ? round($n / $prec * 100) . ' %' : '-',
        ];
        $prec = $n;
    }

    exit(json_encode([
        'periode_jours' => $jours,
        'entonnoir'     => $entonnoir,
        'chaine'        => $chaine,
        'ts'            => date('c'),
    ], JSON_UNESCAPED_UNICODE));
}

// ══════════════════════════════════════════════════════════════════
// ÉCRITURE
// ══════════════════════════════════════════════════════════════════
$in    = json_decode(file_get_contents('php://input'), true) ?: [];
$etape = (string) ($in['etape'] ?? '');
$cle   = substr(preg_replace('/[^a-zA-Z0-9]/', '', (string) ($in['cle'] ?? '')), 0, 32);

if (!in_array($etape, ETAPES, true) || $cle === '') {
    http_response_code(400);
    exit(json_encode(['error' => 'etape ou cle invalide']));
}

try {

    // Une étape n'est comptée qu'une fois par session : on mesure des parcours,
    // pas des rafraîchissements de page.
    $db->prepare("INSERT IGNORE INTO funnel_events (cle, etape, lead_id, meta) VALUES (?,?,?,?)")
       ->execute([
           $cle,
           $etape,
           ((int) ($in['lead_id'] ?? 0)) ?: null,
           isset($in['meta']) ? substr(strip_tags((string) $in['meta']), 0, 190) : null,
       ]);
} catch (Throwable $e) {
    error_log('[ABYS track] ' . $e->getMessage());
}

echo json_encode(['ok' => true]);
