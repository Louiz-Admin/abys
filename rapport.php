<?php
$page_title = 'Votre rapport ABYS — Plan d\'action IA';
include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/nav.php';
require_once __DIR__ . '/api/db.php';

$token = $_GET['token'] ?? '';
if (!$token) { header('Location: /'); exit; }

$stmt = get_db()->prepare("
    SELECT r.*, l.url, l.secteur, a.score
    FROM reports r
    JOIN leads l ON r.lead_id = l.id
    JOIN audits a ON r.audit_id = a.id
    WHERE r.token = ? AND r.paid_at IS NOT NULL AND (r.expires_at IS NULL OR r.expires_at > NOW())
    LIMIT 1
");
$stmt->execute([$token]);
$report = $stmt->fetch();

if (!$report) { ?>
  <div style="text-align:center;padding:80px">
    <h2 style="margin-bottom:16px">Rapport introuvable ou expiré.</h2>
    <a href="/" class="btn btn-primary">Retour à l'accueil →</a>
  </div>
<?php include __DIR__ . '/includes/footer.php'; exit; }

$content = json_decode($report['content'], true);
$opps    = $content['opportunities'] ?? [];
$plan    = $content['action_plan']   ?? [];
?>

<div class="container" style="padding-top:60px;padding-bottom:80px">

  <!-- Header rapport -->
  <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:48px;flex-wrap:wrap;gap:20px">
    <div>
      <div class="badge mb-16">Rapport Premium — Accès sécurisé</div>
      <h1 style="font-size:38px;font-weight:300;letter-spacing:-0.04em;margin-bottom:8px">
        Plan d'action IA pour <strong style="font-weight:700"><?= htmlspecialchars($report['url']) ?></strong>
      </h1>
      <p style="color:var(--ink-4);font-size:14px">
        Généré le <?= date('d/m/Y', strtotime($report['paid_at'])) ?> · Secteur : <?= htmlspecialchars($report['secteur'] ?? '') ?>
      </p>
    </div>
    <div style="text-align:right">
      <div style="font-size:48px;font-weight:700;color:var(--green-deep)"><?= $report['score'] ?></div>
      <div style="font-size:13px;color:var(--ink-4)">score IA / 100</div>
    </div>
  </div>

  <!-- Résumé exécutif -->
  <div class="card card-accent mb-32">
    <div style="font-size:12px;font-weight:600;color:var(--ink-4);text-transform:uppercase;letter-spacing:0.1em;margin-bottom:12px">Résumé exécutif</div>
    <p style="font-size:16px;color:var(--ink-2);line-height:1.7"><?= htmlspecialchars($content['executive_summary'] ?? '') ?></p>
  </div>

  <!-- Analyse concurrentielle -->
  <?php if (!empty($content['competitive_analysis'])): ?>
  <div class="card mb-32" style="border-left:3px solid var(--blue)">
    <div style="font-size:12px;font-weight:600;color:var(--blue);text-transform:uppercase;letter-spacing:0.1em;margin-bottom:8px">📊 Analyse concurrentielle</div>
    <p style="font-size:15px;color:var(--ink-3);line-height:1.65"><?= htmlspecialchars($content['competitive_analysis']) ?></p>
  </div>
  <?php endif; ?>

  <!-- Opportunités + tutoriels -->
  <h2 style="font-size:24px;font-weight:600;margin-bottom:24px">Vos <?= count($opps) ?> opportunités — tutoriels inclus</h2>

  <?php foreach($opps as $i => $opp): ?>
  <div class="card mb-24" style="padding:32px">
    <div style="display:grid;grid-template-columns:1fr auto;gap:20px;margin-bottom:24px">
      <div>
        <div style="font-size:11px;font-weight:600;color:var(--ink-4);text-transform:uppercase;letter-spacing:0.1em;margin-bottom:6px"><?= htmlspecialchars($opp['category'] ?? '') ?></div>
        <h3 style="font-size:20px;font-weight:600;color:var(--ink-2);margin-bottom:8px">
          <?= htmlspecialchars($opp['tool'] ?? '') ?>
          <?php if (!empty($opp['tool_url'])): ?>
          <a href="<?= htmlspecialchars($opp['tool_url']) ?>" target="_blank" rel="noopener sponsored"
             style="font-size:13px;color:var(--blue);font-weight:400;margin-left:8px">Accéder →</a>
          <?php endif; ?>
        </h3>
        <p style="color:var(--ink-3);font-size:15px;line-height:1.6"><?= htmlspecialchars($opp['description'] ?? '') ?></p>
      </div>
      <div style="text-align:right;flex-shrink:0;min-width:120px">
        <div style="font-size:24px;font-weight:700;color:var(--green-deep)">+<?= number_format($opp['money_saved_eur_month'] ?? 0, 0, ',', ' ') ?>€/mois</div>
        <div style="font-size:13px;color:var(--ink-4)">−<?= $opp['time_saved_h_week'] ?? 0 ?>h/sem</div>
        <div style="font-size:12px;color:var(--ink-5);margin-top:4px">ROI 12m : +<?= number_format($opp['roi_12m_eur'] ?? 0, 0, ',', ' ') ?>€</div>
        <span style="display:inline-block;margin-top:8px;padding:4px 10px;border-radius:999px;font-size:11px;font-weight:500;background:rgba(16,185,129,0.1);color:var(--green-deep)"><?= htmlspecialchars($opp['difficulty'] ?? '') ?></span>
      </div>
    </div>

    <?php if (!empty($opp['tutorial'])): $t = $opp['tutorial']; ?>
    <div style="background:var(--bg);border-radius:var(--r-md);padding:24px">
      <div style="font-size:13px;font-weight:600;color:var(--ink-2);margin-bottom:16px">📖 <?= htmlspecialchars($t['title'] ?? '') ?></div>
      <ol style="list-style:none;display:flex;flex-direction:column;gap:10px">
        <?php foreach($t['steps'] ?? [] as $si => $step): ?>
        <li style="display:flex;gap:12px;font-size:14px;color:var(--ink-3)">
          <span style="width:24px;height:24px;border-radius:50%;background:var(--gradient);color:#fff;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0"><?= $si+1 ?></span>
          <?= htmlspecialchars($step) ?>
        </li>
        <?php endforeach; ?>
      </ol>
      <?php if (!empty($t['tips'])): ?>
      <div style="margin-top:16px;padding:12px;background:rgba(14,165,233,0.06);border-radius:var(--r-md)">
        <div style="font-size:12px;font-weight:600;color:var(--blue);margin-bottom:8px">💡 Conseils pratiques</div>
        <?php foreach($t['tips'] as $tip): ?>
        <div style="font-size:13px;color:var(--ink-3);margin-bottom:4px">• <?= htmlspecialchars($tip) ?></div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
      <div style="display:flex;gap:16px;margin-top:12px;font-size:12px;color:var(--ink-4)">
        <?php if (!empty($t['estimated_setup_time'])): ?>
        <span>⏱ Mise en place : <?= htmlspecialchars($t['estimated_setup_time']) ?></span>
        <?php endif; ?>
        <?php if (!empty($t['first_result_delay'])): ?>
        <span>🎯 Premiers résultats : <?= htmlspecialchars($t['first_result_delay']) ?></span>
        <?php endif; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>
  <?php endforeach; ?>

  <!-- Plan d'action 12 mois -->
  <?php if ($plan): ?>
  <h2 style="font-size:24px;font-weight:600;margin:48px 0 24px">Plan d'action sur 12 mois</h2>
  <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:48px">
    <?php foreach(['month_1'=>'Mois 1','month_3'=>'Mois 3','month_6'=>'Mois 6','month_12'=>'Mois 12'] as $key => $label): ?>
    <?php if (!empty($plan[$key])): ?>
    <div class="card">
      <div style="font-size:12px;font-weight:700;color:var(--green-deep);text-transform:uppercase;letter-spacing:0.1em;margin-bottom:12px"><?= $label ?></div>
      <?php foreach($plan[$key] as $action): ?>
      <div style="font-size:13px;color:var(--ink-3);margin-bottom:8px;display:flex;gap:8px">
        <span style="color:var(--green);flex-shrink:0">→</span><?= htmlspecialchars($action) ?>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <!-- CTA assistant -->
  <div style="background:linear-gradient(135deg,#064E3B,var(--ink-2));border-radius:var(--r-xl);padding:48px;text-align:center">
    <h2 style="font-size:28px;font-weight:300;color:#fff;margin-bottom:8px">Besoin d'aide pour la mise en place ?</h2>
    <p style="color:rgba(255,255,255,0.7);margin-bottom:24px">Notre assistant IA répond à toutes vos questions par WhatsApp et email — 29€/mois.</p>
    <a href="/assistant.php" class="btn btn-primary btn-lg">Activer l'assistant IA →</a>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
