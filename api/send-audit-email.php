<?php
// Fichier: abys-ai/api/send-audit-email.php
// Envoie un récapitulatif d'audit gratuit par email au prospect
header('Content-Type: application/json');

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/email.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); die(json_encode(['error' => 'Method not allowed']));
}

$input   = json_decode(file_get_contents('php://input'), true) ?? [];
$prenom  = trim(strip_tags($input['prenom']   ?? ''));
$nom     = trim(strip_tags($input['nom']      ?? ''));
$email   = filter_var(trim($input['email']    ?? ''), FILTER_SANITIZE_EMAIL);
$lead_id = (int)($input['lead_id']  ?? 0);
$audit_id= (int)($input['audit_id'] ?? 0);
$url     = trim($input['url'] ?? '');

/* ── Validation ──────────────────────────────────────────── */
if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    die(json_encode(['error' => 'Email invalide']));
}
if (!$prenom) {
    http_response_code(400);
    die(json_encode(['error' => 'Prénom requis']));
}

/* ── Mise à jour du lead ─────────────────────────────────── */
try {
    $db = get_db();

    /* Ajoute les colonnes prenom/nom si elles n'existent pas encore */
    try {
        $db->exec("ALTER TABLE leads ADD COLUMN IF NOT EXISTS prenom VARCHAR(100) DEFAULT NULL");
        $db->exec("ALTER TABLE leads ADD COLUMN IF NOT EXISTS nom    VARCHAR(100) DEFAULT NULL");
    } catch (Exception $e) { /* colonnes déjà présentes ou SGBD ne supporte pas IF NOT EXISTS */ }

    if ($lead_id > 0) {
        try {
            $stmt = $db->prepare("UPDATE leads SET email = ?, prenom = ?, nom = ? WHERE id = ?");
            $stmt->execute([$email, $prenom, $nom, $lead_id]);
        } catch (Exception $e) {
            /* Fallback sans prenom/nom */
            $stmt = $db->prepare("UPDATE leads SET email = ? WHERE id = ?");
            $stmt->execute([$email, $lead_id]);
        }
    } else {
        try {
            $stmt = $db->prepare("INSERT INTO leads (url, email, prenom, nom, source) VALUES (?,?,?,?,'audit_email')
                                  ON DUPLICATE KEY UPDATE email=VALUES(email), prenom=VALUES(prenom), nom=VALUES(nom)");
            $stmt->execute([$url, $email, $prenom, $nom]);
        } catch (Exception $e) {
            $stmt = $db->prepare("INSERT INTO leads (url, email, source) VALUES (?,?,'audit_email')
                                  ON DUPLICATE KEY UPDATE email=VALUES(email)");
            $stmt->execute([$url, $email]);
        }
        $lead_id = $db->lastInsertId() ?: $lead_id;
    }

    /* ── Récupère l'audit pour construire l'email ──────────── */
    $audit_data = null;
    if ($audit_id > 0) {
        $stmt = $db->prepare("SELECT result_json FROM audits WHERE id = ? LIMIT 1");
        $stmt->execute([$audit_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row && $row['result_json']) {
            $audit_data = json_decode($row['result_json'], true);
        }
    }

} catch (Exception $e) {
    error_log('[send-audit-email] DB error: ' . $e->getMessage());
    $audit_data = null;
}

/* ── Construit le corps de l'email ─────────────────────── */
$display_name = htmlspecialchars($prenom . ' ' . $nom);
$display_url  = htmlspecialchars($url ?: 'votre site');
$score        = '';
$opps_html    = '';
$time_saved   = '';
$money_saved  = '';

if ($audit_data) {
    $score      = isset($audit_data['score']) ? (int)$audit_data['score'] : '';
    $time_saved = isset($audit_data['total_time_saved_h_week']) ? (int)$audit_data['total_time_saved_h_week'] : '';
    $money_saved= isset($audit_data['total_money_saved_eur_month']) ? (int)$audit_data['total_money_saved_eur_month'] : '';

    $opps = $audit_data['opportunities'] ?? $audit_data['top_opportunities'] ?? [];
    $top3 = array_slice($opps, 0, 3);

    foreach ($top3 as $opp) {
        $tool = htmlspecialchars($opp['tool'] ?? $opp['name'] ?? $opp['title'] ?? 'Outil IA');
        $cat  = htmlspecialchars($opp['category'] ?? $opp['cat'] ?? '');
        $desc = htmlspecialchars($opp['description'] ?? $opp['desc'] ?? '');
        $euros_raw = $opp['monthly_gain'] ?? $opp['money_saved_eur_month'] ?? $opp['gain_euros'] ?? null;
        $hours_raw = $opp['hours_saved']  ?? $opp['time_saved_h_week']     ?? $opp['gain_heures'] ?? null;
        $gains = '';
        if ($euros_raw !== null) $gains .= '+' . number_format((int)$euros_raw, 0, ',', ' ') . '€/mois';
        if ($hours_raw !== null) $gains .= ($gains ? ' · ' : '') . '−' . (int)$hours_raw . 'h/sem';

        $opps_html .= "
        <div style='padding:14px 0;border-bottom:1px solid #E5E7EB'>
          <div style='display:flex;justify-content:space-between;align-items:flex-start;gap:12px;flex-wrap:wrap'>
            <div>
              <div style='font-weight:700;font-size:15px;color:#111827'>{$tool}</div>
              " . ($cat ? "<div style='font-size:12px;color:#6B7280;margin-top:2px'>{$cat}</div>" : '') . "
              " . ($desc ? "<div style='font-size:13px;color:#4B5563;margin-top:6px;line-height:1.5'>{$desc}</div>" : '') . "
            </div>
            " . ($gains ? "<div style='font-weight:700;color:#059669;font-size:14px;white-space:nowrap'>{$gains}</div>" : '') . "
          </div>
        </div>";
    }
}

/* Score badge */
$score_html = '';
if ($score !== '') {
    $score_color = $score >= 70 ? '#059669' : ($score >= 40 ? '#D97706' : '#DC2626');
    $score_html = "<div style='text-align:center;margin:20px 0'>
      <div style='display:inline-block;width:72px;height:72px;border-radius:50%;background:{$score_color};color:#fff;font-size:26px;font-weight:800;line-height:72px'>{$score}</div>
      <div style='font-size:13px;color:#6B7280;margin-top:6px'>Score de maturité IA</div>
    </div>";
}

/* Stats */
$stats_html = '';
if ($time_saved || $money_saved) {
    $stats_html = "<div style='display:flex;gap:12px;margin:16px 0;flex-wrap:wrap'>";
    if ($time_saved) $stats_html .= "<div style='flex:1;min-width:120px;background:#F0FDF4;border:1px solid #BBF7D0;border-radius:8px;padding:12px 16px;text-align:center'><div style='font-size:22px;font-weight:800;color:#059669'>{$time_saved}h</div><div style='font-size:12px;color:#065F46'>gagnées / semaine</div></div>";
    if ($money_saved) $stats_html .= "<div style='flex:1;min-width:120px;background:#F0FDF4;border:1px solid #BBF7D0;border-radius:8px;padding:12px 16px;text-align:center'><div style='font-size:22px;font-weight:800;color:#059669'>" . number_format($money_saved, 0, ',', ' ') . "€</div><div style='font-size:12px;color:#065F46'>de valeur / mois</div></div>";
    $stats_html .= "</div>";
}

$opps_section = $opps_html
    ? "<h3 style='font-size:17px;font-weight:700;color:#111827;margin:24px 0 8px'>Vos 3 premières opportunités IA</h3>" . $opps_html
    : '';

$body = "
<h2>Bonjour {$display_name}</h2>
<p>Voici le récapitulatif de votre analyse IA pour <strong>{$display_url}</strong>. Nous avons identifié plusieurs opportunités concrètes pour automatiser vos tâches et gagner du temps dès maintenant.</p>

{$score_html}
{$stats_html}
{$opps_section}

<div style='margin-top:24px;padding:16px;background:#F9FAFB;border-radius:8px;font-size:13px;color:#6B7280;line-height:1.6'>
  Ces 3 opportunités sont gratuites. Pour accéder à l'intégralité de votre rapport avec plan d'action sur 12 mois, simulation ROI et tutoriels personnalisés, consultez votre rapport complet.
</div>

<a class='btn' href='https://abys.ai/audit-resultats.php' style='margin-top:20px'>Revoir mes résultats →</a>
";

/* ── Envoi ─────────────────────────────────────────────── */
$subject = "Votre analyse IA · " . ($url ? preg_replace('#^https?://(www\.)?#i', '', $url) : 'ABYS AI');
$sent    = send_email($email, $subject, $body);

if ($sent) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Échec de l\'envoi, réessayez.']);
}
