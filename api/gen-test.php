<?php
// Fichier: abys-ai/api/gen-test.php
// ENDPOINT D'AUTO-TEST (temporaire) · prouve que la génération premium (Sonnet)
// fonctionne de bout en bout AVEC la vraie clé API, SANS paiement réel et SANS
// toucher aux données clients. Gate par clé.
//
//   Lancer  : https://abys.ai/api/gen-test.php?key=<imap_cron_key>
//   Relire  : https://abys.ai/api/gen-test.php?key=<imap_cron_key>&read=1
//
// Le résultat est aussi stocké dans /tmp pour être relu si la 1re requête expire.

header('Content-Type: application/json; charset=utf-8');
ignore_user_abort(true);
@set_time_limit(240);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/ai-helpers.php';

$db       = get_db();
$settings = get_settings($db);
$expected = $settings['imap_cron_key'] ?? 'abys_cron_2026_x7k9p';
if (($_GET['key'] ?? '') !== $expected) {
    http_response_code(403);
    die(json_encode(['error' => 'Forbidden']));
}

$store = sys_get_temp_dir() . '/abys_gentest.json';

// Relecture du dernier résultat (rapide)
if (isset($_GET['read'])) {
    if (is_file($store)) { echo file_get_contents($store); }
    else { echo json_encode(['status' => 'aucun résultat encore']); }
    exit;
}

// ── Données SYNTHÉTIQUES réalistes (aucune donnée client réelle) ──
$url     = 'https://exemple-plomberie-nice.fr';
$secteur = 'Plomberie / Artisan du bâtiment';
$score   = 31;
$opps = [
    ['id' => 'devis', 'rank' => 1, 'category' => 'Devis & Facturation', 'emoji' => '📄', 'tool' => 'Axonaut', 'tool_url' => 'https://axonaut.com', 'tool_domain' => 'axonaut.com', 'description' => 'Génère vos devis personnalisés en 30 secondes et relance automatiquement les impayés.', 'time_saved_h_week' => 6, 'money_saved_eur_month' => 480, 'productivity_gain_pct' => 25, 'roi_12m_eur' => 5760, 'difficulty' => 'Facile', 'implementation_days' => 2, 'monthly_cost_eur' => 35, 'has_free_plan' => false, 'affiliate_commission_pct' => 20],
    ['id' => 'sav', 'rank' => 2, 'category' => 'Service client', 'emoji' => '💬', 'tool' => 'Crisp', 'tool_url' => 'https://crisp.chat', 'tool_domain' => 'crisp.chat', 'description' => 'Répond à vos clients par email et sur le site 24h/24 sans intervention.', 'time_saved_h_week' => 4, 'money_saved_eur_month' => 300, 'productivity_gain_pct' => 18, 'roi_12m_eur' => 3600, 'difficulty' => 'Facile', 'implementation_days' => 1, 'monthly_cost_eur' => 25, 'has_free_plan' => true, 'affiliate_commission_pct' => 15],
    ['id' => 'compta', 'rank' => 3, 'category' => 'Comptabilité', 'emoji' => '💰', 'tool' => 'Pennylane', 'tool_url' => 'https://pennylane.com', 'tool_domain' => 'pennylane.com', 'description' => 'Synchronise votre banque et catégorise toutes vos dépenses automatiquement.', 'time_saved_h_week' => 3, 'money_saved_eur_month' => 240, 'productivity_gain_pct' => 12, 'roi_12m_eur' => 2880, 'difficulty' => 'Moyen', 'implementation_days' => 3, 'monthly_cost_eur' => 29, 'has_free_plan' => false, 'affiliate_commission_pct' => 18],
    ['id' => 'planning', 'rank' => 4, 'category' => 'Planning', 'emoji' => '📅', 'tool' => 'Cal.com', 'tool_url' => 'https://cal.com', 'tool_domain' => 'cal.com', 'description' => 'Vos clients réservent un créneau d\'intervention en ligne, sans appel.', 'time_saved_h_week' => 2, 'money_saved_eur_month' => 160, 'productivity_gain_pct' => 8, 'roi_12m_eur' => 1920, 'difficulty' => 'Facile', 'implementation_days' => 1, 'monthly_cost_eur' => 0, 'has_free_plan' => true, 'affiliate_commission_pct' => 0],
    ['id' => 'photos', 'rank' => 5, 'category' => 'Communication', 'emoji' => '🎨', 'tool' => 'Canva', 'tool_url' => 'https://canva.com', 'tool_domain' => 'canva.com', 'description' => 'Crée vos supports (chantiers avant/après, cartes de visite) en quelques clics.', 'time_saved_h_week' => 2, 'money_saved_eur_month' => 120, 'productivity_gain_pct' => 6, 'roi_12m_eur' => 1440, 'difficulty' => 'Facile', 'implementation_days' => 1, 'monthly_cost_eur' => 12, 'has_free_plan' => true, 'affiliate_commission_pct' => 10],
];

$provider  = $settings['ai_provider'] ?? 'claude';
$opps_json = json_encode($opps, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

$prompt = <<<PROMPT
Tu es un expert IA pour PME françaises. Génère un rapport premium COMPLET en JSON pour cette entreprise.

Site : {$url}
Secteur : {$secteur}
Score IA actuel : {$score}/100

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
      "tool_url": "URL officielle",
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

$t0 = microtime(true);
try {
    $content = call_ai($provider, $prompt, $settings);
    $elapsed = round(microtime(true) - $t0, 1);

    $tot_h = 0; $tot_eur = 0; $withTut = 0;
    foreach (($content['opportunities'] ?? []) as $o) {
        $tot_h   += (float)($o['time_saved_h_week'] ?? 0);
        $tot_eur += (float)($o['money_saved_eur_month'] ?? 0);
        if (!empty($o['tutorial']['steps'])) $withTut++;
    }

    $out = [
        'status'                      => 'ok',
        'elapsed_seconds'             => $elapsed,
        'opportunities_count'         => count($content['opportunities'] ?? []),
        'opportunities_with_tutorial' => $withTut,
        'has_executive_summary'       => !empty($content['executive_summary']),
        'has_action_plan'             => !empty($content['action_plan']),
        'has_competitive_analysis'    => !empty($content['competitive_analysis']),
        'total_h_week'                => $tot_h,
        'total_eur_month'             => $tot_eur,
        'total_roi_12m'               => $content['total_roi_12m'] ?? null,
        'sample_first_opportunity'    => $content['opportunities'][0] ?? null,
        'ran_at'                      => date('c'),
    ];
} catch (Exception $e) {
    $out = [
        'status'          => 'error',
        'elapsed_seconds' => round(microtime(true) - $t0, 1),
        'message'         => $e->getMessage(),
        'ran_at'          => date('c'),
    ];
}

@file_put_contents($store, json_encode($out, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
echo json_encode($out, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
