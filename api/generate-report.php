<?php
// Fichier: abys-ai/api/generate-report.php
header('Content-Type: application/json');

// La génération premium (Sonnet) peut durer 60-120s. On ne veut PAS qu'elle soit
// tuée si le navigateur ferme l'onglet (le webhook déclenche aussi cette page).
ignore_user_abort(true);
@set_time_limit(240);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/ai-helpers.php';
require_once __DIR__ . '/email.php';

$input = json_decode(file_get_contents('php://input'), true);
$token = trim($input['token'] ?? ($_GET['token'] ?? ''));

if (!$token) { http_response_code(400); die(json_encode(['error' => 'Token manquant'])); }

$db = get_db();

$stmt = $db->prepare("
    SELECT r.*, a.recommendations, a.score, l.url, l.secteur, l.email AS lead_email
    FROM reports r
    JOIN audits a ON r.audit_id = a.id
    JOIN leads l ON r.lead_id = l.id
    WHERE r.token = ? AND r.paid_at IS NOT NULL
    LIMIT 1
");
$stmt->execute([$token]);
$report = $stmt->fetch();

if (!$report) { http_response_code(404); die(json_encode(['error' => 'Rapport non trouvé ou non payé'])); }

// Déjà généré → on renvoie directement, aucun nouvel appel IA, aucun email
if (!empty($report['content'])) {
    echo json_encode(['success' => true, 'report' => json_decode($report['content'], true)]);
    exit;
}

// ── Verrou anti-doublon : une seule génération à la fois par token ──
// (le webhook Stripe et la page de succès peuvent déclencher en parallèle)
$lockName = 'abysgen_' . preg_replace('/[^a-zA-Z0-9]/', '', $token);
$got = (int) $db->query("SELECT GET_LOCK(" . $db->quote($lockName) . ", 0)")->fetchColumn();
if (!$got) {
    // Une autre requête génère déjà : on ne duplique pas l'appel IA ni l'email
    echo json_encode(['success' => false, 'generating' => true]);
    exit;
}

try {
    // Re-vérifie après acquisition du verrou (l'autre process a pu terminer)
    $again = $db->prepare("SELECT content FROM reports WHERE token = ? LIMIT 1");
    $again->execute([$token]);
    $existing = $again->fetchColumn();
    if (!empty($existing)) {
        $db->query("SELECT RELEASE_LOCK(" . $db->quote($lockName) . ")");
        echo json_encode(['success' => true, 'report' => json_decode($existing, true)]);
        exit;
    }

    $settings = get_settings($db);
    $provider = $settings['ai_provider'] ?? 'claude';
    $audit    = json_decode($report['recommendations'], true) ?: [];
    $opps     = $audit['opportunities'] ?? [];
    $opps_json = json_encode($opps, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

    // Carte des chiffres de l'audit, par id, pour garantir des totaux non nuls
    $auditNums = [];
    foreach ($opps as $o) {
        if (isset($o['id'])) {
            $auditNums[$o['id']] = [
                'time_saved_h_week'     => $o['time_saved_h_week']     ?? 0,
                'money_saved_eur_month' => $o['money_saved_eur_month'] ?? 0,
                'roi_12m_eur'           => $o['roi_12m_eur']           ?? 0,
            ];
        }
    }

    $prompt = <<<PROMPT
Tu es un expert IA pour PME françaises. Génère un rapport premium COMPLET en JSON pour cette entreprise.

Site : {$report['url']}
Secteur : {$report['secteur']}
Score IA actuel : {$report['score']}/100

Opportunités identifiées (reprends leur id, leur outil ET leurs chiffres time_saved_h_week / money_saved_eur_month / roi_12m_eur À L'IDENTIQUE) :
{$opps_json}

Retourne UNIQUEMENT ce JSON (pas de markdown, pas de texte autour) :
{
  "executive_summary": "Résumé exécutif 3-4 phrases pour un chef d'entreprise non technique",
  "opportunities": [
    {
      "id": "même id que l'audit",
      "category": "même catégorie",
      "tool": "même outil",
      "tool_url": "URL officielle ou d'affiliation",
      "description": "description détaillée 2-3 phrases adaptées à ce secteur",
      "tutorial": {
        "title": "Comment mettre en place [outil] pour votre activité",
        "steps": ["Étape 1 : ...","Étape 2 : ...","Étape 3 : ...","Étape 4 : ...","Étape 5 : ..."],
        "tips": ["Conseil pratique 1","Conseil pratique 2"],
        "estimated_setup_time": "ex: 2 heures",
        "first_result_delay": "ex: dès la première semaine"
      },
      "time_saved_h_week": <reprends la valeur de l'audit>,
      "money_saved_eur_month": <reprends la valeur de l'audit>,
      "roi_12m_eur": <reprends la valeur de l'audit>,
      "difficulty": "Facile|Moyen|Avancé",
      "competitors_using_pct": 0
    }
  ],
  "action_plan": {
    "month_1": ["Action prioritaire 1","Action prioritaire 2","Action prioritaire 3"],
    "month_3": ["Objectif 3 mois 1","Objectif 3 mois 2"],
    "month_6": ["Objectif 6 mois 1","Objectif 6 mois 2"],
    "month_12": ["Vision 12 mois"]
  },
  "total_roi_12m": 0,
  "competitive_analysis": "Analyse 2-3 phrases : où en sont les concurrents sur l'IA"
}

Génère des tutoriels VRAIMENT personnalisés pour ce secteur. Sois concret et actionnable. Garde les tutoriels concis (5 étapes courtes max).
PROMPT;

    $content = call_ai($provider, $prompt, $settings);

    // ── Garde-fou : garantir des chiffres non nuls en reprenant ceux de l'audit ──
    $tot_h = 0; $tot_eur = 0;
    if (!empty($content['opportunities']) && is_array($content['opportunities'])) {
        foreach ($content['opportunities'] as &$po) {
            $id = $po['id'] ?? null;
            $ref = ($id !== null && isset($auditNums[$id])) ? $auditNums[$id] : null;
            if (empty($po['time_saved_h_week']) && $ref)     $po['time_saved_h_week']     = $ref['time_saved_h_week'];
            if (empty($po['money_saved_eur_month']) && $ref) $po['money_saved_eur_month'] = $ref['money_saved_eur_month'];
            if (empty($po['roi_12m_eur']) && $ref)           $po['roi_12m_eur']           = $ref['roi_12m_eur'];
            $tot_h   += (float)($po['time_saved_h_week'] ?? 0);
            $tot_eur += (float)($po['money_saved_eur_month'] ?? 0);
        }
        unset($po);
    }
    if (empty($content['total_roi_12m'])) {
        $content['total_roi_12m'] = (int) round($tot_eur * 12);
    }

    $db->prepare("UPDATE reports SET content=?, action_plan=? WHERE token=?")
       ->execute([
           json_encode($content, JSON_UNESCAPED_UNICODE),
           json_encode($content['action_plan'] ?? [], JSON_UNESCAPED_UNICODE),
           $token,
       ]);

    // Email client : envoye UNIQUEMENT ici, une fois le rapport reellement pret
    $email_addr = $report['lead_email'] ?? '';
    if ($email_addr && !empty($report['url'])) {
        email_report_paid($email_addr, $report['url'], $token);
    }

    $db->query("SELECT RELEASE_LOCK(" . $db->quote($lockName) . ")");
    echo json_encode(['success' => true, 'report' => $content]);

} catch (Exception $e) {
    $db->query("SELECT RELEASE_LOCK(" . $db->quote($lockName) . ")");
    error_log('[ABYS generate-report] ' . $e->getMessage());
    http_response_code(500);
    die(json_encode(['error' => 'Génération échouée : ' . $e->getMessage()]));
}
