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
    <a href="/" class="btn btn-primary">Retour à l'accueil</a>
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

// Totaux
$tot_h = 0; $tot_eur = 0; $tot_roi = (int)($content['total_roi_12m'] ?? 0);
$max_h = 0.001;
foreach ($opps as $o) {
    $tot_h   += (float)($o['time_saved_h_week'] ?? 0);
    $tot_eur += (float)($o['money_saved_eur_month'] ?? 0);
    $max_h    = max($max_h, (float)($o['time_saved_h_week'] ?? 0));
}
if (!$tot_roi) $tot_roi = (int)round($tot_eur * 12);
$fmt = fn($n) => number_format((float)$n, 0, ',', ' ');

// Logo officiel de l'outil via son domaine (fallback initiale si indisponible)
$logo_of = function(array $o): array {
    $url  = $o['tool_url'] ?? '';
    $host = $url ? parse_url($url, PHP_URL_HOST) : '';
    $host = $host ? preg_replace('/^www\./', '', $host) : '';
    $src  = $host ? 'https://logo.clearbit.com/' . $host . '?size=128' : '';
    $fav  = $host ? 'https://www.google.com/s2/favicons?domain=' . $host . '&sz=128' : '';
    $init = mb_strtoupper(mb_substr(trim($o['tool'] ?? '?'), 0, 1));
    return ['src' => $src, 'fav' => $fav, 'init' => $init];
};

// Courbe ROI cumulée sur 12 mois (montée progressive vers le ROI total)
$roi_pts = [];
for ($m = 0; $m <= 12; $m++) {
    // montée légèrement accélérée (les gains s'installent) : courbe douce
    $frac = $m / 12;
    $val  = $tot_roi * (0.15 * $frac + 0.85 * $frac * $frac); // convexe
    $roi_pts[] = $val;
}
?>

