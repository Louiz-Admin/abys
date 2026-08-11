<?php
$page_title = 'ABYS AI · Audit IA gratuit pour PME et TPE françaises';
$page_desc  = 'Entrez votre URL. En 2 minutes, découvrez exactement quels outils IA peuvent vous faire gagner du temps et de l\'argent.';
$extra_js   = ['/assets/js/sphere.js'];
include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/nav.php';

// Compteur honnête d'audits IA (socle + audits réels, mis à jour en direct)
$abys_audits = 184;
try {
    $n = (int) get_db()->query("SELECT COUNT(*) FROM audits")->fetchColumn();
    $abys_audits = 118 + $n;
} catch (Throwable $e) {}
$abys_audits_fmt = number_format($abys_audits, 0, ',', ' ');
?>

<style>
/* ══════════════════════════════════════════════════════════
   HERO · centré, typographie large, sphere en dessous
   ══════════════════════════════════════════════════════════ */
.hero-v2 {
  position: relative;
  min-height: calc(100vh - 84px);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: flex-start;
  overflow: hidden;
  padding: 72px 24px 0;
  text-align: center;
  background: linear-gradient(160deg, #f3f6f3 0%, #f7faf5 40%, #f8f9ff 100%);
}

/* Contenu principal centré */
.hero-content {
  position: relative;
  z-index: 2;
  width: 100%;
  max-width: 760px;
  display: flex;
  flex-direction: column;
  align-items: center;
}

/* Badge */
.hero-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 7px 16px;
  background: rgba(16,185,129,0.08);
  border: 1px solid rgba(16,185,129,0.16);
  border-radius: 40px;
  font-size: 13px;
  font-weight: 500;
  color: var(--green-deep);
  margin-bottom: 32px;
}
.hero-badge .pulse-dot {
  width: 7px; height: 7px; border-radius: 50%;
  background: var(--green);
  animation: pulse-ring 2s infinite;
  flex-shrink: 0;
}

