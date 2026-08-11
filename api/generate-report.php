<?php
// Fichier: abys-ai/api/generate-report.php
header('Content-Type: application/json');

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/ai-helpers.php';
require_once __DIR__ . '/email.php';

$input = json_decode(file_get_contents('php://input'), true);
$token = trim($input['token'] ?? '');

if (!$token) { http_response_code(400); die(json_encode(['error' => 'Token manquant'])); }

$db = get_db();

$stmt = $db->prepare("
    SELECT r.*, a.recommendations, a.score, l.url, l.secteur
    FROM reports r
    JOIN audits a ON r.audit_id = a.id
    JOIN leads l ON r.lead_id = l.id
    WHERE r.token = ? AND r.paid_at IS NOT NULL
    LIMIT 1
");
$stmt->execute([$token]);
$report = $stmt->fetch();

if (!$report) { http_response_code(404); die(json_encode(['error' => 'Rapport non trouvé ou non payé'])); }

// Si déjà généré, retourner directement
if ($report['content']) {
    echo json_encode(['success' => true, 'report' => json_decode($report['content'], true)]);
    exit;
}

$settings = get_settings($db);
$provider = $settings['ai_provider'] ?? 'claude';
$audit    = json_decode($report['recommendations'], true);
$opps     = $audit['opportunities'] ?? [];
$opps_json = json_encode($opps, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

$prompt = <<<PROMPT
Tu es un expert IA pour PME françaises. Génère un rapport premium COMPLET en JSON pour cette entreprise.

Site : {$report['url']}
Secteur : {$report['secteur']}
Score IA actuel : {$report['score']}/100

Opportunités identifiées :
{$opps_json}

Retourne UNIQUEMENT ce JSON :
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
      "time_saved_h_week": 0,
      "money_saved_eur_month": 0,
      "roi_12m_eur": 0,
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

Génère des tutoriels VRAIMENT personnalisés pour ce secteur. Sois concret et actionnable.
PROMPT;

try {
    $content = call_ai($provider, $prompt, $settings);
} catch (Exception $e) {
    http_response_code(500);
    die(json_encode(['error' => 'Génération échouée : ' . $e->getMessage()]));
}

$db->prepare("UPDATE reports SET content=?, action_plan=? WHERE token=?")
   ->execute([json_encode($content), json_encode($content['action_plan'] ?? []), $token]);

// Email client avec lien vers le rapport (si pas encore envoyé par le webhook)
$lead_email = $db->prepare("SELECT l.email FROM leads l JOIN reports r ON r.lead_id=l.id WHERE r.token=?");
$lead_email->execute([$token]);
$email_addr = $lead_email->fetchColumn();
if ($email_addr && !empty($report['url'])) {
    email_report_paid($email_addr, $report['url'], $token);
}

echo json_encode(['success' => true, 'report' => $content]);