<style>
:root{ --rp-deep1:#041712; --rp-deep2:#052E16; --rp-deep3:#064E3B; }
.rp-wrap{ max-width:1000px; margin:0 auto; padding:0 22px 90px; }

/* ── En-tête premium sombre ───────────────────────────────── */
.rp-hero{ position:relative; margin:0 -22px 34px; padding:44px 46px 40px; overflow:hidden;
  background:linear-gradient(160deg,#041712 0%,#052E16 55%,#07231a 100%); color:#fff; }
.rp-hero::before{ content:''; position:absolute; inset:0; pointer-events:none;
  background:repeating-conic-gradient(from 205deg at 74% -6%,
    transparent 0deg,transparent 7deg,rgba(52,211,153,.10) 10deg,rgba(14,165,233,.12) 12deg,transparent 15deg,transparent 22deg);
  -webkit-mask-image:radial-gradient(120% 90% at 74% -6%,#000 12%,transparent 70%);
          mask-image:radial-gradient(120% 90% at 74% -6%,#000 12%,transparent 70%); }
.rp-hero-in{ position:relative; z-index:2; display:flex; justify-content:space-between; align-items:center; gap:28px; flex-wrap:wrap; }
.rp-badge{ display:inline-flex; align-items:center; gap:8px; font-size:11px; font-weight:700; letter-spacing:.12em; text-transform:uppercase;
  color:#6EE7B7; background:rgba(16,185,129,.14); border:1px solid rgba(16,185,129,.3); border-radius:30px; padding:6px 13px; margin-bottom:16px; }
.rp-hero h1{ font-size:clamp(26px,3.4vw,38px); font-weight:300; letter-spacing:-.03em; line-height:1.12; margin:0 0 8px; }
.rp-hero h1 strong{ font-weight:800; }
.rp-hero .meta{ font-size:13px; color:rgba(255,255,255,.55); }
.rp-hero .meta a{ color:#6EE7B7; }
/* Anneau de score animé */
.rp-ring{ position:relative; width:132px; height:132px; flex-shrink:0; }
.rp-ring svg{ width:132px; height:132px; transform:rotate(-90deg); }
.rp-ring .track{ fill:none; stroke:rgba(255,255,255,.12); stroke-width:9; }
.rp-ring .prog{ fill:none; stroke:url(#rpgrad); stroke-width:9; stroke-linecap:round;
  stroke-dasharray:327; stroke-dashoffset:327; transition:stroke-dashoffset 1.6s cubic-bezier(.22,1,.36,1); }
.rp-ring .lbl{ position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; }
.rp-ring .lbl b{ font-size:40px; font-weight:800; line-height:1; }
.rp-ring .lbl span{ font-size:10.5px; letter-spacing:.14em; text-transform:uppercase; color:rgba(255,255,255,.5); margin-top:3px; }

/* ── Bandeau chiffres animés ─────────────────────────────── */
.rp-stats{ display:grid; grid-template-columns:repeat(3,1fr); gap:14px; margin:0 -22px 30px; padding:0 22px; }
@media(max-width:640px){ .rp-stats{ grid-template-columns:1fr; } }
.rp-stat{ background:#fff; border:1px solid var(--border,#E5E7EB); border-radius:18px; padding:22px 24px; box-shadow:0 6px 24px -12px rgba(2,30,20,.25); }
.rp-stat b{ display:block; font-size:34px; font-weight:800; letter-spacing:-.03em; line-height:1;
  background:linear-gradient(135deg,#059669,#0EA5E9); -webkit-background-clip:text; background-clip:text; -webkit-text-fill-color:transparent; }
.rp-stat span{ font-size:12.5px; color:var(--ink-3,#6B7280); }
.rp-stat small{ display:block; font-size:11px; color:var(--ink-4,#9CA3AF); margin-top:2px; }

/* ── Milo ────────────────────────────────────────────────── */
.rp-milo{ display:flex; gap:15px; align-items:flex-start; background:linear-gradient(135deg,#0A1F1A,#064E3B);
  border-radius:18px; padding:22px 24px; color:#fff; margin-bottom:34px; }
.rp-milo .av{ width:46px; height:46px; border-radius:50%; background:rgba(16,185,129,.2); border:2px solid #10B981;
  display:flex; align-items:center; justify-content:center; font-weight:800; font-size:17px; color:#34D399; flex-shrink:0; }
.rp-milo p{ font-size:14px; line-height:1.65; color:rgba(255,255,255,.85); margin:4px 0 0; }
.rp-milo b{ color:#6EE7B7; }
.rp-milo .tag{ display:inline-block; font-size:9.5px; font-weight:700; letter-spacing:.08em; text-transform:uppercase;
  background:rgba(52,211,153,.15); color:#6EE7B7; border:1px solid rgba(52,211,153,.4); border-radius:20px; padding:2px 8px; margin-left:8px; }

.rp-h2{ font-size:23px; font-weight:300; letter-spacing:-.03em; margin:44px 0 16px; }
.rp-h2 strong{ font-weight:800; }
.rp-summary{ background:#fff; border:1px solid var(--border,#E5E7EB); border-left:4px solid #10B981; border-radius:14px;
  padding:20px 24px; font-size:15px; line-height:1.75; color:var(--ink-2,#1F2937); }

/* ── Courbe ROI animée ───────────────────────────────────── */
.rp-chart{ background:#fff; border:1px solid var(--border,#E5E7EB); border-radius:18px; padding:26px 26px 18px; box-shadow:0 6px 24px -14px rgba(2,30,20,.2); }
.rp-chart-top{ display:flex; justify-content:space-between; align-items:baseline; margin-bottom:12px; flex-wrap:wrap; gap:8px; }
.rp-chart-top .t{ font-size:14px; font-weight:700; color:var(--ink-2,#1F2937); }
.rp-chart-top .v{ font-size:22px; font-weight:800; color:#059669; letter-spacing:-.02em; }
.rp-chart svg{ width:100%; height:180px; display:block; }
.rp-chart .area{ opacity:0; transition:opacity 1.2s ease .3s; }
.rp-chart.in .area{ opacity:1; }
.rp-chart .line{ fill:none; stroke:url(#rpline); stroke-width:3; stroke-linecap:round;
  stroke-dasharray:1400; stroke-dashoffset:1400; transition:stroke-dashoffset 1.8s cubic-bezier(.3,1,.4,1); }
.rp-chart.in .line{ stroke-dashoffset:0; }
.rp-chart .xlbl{ font-size:10px; fill:var(--ink-4,#9CA3AF); }
.rp-chart .dot{ fill:#10B981; opacity:0; transition:opacity .4s ease 1.6s; }
.rp-chart.in .dot{ opacity:1; }

/* ── Opportunités ────────────────────────────────────────── */
.rp-opp{ background:#fff; border:1px solid var(--border,#E5E7EB); border-radius:20px; padding:24px 26px; margin-bottom:18px;
  opacity:0; transform:translateY(14px); transition:opacity .5s ease, transform .5s ease; }
.rp-opp.in{ opacity:1; transform:none; }
.rp-opp-head{ display:flex; gap:16px; align-items:flex-start; }
.rp-logo{ width:52px; height:52px; border-radius:13px; flex-shrink:0; background:#F3FBF8; border:1px solid var(--border,#E5E7EB);
  display:flex; align-items:center; justify-content:center; overflow:hidden; }
.rp-logo img{ width:100%; height:100%; object-fit:contain; padding:8px; box-sizing:border-box; }
.rp-logo .fb{ width:100%; height:100%; display:none; align-items:center; justify-content:center; font-weight:800; font-size:22px;
  color:#fff; background:linear-gradient(135deg,#10B981,#0EA5E9); }
.rp-opp-title{ flex:1; min-width:0; }
.rp-opp-num{ font-size:11px; font-weight:700; letter-spacing:.06em; color:#10B981; }
.rp-opp h3{ font-size:20px; font-weight:800; letter-spacing:-.02em; margin:2px 0 2px; }
.rp-opp-cat{ font-size:12.5px; color:var(--ink-4,#9CA3AF); }
.rp-chips{ display:flex; gap:8px; flex-wrap:wrap; margin-top:2px; }
.rp-chip{ font-size:11.5px; font-weight:600; border-radius:20px; padding:4px 11px; background:rgba(16,185,129,.08); color:#065F46; border:1px solid rgba(16,185,129,.18); white-space:nowrap; }
.rp-opp p.desc{ font-size:14.5px; line-height:1.7; color:var(--ink-3,#4B5563); margin:14px 0 12px; }
/* barre de gain animée */
.rp-bar{ display:flex; align-items:center; gap:12px; margin:2px 0 4px; }
.rp-bar .bt{ font-size:11.5px; color:var(--ink-4,#9CA3AF); width:112px; flex-shrink:0; }
.rp-bar .track{ flex:1; height:9px; border-radius:6px; background:rgba(16,185,129,.1); overflow:hidden; }
.rp-bar .fill{ height:100%; width:0; border-radius:6px; background:linear-gradient(90deg,#10B981,#0EA5E9); transition:width 1.1s cubic-bezier(.3,1,.4,1); }
.rp-bar .bv{ font-size:12px; font-weight:700; color:#059669; width:64px; text-align:right; flex-shrink:0; }
.rp-opp details{ border-top:1px solid var(--border,#E5E7EB); padding-top:14px; margin-top:14px; }
.rp-opp summary{ cursor:pointer; font-size:14px; font-weight:600; color:var(--ink-2,#1F2937); list-style:none; display:flex; align-items:center; gap:8px; }
.rp-opp summary::before{ content:'▸'; color:#10B981; transition:transform .15s; }
.rp-opp details[open] summary::before{ transform:rotate(90deg); }
.rp-steps{ margin:14px 0 0; padding:0; list-style:none; counter-reset:s; }
.rp-steps li{ counter-increment:s; position:relative; padding:0 0 12px 40px; font-size:14px; line-height:1.6; color:var(--ink-2,#1F2937); }
.rp-steps li::before{ content:counter(s); position:absolute; left:0; top:0; width:26px; height:26px; border-radius:50%;
  background:rgba(16,185,129,.1); color:#059669; font-size:12.5px; font-weight:700; display:flex; align-items:center; justify-content:center; }
.rp-tips{ background:rgba(14,165,233,.06); border:1px solid rgba(14,165,233,.15); border-radius:10px; padding:12px 16px; font-size:13px; color:var(--ink-3,#4B5563); line-height:1.6; margin-top:10px; }
.rp-cta{ display:flex; align-items:center; justify-content:space-between; gap:14px; flex-wrap:wrap;
  background:linear-gradient(135deg,rgba(16,185,129,.07),rgba(14,165,233,.05)); border:1px solid rgba(16,185,129,.2);
  border-radius:14px; padding:15px 18px; margin-top:16px; }
.rp-cta .txt{ font-size:13.5px; color:var(--ink-2,#1F2937); line-height:1.5; }
.rp-cta .txt b{ color:#059669; }
.rp-btn{ display:inline-block; background:#10B981; color:#fff; font-weight:600; font-size:14px; border-radius:11px; padding:11px 20px; text-decoration:none; white-space:nowrap; transition:background .15s; }
.rp-btn:hover{ background:#059669; }
.rp-btn.ghost{ background:transparent; color:#059669; border:1px solid rgba(16,185,129,.4); }

/* ── Plan d'action ───────────────────────────────────────── */
.rp-plan{ display:grid; grid-template-columns:repeat(4,1fr); gap:14px; }
@media(max-width:820px){ .rp-plan{ grid-template-columns:repeat(2,1fr); } }
@media(max-width:480px){ .rp-plan{ grid-template-columns:1fr; } }
.rp-plan-col{ background:#fff; border:1px solid var(--border,#E5E7EB); border-radius:14px; padding:18px; }
.rp-plan-col h4{ font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:.08em; color:#059669; margin:0 0 12px; }
.rp-plan-col ul{ margin:0; padding:0 0 0 16px; }
.rp-plan-col li{ font-size:13px; line-height:1.55; color:var(--ink-3,#4B5563); margin-bottom:8px; }

.rp-pack{ background:linear-gradient(135deg,#0A1F1A,#064E3B); border-radius:20px; padding:34px 36px; color:#fff;
  display:flex; justify-content:space-between; align-items:center; gap:24px; flex-wrap:wrap; margin-top:44px; }
.rp-pack h3{ font-size:24px; font-weight:800; letter-spacing:-.02em; margin:0 0 8px; }
.rp-pack p{ font-size:14px; color:rgba(255,255,255,.75); line-height:1.65; margin:0; max-width:520px; }
.rp-pack .price{ font-size:40px; font-weight:800; color:#34D399; }
.rp-pack .price small{ font-size:14px; font-weight:500; color:rgba(255,255,255,.6); }
.rp-note{ text-align:center; font-size:12.5px; color:var(--ink-4,#9CA3AF); margin-top:34px; line-height:1.7; }

@media print{
  nav, footer, .rp-cta, .rp-pack, .rp-noprint{ display:none !important; }
  .rp-hero{ background:#052E16 !important; -webkit-print-color-adjust:exact; print-color-adjust:exact; }
  .rp-opp{ opacity:1 !important; transform:none !important; break-inside:avoid; }
}
</style>

<svg width="0" height="0" style="position:absolute"><defs>
  <linearGradient id="rpgrad" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#34D399"/><stop offset="1" stop-color="#0EA5E9"/></linearGradient>
  <linearGradient id="rpline" x1="0" y1="0" x2="1" y2="0"><stop offset="0" stop-color="#10B981"/><stop offset="1" stop-color="#0EA5E9"/></linearGradient>
  <linearGradient id="rparea" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="rgba(16,185,129,.28)"/><stop offset="1" stop-color="rgba(16,185,129,0)"/></linearGradient>
</defs></svg>

<div class="rp-wrap">

  <!-- En-tête premium -->
  <div class="rp-hero">
    <div class="rp-hero-in">
      <div>
        <div class="rp-badge">Rapport Premium · Accès à vie</div>
        <h1>Plan d'action IA pour <strong><?= htmlspecialchars($domain) ?></strong></h1>
        <div class="meta">
          Généré le <?= date('d/m/Y', strtotime($report['paid_at'])) ?>
          <?= $secteur ? ' · Secteur : ' . htmlspecialchars($secteur) : '' ?>
          · <a href="#" onclick="window.print();return false" class="rp-noprint">Imprimer / PDF</a>
        </div>
      </div>
      <div class="rp-ring" id="rp-ring" data-score="<?= max(0,min(100,$score)) ?>">
        <svg viewBox="0 0 132 132">
          <circle class="track" cx="66" cy="66" r="52"/>
          <circle class="prog" cx="66" cy="66" r="52"/>
        </svg>
        <div class="lbl"><b id="rp-score">0</b><span>score IA</span></div>
      </div>
    </div>
  </div>

  <!-- Chiffres animés -->
  <div class="rp-stats">
    <div class="rp-stat"><b class="cu" data-to="<?= (int)round($tot_h) ?>" data-suffix=" h">0</b><span>récupérées par semaine</span><small>estimation, cumulée</small></div>
    <div class="rp-stat"><b class="cu" data-to="<?= (int)round($tot_eur) ?>" data-suffix=" €">0</b><span>économisés par mois</span><small>estimation, cumulée</small></div>
    <div class="rp-stat"><b class="cu" data-to="<?= (int)round($tot_roi) ?>" data-suffix=" €">0</b><span>de valeur sur 12 mois</span><small>projection</small></div>
  </div>

  <!-- Milo -->
  <div class="rp-milo">
    <div class="av">M</div>
    <div>
      <div style="font-size:15px;font-weight:700">Milo, votre copilote de mise en action<span class="tag">IA</span></div>
      <p>Ce rapport n'est pas fait pour être lu puis rangé. Pour chaque outil ci-dessous, je peux vous accompagner
      pas à pas jusqu'à ce qu'il tourne vraiment dans votre entreprise : création du compte, paramétrage, premier résultat.
      <b>Votre accès à mon assistance est inclus pendant 30 jours.</b></p>
    </div>
  </div>

  <?php if ($summary): ?>
  <h2 class="rp-h2">L'<strong>essentiel</strong> en 30 secondes</h2>
  <div class="rp-summary"><?= nl2br(htmlspecialchars($summary)) ?></div>
  <?php endif; ?>

  <!-- Courbe ROI animée -->
  <?php if ($tot_roi > 0): ?>
  <h2 class="rp-h2">La <strong>valeur générée</strong>, mois après mois</h2>
  <div class="rp-chart" id="rp-chart">
    <div class="rp-chart-top">
      <div class="t">Valeur cumulée estimée sur 12 mois</div>
      <div class="v"><?= $fmt($tot_roi) ?> €</div>
    </div>
    <?php
      // Construit le path SVG (viewBox 0..600 x, 0..160 y ; y inversé)
      $W=600; $H=160; $pad=8; $n=count($roi_pts)-1;
      $maxv = max($roi_pts) ?: 1;
      $coords=[];
      foreach ($roi_pts as $i=>$v){
        $x = $pad + ($W-2*$pad) * ($i/$n);
        $y = ($H-14) - ($H-24) * ($v/$maxv);
        $coords[] = [round($x,1), round($y,1)];
      }
      $line = 'M ' . implode(' L ', array_map(fn($c)=>$c[0].' '.$c[1], $coords));
      $area = $line . ' L '.$coords[$n][0].' '.($H-6).' L '.$coords[0][0].' '.($H-6).' Z';
      $last = $coords[$n];
    ?>
    <svg viewBox="0 0 600 160" preserveAspectRatio="none">
      <path class="area" d="<?= $area ?>" fill="url(#rparea)"/>
      <path class="line" d="<?= $line ?>"/>
      <circle class="dot" cx="<?= $last[0] ?>" cy="<?= $last[1] ?>" r="5"/>
    </svg>
    <div style="display:flex;justify-content:space-between;font-size:10.5px;color:var(--ink-4,#9CA3AF);margin-top:4px">
      <span>Aujourd'hui</span><span>Mois 3</span><span>Mois 6</span><span>Mois 9</span><span>Mois 12</span>
    </div>
  </div>
  <?php endif; ?>

  <!-- Opportunités -->
  <h2 class="rp-h2">Vos <strong><?= count($opps) ?> opportunités</strong>, outil par outil</h2>

  <?php foreach ($opps as $i => $o):
      $tool  = $o['tool'] ?? 'Outil IA';
      $turl  = $o['tool_url'] ?? '';
      $tut   = $o['tutorial'] ?? [];
      $steps = $tut['steps'] ?? [];
      $tips  = $tut['tips'] ?? [];
      $lg    = $logo_of($o);
      $h     = (float)($o['time_saved_h_week'] ?? 0);
      $barpct = (int)round(min(100, ($h / $max_h) * 100));
  ?>
  <div class="rp-opp" data-reveal>
    <div class="rp-opp-head">
      <div class="rp-logo">
        <?php if ($lg['src']): ?><img src="<?= htmlspecialchars($lg['src']) ?>" alt="<?= htmlspecialchars($tool) ?>" loading="lazy" data-fav="<?= htmlspecialchars($lg['fav']) ?>" onerror="if(this.dataset.fav){this.src=this.dataset.fav;this.removeAttribute('data-fav');}else{this.style.display='none';this.nextElementSibling.style.display='flex';}"><?php endif; ?>
        <div class="fb" style="<?= $lg['src'] ? '' : 'display:flex' ?>"><?= htmlspecialchars($lg['init']) ?></div>
      </div>
      <div class="rp-opp-title">
        <div class="rp-opp-num">OPPORTUNITÉ <?= $i + 1 ?></div>
        <h3><?= htmlspecialchars($tool) ?></h3>
        <div class="rp-opp-cat"><?= htmlspecialchars($o['category'] ?? '') ?></div>
      </div>
      <div class="rp-chips">
        <?php if (!empty($o['difficulty'])): ?><span class="rp-chip"><?= htmlspecialchars($o['difficulty']) ?></span><?php endif; ?>
        <?php if (!empty($tut['estimated_setup_time'])): ?><span class="rp-chip">⏱ <?= htmlspecialchars($tut['estimated_setup_time']) ?></span><?php endif; ?>
      </div>
    </div>

    <p class="desc"><?= htmlspecialchars($o['description'] ?? '') ?></p>

    <?php if ($h > 0): ?>
    <div class="rp-bar">
      <span class="bt">Temps récupéré</span>
      <span class="track"><span class="fill" data-w="<?= $barpct ?>"></span></span>
      <span class="bv"><?= $fmt($h) ?> h/sem</span>
    </div>
    <?php endif; ?>
    <?php if (!empty($o['money_saved_eur_month'])): ?>
    <div class="rp-bar">
      <span class="bt">Argent économisé</span>
      <span class="track"><span class="fill" data-w="<?= (int)round(min(100, ((float)$o['money_saved_eur_month'] / max(1,$tot_eur)) * 100)) ?>"></span></span>
      <span class="bv"><?= $fmt($o['money_saved_eur_month']) ?> €/mois</span>
    </div>
    <?php endif; ?>

    <?php if ($steps): ?>
    <details>
      <summary><?= htmlspecialchars($tut['title'] ?? 'Guide de mise en place') ?></summary>
      <ol class="rp-steps">
        <?php foreach ($steps as $s): ?><li><?= htmlspecialchars($s) ?></li><?php endforeach; ?>
      </ol>
      <?php if ($tips): ?><div class="rp-tips"><b>Conseils :</b> <?= htmlspecialchars(implode(' · ', $tips)) ?></div><?php endif; ?>
    </details>
    <?php endif; ?>

    <div class="rp-cta rp-noprint">
      <div class="txt"><b>Mission lancement avec Milo</b> : cet outil installé, paramétré et actif dans votre entreprise, guidé jusqu'au premier résultat. Satisfait ou remboursé.</div>
      <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
        <?php if ($turl): ?><a class="rp-btn ghost" href="<?= htmlspecialchars($turl) ?>" target="_blank" rel="noopener">Voir l'outil</a><?php endif; ?>
        <a class="rp-btn" href="/facturation.php?plan=mission&tool=<?= urlencode($tool) ?>">Lancer la mission · 79€</a>
      </div>
    </div>
  </div>
  <?php endforeach; ?>

  <!-- Plan d'action -->
  <?php if ($plan): ?>
  <h2 class="rp-h2">Votre <strong>feuille de route</strong> sur 12 mois</h2>
  <div class="rp-plan">
    <?php foreach (['month_1'=>'Mois 1','month_3'=>'Mois 3','month_6'=>'Mois 6','month_12'=>'Mois 12'] as $k=>$label):
        $items = $plan[$k] ?? []; if (!$items) continue; ?>
    <div class="rp-plan-col">
      <h4><?= $label ?></h4>
      <ul><?php foreach ((array)$items as $it): ?><li><?= htmlspecialchars($it) ?></li><?php endforeach; ?></ul>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <?php if ($compet): ?>
  <h2 class="rp-h2">Où en sont vos <strong>concurrents</strong></h2>
  <div class="rp-summary" style="border-left-color:#0EA5E9"><?= nl2br(htmlspecialchars($compet)) ?></div>
  <?php endif; ?>

  <!-- Forfait Lancement -->
  <div class="rp-pack rp-noprint">
    <div>
      <h3>Forfait Lancement · 3 outils mis en action</h3>
      <p>Choisissez vos 3 outils prioritaires : Milo les met en place avec vous, un par un, jusqu'au premier résultat. Inclut 90 jours d'assistance complète.</p>
    </div>
    <div style="text-align:center">
      <div class="price">199€ <small>· 3 missions + 90 j</small></div>
      <a class="rp-btn" style="margin-top:10px" href="/facturation.php?plan=lancement">Démarrer le forfait</a>
    </div>
  </div>

  <div class="rp-note">
    Accès à vie à ce rapport via votre lien sécurisé · Assistance Milo incluse 30 jours ·
    <a href="/compte/" style="color:#059669">Accéder à mon espace</a><br>
    Une question ? <a href="mailto:contact@abys.ai" style="color:#059669">contact@abys.ai</a> · Satisfait ou remboursé 14 jours
  </div>

</div>

<script>
(function(){
  var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  function countUp(el){
    var to = parseFloat(el.dataset.to)||0, suf = el.dataset.suffix||'', dur = 1400, t0 = null;
    if (reduce){ el.textContent = to.toLocaleString('fr-FR')+suf; return; }
    function step(ts){ if(!t0)t0=ts; var p=Math.min(1,(ts-t0)/dur); var e=1-Math.pow(1-p,3);
      el.textContent = Math.round(to*e).toLocaleString('fr-FR')+suf; if(p<1) requestAnimationFrame(step); }
    requestAnimationFrame(step);
  }

  var io = new IntersectionObserver(function(entries){
    entries.forEach(function(en){
      if(!en.isIntersecting) return;
      var el = en.target;
      if(el.classList.contains('cu')) countUp(el);
      else if(el.id==='rp-chart') el.classList.add('in');
      else if(el.hasAttribute('data-reveal')){ el.classList.add('in');
        el.querySelectorAll('.fill').forEach(function(f){ f.style.width=(f.dataset.w||0)+'%'; }); }
      io.unobserve(el);
    });
  }, {threshold:.25});

  document.querySelectorAll('.cu, #rp-chart, [data-reveal]').forEach(function(el){ io.observe(el); });

  // Anneau de score
  var ring = document.getElementById('rp-ring');
  if(ring){
    var sc = parseInt(ring.dataset.score)||0, C=327;
    var prog = ring.querySelector('.prog'), num = document.getElementById('rp-score');
    var ro = new IntersectionObserver(function(e){ e.forEach(function(en){ if(!en.isIntersecting) return;
      prog.style.strokeDashoffset = C*(1 - sc/100);
      if(reduce){ num.textContent=sc; ro.disconnect(); return; }
      var t0=null; function s(ts){ if(!t0)t0=ts; var p=Math.min(1,(ts-t0)/1300); var e2=1-Math.pow(1-p,3);
        num.textContent=Math.round(sc*e2); if(p<1) requestAnimationFrame(s); } requestAnimationFrame(s);
      ro.disconnect(); }); }, {threshold:.4});
    ro.observe(ring);
  }
})();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