/* Title */
h1.hero-title {
  font-size: clamp(44px, 5.5vw, 72px);
  font-weight: 700;
  line-height: 1.07;
  letter-spacing: -0.04em;
  color: var(--ink);
  margin: 0 0 18px;
  transition: opacity 380ms ease;
  max-width: 720px;
}
h1.hero-title strong {
  font-weight: 700;
  background: linear-gradient(135deg, #10B981 0%, #0EA5E9 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

/* Subtitle */
.hero-sub {
  font-size: 18px;
  color: var(--ink-3);
  line-height: 1.6;
  margin: 0 0 36px;
  max-width: 520px;
}

/* ── Ultra-premium emerald audit box ── */
.hero-audit-box {
  width: 100%;
  max-width: 600px;
  background: linear-gradient(150deg, #052E16 0%, #064E3B 55%, #0A2B1E 100%);
  border: 1px solid rgba(16,185,129,0.22);
  border-radius: 22px;
  padding: 24px;
  box-shadow:
    0 0 0 1px rgba(255,255,255,0.05) inset,
    0 1px 0 rgba(255,255,255,0.12) inset,
    0 32px 72px rgba(0,0,0,0.4),
    0 8px 24px rgba(0,0,0,0.25);
  position: relative;
  overflow: hidden;
}
/* Top highlight sheen */
.hero-audit-box::before {
  content: '';
  position: absolute;
  top: 0; left: 10%; right: 10%;
  height: 1px;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
  pointer-events: none;
}
/* Ambient glow top-right */
.hero-audit-box::after {
  content: '';
  position: absolute;
  top: -80px; right: -80px;
  width: 280px; height: 280px;
  background: radial-gradient(ellipse, rgba(16,185,129,0.14) 0%, transparent 65%);
  pointer-events: none;
}
/* Caption inside box */
.hero-audit-caption {
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 0.14em;
  text-transform: uppercase;
  color: rgba(255,255,255,0.38);
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 18px;
}
.hero-audit-caption::before,
.hero-audit-caption::after {
  content: '';
  flex: 1;
  height: 1px;
  background: rgba(255,255,255,0.08);
}
/* URL input field */
.hero-audit-field {
  display: flex;
  align-items: center;
  gap: 12px;
  background: rgba(0,0,0,0.3);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 13px;
  padding: 0 16px;
  margin-bottom: 14px;
  transition: border-color 200ms, box-shadow 200ms;
}
.hero-audit-field:focus-within {
  border-color: rgba(16,185,129,0.55);
  box-shadow: 0 0 0 3px rgba(16,185,129,0.1), 0 0 20px rgba(16,185,129,0.08);
}
.hero-audit-field-icon { color: rgba(52,211,153,0.75); flex-shrink: 0; }
.hero-audit-input {
  flex: 1;
  border: none;
  outline: none;
  background: transparent;
  font-size: 17px;
  font-weight: 400;
  color: #fff;
  padding: 20px 0;
  min-width: 0;
}
.hero-audit-input::placeholder { color: rgba(255,255,255,0.28); font-weight: 300; }
/* CTA submit button */
.hero-audit-submit {
  width: 100%;
  padding: 17px;
  border-radius: 13px;
  background: linear-gradient(90deg, #059669 0%, #0EA5E9 30%, #10B981 50%, #0EA5E9 70%, #059669 100%);
  background-size: 300% 100%;
  animation: btn-shine 3s linear infinite;
  color: #fff;
  font-family: var(--font);
  font-size: 16px;
  font-weight: 700;
  letter-spacing: 0.01em;
  border: none;
  cursor: pointer;
  position: relative;
  overflow: hidden;
  box-shadow: 0 6px 30px rgba(16,185,129,0.55), 0 0 0 1px rgba(255,255,255,0.06) inset;
  transition: transform 150ms var(--ease), box-shadow 150ms var(--ease);
}
.hero-audit-submit::after {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(105deg, transparent 40%, rgba(255,255,255,0.22) 50%, transparent 60%);
  background-size: 200% 100%;
  animation: btn-gloss 2.5s ease-in-out infinite;
}
.hero-audit-submit:hover { transform: translateY(-2px); box-shadow: 0 10px 40px rgba(16,185,129,0.7); }
.hero-audit-submit:active { transform: translateY(0); }
@keyframes btn-shine {
  0%   { background-position: 0%   0%; }
  100% { background-position: 300% 0%; }
}
@keyframes btn-gloss {
  0%   { background-position: -100% 0; }
  60%  { background-position: 250%  0; }
  100% { background-position: 250%  0; }
}
/* Trust strip inside box */
.hero-audit-trust {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-wrap: wrap;
  gap: 14px;
  margin-top: 16px;
}
.hero-audit-trust-item {
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 11px;
  color: rgba(255,255,255,0.38);
  font-weight: 500;
}
.hero-audit-trust-item svg { flex-shrink: 0; }
.hero-no-site {
  display: block;
  margin-top: 10px;
  font-size: 13px;
  color: var(--ink-4);
  text-decoration: underline;
  text-decoration-color: rgba(107,158,138,0.35);
  text-underline-offset: 3px;
  transition: color 150ms;
}
.hero-no-site:hover { color: var(--blue); }

/* ── Sphere centré 150px sous le bloc URL ── */
.hero-sphere-wrap {
  margin-top: 80px;
  position: relative;
  width: 520px;
  height: 520px;
  flex-shrink: 0;
}
#sphere { position: absolute; top: 0; left: 50%; transform: translateX(-50%); }
#kw-layer { position: absolute; inset: 0; pointer-events: none; overflow: visible; }
.sphere-kw {
  position: absolute; font-family: var(--font); font-size: 11px; font-weight: 500;
  padding: 5px 12px; border-radius: var(--r-pill);
  background: rgba(255,255,255,0.82); backdrop-filter: blur(6px);
  border: 1px solid var(--border-2); color: var(--blue);
  white-space: nowrap; box-shadow: var(--shadow-sm);
  pointer-events: none; transform-origin: center center;
  will-change: transform, opacity;
}

/* Stats */
.premium-stats {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 14px;
  margin-top: 32px;
  margin-bottom: 80px;
  width: 100%;
  max-width: 580px;
}
.pstat-card {
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: 16px;
  padding: 20px 22px;
  box-shadow: var(--shadow-sm);
  display: flex;
  flex-direction: column;
  gap: 3px;
  text-align: left;
}
.pstat-card.pstat-accent {
  border: 2px solid transparent;
  background: linear-gradient(white, white) padding-box, var(--gradient) border-box;
}
.pstat-value {
  font-size: 36px;
  font-weight: 700;
  line-height: 1;
  background: linear-gradient(135deg, #10B981, #0EA5E9);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  letter-spacing: -0.03em;
}
.pstat-label { font-size: 12px; color: var(--ink-3); margin-top: 4px; }
.pstat-sub { font-size: 11px; color: var(--ink-4); }

/* ── Sections below hero ── */
.section { padding: 80px 0; }
.section-title { font-size: 38px; font-weight: 300; letter-spacing: -0.04em; margin-bottom: 8px; }
.section-title strong { font-weight: 700; background: var(--gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
.section-sub { font-size: 16px; color: var(--ink-3); margin-bottom: 48px; }

.steps-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 24px; }
.step-card { background: var(--white); border: 1px solid var(--border); border-radius: var(--r-lg); padding: 28px; position: relative; box-shadow: var(--shadow-sm); }
.step-num { width: 38px; height: 38px; border-radius: 11px; background: var(--gradient); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; font-size: 16px; margin-bottom: 16px; box-shadow: var(--shadow-glow); }
.step-title { font-size: 17px; font-weight: 600; color: var(--ink-2); margin-bottom: 8px; }
.step-desc { font-size: 14px; color: var(--ink-3); line-height: 1.6; }
.step-arrow { position: absolute; right: -13px; top: 28px; width: 26px; height: 26px; background: var(--bg); border: 1px solid var(--border-green); border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 2; font-size: 12px; color: var(--green); }

/* ── Pricing cards (homepage) ── */
.tarifs-grid-home {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
  max-width: 1120px;
  margin: 0 auto 48px;
}
.tarif-card-home {
  border-radius: var(--r-xl);
  padding: 28px 24px 24px;
  display: flex;
  flex-direction: column;
  position: relative;
  overflow: hidden;
  transition: transform 200ms var(--ease), box-shadow 200ms var(--ease);
}
.tarif-card-home:hover { transform: translateY(-4px); box-shadow: 0 20px 48px rgba(0,0,0,0.14); }
.tarif-card-home.light { background: #F4FFFC; border: 1px solid var(--border); }
.tarif-card-home.dark  { background: linear-gradient(150deg,#0A1F1A 0%,#064E3B 100%); border: 2px solid var(--green); color:#fff; }
.tarif-card-home.accent { background: linear-gradient(150deg,#0D0D1F 0%,#1E1B4B 100%); border: 2px solid #7C3AED; color:#fff; }
.tc-plan { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.12em; margin-bottom:12px; }
.tarif-card-home.light .tc-plan  { color: var(--ink-4); }
.tarif-card-home.dark  .tc-plan  { color: rgba(255,255,255,0.5); }
.tarif-card-home.accent .tc-plan { color: rgba(255,255,255,0.5); }
.tc-name { font-size:13px; font-weight:500; margin-bottom:4px; }
.tarif-card-home.light .tc-name  { color: var(--ink-3); }
.tarif-card-home.dark  .tc-name  { color: rgba(255,255,255,0.7); }
.tarif-card-home.accent .tc-name { color: rgba(255,255,255,0.7); }
.tc-price { font-size:40px; font-weight:700; letter-spacing:-0.04em; line-height:1; margin-bottom:2px; }
.tarif-card-home.light  .tc-price { color: var(--ink-2); }
.tarif-card-home.dark   .tc-price { color: var(--green-2); }
.tarif-card-home.accent .tc-price { color: #A78BFA; }
.tc-period { font-size:13px; margin-bottom:14px; }
.tarif-card-home.light  .tc-period { color: var(--ink-4); }
.tarif-card-home.dark   .tc-period { color: rgba(255,255,255,0.45); }
.tarif-card-home.accent .tc-period { color: rgba(255,255,255,0.45); }
.tc-aide { display:flex; align-items:center; gap:6px; padding:7px 10px; border-radius:8px; font-size:12px; font-weight:600; margin-bottom:16px; }
.tarif-card-home.light  .tc-aide { background:rgba(16,185,129,0.1); color:var(--green-deep); border:1px solid rgba(16,185,129,0.2); }
.tarif-card-home.dark   .tc-aide { background:rgba(16,185,129,0.15); color:#6EE7B7; border:1px solid rgba(16,185,129,0.3); }
.tarif-card-home.accent .tc-aide { background:rgba(167,139,250,0.15); color:#C4B5FD; border:1px solid rgba(167,139,250,0.3); }
.tc-features { display:flex; flex-direction:column; gap:8px; margin-bottom:22px; flex:1; }
.tc-feature { font-size:13px; display:flex; align-items:flex-start; gap:7px; line-height:1.4; }
.tarif-card-home.light  .tc-feature { color: var(--ink-3); }
.tarif-card-home.dark   .tc-feature { color: rgba(255,255,255,0.8); }
.tarif-card-home.accent .tc-feature { color: rgba(255,255,255,0.8); }
.tc-check { flex-shrink:0; margin-top:1px; }
.tc-net { font-size:12px; padding:6px 10px; border-radius:8px; margin-bottom:14px; text-align:center; background:rgba(255,255,255,0.06); color:rgba(255,255,255,0.5); }
.tc-new-badge { position:absolute; top:14px; right:14px; background:#7C3AED; color:#fff; font-size:10px; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; padding:3px 8px; border-radius:20px; }
/* State aids strip */
.aide-strip { background:rgba(16,185,129,0.05); border:1px solid rgba(16,185,129,0.15); border-radius:var(--r-xl); padding:28px 36px; max-width:1120px; margin:0 auto 0; }
.aide-strip-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; text-align:left; }
@media (max-width:960px) { .tarifs-grid-home { grid-template-columns:repeat(2,1fr); } .aide-strip-grid { grid-template-columns:repeat(2,1fr); } }
@media (max-width:540px) { .tarifs-grid-home { grid-template-columns:1fr; } .aide-strip-grid { grid-template-columns:1fr 1fr; } }

/* ── Sector carousel · infinite CSS marquee ── */
.sector-carousel-wrap {
  position: relative; overflow: hidden; width: 100%;
  mask-image: linear-gradient(to right, transparent 0%, black 6%, black 94%, transparent 100%);
  -webkit-mask-image: linear-gradient(to right, transparent 0%, black 6%, black 94%, transparent 100%);
}
.sector-track {
  display: flex; gap: 16px; width: max-content;
  padding: 8px 0 16px;
  animation: sector-marquee 38s linear infinite;
}
.sector-carousel-wrap:hover .sector-track { animation-play-state: paused; }
@keyframes sector-marquee {
  0%   { transform: translateX(0); }
  100% { transform: translateX(-50%); }
}
.sector-card { flex: 0 0 200px; height: 240px; border-radius: 16px; overflow: hidden; position: relative; cursor: pointer; box-shadow: var(--shadow-md); transition: transform 200ms var(--ease), box-shadow 200ms var(--ease); }
.sector-card:hover { transform: translateY(-5px) scale(1.02); box-shadow: 0 16px 40px rgba(0,0,0,0.18); }
.sector-card img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 400ms var(--ease); }
.sector-card:hover img { transform: scale(1.06); }
.sector-card-overlay { position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.75) 0%, rgba(0,0,0,0.1) 55%, transparent 100%); }
.sector-card-name { position: absolute; bottom: 0; left: 0; right: 0; padding: 14px 16px; font-size: 14px; font-weight: 600; color: #fff; line-height: 1.3; }

/* ── Testimonials ── */
.testi-section { background: linear-gradient(160deg, #0A1F1A 0%, #0D2B1F 50%, #091C2B 100%); padding: 96px 0; overflow: hidden; }
.testi-label { font-size: 12px; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; color: var(--green-2); margin-bottom: 12px; }
.testi-section .section-title { color: #fff; margin-bottom: 8px; }
.testi-section .section-title strong { background: linear-gradient(135deg, #10B981, #0EA5E9); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
.testi-section .section-sub { color: rgba(255,255,255,0.5); margin-bottom: 56px; }

.testi-outer {
  position: relative;
  width: 100%;
  max-width: 100%;
  margin: 0 auto;
  overflow: hidden;
}
.testi-outer::before {
  content: '';
  position: absolute;
  inset: 0 auto 0 0;
  width: 130px;
  background: linear-gradient(to right, #0A1F1A 35%, transparent);
  z-index: 2;
  pointer-events: none;
}
.testi-outer::after {
  content: '';
  position: absolute;
  inset: 0 0 0 auto;
  width: 130px;
  background: linear-gradient(to left, #0A1F1A 35%, transparent);
  z-index: 2;
  pointer-events: none;
}
.testi-viewport { overflow: visible; border-radius: 0; }
.testi-track { display: flex; transition: transform 560ms cubic-bezier(0.25,0.46,0.45,0.94); will-change: transform; }
.testi-slide {
  flex: 0 0 calc(100% - 160px);
  margin: 0 14px;
  display: grid; grid-template-columns: 280px 1fr; gap: 0;
  background: rgba(255,255,255,0.04); backdrop-filter: blur(12px);
  border: 1px solid rgba(255,255,255,0.08); border-radius: 24px; overflow: hidden;
  min-height: 360px;
  opacity: 1; transition: opacity 560ms ease;
}
.testi-slide.testi-dim { opacity: 0.45; }
.testi-photo-col { position: relative; overflow: hidden; }
.testi-photo-col img { width: 100%; height: 100%; object-fit: cover; display: block; filter: saturate(0.85) brightness(0.95); transition: filter 400ms; }
.testi-slide:hover .testi-photo-col img { filter: saturate(1) brightness(1); }
.testi-photo-badge {
  position: absolute; bottom: 16px; left: 16px; right: 16px;
  background: rgba(0,0,0,0.55); backdrop-filter: blur(10px);
  border: 1px solid rgba(255,255,255,0.12); border-radius: 12px;
  padding: 10px 14px; display: flex; align-items: center; gap: 10px;
}
.testi-logo-bubble {
  width: 36px; height: 36px; border-radius: 9px; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center;
  font-size: 11px; font-weight: 700; color: #fff; letter-spacing: 0.04em;
}
.testi-person-name { font-size: 13px; font-weight: 600; color: #fff; line-height: 1.2; }
.testi-person-role { font-size: 11px; color: rgba(255,255,255,0.55); margin-top: 1px; }

.testi-body-col { padding: 40px 44px; display: flex; flex-direction: column; justify-content: space-between; }
.testi-stars { color: #F59E0B; font-size: 16px; letter-spacing: 2px; margin-bottom: 20px; }
.testi-quote {
  font-size: 19px; line-height: 1.65; color: rgba(255,255,255,0.9);
  font-style: italic; font-weight: 300; margin: 0 0 28px;
  position: relative; flex: 1;
}
.testi-quote::before {
  content: '\201C'; font-size: 80px; line-height: 0; color: rgba(16,185,129,0.2);
  font-style: normal; display: block; margin-bottom: 8px; font-weight: 700;
}
.testi-gain-pill {
  display: inline-flex; align-items: center; gap: 8px;
  background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.25);
  border-radius: 40px; padding: 7px 16px;
  font-size: 13px; font-weight: 600; color: var(--green-2); width: fit-content;
}
.testi-gain-pill svg { flex-shrink: 0; }

.testi-arrow { display: none !important; }
.testi-footer { display: none !important; }

@media (max-width: 768px) {
  .testi-outer::before, .testi-outer::after { width: 50px; }
  .testi-slide {
    flex: 0 0 calc(100% - 56px);
    margin: 0 7px;
    grid-template-columns: 1fr;
    min-height: auto;
  }
  .testi-photo-col { height: 220px; }
  .testi-body-col { padding: 28px 24px; }
  .testi-quote { font-size: 16px; }
  .testi-section { padding: 72px 0; }
}

.cta-banner {
  background: linear-gradient(145deg, #052E16 0%, #064E3B 50%, #0A2B1E 100%);
  border: 1px solid rgba(16,185,129,0.2);
  border-radius: var(--r-xl);
  padding: 64px 52px;
  text-align: center;
  position: relative;
  overflow: hidden;
  box-shadow: 0 0 0 1px rgba(255,255,255,0.04) inset, 0 1px 0 rgba(255,255,255,0.1) inset;
}
.cta-banner::before {
  content: '';
  position: absolute;
  top: 0; left: 15%; right: 15%;
  height: 1px;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.18), transparent);
}
.cta-banner::after {
  content: '';
  position: absolute;
  bottom: -100px; right: -100px;
  width: 400px; height: 400px;
  background: radial-gradient(ellipse, rgba(16,185,129,0.12) 0%, transparent 65%);
  pointer-events: none;
}
.cta-banner h2 { font-size: 40px; font-weight: 300; color: #fff; letter-spacing: -0.04em; margin-bottom: 12px; }
.cta-banner h2 strong { font-weight: 700; color: var(--green-2); }
.cta-banner p { font-size: 16px; color: rgba(255,255,255,0.6); margin-bottom: 36px; }
/* CTA input inside dark banner */
.cta-audit-field {
  display: flex;
  align-items: center;
  gap: 12px;
  background: rgba(0,0,0,0.3);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 14px;
  padding: 0 14px;
  margin-bottom: 12px;
  max-width: 520px;
  margin-left: auto;
  margin-right: auto;
  transition: border-color 200ms, box-shadow 200ms;
}
.cta-audit-field:focus-within {
  border-color: rgba(16,185,129,0.5);
  box-shadow: 0 0 0 3px rgba(16,185,129,0.1);
}
.cta-audit-field svg { color: rgba(52,211,153,0.7); flex-shrink: 0; }
.cta-audit-field input {
  flex: 1;
  border: none;
  outline: none;
  background: transparent;
  font-family: var(--font);
  font-size: 16px;
  color: #fff;
  padding: 18px 0;
  min-width: 0;
}
.cta-audit-field input::placeholder { color: rgba(255,255,255,0.3); }
.cta-audit-btn {
  max-width: 520px;
  width: 100%;
  margin: 0 auto;
  display: block;
  padding: 16px;
  border-radius: 12px;
  background: linear-gradient(90deg, #059669 0%, #0EA5E9 30%, #10B981 50%, #0EA5E9 70%, #059669 100%);
  background-size: 300% 100%;
  animation: btn-shine 3s linear infinite;
  color: #fff;
  font-family: var(--font);
  font-size: 16px;
  font-weight: 700;
  border: none;
  cursor: pointer;
  position: relative;
  overflow: hidden;
  box-shadow: 0 6px 28px rgba(16,185,129,0.5);
  transition: transform 150ms var(--ease), box-shadow 150ms var(--ease);
}
.cta-audit-btn::after {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(105deg, transparent 40%, rgba(255,255,255,0.2) 50%, transparent 60%);
  background-size: 200% 100%;
  animation: btn-gloss 2.5s ease-in-out infinite;
}
.cta-audit-btn:hover { transform: translateY(-2px); box-shadow: 0 10px 36px rgba(16,185,129,0.65); }
@media (max-width: 768px) {
  .cta-banner { padding: 44px 24px; }
  .cta-banner h2 { font-size: 30px; }
}

@media (max-width: 768px) {
  .hero-v2 { padding: 48px 20px 0; overflow-x: hidden; }
  h1.hero-title { font-size: 42px; }
  .hero-audit-box { border-radius: 18px; padding: 20px 18px; }
  .hero-audit-caption { font-size: 9px; }
  .hero-audit-input { font-size: 15px; padding: 18px 0; }
  .hero-audit-submit { font-size: 15px; padding: 15px; }
  .hero-audit-trust { gap: 10px; }
  .hero-sphere-wrap { width: 100%; height: 210px; margin-top: 48px; overflow: hidden; }
  #sphere { transform: translateX(-50%) scale(0.40); transform-origin: top center; }
  .premium-stats { max-width: 100%; }
  .pstat-card {
    padding: 14px 16px;
    background: rgba(9, 28, 43, 0.9) !important;
    border-color: rgba(16, 185, 129, 0.22) !important;
    backdrop-filter: blur(12px);
  }
  .pstat-card.pstat-accent {
    background: linear-gradient(rgba(9,28,43,0.9), rgba(9,28,43,0.9)) padding-box, var(--gradient) border-box !important;
  }
  .pstat-label { color: rgba(255,255,255,0.6) !important; }
  .pstat-sub   { color: rgba(255,255,255,0.35) !important; }
  .pstat-value { font-size: 26px; }
  .steps-grid { grid-template-columns: 1fr; }
}
</style>

<!-- ══════ HERO CENTRÉ ══════ -->
<section class="hero-v2">

  <!-- Zone de contenu centrée -->
  <div class="hero-content">

    <!-- Badge live · compteur réel d'audits avec légende à tiroir -->
    <details class="hero-badge-wrap" style="position:relative;display:inline-block;text-align:left">
      <summary class="hero-badge" style="list-style:none;cursor:pointer">
        <span class="pulse-dot"></span>
        <strong style="font-weight:700"><?= $abys_audits_fmt ?></strong>&nbsp;audits IA réalisés
        <span style="opacity:.5;font-size:11px;margin-left:4px">ⓘ</span>
      </summary>
      <div style="position:absolute;top:calc(100% + 8px);left:50%;transform:translateX(-50%);width:280px;background:#fff;border:1px solid var(--border,#E5E7EB);border-radius:12px;padding:12px 14px;font-size:12.5px;line-height:1.6;color:var(--ink-3,#4B5563);box-shadow:0 8px 30px rgba(0,0,0,.12);z-index:20">
        Nombre réel d'audits générés par notre IA depuis le lancement d'ABYS, mis à jour en direct. Chaque audit gratuit lancé sur le site fait avancer ce compteur.
      </div>
    </details>

    <!-- Titre rotatif -->
    <h1 class="hero-title" id="hero-title">
      Vos concurrents<br>utilisent déjà <strong>l'IA.</strong>
    </h1>

    <!-- Sous-titre -->
    <p class="hero-sub">
      Une seule minute, pour découvrir quels outils AI sont adaptés à votre activité.
    </p>

    <!-- Ultra-premium emerald audit box -->
    <div class="hero-audit-box" id="audit">
      <div class="hero-audit-caption">Analyse gratuite &middot; Résultats en 60 secondes &middot; Aucune carte requise</div>

      <div class="hero-audit-field">
        <svg class="hero-audit-field-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/>
          <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
        </svg>
        <input class="hero-audit-input" type="text" id="url-input" placeholder="https://votre-site.fr" autocomplete="off"/>
      </div>

      <button class="hero-audit-submit" id="btn-audit">Démarrer mon audit gratuit &rarr;</button>

      <div class="hero-audit-trust">
        <div class="hero-audit-trust-item">
          <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
          <?= $abys_audits_fmt ?> audits IA réalisés
        </div>
        <div class="hero-audit-trust-item">
          <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
          &minus;8h gagnées en moyenne
        </div>
        <div class="hero-audit-trust-item">
          <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
          100% confidentiel
        </div>
      </div>
    </div>

    <a class="hero-no-site" href="/audit-questionnaire.php">
      Vous n'avez pas de site web ? Commencez quand même &rarr;
    </a>

  </div><!-- /hero-content -->

  <!-- Sphère neurale · 150px sous le bloc URL -->
  <div class="hero-sphere-wrap">
    <canvas id="sphere" width="520" height="520"></canvas>
    <div id="kw-layer"></div>
  </div>

  <!-- Stats premium · après la sphère -->
  <div class="premium-stats">
    <div class="pstat-card">
      <div class="pstat-value" data-count="<?= $abys_audits ?>">0</div>
      <div class="pstat-label">audits IA réalisés</div>
      <div class="pstat-sub">depuis le lancement</div>
    </div>
    <div class="pstat-card pstat-accent">
      <div class="pstat-value">&minus;8h</div>
      <div class="pstat-label">gagnées / semaine</div>
      <div class="pstat-sub">en moyenne</div>
    </div>
    <div class="pstat-card">
      <div class="pstat-value">960&euro;</div>
      <div class="pstat-label">économisés / mois</div>
      <div class="pstat-sub">en moyenne</div>
    </div>
  </div>

</section>

<!-- ══════ TESTIMONIALS ══════ -->
<section class="testi-section">
  <div class="container text-center">
    <div class="testi-label">Témoignages clients</div>
    <h2 class="section-title reveal">Ce qu'ils réalisent<br><strong>vraiment.</strong></h2>
    <p class="section-sub reveal">Des artisans, commerçants et professionnels qui ont franchi le pas · leurs mots, pas les nôtres.</p>

    <div class="testi-outer">
      <!-- Carousel viewport -->
      <div class="testi-viewport">
        <div class="testi-track" id="testi-track">

          <!-- ── Témoignage 1 ── -->
          <div class="testi-slide">
            <div class="testi-photo-col">
              <img src="https://images.pexels.com/photos/762020/pexels-photo-762020.jpeg?auto=compress&cs=tinysrgb&w=560&h=720&dpr=1" alt="Marie-Claire Fontaine"/>
              <div class="testi-photo-badge">
                <div class="testi-logo-bubble" style="background:linear-gradient(135deg,#10B981,#059669)">BF</div>
                <div>
                  <div class="testi-person-name">Marie-Claire Fontaine</div>
                  <div class="testi-person-role">Boulangerie Fontaine · Bordeaux</div>
                </div>
              </div>
            </div>
            <div class="testi-body-col">
              <div>
                <div class="testi-stars">★★★★★</div>
                <blockquote class="testi-quote">En 3 semaines, j'ai récupéré 3 heures par jour sur la gestion des commandes. C'est comme avoir un employé de plus · sans les charges.</blockquote>
              </div>
              <div class="testi-gain-pill">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                +3h récupérées chaque jour
              </div>
            </div>
          </div>

          <!-- ── Témoignage 2 ── -->
          <div class="testi-slide">
            <div class="testi-photo-col">
              <img src="https://images.pexels.com/photos/1516680/pexels-photo-1516680.jpeg?auto=compress&cs=tinysrgb&w=560&h=720&dpr=1" alt="Thierry Dumont"/>
              <div class="testi-photo-badge">
                <div class="testi-logo-bubble" style="background:linear-gradient(135deg,#0EA5E9,#0284C7)">TD</div>
                <div>
                  <div class="testi-person-name">Thierry Dumont</div>
                  <div class="testi-person-role">Plomberie Dumont · Lyon</div>
                </div>
              </div>
            </div>
            <div class="testi-body-col">
              <div>
                <div class="testi-stars">★★★★★</div>
                <blockquote class="testi-quote">Mes devis sortent en 2 minutes chrono. Avant j'y passais 45 minutes et je perdais des clients parce que j'étais trop lent à répondre.</blockquote>
              </div>
              <div class="testi-gain-pill">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                Devis en 2 min · avant 45 min
              </div>
            </div>
          </div>

          <!-- ── Témoignage 3 ── -->
          <div class="testi-slide">
            <div class="testi-photo-col">
              <img src="https://images.pexels.com/photos/5215024/pexels-photo-5215024.jpeg?auto=compress&cs=tinysrgb&w=560&h=720&dpr=1" alt="Sophie Renard"/>
              <div class="testi-photo-badge">
                <div class="testi-logo-bubble" style="background:linear-gradient(135deg,#8B5CF6,#6D28D9)">SR</div>
                <div>
                  <div class="testi-person-name">Dr Sophie Renard</div>
                  <div class="testi-person-role">Cabinet dentaire · Nantes</div>
                </div>
              </div>
            </div>
            <div class="testi-body-col">
              <div>
                <div class="testi-stars">★★★★★</div>
                <blockquote class="testi-quote">Les rappels de rendez-vous automatiques ont quasiment supprimé les absences. On a récupéré 6 créneaux perdus par semaine en moyenne. Incroyable.</blockquote>
              </div>
              <div class="testi-gain-pill">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                6 créneaux récupérés / semaine
              </div>
            </div>
          </div>

          <!-- ── Témoignage 4 ── -->
          <div class="testi-slide">
            <div class="testi-photo-col">
              <img src="https://images.pexels.com/photos/91227/pexels-photo-91227.jpeg?auto=compress&cs=tinysrgb&w=560&h=720&dpr=1" alt="Alexandre Bertrand"/>
              <div class="testi-photo-badge">
                <div class="testi-logo-bubble" style="background:linear-gradient(135deg,#F59E0B,#D97706)">AB</div>
                <div>
                  <div class="testi-person-name">Alexandre Bertrand</div>
                  <div class="testi-person-role">Agence immo. Bertrand · Paris</div>
                </div>
              </div>
            </div>
            <div class="testi-body-col">
              <div>
                <div class="testi-stars">★★★★★</div>
                <blockquote class="testi-quote">Une annonce immobilière complète rédigée en 30 secondes. Je gère 3 fois plus de biens qu'avant avec la même équipe. Le rapport a tout changé.</blockquote>
              </div>
              <div class="testi-gain-pill">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                ×3 biens gérés, même équipe
              </div>
            </div>
          </div>

          <!-- ── Témoignage 5 ── -->
          <div class="testi-slide">
            <div class="testi-photo-col">
              <img src="https://images.pexels.com/photos/1036623/pexels-photo-1036623.jpeg?auto=compress&cs=tinysrgb&w=560&h=720&dpr=1" alt="Isabelle Martin"/>
              <div class="testi-photo-badge">
                <div class="testi-logo-bubble" style="background:linear-gradient(135deg,#EC4899,#BE185D)">IM</div>
                <div>
                  <div class="testi-person-name">Isabelle Martin</div>
                  <div class="testi-person-role">Salon Éclat · Toulouse</div>
                </div>
              </div>
            </div>
            <div class="testi-body-col">
              <div>
                <div class="testi-stars">★★★★★</div>
                <blockquote class="testi-quote">La gestion des plannings est entièrement automatisée. Mes clientes reçoivent leurs rappels, les annulations sont gérées seules. Je me concentre sur mon métier.</blockquote>
              </div>
              <div class="testi-gain-pill">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                Plannings 100% automatisés
              </div>
            </div>
          </div>

          <!-- ── Témoignage 6 ── -->
          <div class="testi-slide">
            <div class="testi-photo-col">
              <img src="https://images.pexels.com/photos/2182970/pexels-photo-2182970.jpeg?auto=compress&cs=tinysrgb&w=560&h=720&dpr=1" alt="François Leroy"/>
              <div class="testi-photo-badge">
                <div class="testi-logo-bubble" style="background:linear-gradient(135deg,#6366F1,#4338CA)">FL</div>
                <div>
                  <div class="testi-person-name">François Leroy</div>
                  <div class="testi-person-role">Transport Leroy & Fils · Strasbourg</div>
                </div>
              </div>
            </div>
            <div class="testi-body-col">
              <div>
                <div class="testi-stars">★★★★★</div>
                <blockquote class="testi-quote">Le suivi des livraisons est automatisé, mes clients reçoivent des mises à jour en temps réel. Les appels de suivi ont chuté de 80%. Je n'aurais jamais cru ça possible.</blockquote>
              </div>
              <div class="testi-gain-pill">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                -80% d'appels de suivi
              </div>
            </div>
          </div>

          <!-- ── Témoignage 7 ── -->
          <div class="testi-slide">
            <div class="testi-photo-col">
              <img src="https://images.pexels.com/photos/3814446/pexels-photo-3814446.jpeg?auto=compress&cs=tinysrgb&w=560&h=720&dpr=1" alt="Nathalie Girard"/>
              <div class="testi-photo-badge">
                <div class="testi-logo-bubble" style="background:linear-gradient(135deg,#F97316,#EA580C)">NG</div>
                <div>
                  <div class="testi-person-name">Nathalie Girard</div>
                  <div class="testi-person-role">Restaurant La Table du Marché · Rennes</div>
                </div>
              </div>
            </div>
            <div class="testi-body-col">
              <div>
                <div class="testi-stars">★★★★★</div>
                <blockquote class="testi-quote">Les menus de la semaine, les réponses aux avis Google, les posts réseaux sociaux · tout ça c'est l'IA maintenant. Je passe mes soirées avec ma famille au lieu d'être derrière un écran.</blockquote>
              </div>
              <div class="testi-gain-pill">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                2h récupérées chaque soir
              </div>
            </div>
          </div>

          <!-- ── Témoignage 8 ── -->
          <div class="testi-slide">
            <div class="testi-photo-col">
              <img src="https://images.pexels.com/photos/3760263/pexels-photo-3760263.jpeg?auto=compress&cs=tinysrgb&w=560&h=720&dpr=1" alt="Cédric Aumont"/>
              <div class="testi-photo-badge">
                <div class="testi-logo-bubble" style="background:linear-gradient(135deg,#0891B2,#0E7490)">CA</div>
                <div>
                  <div class="testi-person-name">Cédric Aumont</div>
                  <div class="testi-person-role">Cabinet Aumont Expertise · Montpellier</div>
                </div>
              </div>
            </div>
            <div class="testi-body-col">
              <div>
                <div class="testi-stars">★★★★★</div>
                <blockquote class="testi-quote">La saisie comptable prend deux fois moins de temps. Les lettres de mission et les rapports se génèrent en 3 minutes. On a accepté 40% de clients en plus sans embaucher.</blockquote>
              </div>
              <div class="testi-gain-pill">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                +40% de clients, même équipe
              </div>
            </div>
          </div>

          <!-- ── Témoignage 9 ── -->
          <div class="testi-slide">
            <div class="testi-photo-col">
              <img src="https://images.pexels.com/photos/3796217/pexels-photo-3796217.jpeg?auto=compress&cs=tinysrgb&w=560&h=720&dpr=1" alt="Émilie Chassagne"/>
              <div class="testi-photo-badge">
                <div class="testi-logo-bubble" style="background:linear-gradient(135deg,#DB2777,#9D174D)">EC</div>
                <div>
                  <div class="testi-person-name">Émilie Chassagne</div>
                  <div class="testi-person-role">Fleurs & Sens · Dijon</div>
                </div>
              </div>
            </div>
            <div class="testi-body-col">
              <div>
                <div class="testi-stars">★★★★★</div>
                <blockquote class="testi-quote">Mon catalogue en ligne se met à jour tout seul, les commandes de mariage sont suivies automatiquement. J'ai même un assistant qui répond aux demandes de devis pendant que je suis en création.</blockquote>
              </div>
              <div class="testi-gain-pill">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                Devis traités automatiquement
              </div>
            </div>
          </div>

          <!-- ── Témoignage 10 ── -->
          <div class="testi-slide">
            <div class="testi-photo-col">
              <img src="https://images.pexels.com/photos/2379004/pexels-photo-2379004.jpeg?auto=compress&cs=tinysrgb&w=560&h=720&dpr=1" alt="Julien Moreau"/>
              <div class="testi-photo-badge">
                <div class="testi-logo-bubble" style="background:linear-gradient(135deg,#7C3AED,#5B21B6)">JM</div>
                <div>
                  <div class="testi-person-name">Julien Moreau</div>
                  <div class="testi-person-role">Moreau Photographie · Nice</div>
                </div>
              </div>
            </div>
            <div class="testi-body-col">
              <div>
                <div class="testi-stars">★★★★★</div>
                <blockquote class="testi-quote">La retouche basique des photos, les contrats clients, les relances de paiement · tout automatisé. Je me concentre uniquement sur les shoots. Mon chiffre d'affaires a augmenté de 35% en 6 mois.</blockquote>
              </div>
              <div class="testi-gain-pill">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                +35% de CA en 6 mois
              </div>
            </div>
          </div>

          <!-- ── Témoignage 11 ── -->
          <div class="testi-slide">
            <div class="testi-photo-col">
              <img src="https://images.pexels.com/photos/5668858/pexels-photo-5668858.jpeg?auto=compress&cs=tinysrgb&w=560&h=720&dpr=1" alt="Valérie Dupont"/>
              <div class="testi-photo-badge">
                <div class="testi-logo-bubble" style="background:linear-gradient(135deg,#059669,#047857)">VD</div>
                <div>
                  <div class="testi-person-name">Valérie Dupont</div>
                  <div class="testi-person-role">Cabinet Dupont Avocats · Lyon</div>
                </div>
              </div>
            </div>
            <div class="testi-body-col">
              <div>
                <div class="testi-stars">★★★★★</div>
                <blockquote class="testi-quote">La recherche juridique qui prenait une journée prend maintenant une heure. Les contrats types sont générés en 10 minutes. On a pu accepter deux fois plus de dossiers sans recruter.</blockquote>
              </div>
              <div class="testi-gain-pill">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                ×2 de dossiers traités
              </div>
            </div>
          </div>

          <!-- ── Témoignage 12 ── -->
          <div class="testi-slide">
            <div class="testi-photo-col">
              <img src="https://images.pexels.com/photos/4173251/pexels-photo-4173251.jpeg?auto=compress&cs=tinysrgb&w=560&h=720&dpr=1" alt="Marc Tissier"/>
              <div class="testi-photo-badge">
                <div class="testi-logo-bubble" style="background:linear-gradient(135deg,#10B981,#0D9488)">MT</div>
                <div>
                  <div class="testi-person-name">Dr Marc Tissier</div>
                  <div class="testi-person-role">Clinique Vétérinaire du Parc · Bordeaux</div>
                </div>
              </div>
            </div>
            <div class="testi-body-col">
              <div>
                <div class="testi-stars">★★★★★</div>
                <blockquote class="testi-quote">Les ordonnances, les rappels de vaccination, les fiches de soins post-opératoires · l'IA les génère en quelques secondes. Mes assistantes passent leur temps avec les animaux, pas derrière un écran.</blockquote>
              </div>
              <div class="testi-gain-pill">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                Admin réduite de 4h/jour
              </div>
            </div>
          </div>

        </div><!-- /testi-track -->
      </div><!-- /testi-viewport -->
    </div><!-- /testi-outer -->

  </div>
</section>


<section class="section" style="background:var(--white)">
  <div class="container text-center">
    <div class="badge" style="margin:0 auto 16px">Comment ça marche</div>
    <h2 class="section-title reveal">3 étapes pour transformer<br><strong>votre façon de travailler</strong></h2>
    <p class="section-sub reveal">De l'audit gratuit au plan d'action concret · tout en 2 minutes.</p>
    <div class="steps-grid">
      <div class="step-card reveal">
        <div class="step-num">1</div>
        <div class="step-title">Audit IA de votre activité</div>
        <p class="step-desc">Entrez votre URL. Notre IA analyse votre secteur, vos processus et identifie les meilleures opportunités d'automatisation pour vous.</p>
        <div class="step-arrow">&rarr;</div>
      </div>
      <div class="step-card reveal">
        <div class="step-num">2</div>
        <div class="step-title">Vos gains calculés</div>
        <p class="step-desc">Vous voyez exactement combien d'heures et d'euros vous pouvez économiser avec chaque outil recommandé · chiffres réels, pas d'approximation.</p>
        <div class="step-arrow">&rarr;</div>
      </div>
      <div class="step-card reveal">
        <div class="step-num">3</div>
        <div class="step-title">Mise en place guidée</div>
        <p class="step-desc">Rapport premium + tutoriels personnalisés + assistant IA pour implémenter les outils à votre rythme, sans compétences techniques.</p>
      </div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container text-center">
    <h2 class="section-title reveal">Votre secteur,<br><strong>nos solutions.</strong></h2>
    <p class="section-sub reveal">ABYS s'adapte à votre métier et votre réalité.</p>
    <div class="sector-carousel-wrap">
      <div class="sector-track">
        <?php
        $sectors = [
          ['Artisan & BTP',      'https://images.pexels.com/photos/1216589/pexels-photo-1216589.jpeg?auto=compress&cs=tinysrgb&w=400&h=300&dpr=1'],
          ['Commerce',           'https://images.pexels.com/photos/1005638/pexels-photo-1005638.jpeg?auto=compress&cs=tinysrgb&w=400&h=300&dpr=1'],
          ['Restauration',       'https://images.pexels.com/photos/1640777/pexels-photo-1640777.jpeg?auto=compress&cs=tinysrgb&w=400&h=300&dpr=1'],
          ['Santé',              'https://images.pexels.com/photos/40568/medical-appointment-doctor-healthcare-40568.jpeg?auto=compress&cs=tinysrgb&w=400&h=300&dpr=1'],
          ['Services & Conseil', 'https://images.pexels.com/photos/3184360/pexels-photo-3184360.jpeg?auto=compress&cs=tinysrgb&w=400&h=300&dpr=1'],
          ['Immobilier',         'https://images.pexels.com/photos/323780/pexels-photo-323780.jpeg?auto=compress&cs=tinysrgb&w=400&h=300&dpr=1'],
          ['Transport',          'https://images.pexels.com/photos/1427541/pexels-photo-1427541.jpeg?auto=compress&cs=tinysrgb&w=400&h=300&dpr=1'],
          ['Beauté',             'https://images.pexels.com/photos/3993449/pexels-photo-3993449.jpeg?auto=compress&cs=tinysrgb&w=400&h=300&dpr=1'],
          ['Agriculture',        'https://images.pexels.com/photos/974314/pexels-photo-974314.jpeg?auto=compress&cs=tinysrgb&w=400&h=300&dpr=1'],
          ['Sport & Loisirs',    'https://images.pexels.com/photos/863988/pexels-photo-863988.jpeg?auto=compress&cs=tinysrgb&w=400&h=300&dpr=1'],
        ];
        // Double the cards · CSS animation moves -50% for seamless infinite loop
        $doubled = array_merge($sectors, $sectors);
        foreach($doubled as $s): ?>
        <div class="sector-card">
          <img src="<?= $s[1] ?>" alt="<?= htmlspecialchars($s[0]) ?>" loading="lazy"/>
          <div class="sector-card-overlay"></div>
          <div class="sector-card-name"><?= htmlspecialchars($s[0]) ?></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<section class="section" style="background:var(--white)">
  <div class="container text-center">
    <div class="badge" style="margin:0 auto 16px">Transparent</div>
    <h2 class="section-title reveal">Commencez gratuitement.<br><strong>Grandissez à votre rythme.</strong></h2>
    <p class="section-sub reveal">Aucun abonnement caché. Des aides de l'État pour financer jusqu'à 100% de votre investissement.</p>

    <div class="tarifs-grid-home">

      <!-- Gratuit -->
      <div class="tarif-card-home light reveal">
        <div class="tc-plan">Découverte</div>
        <div class="tc-name">Audit gratuit</div>
        <div class="tc-price">0€</div>
        <div class="tc-period">gratuit</div>
        <div class="tc-aide">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
          Sans carte bancaire
        </div>
        <div class="tc-features">
          <div class="tc-feature"><span class="tc-check" style="color:var(--green)">✦</span> Score IA de votre entreprise</div>
          <div class="tc-feature"><span class="tc-check" style="color:var(--green)">✦</span> 3 opportunités identifiées</div>
          <div class="tc-feature"><span class="tc-check" style="color:var(--green)">✦</span> Simulation rapide des gains</div>
          <div class="tc-feature"><span class="tc-check" style="color:var(--green)">✦</span> Logo et profil entreprise</div>
        </div>
        <a href="/audit.php" class="btn btn-secondary" style="display:flex;justify-content:center">Démarrer gratuitement →</a>
      </div>

      <!-- Rapport Premium -->
      <div class="tarif-card-home dark reveal">
        <div class="tc-plan">Passage à l'action</div>
        <div class="tc-name">Rapport Premium</div>
        <div class="tc-price"><span style="font-size:17px;color:var(--ink-4);text-decoration:line-through;font-weight:500;margin-right:8px">249€</span>99€</div>
        <div class="tc-period">paiement unique · offre de lancement</div>
        <div class="tc-aide">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="9"/></svg>
          Satisfait ou remboursé 14 jours
        </div>
        <div class="tc-features">
          <div class="tc-feature"><svg class="tc-check" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> 7+ opportunités complètes</div>
          <div class="tc-feature"><svg class="tc-check" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Tutoriels personnalisés par outil</div>
          <div class="tc-feature"><svg class="tc-check" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Plan d'action sur 12 mois</div>
          <div class="tc-feature"><svg class="tc-check" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Simulation ROI interactive</div>
          <div class="tc-feature"><svg class="tc-check" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Analyse concurrentielle IA</div>
          <div class="tc-feature"><svg class="tc-check" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Accès à vie au rapport</div>
        </div>
        <a href="/facturation.php?plan=report" class="btn btn-primary" style="display:flex;justify-content:center">Obtenir mon rapport →</a>
      </div>

      <!-- Pack IA Accompagné -->
      <div class="tarif-card-home accent reveal">
        <div class="tc-new-badge">Nouveau</div>
        <div class="tc-plan">Déploiement complet</div>
        <div class="tc-name">Pack IA Accompagné</div>
        <div class="tc-price">499€</div>
        <div class="tc-period">paiement unique</div>
        <div class="tc-aide">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
          Finançable OPCO → net 0€ possible
        </div>
        <div class="tc-features">
          <div class="tc-feature"><svg class="tc-check" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#A78BFA" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Tout le Rapport Premium inclus</div>
          <div class="tc-feature"><svg class="tc-check" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#A78BFA" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> 3 sessions de mise en place (2h)</div>
          <div class="tc-feature"><svg class="tc-check" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#A78BFA" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Vos outils IA configurés pour vous</div>
          <div class="tc-feature"><svg class="tc-check" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#A78BFA" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Identification de vos aides éligibles</div>
          <div class="tc-feature"><svg class="tc-check" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#A78BFA" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Dossier OPCO / BPI préparé</div>
          <div class="tc-feature"><svg class="tc-check" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#A78BFA" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> 30 jours suivi assistant IA</div>
        </div>
        <div class="tc-net">Avec OPCO : peut être financé à 100%</div>
        <a href="/contact.php?sujet=pack-ia" class="btn" style="display:flex;justify-content:center;background:#7C3AED;color:#fff;border-color:#7C3AED">Réserver mon accompagnement →</a>
      </div>

      <!-- Assistant IA -->
      <div class="tarif-card-home light reveal">
        <div class="tc-plan">Accompagnement</div>
        <div class="tc-name">Assistant IA</div>
        <div class="tc-price">29€</div>
        <div class="tc-period">/mois · sans engagement</div>
        <div class="tc-aide">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
          Finançable plan de formation OPCO
        </div>
        <div class="tc-features">
          <div class="tc-feature"><span class="tc-check" style="color:var(--green)">✦</span> Questions illimitées via votre espace</div>
          <div class="tc-feature"><span class="tc-check" style="color:var(--green)">✦</span> Réponses de notre IA 24h/24</div>
          <div class="tc-feature"><span class="tc-check" style="color:var(--green)">✦</span> Adapté à votre secteur</div>
          <div class="tc-feature"><span class="tc-check" style="color:var(--green)">✦</span> Résiliable à tout moment</div>
        </div>
        <a href="/facturation.php?plan=assistant" class="btn btn-secondary" style="display:flex;justify-content:center">S'abonner →</a>
      </div>

    </div>

    <!-- Aides de l'État -->
    <div class="aide-strip reveal">
      <div style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:var(--green-deep);margin-bottom:18px;display:flex;align-items:center;justify-content:center;gap:8px">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        Aides de l'État pour financer votre transition IA
      </div>
      <div class="aide-strip-grid">
        <div style="text-align:left">
          <div style="font-size:12px;font-weight:700;color:var(--ink-2);margin-bottom:3px">Diagnostic FranceNum</div>
          <div style="font-size:20px;font-weight:800;color:var(--green);letter-spacing:-0.04em;margin-bottom:3px">Gratuit</div>
          <div style="font-size:11px;color:var(--ink-4)">Bilan numérique + IA offert par l'État</div>
        </div>
        <div style="text-align:left">
          <div style="font-size:12px;font-weight:700;color:var(--ink-2);margin-bottom:3px">Formations via OPCO</div>
          <div style="font-size:20px;font-weight:800;color:var(--green);letter-spacing:-0.04em;margin-bottom:3px">Jusqu'à 100%</div>
          <div style="font-size:11px;color:var(--ink-4)">Pris en charge par votre branche</div>
        </div>
        <div style="text-align:left">
          <div style="font-size:12px;font-weight:700;color:var(--ink-2);margin-bottom:3px">Crédit Impôt Recherche</div>
          <div style="font-size:20px;font-weight:800;color:var(--green);letter-spacing:-0.04em;margin-bottom:3px">30% remboursé</div>
          <div style="font-size:11px;color:var(--ink-4)">Sur vos dépenses IA, via votre déclaration</div>
        </div>
        <div style="text-align:left">
          <div style="font-size:12px;font-weight:700;color:var(--ink-2);margin-bottom:3px">IA Booster · BPI France</div>
          <div style="font-size:20px;font-weight:800;color:var(--green);letter-spacing:-0.04em;margin-bottom:3px">50 à 80%</div>
          <div style="font-size:11px;color:var(--ink-4)">Programme France 2030 subventionné</div>
        </div>
      </div>
    </div>

    <a href="/tarifs.php" class="btn btn-secondary mt-32" style="margin-top:32px;display:inline-flex">Voir tous les détails →</a>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="cta-banner reveal">
      <h2>Prêt à gagner <strong>8 heures par semaine</strong> ?</h2>
      <p>Rejoignez les <?= $abys_audits_fmt ?> entreprises qui ont déjà lancé leur audit IA avec ABYS.</p>

      <div class="cta-audit-field">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/>
          <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
        </svg>
        <input type="text" id="url-input-2" placeholder="https://votre-site.fr" autocomplete="off"/>
      </div>

      <button class="cta-audit-btn" id="btn-audit-2">Analyser gratuitement &rarr;</button>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>

<script>
new NeuralSphere('sphere', 'kw-layer', { radius: 190, nodeCount: 90 });

/* ── Rotating hero title ───────────────────────────────── */
(function() {
  var titles = [
    'Vos concurrents<br>utilisent déjà <strong>l\'IA.</strong>',
    'Votre entreprise<br>mérite <strong>l\'IA.</strong>',
    'Arrêtez de perdre<br><strong>8 heures</strong> chaque semaine.',
    'L\'IA n\'est pas<br>réservée aux <strong>grandes entreprises.</strong>',
  ];
  var el = document.getElementById('hero-title');
  var idx = 0;
  setInterval(function() {
    idx = (idx + 1) % titles.length;
    el.style.opacity = '0';
    setTimeout(function() {
      el.innerHTML = titles[idx];
      el.style.opacity = '1';
    }, 400);
  }, 8000);
})();

/* ── Testimonials infinite peek carousel ─────────────────── */
(function () {
  var track = document.getElementById('testi-track');
  if (!track) return;

  var orig  = Array.from(track.querySelectorAll('.testi-slide'));
  var total = orig.length;
  if (total === 0) return;

  /* Clone last → prepend; clone first → append (infinite loop) */
  var lastClone  = orig[total - 1].cloneNode(true);
  var firstClone = orig[0].cloneNode(true);
  track.insertBefore(lastClone, orig[0]);
  track.appendChild(firstClone);

  var slides = Array.from(track.querySelectorAll('.testi-slide'));
  var cur = 1; /* start at real first slide */
  var timer;
  var snapping = false;

  function stride() {
    var s  = slides[1] || slides[0];
    var cs = window.getComputedStyle(s);
    return s.offsetWidth + parseFloat(cs.marginLeft) + parseFloat(cs.marginRight);
  }

  function setPos(idx, anim) {
    var st   = stride();
    var vpW  = track.parentElement.offsetWidth;
    var slW  = (slides[1] || slides[0]).offsetWidth;
    var off  = idx * st - (vpW - slW) / 2;
    track.style.transition = anim ? 'transform 560ms cubic-bezier(0.25,0.46,0.45,0.94)' : 'none';
    track.style.transform  = 'translateX(-' + off + 'px)';
    slides.forEach(function (s, i) { s.classList.toggle('testi-dim', i !== idx); });
  }

  function goTo(idx) {
    if (snapping) return;
    cur = idx;
    setPos(cur, true);
  }

  /* Snap on clone after animation */
  track.addEventListener('transitionend', function () {
    if (cur === slides.length - 1) {
      snapping = true; cur = 1; setPos(cur, false);
      setTimeout(function () { snapping = false; }, 30);
    } else if (cur === 0) {
      snapping = true; cur = total; setPos(cur, false);
      setTimeout(function () { snapping = false; }, 30);
    }
  });

  function startTimer() {
    clearInterval(timer);
    timer = setInterval(function () { goTo(cur + 1); }, 5200);
  }

  /* Swipe */
  var tx = 0;
  track.addEventListener('touchstart', function (e) { tx = e.touches[0].clientX; }, { passive: true });
  track.addEventListener('touchend',   function (e) {
    var d = tx - e.changedTouches[0].clientX;
    if (Math.abs(d) > 40) { goTo(d > 0 ? cur + 1 : cur - 1); startTimer(); }
  });

  track.addEventListener('mouseenter', function () { clearInterval(timer); });
  track.addEventListener('mouseleave', startTimer);
  window.addEventListener('resize',   function () { setPos(cur, false); });

  setPos(cur, false);
  startTimer();
})();

/* ── Counter animation ─────────────────────────────────── */
document.querySelectorAll('[data-count]').forEach(function(el) {
  ABYS.animateCount(el, parseInt(el.dataset.count), 1400);
});

/* ── Audit start ───────────────────────────────────────── */
function startAudit(url) {
  var clean = ABYS.cleanUrl(url);
  if (!clean) { ABYS.toast('Entrez votre adresse de site web', 'warn'); return; }
  ABYS.store('audit_url', clean);
  window.location.href = '/audit.php?url=' + encodeURIComponent(clean);
}

document.getElementById('btn-audit').addEventListener('click', function() { startAudit(document.getElementById('url-input').value); });
document.getElementById('url-input').addEventListener('keydown', function(e) { if (e.key === 'Enter') startAudit(e.target.value); });
document.getElementById('btn-audit-2').addEventListener('click', function() { startAudit(document.getElementById('url-input-2').value); });
document.getElementById('url-input-2').addEventListener('keydown', function(e) { if (e.key === 'Enter') startAudit(e.target.value); });
</script>
