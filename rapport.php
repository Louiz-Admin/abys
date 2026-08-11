<?php
$page_title = 'Votre rapport ABYS · Plan d\'action IA';
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

$content = json_decode($report['content'] ?? '', true) ?: [];
$opps    = $content['opportunities'] ?? [];
$plan    = $content['action_plan']   ?? [];
$summary = $content['executive_summary'] ?? '';
$compet  = $content['competitive_analysis'] ?? '';
$score   = (int)($report['score'] ?? 0);
$domain  = $report['url'] ?? '';
$secteur = $report['secteur'] ?? '';

// Totaux calculés depuis les opportunités
$tot_h = 0; $tot_eur = 0; $tot_roi = (int)($content['total_roi_12m'] ?? 0);
foreach ($opps as $o) {
    $tot_h   += (float)($o['time_saved_h_week'] ?? 0);
    $tot_eur += (float)($o['money_saved_eur_month'] ?? 0);
}
if (!$tot_roi) $tot_roi = (int)round($tot_eur * 12);
$fmt = fn($n) => number_format((float)$n, 0, ',', ' ');
?>

<style>
.rp-wrap { max-width: 980px; margin: 0 auto; padding: 48px 24px 80px; }
.rp-hero { display: flex; justify-content: space-between; align-items: flex-start; gap: 24px; flex-wrap: wrap; margin-bottom: 28px; }
.rp-hero h1 { font-size: 34px; font-weight: 300; letter-spacing: -0.04em; line-height: 1.2; margin: 10px 0 6px; }
.rp-hero h1 strong { font-weight: 700; }
.rp-meta { color: var(--ink-4); font-size: 13.5px; }
.rp-score { text-align: center; flex-shrink: 0; }
.rp-ring { width: 108px; height: 108px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
  background: conic-gradient(#10B981 calc(<?= max(0,min(100,$score)) ?> * 1%), rgba(16,185,129,0.12) 0); }
.rp-ring > div { width: 84px; height: 84px; border-radius: 50%; background: var(--bg, #F2FBF7); display: flex; flex-direction: column; align-items: center; justify-content: center; }
.rp-ring b { font-size: 30px; font-weight: 800; color: var(--ink); line-height: 1; }
.rp-ring span { font-size: 10.5px; color: var(--ink-4); }

.rp-milo { display: flex; gap: 16px; align-items: flex-start; background: linear-gradient(135deg, #0A1F1A, #064E3B);
  border-radius: 18px; padding: 22px 24px; color: #fff; margin-bottom: 28px; }
.rp-milo-avatar { width: 46px; height: 46px; border-radius: 50%; background: rgba(16,185,129,0.2); border: 2px solid #10B981;
  display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 17px; color: #34D399; flex-shrink: 0; }
.rp-milo p { font-size: 14.5px; line-height: 1.65; color: rgba(255,255,255,0.85); margin: 4px 0 0; }
.rp-milo b { color: #6EE7B7; }
.rp-milo .tagIA { display: inline-block; font-size: 10px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase;
  background: rgba(52,211,153,0.15); color: #6EE7B7; border: 1px solid rgba(52,211,153,0.4); border-radius: 20px; padding: 2px 9px; margin-left: 8px; vertical-align: 2px; }

.rp-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 34px; }
@media (max-width: 640px) { .rp-stats { grid-template-columns: 1fr; } }
.rp-stat { background: var(--white, #fff); border: 1px solid var(--border, #E5E7EB); border-radius: 16px; padding: 20px 22px; }
.rp-stat b { display: block; font-size: 30px; font-weight: 800; color: var(--green-deep, #059669); letter-spacing: -0.03em; }
.rp-stat span { font-size: 12.5px; color: var(--ink-3, #6B7280); }

.rp-section-title { font-size: 24px; font-weight: 300; letter-spacing: -0.03em; margin: 40px 0 18px; }
.rp-section-title strong { font-weight: 700; }

.rp-summary { background: var(--white, #fff); border: 1px solid var(--border, #E5E7EB); border-left: 4px solid #10B981;
  border-radius: 14px; padding: 20px 24px; font-size: 15px; line-height: 1.75; color: var(--ink-2, #1F2937); margin-bottom: 8px; }

.rp-opp { background: var(--white, #fff); border: 1px solid var(--border, #E5E7EB); border-radius: 18px; padding: 26px 28px; margin-bottom: 18px; }
.rp-opp-head { display: flex; justify-content: space-between; gap: 14px; align-items: flex-start; flex-wrap: wrap; }
.rp-opp-num { font-size: 12px; font-weight: 700; color: #10B981; letter-spacing: 0.06em; }
.rp-opp h3 { font-size: 20px; font-weight: 700; letter-spacing: -0.02em; margin: 4px 0 2px; }
.rp-opp-cat { font-size: 12.5px; color: var(--ink-4); }
.rp-chips { display: flex; gap: 8px; flex-wrap: wrap; }
.rp-chip { font-size: 11.5px; font-weight: 600; border-radius: 20px; padding: 4px 11px; background: rgba(16,185,129,0.08); color: #065F46; border: 1px solid rgba(16,185,129,0.18); white-space: nowrap; }
.rp-opp p.desc { font-size: 14.5px; line-height: 1.7; color: var(--ink-3, #4B5563); margin: 12px 0 14px; }
.rp-opp details { border-top: 1px solid var(--border, #E5E7EB); padding-top: 14px; margin-top: 4px; }
.rp-opp summary { cursor: pointer; font-size: 14px; font-weight: 600; color: var(--ink-2); list-style: none; display: flex; align-items: center; gap: 8px; }
.rp-opp summary::before { content: '▸'; color: #10B981; transition: transform 150ms; }
.rp-opp details[open] summary::before { transform: rotate(90deg); }
.rp-steps { margin: 14px 0 0; padding: 0; list-style: none; counter-reset: rpstep; }
.rp-steps li { counter-increment: rpstep; position: relative; padding: 0 0 12px 40px; font-size: 14px; line-height: 1.6; color: var(--ink-2); }
.rp-steps li::before { content: counter(rpstep); position: absolute; left: 0; top: 0; width: 26px; height: 26px; border-radius: 50%;
  background: rgba(16,185,129,0.1); color: #059669; font-size: 12.5px; font-weight: 700; display: flex; align-items: center; justify-content: center; }
.rp-tips { background: rgba(14,165,233,0.06); border: 1px solid rgba(14,165,233,0.15); border-radius: 10px; padding: 12px 16px; font-size: 13px; color: var(--ink-3); line-height: 1.6; margin-top: 10px; }
.rp-opp-cta { display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap;
  background: linear-gradient(135deg, rgba(16,185,129,0.07), rgba(14,165,233,0.05)); border: 1px solid rgba(16,185,129,0.2);
  border-radius: 14px; padding: 16px 18px; margin-top: 16px; }
.rp-opp-cta .txt { font-size: 13.5px; color: var(--ink-2); line-height: 1.5; }
.rp-opp-cta .txt b { color: var(--green-deep); }
.rp-btn { display: inline-block; background: #10B981; color: #fff; font-weight: 600; font-size: 14px; border-radius: 11px;
  padding: 11px 20px; text-decoration: none; white-space: nowrap; transition: background 150ms; }
.rp-btn:hover { background: #059669; }
.rp-btn.ghost { background: transparent; color: var(--green-deep); border: 1px solid rgba(16,185,129,0.4); }
.rp-btn.ghost:hover { background: rgba(16,185,129,0.06); }

.rp-plan { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; }
@media (max-width: 820px) { .rp-plan { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 480px) { .rp-plan { grid-template-columns: 1fr; } }
.rp-plan-col { background: var(--white, #fff); border: 1px solid var(--border, #E5E7EB); border-radius: 14px; padding: 18px; }
.rp-plan-col h4 { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #059669; margin: 0 0 12px; }
.rp-plan-col ul { margin: 0; padding: 0 0 0 16px; }
.rp-plan-col li { font-size: 13px; line-height: 1.55; color: var(--ink-3); margin-bottom: 8px; }

.rp-pack { background: linear-gradient(135deg, #0A1F1A, #064E3B); border-radius: 20px; padding: 34px 36px; color: #fff;
  display: flex; justify-content: space-between; align-items: center; gap: 24px; flex-wrap: wrap; margin-top: 44px; }
.rp-pack h3 { font-size: 24px; font-weight: 700; letter-spacing: -0.02em; margin: 0 0 8px; }
.rp-pack p { font-size: 14px; color: rgba(255,255,255,0.75); line-height: 1.65; margin: 0; max-width: 520px; }
.rp-pack .price { font-size: 40px; font-weight: 800; color: #34D399; }
.rp-pack .price small { font-size: 14px; font-weight: 500; color: rgba(255,255,255,0.6); }

.rp-note { text-align: center; font-size: 12.5px; color: var(--ink-4); margin-top: 34px; line-height: 1.7; }
@media print {
  nav, footer, .rp-opp-cta, .rp-pack, .rp-noprint { display: none !important; }
  .rp-wrap { padding: 0; }
}
</style>

<div class="rp-wrap">

  <!-- En-tête -->
  <div class="rp-hero">
    <div>
      <div class="badge">Rapport Premium · Accès à vie</div>
      <h1>Plan d'action IA pour <strong><?= htmlspecialchars($domain) ?></strong></h1>
      <div class="rp-meta">
        Généré le <?= date('d/m/Y', strtotime($report['paid_at'])) ?>
        <?= $secteur ? ' · Secteur : ' . htmlspecialchars($secteur) : '' ?>
        · <a href="#" onclick="window.print();return false" style="color:var(--green-deep)" class="rp-noprint">Imprimer / PDF</a>
      </div>
    </div>
    <div class="rp-score">
      <div class="rp-ring"><div><b><?= $score ?></b><span>score IA</span></div></div>
    </div>
  </div>

  <!-- Milo -->
  <div class="rp-milo">
    <div class="rp-milo-avatar">M</div>
    <div>
      <div style="font-size:15px;font-weight:700">Milo, votre copilote de mise en action<span class="tagIA">IA</span></div>
      <p>Ce rapport n'est pas fait pour être lu puis rangé. Pour chaque outil ci-dessous, je peux vous accompagner
      pas à pas jusqu'à ce qu'il tourne vraiment dans votre entreprise : création du compte, paramétrage, premier résultat.
      <b>Votre accès à mon assistance est inclus pendant 30 jours</b> avec ce rapport.</p>
    </div>
  </div>

  <!-- Chiffres clés -->
  <div class="rp-stats">
    <div class="rp-stat"><b><?= $fmt($tot_h) ?> h</b><span>récupérées par semaine (estimation)</span></div>
    <div class="rp-stat"><b><?= $fmt($tot_eur) ?> €</b><span>économisés par mois (estimation)</span></div>
    <div class="rp-stat"><b><?= $fmt($tot_roi) ?> €</b><span>de valeur potentielle sur 12 mois</span></div>
  </div>

  <!-- Résumé -->
  <?php if ($summary): ?>
  <h2 class="rp-section-title">L'<strong>essentiel</strong> en 30 secondes</h2>
  <div class="rp-summary"><?= nl2br(htmlspecialchars($summary)) ?></div>
  <?php endif; ?>

  <!-- Opportunités -->
  <h2 class="rp-section-title">Vos <strong><?= count($opps) ?> opportunités</strong>, outil par outil</h2>

  <?php foreach ($opps as $i => $o):
      $tool  = $o['tool'] ?? ($o['name'] ?? 'Outil IA');
      $turl  = $o['tool_url'] ?? '';
      $tut   = $o['tutorial'] ?? [];
      $steps = $tut['steps'] ?? [];
      $tips  = $tut['tips'] ?? [];
  ?>
  <div class="rp-opp">
    <div class="rp-opp-head">
      <div>
        <div class="rp-opp-num">OPPORTUNITÉ <?= $i + 1 ?></div>
        <h3><?= htmlspecialchars($tool) ?></h3>
        <div class="rp-opp-cat"><?= htmlspecialchars($o['category'] ?? '') ?></div>
      </div>
      <div class="rp-chips">
        <?php if (!empty($o['difficulty'])): ?><span class="rp-chip"><?= htmlspecialchars($o['difficulty']) ?></span><?php endif; ?>
        <?php if (!empty($o['time_saved_h_week'])): ?><span class="rp-chip"><?= $fmt($o['time_saved_h_week']) ?> h/sem</span><?php endif; ?>
        <?php if (!empty($o['money_saved_eur_month'])): ?><span class="rp-chip"><?= $fmt($o['money_saved_eur_month']) ?> €/mois</span><?php endif; ?>
        <?php if (!empty($tut['estimated_setup_time'])): ?><span class="rp-chip">⏱ <?= htmlspecialchars($tut['estimated_setup_time']) ?></span><?php endif; ?>
      </div>
    </div>

    <p class="desc"><?= htmlspecialchars($o['description'] ?? '') ?></p>

    <?php if ($steps): ?>
    <details>
      <summary><?= htmlspecialchars($tut['title'] ?? 'Guide de mise en place') ?></summary>
      <ol class="rp-steps">
        <?php foreach ($steps as $s): ?><li><?= htmlspecialchars($s) ?></li><?php endforeach; ?>
      </ol>
      <?php if ($tips): ?>
      <div class="rp-tips"><b>Conseils :</b> <?= htmlspecialchars(implode(' · ', $tips)) ?></div>
      <?php endif; ?>
    </details>
    <?php endif; ?>

    <div class="rp-opp-cta rp-noprint">
      <div class="txt">
        <b>Mission lancement avec Milo</b> : cet outil installé, paramétré et actif dans votre entreprise,
        guidé main dans la main jusqu'au premier résultat. Satisfait ou remboursé.
      </div>
      <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
        <?php if ($turl): ?><a class="rp-btn ghost" href="<?= htmlspecialchars($turl) ?>" target="_blank" rel="noopener">Voir l'outil</a><?php endif; ?>
        <a class="rp-btn" href="/facturation.php?plan=mission&tool=<?= urlencode($tool) ?>">Lancer la mission · 79€ →</a>
      </div>
    </div>
  </div>
  <?php endforeach; ?>

  <!-- Plan d'action -->
  <?php if ($plan): ?>
  <h2 class="rp-section-title">Votre <strong>feuille de route</strong> sur 12 mois</h2>
  <div class="rp-plan">
    <?php foreach (['month_1' => 'Mois 1', 'month_3' => 'Mois 3', 'month_6' => 'Mois 6', 'month_12' => 'Mois 12'] as $k => $label):
        $items = $plan[$k] ?? []; if (!$items) continue; ?>
    <div class="rp-plan-col">
      <h4><?= $label ?></h4>
      <ul><?php foreach ((array)$items as $it): ?><li><?= htmlspecialchars($it) ?></li><?php endforeach; ?></ul>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <?php if ($compet): ?>
  <h2 class="rp-section-title">Où en sont vos <strong>concurrents</strong></h2>
  <div class="rp-summary" style="border-left-color:#0EA5E9"><?= nl2br(htmlspecialchars($compet)) ?></div>
  <?php endif; ?>

  <!-- Forfait Lancement -->
  <div class="rp-pack rp-noprint">
    <div>
      <h3>Forfait Lancement · 3 outils mis en action</h3>
      <p>Choisissez vos 3 outils prioritaires : Milo les met en place avec vous, un par un, jusqu'au premier résultat.
      Inclut 90 jours d'assistance complète. L'option la plus choisie pour passer du rapport aux résultats.</p>
    </div>
    <div style="text-align:center">
      <div class="price">199€ <small>· 3 missions + 90 j</small></div>
      <a class="rp-btn" style="margin-top:10px" href="/facturation.php?plan=lancement">Démarrer le forfait →</a>
    </div>
  </div>

  <div class="rp-note">
    Accès à vie à ce rapport via votre lien sécurisé · Assistance Milo incluse 30 jours ·
    <a href="/compte/" style="color:var(--green-deep)">Accéder à mon espace</a><br>
    Une question ? <a href="mailto:contact@abys.ai" style="color:var(--green-deep)">contact@abys.ai</a> · Satisfait ou remboursé 14 jours
  </div>

</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
