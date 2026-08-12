<?php
$page_title = 'Votre rapport ABYS · Plan d\'action IA';
include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/nav.php';
require_once __DIR__ . '/api/db.php';

$token = $_GET['token'] ?? '';
if (!$token) { header('Location: /'); exit; }

$stmt = get_db()->prepare("
    SELECT r.*, l.url, l.secteur, a.score, a.recommendations
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

// Totaux + max pour les barres
$tot_h = 0; $tot_eur = 0; $tot_roi = (int)($content['total_roi_12m'] ?? 0);
$max_h = 0.001;
foreach ($opps as $o) {
    $tot_h   += (float)($o['time_saved_h_week'] ?? 0);
    $tot_eur += (float)($o['money_saved_eur_month'] ?? 0);
    $max_h    = max($max_h, (float)($o['time_saved_h_week'] ?? 0));
}
if (!$tot_roi) $tot_roi = (int)round($tot_eur * 12);
$fmt = fn($n) => number_format((float)$n, 0, ',', ' ');

// Opportunités triées par heures (pour l'infographie de répartition)
$opps_sorted = $opps;
usort($opps_sorted, fn($a,$b) => ((float)($b['time_saved_h_week']??0)) <=> ((float)($a['time_saved_h_week']??0)));

// Logo officiel servi depuis notre domaine (proxy + cache serveur : toujours une image)
$logo_of = function(array $o): array {
    $url  = $o['tool_url'] ?? '';
    $host = $url ? parse_url($url, PHP_URL_HOST) : '';
    $host = $host ? preg_replace('/^www\./', '', $host) : '';
    $src  = $host ? '/api/logo.php?d=' . rawurlencode($host) : '';
    $init = mb_strtoupper(mb_substr(trim($o['tool'] ?? '?'), 0, 1));
    return ['src' => $src, 'init' => $init];
};

// Données concurrents (robustes) : moyenne des valeurs non nulles
$comp_vals = [];
foreach ($opps as $o) { $cp = (int)($o['competitors_using_pct'] ?? 0); if ($cp > 0) $comp_vals[] = $cp; }
$has_comp = count($comp_vals) > 0;
$comp_avg = $has_comp ? (int)round(array_sum($comp_vals) / count($comp_vals)) : 0;

// Journée avant/après (générée à l'audit, réutilisée ici sans coût supplémentaire)
$audit_raw  = json_decode($report['recommendations'] ?? '', true) ?: [];
$transfos    = $audit_raw['transformations'] ?? [];
$day_verdict = $audit_raw['transformations_verdict'] ?? ($audit_raw['day_verdict'] ?? '');

// Courbe ROI cumulée sur 12 mois
$roi_pts = [];
for ($m = 0; $m <= 12; $m++) {
    $frac = $m / 12;
    $roi_pts[] = round($tot_roi * (0.15 * $frac + 0.85 * $frac * $frac));
}
?>

<style>
:root{ --rp-deep1:#041712; --rp-deep2:#052E16; --rp-deep3:#064E3B; }
.rp-wrap{ max-width:1000px; margin:0 auto; padding:0 22px 90px; }

/* ── En-tête premium sombre + faisceaux animés + photo ────── */
.rp-hero{ position:relative; margin:0 -22px 34px; padding:44px 46px 40px; overflow:hidden; color:#fff;
  background:linear-gradient(160deg,#041712 0%,#052E16 55%,#07231a 100%); }
.rp-hero .bg{ position:absolute; inset:0; background:url('/assets/img/success-bg.jpg') center/cover no-repeat; opacity:.28; }
.rp-hero .beams{ position:absolute; inset:0; overflow:hidden; pointer-events:none; }
.rp-hero .beams span{ position:absolute; top:-40%; left:var(--l); width:120px; height:190%; transform-origin:top center; transform:rotate(var(--a)); }
.rp-hero .beams span::before{ content:''; position:absolute; inset:0;
  background:linear-gradient(to bottom, rgba(155,247,208,.30), rgba(58,206,231,.10) 55%, transparent 82%);
  -webkit-mask-image:linear-gradient(to right, transparent, #000 42%, #000 58%, transparent);
          mask-image:linear-gradient(to right, transparent, #000 42%, #000 58%, transparent);
  filter:blur(7px); mix-blend-mode:screen; transform-origin:top center; will-change:transform;
  animation:rp-ray var(--d) ease-in-out var(--delay,0s) infinite alternate; }
@keyframes rp-ray{ from{ transform:rotate(calc(var(--s) * -1)); } to{ transform:rotate(var(--s)); } }
@media (prefers-reduced-motion: reduce){ .rp-hero .beams span::before{ animation:none; } }
.rp-hero-in{ position:relative; z-index:2; display:flex; justify-content:space-between; align-items:center; gap:28px; flex-wrap:wrap; }
.rp-badge{ display:inline-flex; align-items:center; gap:8px; font-size:11px; font-weight:700; letter-spacing:.12em; text-transform:uppercase;
  color:#6EE7B7; background:rgba(16,185,129,.14); border:1px solid rgba(16,185,129,.3); border-radius:30px; padding:6px 13px; margin-bottom:16px; }
.rp-hero h1{ font-size:clamp(26px,3.4vw,38px); font-weight:300; letter-spacing:-.03em; line-height:1.12; margin:0 0 8px; }
.rp-hero h1 strong{ font-weight:800; }
.rp-hero .meta{ font-size:13px; color:rgba(255,255,255,.6); }
.rp-hero .meta a{ color:#6EE7B7; }
.rp-ring{ position:relative; width:132px; height:132px; flex-shrink:0; }
.rp-ring svg{ width:132px; height:132px; transform:rotate(-90deg); }
.rp-ring .track{ fill:none; stroke:rgba(255,255,255,.14); stroke-width:9; }
.rp-ring .prog{ fill:none; stroke:url(#rpgrad); stroke-width:9; stroke-linecap:round; stroke-dasharray:327; stroke-dashoffset:327; transition:stroke-dashoffset 1.6s cubic-bezier(.22,1,.36,1); }
.rp-ring .lbl{ position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; }
.rp-ring .lbl b{ font-size:40px; font-weight:800; line-height:1; }
.rp-ring .lbl span{ font-size:10.5px; letter-spacing:.14em; text-transform:uppercase; color:rgba(255,255,255,.55); margin-top:3px; }

/* ── Chiffres animés ─────────────────────────────────────── */
.rp-stats{ display:grid; grid-template-columns:repeat(3,1fr); gap:14px; margin:0 -22px 30px; padding:0 22px; }
@media(max-width:640px){ .rp-stats{ grid-template-columns:1fr; } }
.rp-stat{ position:relative; background:#fff; border:1px solid var(--border,#E5E7EB); border-radius:18px; padding:22px 24px; box-shadow:0 6px 24px -12px rgba(2,30,20,.25); overflow:hidden; }
.rp-stat .ic{ position:absolute; top:16px; right:16px; opacity:.16; }
.rp-stat b{ display:block; font-size:34px; font-weight:800; letter-spacing:-.03em; line-height:1;
  background:linear-gradient(135deg,#059669,#0EA5E9); -webkit-background-clip:text; background-clip:text; -webkit-text-fill-color:transparent; }
.rp-stat span{ font-size:12.5px; color:var(--ink-3,#6B7280); }
.rp-stat small{ display:block; font-size:11px; color:var(--ink-4,#9CA3AF); margin-top:2px; }

.rp-milo{ display:flex; gap:15px; align-items:flex-start; background:linear-gradient(135deg,#0A1F1A,#064E3B); border-radius:18px; padding:22px 24px; color:#fff; margin-bottom:34px; }
.rp-milo .av{ width:60px; height:60px; border-radius:50%; border:2px solid #10B981; overflow:hidden; flex-shrink:0; background:#052E16; box-shadow:0 0 0 4px rgba(16,185,129,.14); }
.rp-milo .av img{ width:100%; height:100%; object-fit:cover; display:block; }
.rp-milo p{ font-size:14px; line-height:1.65; color:rgba(255,255,255,.85); margin:4px 0 0; }
.rp-milo b{ color:#6EE7B7; }
.rp-milo .tag{ display:inline-block; font-size:9.5px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; background:rgba(52,211,153,.15); color:#6EE7B7; border:1px solid rgba(52,211,153,.4); border-radius:20px; padding:2px 8px; margin-left:8px; }

.rp-h2{ font-size:23px; font-weight:300; letter-spacing:-.03em; margin:44px 0 16px; }
.rp-h2 strong{ font-weight:800; }
.rp-card{ background:#fff; border:1px solid var(--border,#E5E7EB); border-radius:18px; padding:24px 26px; box-shadow:0 6px 24px -16px rgba(2,30,20,.2); }
.rp-summary{ background:#fff; border:1px solid var(--border,#E5E7EB); border-left:4px solid #10B981; border-radius:14px; padding:20px 24px; font-size:15px; line-height:1.75; color:var(--ink-2,#1F2937); }

/* ── Infographie répartition du temps ────────────────────── */
.rp-split .row{ display:flex; align-items:center; gap:13px; padding:11px 0; border-top:1px solid var(--border,#F0F3F1); }
.rp-split .row:first-child{ border-top:none; }
.rp-split .lg{ width:38px; height:38px; border-radius:10px; background:#F3FBF8; border:1px solid var(--border,#E5E7EB); display:flex; align-items:center; justify-content:center; overflow:hidden; flex-shrink:0; }
.rp-split .lg img{ width:100%; height:100%; object-fit:contain; padding:6px; box-sizing:border-box; }
.rp-split .lg .fb{ width:100%; height:100%; display:none; align-items:center; justify-content:center; font-weight:800; font-size:15px; color:#fff; background:linear-gradient(135deg,#10B981,#0EA5E9); }
.rp-split .nm{ width:120px; font-size:13.5px; font-weight:600; color:var(--ink-2,#1F2937); flex-shrink:0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.rp-split .track{ flex:1; height:12px; border-radius:7px; background:rgba(16,185,129,.09); overflow:hidden; }
.rp-split .fill{ height:100%; width:0; border-radius:7px; background:linear-gradient(90deg,#10B981,#0EA5E9); transition:width 1.1s cubic-bezier(.3,1,.4,1); }
.rp-split .val{ width:70px; text-align:right; font-size:13px; font-weight:800; color:#059669; flex-shrink:0; }

/* ── Courbe ROI ──────────────────────────────────────────── */
.rp-chart-top{ display:flex; justify-content:space-between; align-items:baseline; margin-bottom:14px; flex-wrap:wrap; gap:8px; }
.rp-chart-top .t{ font-size:14px; font-weight:700; color:var(--ink-2,#1F2937); }
.rp-chart-top .v{ font-size:24px; font-weight:800; color:#059669; letter-spacing:-.02em; }
.rp-chart{ position:relative; }
.rp-chart svg.plot{ width:100%; height:210px; display:block; overflow:visible; }
.rp-grid line{ stroke:#EEF2F0; stroke-width:1; }
.rp-glabel{ font-size:10px; fill:var(--ink-4,#9CA3AF); }
.rp-area{ opacity:0; transition:opacity 1.1s ease .3s; } .rp-chart.in .rp-area{ opacity:1; }
.rp-line{ fill:none; stroke:url(#rpline); stroke-width:3; stroke-linecap:round; stroke-dasharray:1600; stroke-dashoffset:1600; transition:stroke-dashoffset 1.8s cubic-bezier(.3,1,.4,1); }
.rp-chart.in .rp-line{ stroke-dashoffset:0; }
.rp-dot{ fill:#10B981; stroke:#fff; stroke-width:2; opacity:0; transition:opacity .4s ease 1.5s; } .rp-chart.in .rp-dot{ opacity:1; }
.rp-cross{ stroke:#10B981; stroke-width:1; stroke-dasharray:3 3; opacity:0; }
.rp-hoverdot{ fill:#059669; stroke:#fff; stroke-width:2; opacity:0; }
.rp-tip{ position:absolute; pointer-events:none; background:#0A1F1A; color:#fff; font-size:12px; font-weight:600; padding:6px 10px; border-radius:8px; transform:translate(-50%,-130%); white-space:nowrap; opacity:0; transition:opacity .12s; box-shadow:0 6px 20px rgba(0,0,0,.25); }
.rp-tip small{ display:block; font-weight:400; color:rgba(255,255,255,.6); font-size:10px; }

/* ── Concurrents (graphique) ─────────────────────────────── */
.rp-comp{ display:grid; grid-template-columns:250px 1fr; gap:28px; align-items:center; }
@media(max-width:720px){ .rp-comp{ grid-template-columns:1fr; } }
.rp-gauge{ position:relative; width:200px; height:200px; margin:0 auto; }
.rp-gauge svg{ width:200px; height:200px; transform:rotate(-90deg); }
.rp-gauge .tk{ fill:none; stroke:#EEF2F0; stroke-width:14; }
.rp-gauge .pg{ fill:none; stroke:url(#rpwarm); stroke-width:14; stroke-linecap:round; stroke-dasharray:534; stroke-dashoffset:534; transition:stroke-dashoffset 1.7s cubic-bezier(.22,1,.36,1); }
.rp-gauge .lbl{ position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; }
.rp-gauge .lbl b{ font-size:44px; font-weight:800; color:#0A1F1A; line-height:1; }
.rp-gauge .lbl span{ font-size:11.5px; color:var(--ink-3,#6B7280); max-width:150px; margin-top:6px; line-height:1.4; }
.rp-cbar{ display:flex; align-items:center; gap:13px; padding:9px 0; }
.rp-cbar .cn{ width:130px; font-size:13px; color:var(--ink-2,#1F2937); flex-shrink:0; }
.rp-cbar .track{ flex:1; height:11px; border-radius:6px; background:rgba(245,158,11,.1); overflow:hidden; }
.rp-cbar .fill{ height:100%; width:0; border-radius:6px; background:linear-gradient(90deg,#F59E0B,#EF4444); transition:width 1.1s cubic-bezier(.3,1,.4,1); }
.rp-cbar .cv{ width:44px; text-align:right; font-size:13px; font-weight:800; color:#D97706; flex-shrink:0; }
.rp-comp-note{ font-size:14.5px; line-height:1.7; color:var(--ink-3,#4B5563); margin-top:18px; }

/* ── Opportunités ────────────────────────────────────────── */
.rp-opp{ background:#fff; border:1px solid var(--border,#E5E7EB); border-radius:20px; padding:24px 26px; margin-bottom:18px; opacity:0; transform:translateY(16px); transition:opacity .55s ease, transform .55s ease; }
.rp-opp.in{ opacity:1; transform:none; }
.rp-opp-head{ display:flex; gap:16px; align-items:flex-start; }
.rp-logo{ width:56px; height:56px; border-radius:14px; flex-shrink:0; background:#fff; border:1px solid var(--border,#E5E7EB); display:flex; align-items:center; justify-content:center; overflow:hidden; box-shadow:0 4px 14px -8px rgba(2,30,20,.3); }
.rp-logo img{ width:100%; height:100%; object-fit:contain; padding:9px; box-sizing:border-box; }
.rp-logo .fb{ width:100%; height:100%; display:none; align-items:center; justify-content:center; font-weight:800; font-size:24px; color:#fff; background:linear-gradient(135deg,#10B981,#0EA5E9); }
.rp-opp-title{ flex:1; min-width:0; }
.rp-opp-num{ font-size:11px; font-weight:700; letter-spacing:.06em; color:#10B981; }
.rp-opp h3{ font-size:20px; font-weight:800; letter-spacing:-.02em; margin:2px 0 2px; }
.rp-opp-cat{ font-size:12.5px; color:var(--ink-4,#9CA3AF); }
.rp-chips{ display:flex; gap:8px; flex-wrap:wrap; }
.rp-chip{ font-size:11.5px; font-weight:600; border-radius:20px; padding:4px 11px; background:rgba(16,185,129,.08); color:#065F46; border:1px solid rgba(16,185,129,.18); white-space:nowrap; }
.rp-opp p.desc{ font-size:14.5px; line-height:1.7; color:var(--ink-3,#4B5563); margin:14px 0 12px; }
.rp-bar{ display:flex; align-items:center; gap:12px; margin:2px 0 4px; }
.rp-bar .bt{ font-size:11.5px; color:var(--ink-4,#9CA3AF); width:118px; flex-shrink:0; }
.rp-bar .track{ flex:1; height:9px; border-radius:6px; background:rgba(16,185,129,.1); overflow:hidden; }
.rp-bar .fill{ height:100%; width:0; border-radius:6px; background:linear-gradient(90deg,#10B981,#0EA5E9); transition:width 1.1s cubic-bezier(.3,1,.4,1); }
.rp-bar .bv{ font-size:12px; font-weight:700; color:#059669; width:64px; text-align:right; flex-shrink:0; }
.rp-opp details{ border-top:1px solid var(--border,#E5E7EB); padding-top:14px; margin-top:14px; }
.rp-opp summary{ cursor:pointer; font-size:14px; font-weight:600; color:var(--ink-2,#1F2937); list-style:none; display:flex; align-items:center; gap:8px; }
.rp-opp summary::before{ content:'▸'; color:#10B981; transition:transform .15s; }
.rp-opp details[open] summary::before{ transform:rotate(90deg); }
.rp-steps{ margin:14px 0 0; padding:0; list-style:none; counter-reset:s; }
.rp-steps li{ counter-increment:s; position:relative; padding:0 0 12px 40px; font-size:14px; line-height:1.6; color:var(--ink-2,#1F2937); }
.rp-steps li::before{ content:counter(s); position:absolute; left:0; top:0; width:26px; height:26px; border-radius:50%; background:rgba(16,185,129,.1); color:#059669; font-size:12.5px; font-weight:700; display:flex; align-items:center; justify-content:center; }
.rp-tips{ background:rgba(14,165,233,.06); border:1px solid rgba(14,165,233,.15); border-radius:10px; padding:12px 16px; font-size:13px; color:var(--ink-3,#4B5563); line-height:1.6; margin-top:10px; }
.rp-cta{ display:flex; align-items:center; justify-content:space-between; gap:14px; flex-wrap:wrap; background:linear-gradient(135deg,rgba(16,185,129,.07),rgba(14,165,233,.05)); border:1px solid rgba(16,185,129,.2); border-radius:14px; padding:15px 18px; margin-top:16px; }
.rp-cta .txt{ font-size:13.5px; color:var(--ink-2,#1F2937); line-height:1.5; display:flex; align-items:center; gap:12px; }
.rp-cta .txt b{ color:#059669; }
.rp-milo-mini{ display:none; width:46px; height:46px; border-radius:50%; border:2px solid #10B981; object-fit:cover; flex-shrink:0;
  box-shadow:0 0 0 4px rgba(16,185,129,.14); animation:milo-pop .35s cubic-bezier(.3,1.4,.5,1); }
.rp-milo-mini.on{ display:block; }
@keyframes milo-pop{ from{ transform:scale(.5); opacity:0; } to{ transform:scale(1); opacity:1; } }
.rp-btn{ display:inline-block; background:#10B981; color:#fff; font-weight:600; font-size:14px; border-radius:11px; padding:11px 20px; text-decoration:none; white-space:nowrap; transition:background .15s, transform .15s; }
.rp-btn:hover{ background:#059669; transform:translateY(-1px); }
.rp-btn.ghost{ background:transparent; color:#059669; border:1px solid rgba(16,185,129,.4); }

.rp-plan{ display:grid; grid-template-columns:repeat(4,1fr); gap:14px; }
@media(max-width:820px){ .rp-plan{ grid-template-columns:repeat(2,1fr); } }
@media(max-width:480px){ .rp-plan{ grid-template-columns:1fr; } }
.rp-plan-col{ background:#fff; border:1px solid var(--border,#E5E7EB); border-radius:14px; padding:18px; position:relative; }
.rp-plan-col::before{ content:''; position:absolute; top:0; left:18px; right:18px; height:3px; border-radius:3px; background:linear-gradient(90deg,#10B981,#0EA5E9); }
.rp-plan-col h4{ font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:.08em; color:#059669; margin:6px 0 12px; }
.rp-plan-col ul{ margin:0; padding:0 0 0 16px; }
.rp-plan-col li{ font-size:13px; line-height:1.55; color:var(--ink-3,#4B5563); margin-bottom:8px; }

.rp-pack{ position:relative; overflow:hidden; background:linear-gradient(135deg,#0A1F1A,#064E3B); border-radius:20px; padding:34px 36px; color:#fff; display:flex; justify-content:space-between; align-items:center; gap:24px; flex-wrap:wrap; margin-top:44px; }
.rp-pack .bg{ position:absolute; inset:0; background:url('/assets/img/success-bg.jpg') center/cover; opacity:.22; }
.rp-pack > div{ position:relative; z-index:2; }
.rp-pack h3{ font-size:24px; font-weight:800; letter-spacing:-.02em; margin:0 0 8px; }
.rp-pack p{ font-size:14px; color:rgba(255,255,255,.78); line-height:1.65; margin:0; max-width:520px; }
.rp-pack .price{ font-size:40px; font-weight:800; color:#34D399; }
.rp-pack .price small{ font-size:14px; font-weight:500; color:rgba(255,255,255,.6); }
.rp-note{ text-align:center; font-size:12.5px; color:var(--ink-4,#9CA3AF); margin-top:34px; line-height:1.7; }

@media print{
  nav, footer, .rp-cta, .rp-pack, .rp-noprint, .rp-hero .beams{ display:none !important; }
  .rp-hero{ background:#052E16 !important; -webkit-print-color-adjust:exact; print-color-adjust:exact; }
  .rp-opp{ opacity:1 !important; transform:none !important; break-inside:avoid; }
  .fill,.prog,.pg{ transition:none !important; }
}
</style>

<svg width="0" height="0" style="position:absolute"><defs>
  <linearGradient id="rpgrad" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#34D399"/><stop offset="1" stop-color="#0EA5E9"/></linearGradient>
  <linearGradient id="rpline" x1="0" y1="0" x2="1" y2="0"><stop offset="0" stop-color="#10B981"/><stop offset="1" stop-color="#0EA5E9"/></linearGradient>
  <linearGradient id="rparea" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="rgba(16,185,129,.30)"/><stop offset="1" stop-color="rgba(16,185,129,0)"/></linearGradient>
  <linearGradient id="rpwarm" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#F59E0B"/><stop offset="1" stop-color="#EF4444"/></linearGradient>
</defs></svg>

<div class="rp-wrap">

  <!-- En-tête -->
  <div class="rp-hero">
    <div class="bg"></div>
    <div class="beams" aria-hidden="true">
      <span style="--a:-24deg;--l:52%;--d:8.5s;--s:8deg;--delay:-2s"></span>
      <span style="--a:-10deg;--l:58%;--d:6.5s;--s:10deg;--delay:-5s"></span>
      <span style="--a:4deg;--l:64%;--d:10s;--s:7deg;--delay:-1s"></span>
      <span style="--a:18deg;--l:70%;--d:7.5s;--s:9deg;--delay:-4s"></span>
      <span style="--a:32deg;--l:76%;--d:9.5s;--s:8deg;--delay:-6.5s"></span>
    </div>
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
        <svg viewBox="0 0 132 132"><circle class="track" cx="66" cy="66" r="52"/><circle class="prog" cx="66" cy="66" r="52"/></svg>
        <div class="lbl"><b id="rp-score">0</b><span>score IA</span></div>
      </div>
    </div>
  </div>

  <!-- Chiffres -->
  <div class="rp-stats">
    <div class="rp-stat">
      <svg class="ic" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="1.6"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
      <b class="cu" data-to="<?= (int)round($tot_h) ?>" data-suffix=" h">0</b><span>récupérées par semaine</span><small>estimation cumulée</small>
    </div>
    <div class="rp-stat">
      <svg class="ic" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="1.6"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
      <b class="cu" data-to="<?= (int)round($tot_eur) ?>" data-suffix=" €">0</b><span>économisés par mois</span><small>estimation cumulée</small>
    </div>
    <div class="rp-stat">
      <svg class="ic" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="1.6"><path d="M3 17l6-6 4 4 8-8"/><path d="M21 7v6h-6"/></svg>
      <b class="cu" data-to="<?= (int)round($tot_roi) ?>" data-suffix=" €">0</b><span>de valeur sur 12 mois</span><small>projection</small>
    </div>
  </div>

  <!-- Milo -->
  <div class="rp-milo">
    <div class="av"><img src="/assets/img/milo-avatar.jpg" alt="Milo, votre copilote IA"></div>
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

  <!-- Concrètement, ce qui change (tâche par tâche) -->
  <?php if ($transfos): ?>
  <style>
  .rt-row{ display:grid; grid-template-columns:1fr 40px 1fr; align-items:stretch; margin-bottom:12px; }
  @media(max-width:820px){ .rt-row{ grid-template-columns:1fr; } .rt-mid{ transform:rotate(90deg); margin:2px auto; } }
  .rt-cell{ border-radius:16px; padding:17px 19px; }
  .rt-today{ background:#fff; border:2px solid var(--border,#E5E7EB); }
  .rt-ai{ background:linear-gradient(155deg,#0A1F1A,#064E3B); border:2px solid #10B981; }
  .rt-mid{ display:flex; align-items:center; justify-content:center; color:#10B981; }
  .rt-task{ display:flex; align-items:center; gap:9px; font-size:11.5px; font-weight:800; letter-spacing:.04em; text-transform:uppercase; margin-bottom:8px; }
  .rt-today .rt-task{ color:var(--ink-4,#9CA3AF); } .rt-ai .rt-task{ color:#6EE7B7; }
  .rt-hint{ margin-left:auto; font-size:11px; font-weight:600; text-transform:none; letter-spacing:0; background:rgba(16,185,129,.2); color:#6EE7B7; border-radius:20px; padding:3px 9px; }
  .rt-txt{ font-size:13.5px; line-height:1.65; }
  .rt-today .rt-txt{ color:var(--ink-3,#4B5563); } .rt-ai .rt-txt{ color:rgba(255,255,255,.85); }
  .rt-verdict{ margin-top:16px; background:linear-gradient(135deg,rgba(16,185,129,.09),rgba(14,165,233,.06));
    border:1px solid rgba(16,185,129,.25); border-radius:14px; padding:16px 20px; font-size:15px; line-height:1.6;
    color:var(--ink-2,#1F2937); text-align:center; font-weight:500; }
  @media print{ .rt-ai{ -webkit-print-color-adjust:exact; print-color-adjust:exact; } .rt-row{ break-inside:avoid; } }
  </style>
  <h2 class="rp-h2">Concrètement, <strong>ce qui change</strong></h2>
  <?php foreach ($transfos as $t): ?>
  <div class="rt-row">
    <div class="rt-cell rt-today">
      <div class="rt-task">Aujourd'hui : <?= htmlspecialchars($t['task'] ?? '') ?></div>
      <div class="rt-txt"><?= htmlspecialchars($t['today'] ?? '') ?></div>
    </div>
    <div class="rt-mid" aria-hidden="true">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
    </div>
    <div class="rt-cell rt-ai">
      <div class="rt-task">Avec l'IA<?= !empty($t['time_hint']) ? '<span class="rt-hint">' . htmlspecialchars($t['time_hint']) . '</span>' : '' ?></div>
      <div class="rt-txt"><?= htmlspecialchars($t['with_ai'] ?? '') ?></div>
    </div>
  </div>
  <?php endforeach; ?>
  <?php if ($day_verdict): ?><div class="rt-verdict"><?= htmlspecialchars($day_verdict) ?></div><?php endif; ?>
  <?php endif; ?>

  <!-- Infographie : répartition du temps récupéré -->
  <?php if ($tot_h > 0): ?>
  <h2 class="rp-h2">D'où vient le <strong>temps récupéré</strong></h2>
  <div class="rp-card rp-split" data-reveal>
    <?php foreach ($opps_sorted as $o):
        $h = (float)($o['time_saved_h_week'] ?? 0); if ($h <= 0) continue;
        $lg = $logo_of($o); $pct = (int)round(($h / $max_h) * 100); ?>
    <div class="row">
      <div class="lg">
        <?php if ($lg['src']): ?><img src="<?= htmlspecialchars($lg['src']) ?>" alt="" loading="lazy" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'"><?php endif; ?>
        <div class="fb" style="<?= $lg['src'] ? '' : 'display:flex' ?>"><?= htmlspecialchars($lg['init']) ?></div>
      </div>
      <div class="nm"><?= htmlspecialchars($o['tool'] ?? 'Outil') ?></div>
      <div class="track"><span class="fill" data-w="<?= $pct ?>"></span></div>
      <div class="val"><?= $fmt($h) ?> h/sem</div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <!-- Courbe ROI -->
  <?php if ($tot_roi > 0): ?>
  <h2 class="rp-h2">La <strong>valeur générée</strong>, mois après mois</h2>
  <div class="rp-card rp-chart" id="rp-chart" data-pts='<?= htmlspecialchars(json_encode($roi_pts), ENT_QUOTES) ?>'>
    <div class="rp-chart-top"><div class="t">Valeur cumulée estimée sur 12 mois</div><div class="v"><?= $fmt($tot_roi) ?> €</div></div>
    <?php
      $W=620; $H=210; $padL=8; $padR=8; $padT=10; $padB=22; $n=count($roi_pts)-1; $maxv=max($roi_pts)?:1;
      $X = fn($i) => round($padL + ($W-$padL-$padR) * ($i/$n), 1);
      $Y = fn($v) => round(($H-$padB) - ($H-$padT-$padB) * ($v/$maxv), 1);
      $coords=[]; foreach ($roi_pts as $i=>$v) $coords[]=[$X($i),$Y($v)];
      $line='M '.implode(' L ', array_map(fn($c)=>$c[0].' '.$c[1], $coords));
      $area=$line.' L '.$coords[$n][0].' '.($H-$padB).' L '.$coords[0][0].' '.($H-$padB).' Z';
    ?>
    <svg class="plot" viewBox="0 0 <?= $W ?> <?= $H ?>" preserveAspectRatio="none">
      <g class="rp-grid">
        <?php for($g=0;$g<=3;$g++){ $yy=round($padT+($H-$padT-$padB)*($g/3),1); ?>
        <line x1="0" y1="<?= $yy ?>" x2="<?= $W ?>" y2="<?= $yy ?>"/>
        <?php } ?>
      </g>
      <text class="rp-glabel" x="2" y="<?= $padT+8 ?>"><?= $fmt($maxv) ?> €</text>
      <text class="rp-glabel" x="2" y="<?= $H-$padB-2 ?>">0 €</text>
      <path class="rp-area" d="<?= $area ?>" fill="url(#rparea)"/>
      <path class="rp-line" d="<?= $line ?>"/>
      <circle class="rp-dot" cx="<?= $coords[$n][0] ?>" cy="<?= $coords[$n][1] ?>" r="5"/>
      <line class="rp-cross" id="rp-cross" x1="0" y1="<?= $padT ?>" x2="0" y2="<?= $H-$padB ?>"/>
      <circle class="rp-hoverdot" id="rp-hoverdot" r="5" cx="0" cy="0"/>
      <rect id="rp-hit" x="0" y="0" width="<?= $W ?>" height="<?= $H ?>" fill="transparent"/>
    </svg>
    <div style="display:flex;justify-content:space-between;font-size:10.5px;color:var(--ink-4,#9CA3AF);margin-top:2px">
      <span>Aujourd'hui</span><span>Mois 3</span><span>Mois 6</span><span>Mois 9</span><span>Mois 12</span>
    </div>
    <div class="rp-tip" id="rp-tip"></div>
  </div>
  <?php endif; ?>

  <!-- Opportunités -->
  <h2 class="rp-h2">Vos <strong><?= count($opps) ?> opportunités</strong>, outil par outil</h2>

  <?php foreach ($opps as $i => $o):
      $tool=$o['tool']??'Outil IA'; $turl=$o['tool_url']??''; $tut=$o['tutorial']??[];
      $steps=$tut['steps']??[]; $tips=$tut['tips']??[]; $lg=$logo_of($o);
      $h=(float)($o['time_saved_h_week']??0); $barpct=(int)round(min(100,($h/$max_h)*100));
  ?>
  <div class="rp-opp" data-reveal style="transition-delay:<?= min($i*60,240) ?>ms">
    <div class="rp-opp-head">
      <div class="rp-logo">
        <?php if ($lg['src']): ?><img src="<?= htmlspecialchars($lg['src']) ?>" alt="<?= htmlspecialchars($tool) ?>" loading="lazy" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'"><?php endif; ?>
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
    <div class="rp-bar"><span class="bt">Temps récupéré</span><span class="track"><span class="fill" data-w="<?= $barpct ?>"></span></span><span class="bv"><?= $fmt($h) ?> h/sem</span></div>
    <?php endif; ?>
    <?php if (!empty($o['money_saved_eur_month'])): ?>
    <div class="rp-bar"><span class="bt">Argent économisé</span><span class="track"><span class="fill" data-w="<?= (int)round(min(100,((float)$o['money_saved_eur_month']/max(1,$tot_eur))*100)) ?>"></span></span><span class="bv"><?= $fmt($o['money_saved_eur_month']) ?> €/mois</span></div>
    <?php endif; ?>
    <?php if ($steps): ?>
    <details>
      <summary><?= htmlspecialchars($tut['title'] ?? 'Guide de mise en place') ?></summary>
      <ol class="rp-steps"><?php foreach ($steps as $s): ?><li><?= htmlspecialchars($s) ?></li><?php endforeach; ?></ol>
      <?php if ($tips): ?><div class="rp-tips"><b>Conseils :</b> <?= htmlspecialchars(implode(' · ', $tips)) ?></div><?php endif; ?>
    </details>
    <?php endif; ?>
    <div class="rp-cta rp-noprint">
      <div class="txt"><img class="rp-milo-mini" src="/assets/img/milo-avatar.jpg" alt="Milo"><span class="txt-msg"><b>Mission lancement avec Milo</b> : cet outil installé, paramétré et actif dans votre entreprise, guidé jusqu'au premier résultat. Satisfait ou remboursé.</span></div>
      <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
        <?php if ($turl): ?><a class="rp-btn ghost" href="<?= htmlspecialchars($turl) ?>" target="_blank" rel="noopener">Voir l'outil</a><?php endif; ?>
        <a class="rp-btn rp-mission-btn" data-plan="mission" data-tool="<?= htmlspecialchars($tool, ENT_QUOTES) ?>"
           href="/facturation.php?plan=mission&tool=<?= urlencode($tool) ?>">Lancer la mission · 79€</a>
      </div>
    </div>
  </div>
  <?php endforeach; ?>

  <!-- Plan d'action -->
  <?php if ($plan): ?>
  <h2 class="rp-h2">Votre <strong>feuille de route</strong> sur 12 mois</h2>
  <div class="rp-plan">
    <?php foreach (['month_1'=>'Mois 1','month_3'=>'Mois 3','month_6'=>'Mois 6','month_12'=>'Mois 12'] as $k=>$label):
        $items=$plan[$k]??[]; if(!$items) continue; ?>
    <div class="rp-plan-col"><h4><?= $label ?></h4><ul><?php foreach ((array)$items as $it): ?><li><?= htmlspecialchars($it) ?></li><?php endforeach; ?></ul></div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <!-- Concurrents en graphique -->
  <?php if ($has_comp || $compet): ?>
  <h2 class="rp-h2">Où en sont vos <strong>concurrents</strong></h2>
  <?php if ($has_comp): ?>
  <div class="rp-card rp-comp" data-reveal>
    <div class="rp-gauge" id="rp-gauge" data-pct="<?= $comp_avg ?>">
      <svg viewBox="0 0 200 200"><circle class="tk" cx="100" cy="100" r="85"/><circle class="pg" cx="100" cy="100" r="85"/></svg>
      <div class="lbl"><b id="rp-gnum">0%</b><span>de vos concurrents utilisent déjà ce type d'outil, en moyenne</span></div>
    </div>
    <div>
      <?php foreach ($opps_sorted as $o):
          $cp=(int)($o['competitors_using_pct'] ?? 0); if(!$cp) continue; ?>
      <div class="rp-cbar"><div class="cn"><?= htmlspecialchars($o['category'] ?? $o['tool'] ?? '') ?></div><div class="track"><span class="fill" data-w="<?= max(0,min(100,$cp)) ?>"></span></div><div class="cv"><?= $cp ?>%</div></div>
      <?php endforeach; ?>
      <?php if ($compet): ?><p class="rp-comp-note"><?= nl2br(htmlspecialchars($compet)) ?></p><?php endif; ?>
    </div>
  </div>
  <?php else: ?>
  <div class="rp-card" data-reveal style="display:flex;gap:18px;align-items:flex-start">
    <div style="width:46px;height:46px;border-radius:12px;flex-shrink:0;background:rgba(14,165,233,.1);display:flex;align-items:center;justify-content:center">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#0EA5E9" stroke-width="1.8"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
    </div>
    <p style="font-size:15px;line-height:1.75;color:var(--ink-2,#1F2937);margin:0"><?= nl2br(htmlspecialchars($compet)) ?></p>
  </div>
  <?php endif; ?>
  <?php endif; ?>

  <!-- Forfait Lancement -->
  <div class="rp-pack rp-noprint">
    <div class="bg"></div>
    <div>
      <h3>Forfait Lancement · 3 outils mis en action</h3>
      <p>Choisissez vos 3 outils prioritaires : Milo les met en place avec vous, un par un, jusqu'au premier résultat. Inclut 90 jours d'assistance complète.</p>
    </div>
    <div style="text-align:center">
      <img class="rp-milo-mini" src="/assets/img/milo-avatar.jpg" alt="Milo" style="margin:0 auto 8px">
      <div class="price">199€ <small>· 3 missions + 90 j</small></div>
      <a class="rp-btn rp-mission-btn" data-plan="lancement" data-tool="" style="margin-top:10px" href="/facturation.php?plan=lancement">Démarrer le forfait</a>
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
  function cu(el){ var to=parseFloat(el.dataset.to)||0, suf=el.dataset.suffix||'', d=1400, t0=null;
    if(reduce){ el.textContent=to.toLocaleString('fr-FR')+suf; return; }
    function s(ts){ if(!t0)t0=ts; var p=Math.min(1,(ts-t0)/d), e=1-Math.pow(1-p,3);
      el.textContent=Math.round(to*e).toLocaleString('fr-FR')+suf; if(p<1)requestAnimationFrame(s); } requestAnimationFrame(s); }

  var io=new IntersectionObserver(function(en){ en.forEach(function(e){ if(!e.isIntersecting)return; var el=e.target;
    if(el.classList.contains('cu')) cu(el);
    else if(el.id==='rp-chart') el.classList.add('in');
    else if(el.hasAttribute('data-reveal')){ el.classList.add('in'); el.querySelectorAll('.fill').forEach(function(f){ f.style.width=(f.dataset.w||0)+'%'; }); }
    io.unobserve(el); }); }, {threshold:.22});
  document.querySelectorAll('.cu, #rp-chart, [data-reveal]').forEach(function(el){ io.observe(el); });

  function ring(id,numId,C,val,suffix){ var r=document.getElementById(id); if(!r)return;
    var pg=r.querySelector('.prog')||r.querySelector('.pg'), num=document.getElementById(numId);
    var o=new IntersectionObserver(function(e){ e.forEach(function(en){ if(!en.isIntersecting)return;
      pg.style.strokeDashoffset=C*(1-val/100);
      if(reduce){ num.textContent=val+(suffix||''); o.disconnect(); return; }
      var t0=null; function s(ts){ if(!t0)t0=ts; var p=Math.min(1,(ts-t0)/1300),e2=1-Math.pow(1-p,3);
        num.textContent=Math.round(val*e2)+(suffix||''); if(p<1)requestAnimationFrame(s);} requestAnimationFrame(s);
      o.disconnect(); }); }, {threshold:.4}); o.observe(r); }
  var rg=document.getElementById('rp-ring'); if(rg) ring('rp-ring','rp-score',327,parseInt(rg.dataset.score)||0,'');
  var gg=document.getElementById('rp-gauge'); if(gg) ring('rp-gauge','rp-gnum',534,parseInt(gg.dataset.pct)||0,'%');

  // Survol de la courbe ROI
  var chart=document.getElementById('rp-chart');
  if(chart){
    var pts=[]; try{ pts=JSON.parse(chart.dataset.pts||'[]'); }catch(e){}
    var svg=chart.querySelector('svg.plot'), hit=document.getElementById('rp-hit'),
        cross=document.getElementById('rp-cross'), hd=document.getElementById('rp-hoverdot'),
        tip=document.getElementById('rp-tip');
    var W=620,padL=8,padR=8,padT=10,padB=22,H=210,n=pts.length-1,maxv=Math.max.apply(null,pts)||1;
    function X(i){return padL+(W-padL-padR)*(i/n);} function Y(v){return (H-padB)-(H-padT-padB)*(v/maxv);}
    function move(ev){ var r=svg.getBoundingClientRect(); var rel=(ev.clientX-r.left)/r.width; var i=Math.round(rel*n);
      i=Math.max(0,Math.min(n,i)); var vx=X(i),vy=Y(pts[i]);
      cross.setAttribute('x1',vx); cross.setAttribute('x2',vx); cross.style.opacity=1;
      hd.setAttribute('cx',vx); hd.setAttribute('cy',vy); hd.style.opacity=1;
      tip.style.left=(vx/W*100)+'%'; tip.style.top=(vy/H*100)+'%'; tip.style.opacity=1;
      tip.innerHTML=pts[i].toLocaleString('fr-FR')+' €<small>Mois '+i+'</small>'; }
    function leave(){ cross.style.opacity=0; hd.style.opacity=0; tip.style.opacity=0; }
    hit.addEventListener('mousemove',move); hit.addEventListener('mouseleave',leave);
    svg.addEventListener('touchmove',function(e){ if(e.touches[0]) move(e.touches[0]); },{passive:true});
  }

  // ── Paiement 1 clic des missions (carte enregistrée) ──
  var TOKEN = new URLSearchParams(location.search).get('token') || '';
  var card = null;
  function api(body){ return fetch('/api/charge-mission.php',{method:'POST',headers:{'Content-Type':'application/json'},
    body:JSON.stringify(Object.assign({token:TOKEN},body))}).then(function(r){return r.json();}); }

  if (TOKEN) {
    api({action:'info'}).then(function(j){
      if (!j || !j.card) return;   // pas de carte : liens classiques inchangés
      card = j.card;
      document.querySelectorAll('.rp-mission-btn').forEach(function(btn){
        btn.dataset.armed = '0';
        var zone = btn.closest('.rp-cta') || btn.parentElement;
        var milo = zone ? zone.querySelector('.rp-milo-mini') : null;
        var msg  = zone ? zone.querySelector('.txt-msg') : null;
        if (msg) msg.dataset.orig = msg.innerHTML;
        function showMilo(html){ if (milo) milo.classList.add('on'); if (msg && html) msg.innerHTML = html; }
        function resetMilo(){ if (milo) milo.classList.remove('on'); if (msg) msg.innerHTML = msg.dataset.orig; }

        btn.addEventListener('click', function(ev){
          ev.preventDefault();
          if (btn.dataset.armed === 'done') { window.location.href = '/compte/'; return; }
          if (btn.dataset.armed === '2') { return; }   // paiement en cours
          var plan = btn.dataset.plan, tool = btn.dataset.tool || '';
          var price = plan === 'lancement' ? '199€' : '79€';
          if (btn.dataset.armed !== '1') {
            // Clic 1 : armer la confirmation (10 s), Milo apparaît
            btn.dataset.armed = '1';
            btn.dataset.label = btn.textContent;
            btn.textContent = 'Confirmer · ' + price + ' sur •••• ' + card.last4;
            btn.style.background = '#0A1F1A';
            showMilo('<b>Je suis prêt.</b> Confirmez, et je démarre ' +
              (plan === 'lancement' ? 'la mise en action de vos 3 outils' : ('la mise en action' + (tool ? ' de <b>' + tool + '</b>' : ''))) +
              ' avec vous, jusqu’au premier résultat.');
            setTimeout(function(){ if (btn.dataset.armed === '1'){ btn.dataset.armed='0'; btn.textContent=btn.dataset.label; btn.style.background=''; resetMilo(); } }, 10000);
            return;
          }
          // Clic 2 : débit
          btn.dataset.armed = '2';
          btn.textContent = 'Paiement en cours…';
          api({action:'charge', plan:plan, tool:tool}).then(function(r){
            if (r && r.success) {
              btn.textContent = 'Mission activée · rejoindre Milo';
              btn.style.background = '#059669';
              btn.href = '/compte/';
              btn.dataset.armed = 'done';
              showMilo('<b>C’est parti.</b> Votre mission est activée : je vous attends dans votre espace pour commencer tout de suite.');
              setTimeout(function(){ window.location.href = '/compte/'; }, 2200);
            } else if (r && r.fallback) {
              window.location.href = btn.getAttribute('href');   // parcours classique
            } else {
              btn.textContent = (r && r.error) ? 'Erreur · réessayer' : 'Erreur · réessayer';
              btn.style.background = '';
              btn.dataset.armed = '0';
              resetMilo();
            }
          }).catch(function(){ window.location.href = btn.getAttribute('href'); });
        });
      });
    }).catch(function(){});
  }
})();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
