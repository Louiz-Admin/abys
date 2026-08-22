<?php
$page_title = 'Votre audit IA · ABYS AI';
$page_desc  = 'Huit questions, deux minutes. Milo analyse votre activité et vous montre où l\'IA vous fait gagner du temps et de l\'argent.';
include __DIR__ . '/includes/head.php';
// TUNNEL PLEIN ÉCRAN · aucune navigation, aucune sortie. Milo mène l'entretien.
?>
<style>
  html, body { margin:0; padding:0; background:#041712; }
  body { font-family:var(--font, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif);
         color:#EAF6F1; overflow-x:hidden; -webkit-font-smoothing:antialiased; }

  .qt { --pan:rgba(255,255,255,.035); --line:rgba(255,255,255,.10);
        --line2:rgba(255,255,255,.20); --txt:#EAF6F1; --dim:#8CA79E; --acc:#10B981;
        --acc2:#34D399; --blue:#38BDF8; }

  /* ═══ Fond : faisceaux vivants ═══ */
  .qt-beams { position:fixed; inset:0; z-index:0; overflow:hidden; pointer-events:none; }
  .qt-beams span { position:absolute; top:-40%; left:var(--l); width:190px; height:190%;
    transform-origin:top center; transform:rotate(var(--a)); }
  .qt-beams span::before { content:''; position:absolute; inset:0;
    background:linear-gradient(to bottom, rgba(52,211,153,.30), rgba(56,189,248,.10) 52%, transparent 82%);
    -webkit-mask-image:linear-gradient(to right, transparent, #000 44%, #000 56%, transparent);
            mask-image:linear-gradient(to right, transparent, #000 44%, #000 56%, transparent);
    filter:blur(9px); mix-blend-mode:screen; transform-origin:top center; will-change:transform;
    animation:qtray var(--d) ease-in-out var(--dl,0s) infinite alternate; }
  @keyframes qtray { from { transform:rotate(calc(var(--s) * -1)); } to { transform:rotate(var(--s)); } }
  .qt-glow { position:fixed; inset:auto 0 -30% 0; height:70%; z-index:0; pointer-events:none;
    background:radial-gradient(60% 100% at 50% 100%, rgba(16,185,129,.16), transparent 70%); }
  @media (prefers-reduced-motion: reduce) { .qt-beams span::before { animation:none; } }

  /* ═══ Ossature ═══ */
  .qt-shell { position:relative; z-index:2; display:grid; grid-template-columns:392px 1fr;
    min-height:100vh; min-height:100dvh; }

  /* ── Colonne Milo ── */
  .qt-side { border-right:1px solid var(--line); background:linear-gradient(180deg, rgba(255,255,255,.045), rgba(255,255,255,.012));
    padding:30px 32px 26px; display:flex; flex-direction:column; gap:24px; }
  .qt-brand { display:flex; align-items:center; gap:9px; letter-spacing:.16em; font-size:13px; color:#CFE9E0; }
  .qt-brand b { font-weight:800; } .qt-brand i { font-style:normal; color:var(--acc2); font-weight:600; }

  .qt-milo { display:flex; align-items:center; gap:14px; }
  .qt-milo img { width:64px; height:64px; border-radius:50%; object-fit:cover;
    border:2px solid rgba(52,211,153,.65); box-shadow:0 0 0 6px rgba(16,185,129,.10), 0 14px 34px -14px rgba(0,0,0,.8); }
  .qt-milo .nm { font-size:15.5px; font-weight:650; letter-spacing:-.01em; }
  .qt-milo .rl { font-size:12.5px; color:var(--dim); margin-top:3px; line-height:1.45; }

  .qt-sect { border-top:1px solid var(--line); padding-top:20px; }
  .qt-sect h3 { font-size:11px; font-weight:700; letter-spacing:.16em; text-transform:uppercase;
    color:#6E8C84; margin:0 0 14px; }

  .qt-chips { display:flex; flex-direction:column; gap:8px; }
  .qt-chip { display:flex; align-items:flex-start; gap:10px; font-size:13px; line-height:1.45;
    background:rgba(255,255,255,.045); border:1px solid var(--line); border-radius:11px; padding:9px 12px;
    animation:qtchip .42s cubic-bezier(.22,1,.36,1) both; }
  @keyframes qtchip { from { opacity:0; transform:translateY(9px); } to { opacity:1; transform:none; } }
  .qt-chip svg { width:15px; height:15px; color:var(--acc2); flex-shrink:0; margin-top:2px; }
  .qt-chip .lb { color:#7E9C93; display:block; font-size:10px; letter-spacing:.12em; text-transform:uppercase; margin-bottom:2px; }
  .qt-chip .vl { color:#DCEFE8; }
  .qt-empty { font-size:12.5px; color:#6E8C84; line-height:1.6; }

  .qt-est { margin-top:auto; border:1px solid rgba(52,211,153,.28); border-radius:16px; padding:16px 17px;
    background:linear-gradient(160deg, rgba(16,185,129,.14), rgba(56,189,248,.05)); }
  .qt-est .k { font-size:10px; letter-spacing:.14em; text-transform:uppercase; color:#8ED9BE; }
  .qt-est .v { font-size:26px; font-weight:750; letter-spacing:-.03em; margin:6px 0 3px;
    background:linear-gradient(90deg,#34D399,#7DD3FC); -webkit-background-clip:text; background-clip:text; color:transparent; }
  .qt-est .u { font-size:12.5px; color:#A9C9BF; line-height:1.45; }
  .qt-est .n { font-size:11px; color:#6E8C84; margin-top:9px; line-height:1.5; }
  .qt-est.idle .v { background:none; -webkit-text-fill-color:#3E5B53; color:#3E5B53; }

  .qt-safe { font-size:11.5px; color:#5F7D75; line-height:1.6; }

  /* ── Colonne question ── */
  .qt-main { display:flex; flex-direction:column; padding:26px 5vw 30px; min-width:0; }

  .qt-rail { display:flex; align-items:center; gap:14px; }
  .qt-phase { display:flex; align-items:center; gap:7px; font-size:11.5px; letter-spacing:.1em;
    text-transform:uppercase; color:#5F7D75; white-space:nowrap; transition:color .3s; }
  .qt-phase.on { color:var(--acc2); }
  .qt-phase i { width:6px; height:6px; border-radius:50%; background:#22453D; transition:background .3s, box-shadow .3s; }
  .qt-phase.on i { background:var(--acc2); box-shadow:0 0 0 4px rgba(52,211,153,.16); }
  .qt-phase.done i { background:#2F6B5B; }
  .qt-rail .sep { flex:1; height:1px; background:var(--line); }
  .qt-num { font-size:12px; font-weight:650; color:#6E8C84; letter-spacing:.04em; }

  .qt-bar { height:2px; background:rgba(255,255,255,.08); border-radius:2px; margin-top:16px; overflow:hidden; }
  .qt-bar i { display:block; height:100%; width:0; border-radius:2px;
    background:linear-gradient(90deg,var(--acc),var(--blue)); transition:width .5s cubic-bezier(.3,1,.4,1); }

  .qt-scene { flex:1; display:flex; align-items:center; position:relative; padding:30px 0; }
  .qt-steps { width:100%; max-width:840px; position:relative; }
  .qt-step { opacity:0; transform:translateY(26px); pointer-events:none; position:absolute; inset:0 0 auto 0;
    transition:opacity .40s ease, transform .45s cubic-bezier(.22,1,.36,1); }
  .qt-step.on { opacity:1; transform:none; pointer-events:auto; position:relative; }
  .qt-step.out { opacity:0; transform:translateY(-26px); }

  .qt-ask { margin-bottom:26px; }
  .qt-ask h1 { font-size:clamp(23px,3.1vw,33px); font-weight:640; letter-spacing:-.035em; line-height:1.2;
    margin:0; color:#F3FBF8; }
  .qt-ask p { font-size:14.5px; color:#8CA79E; margin:11px 0 0; line-height:1.55; max-width:580px; }

  .qt-opts { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:11px; }
  .qt-opts.one { grid-template-columns:1fr; max-width:560px; }
  .qt-opts.three { grid-template-columns:repeat(3,minmax(0,1fr)); }

  .qt-opt { position:relative; display:flex; align-items:center; gap:13px; text-align:left; cursor:pointer;
    background:rgba(255,255,255,.045); border:1px solid var(--line); border-radius:14px; padding:14px 15px;
    color:#E4F3EE; font-family:inherit; font-size:14.5px; font-weight:500; line-height:1.35;
    transition:border-color .18s, background .18s, transform .16s, box-shadow .18s;
    animation:qtopt .42s cubic-bezier(.22,1,.36,1) both; animation-delay:calc(var(--i) * 38ms); }
  @keyframes qtopt { from { opacity:0; transform:translateY(14px); } to { opacity:1; transform:none; } }
  .qt-opt .ic { width:36px; height:36px; border-radius:10px; flex-shrink:0; display:grid; place-items:center;
    background:rgba(52,211,153,.10); border:1px solid rgba(52,211,153,.20); color:var(--acc2);
    transition:background .18s, color .18s, border-color .18s; }
  .qt-opt .ic svg { width:18px; height:18px; display:block; }
  .qt-opt .tx { flex:1; min-width:0; }
  .qt-opt .kb { font-size:10.5px; font-weight:700; color:#5F7D75; border:1px solid var(--line);
    border-radius:6px; padding:2px 6px; flex-shrink:0; transition:color .18s, border-color .18s; }
  .qt-opt:hover { border-color:rgba(52,211,153,.5); background:rgba(52,211,153,.07); transform:translateY(-2px);
    box-shadow:0 16px 34px -20px rgba(16,185,129,.9); }
  .qt-opt.sel { border-color:var(--acc2); background:rgba(16,185,129,.14);
    box-shadow:0 0 0 1px rgba(52,211,153,.35), 0 16px 34px -20px rgba(16,185,129,.9); }
  .qt-opt.sel .ic { background:var(--acc); border-color:var(--acc); color:#04231A; }
  .qt-opt.sel .kb { color:var(--acc2); border-color:rgba(52,211,153,.45); }

  /* ── Étape finale ── */
  .qt-unlock { display:grid; grid-template-columns:1.05fr .95fr; gap:24px; align-items:start; }
  .qt-teaser { border:1px solid rgba(52,211,153,.26); border-radius:18px; padding:20px 21px;
    background:linear-gradient(160deg, rgba(16,185,129,.13), rgba(56,189,248,.045)); }
  .qt-teaser .big { font-size:33px; font-weight:750; letter-spacing:-.035em; line-height:1.1;
    background:linear-gradient(90deg,#34D399,#7DD3FC); -webkit-background-clip:text; background-clip:text; color:transparent; }
  .qt-teaser .cap { font-size:12.5px; color:#A9C9BF; margin-top:5px; line-height:1.5; }
  .qt-teaser .row { display:flex; align-items:center; justify-content:space-between; gap:14px;
    padding:11px 0; border-top:1px solid rgba(255,255,255,.09); font-size:13.5px; color:#CFE9E0; }
  .qt-teaser .row:first-of-type { margin-top:16px; }
  .qt-teaser .row .lk { display:flex; align-items:center; gap:6px; color:#6E8C84; font-size:12px; white-space:nowrap; }
  .qt-teaser .row .lk svg { width:13px; height:13px; }
  .qt-teaser .blur { filter:blur(6px); opacity:.8; user-select:none; font-weight:650; color:#9FE7C6; white-space:nowrap; }

  .qt-form { display:flex; flex-direction:column; gap:11px; }
  .qt-form label { font-size:11.5px; letter-spacing:.12em; text-transform:uppercase; color:#6E8C84; }
  .qt-form input { padding:15px 16px; border-radius:13px; border:1px solid var(--line2); font-size:15.5px;
    font-family:inherit; outline:none; background:rgba(255,255,255,.05); color:#F3FBF8;
    transition:border-color .16s, box-shadow .16s, background .16s; }
  .qt-form input::placeholder { color:#5F7D75; }
  .qt-form input:focus { border-color:var(--acc2); background:rgba(16,185,129,.09);
    box-shadow:0 0 0 3px rgba(16,185,129,.16); }
  .qt-form input.err { border-color:#F87171; box-shadow:0 0 0 3px rgba(248,113,113,.16); }

  /* ── Navigation ── */
  .qt-nav { display:flex; align-items:center; gap:14px; margin-top:26px; }
  .qt-back, .qt-skip { background:none; border:1px solid transparent; color:#7E9C93; font-size:13.5px;
    cursor:pointer; font-family:inherit; display:flex; align-items:center; gap:7px; padding:10px 12px;
    border-radius:11px; transition:color .16s, border-color .16s, background .16s; }
  .qt-back svg, .qt-skip svg { width:15px; height:15px; }
  .qt-back:hover, .qt-skip:hover { color:#E4F3EE; border-color:var(--line); background:rgba(255,255,255,.04); }
  .qt-skip { margin-left:auto; }
  .qt-go { margin-left:auto; display:flex; align-items:center; gap:9px; border:none; cursor:pointer;
    font-family:inherit; font-size:15px; font-weight:680; letter-spacing:-.01em; color:#03251B;
    background:linear-gradient(90deg,#34D399,#5EEAD4 55%,#7DD3FC); border-radius:13px; padding:14px 26px;
    box-shadow:0 18px 40px -18px rgba(52,211,153,.95); transition:transform .14s, filter .16s, opacity .16s; }
  .qt-go svg { width:17px; height:17px; }
  .qt-go:hover { transform:translateY(-2px); filter:brightness(1.06); }
  .qt-go:disabled { opacity:.35; cursor:not-allowed; transform:none; box-shadow:none; }
  .qt-hint { font-size:12px; color:#5F7D75; margin-top:14px; line-height:1.5; }

  /* ── Analyse : plein écran, aucun clic possible ── */
  .qt-load { position:fixed; inset:0; z-index:60; display:none; place-items:center; text-align:center;
    background:radial-gradient(80% 60% at 50% 42%, #0A2E24, #041712 72%); padding:24px; }
  .qt-load.on { display:grid; animation:qtfade .5s ease both; }
  @keyframes qtfade { from { opacity:0; } to { opacity:1; } }
  .qt-load img { width:92px; height:92px; border-radius:50%; object-fit:cover; border:2px solid rgba(52,211,153,.7);
    box-shadow:0 0 0 8px rgba(16,185,129,.10); margin-bottom:26px; }
  .qt-load h2 { font-size:clamp(23px,3vw,30px); font-weight:400; letter-spacing:-.03em; margin:0 0 10px; color:#F3FBF8; }
  .qt-load h2 b { font-weight:700; }
  .qt-load p { font-size:14.5px; color:#8CA79E; min-height:24px; transition:opacity .35s; margin:0; }
  .qt-pulse { width:170px; height:2px; border-radius:2px; margin:26px auto 0; overflow:hidden;
    background:rgba(255,255,255,.09); }
  .qt-pulse i { display:block; height:100%; width:38%; border-radius:2px;
    background:linear-gradient(90deg,transparent,#34D399,#7DD3FC,transparent); animation:qtslide 1.5s ease-in-out infinite; }
  @keyframes qtslide { 0% { transform:translateX(-110%); } 100% { transform:translateX(320%); } }

  /* ── Écrans plus petits ── */
  @media (max-width:1080px) {
    .qt-shell { grid-template-columns:1fr; }
    .qt-side { flex-direction:row; align-items:center; gap:16px; border-right:none;
      border-bottom:1px solid var(--line); padding:14px 20px; }
    .qt-side .qt-sect, .qt-side .qt-safe, .qt-side .qt-brand { display:none; }
    .qt-milo img { width:46px; height:46px; }
    .qt-milo .rl { display:none; }
    .qt-est { margin:0 0 0 auto; padding:9px 14px; text-align:right; }
    .qt-est.idle { display:none; }
    .qt-est .v { font-size:18px; margin:2px 0 0; } .qt-est .u, .qt-est .n { display:none; }
    .qt-main { padding:20px 22px 30px; }
    .qt-opts, .qt-opts.three { grid-template-columns:1fr; }
    .qt-unlock { grid-template-columns:1fr; }
    .qt-scene { align-items:flex-start; padding:22px 0; }
    .qt-phase span { display:none; }
  }
  @media (max-width:560px) {
    .qt-ask h1 { font-size:22px; }
    .qt-teaser .big { font-size:26px; }
    .qt-go { padding:13px 20px; font-size:14.5px; }
  }
</style>

<div class="qt">
  <div class="qt-beams" aria-hidden="true">
    <span style="--a:-26deg;--l:14%;--d:11s;--s:7deg;--dl:-3s"></span>
    <span style="--a:-12deg;--l:31%;--d:8.5s;--s:9deg;--dl:-6s"></span>
    <span style="--a:4deg;--l:49%;--d:12.5s;--s:6deg;--dl:-1s"></span>
    <span style="--a:17deg;--l:66%;--d:9.5s;--s:8deg;--dl:-4.5s"></span>
    <span style="--a:30deg;--l:84%;--d:10.5s;--s:7deg;--dl:-2s"></span>
  </div>
  <div class="qt-glow" aria-hidden="true"></div>

  <div class="qt-shell">
    <aside class="qt-side">
      <div class="qt-brand">
        <svg width="26" height="26" viewBox="0 0 32 32" fill="none"><rect width="32" height="32" rx="9" fill="#0A3A2C"/><path d="M16 7L24.5 24" stroke="#34D399" stroke-width="2.4" stroke-linecap="round"/><path d="M16 7L7.5 24" stroke="#34D399" stroke-width="2.4" stroke-linecap="round"/><line x1="10.5" y1="19" x2="21.5" y2="19" stroke="#34D399" stroke-width="2" stroke-linecap="round"/><circle cx="16" cy="7" r="2" fill="#7DD3FC"/></svg>
        <span><b>ABYS</b><i> AI</i></span>
      </div>

      <div class="qt-milo">
        <img src="/assets/img/milo-avatar.jpg" alt="Milo">
        <div>
          <div class="nm">Milo</div>
          <div class="rl">Je mène cet audit, je croise vos réponses avec 300 outils, et je vous rends un plan.</div>
        </div>
      </div>

      <div class="qt-sect" style="flex:1;min-height:0">
        <h3>Ce que je sais de vous</h3>
        <div class="qt-chips" id="qt-chips"></div>
        <div class="qt-empty" id="qt-empty">Rien encore. Chaque réponse affine mon analyse, et vous verrez cette colonne se remplir.</div>
      </div>

      <div class="qt-est idle" id="qt-est">
        <div class="k">Estimation en cours</div>
        <div class="v" id="qt-estv">en attente</div>
        <div class="u" id="qt-estu">par semaine, récupérables</div>
        <div class="n" id="qt-estn">Je l'affine à chaque réponse, puis je la vérifie outil par outil.</div>
      </div>

      <div class="qt-safe">Gratuit, sans carte bancaire. Vos réponses servent à produire votre audit, rien d'autre.</div>
    </aside>

    <main class="qt-main">
      <div class="qt-rail" id="qt-rail"></div>
      <div class="qt-bar"><i id="qt-bar"></i></div>
      <div class="qt-scene"><div class="qt-steps" id="qt-steps"></div></div>
    </main>
  </div>

  <div class="qt-load" id="qt-load">
    <div>
      <img src="/assets/img/milo-avatar.jpg" alt="Milo">
      <h2>Merci. <b>J'analyse vos réponses.</b></h2>
      <p id="qt-loadmsg">Je croise votre profil avec plus de 300 outils IA.</p>
      <div class="qt-pulse"><i></i></div>
    </div>
  </div>
</div>

<script src="/assets/js/app.js"></script>
<script src="/assets/js/audit.js"></script>
<script>
/* ════════ Icônes · trait fin, aucune emoji ════════ */
var S = function (p) {
  return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">' + p + '</svg>';
};
var BARS = function (n) {
  var o = '';
  for (var i = 1; i <= 4; i++) {
    var h = 3 + i * 3.7, y = 20.5 - h;
    o += '<rect x="' + (3 + (i - 1) * 5.1) + '" y="' + y.toFixed(1) + '" width="3.4" height="' + h.toFixed(1) +
         '" rx="1.2" fill="currentColor" opacity="' + (i <= n ? 1 : .22) + '"/>';
  }
  return '<svg viewBox="0 0 24 24" fill="none">' + o + '</svg>';
};

var I = {
  wrench:   S('<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.8-3.8a6 6 0 0 1-7.9 7.9l-6.9 6.9a2.1 2.1 0 0 1-3-3l6.9-6.9a6 6 0 0 1 7.9-7.9z"/>'),
  plate:    S('<path d="M3 2v7a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2V2"/><path d="M5.5 2v20"/><path d="M20 2a4 4 0 0 0-3 3.9V13h3z"/><path d="M20 13v9"/>'),
  bag:      S('<path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/>'),
  briefcase:S('<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>'),
  pulse:    S('<path d="M19 14c1.5-1.5 3-3.2 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.8 0-3 .5-4.5 2-1.5-1.5-2.7-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4 3 5.5l7 7z"/><path d="M3.5 13h4l1.5-2.5 2 5 2-7 1.5 4.5h6"/>'),
  bed:      S('<path d="M2 4v16"/><path d="M2 9h16a3 3 0 0 1 3 3v8"/><path d="M2 16h19"/><circle cx="7" cy="12" r="1.6"/>'),
  truck:    S('<path d="M14 17V6a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v11h2"/><path d="M9 17h3"/><path d="M18 17h3a1 1 0 0 0 1-1v-3.3a1 1 0 0 0-.2-.6L19 8.4a1 1 0 0 0-.8-.4H14"/><circle cx="16" cy="17.5" r="2"/><circle cx="6.5" cy="17.5" r="2"/>'),
  leaf:     S('<path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.5 19 2c1 2 2 4.2 2 8 0 5.5-4.8 10-10 10z"/><path d="M2 21c0-3 1.9-5.4 5.1-6C9.5 14.5 12 13 13 12"/>'),
  house:    S('<path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M9 22V13h6v9"/>'),
  spark:    S('<path d="m12 3 1.9 5.8L19.7 11l-5.8 1.9L12 18.7l-1.9-5.8L4.3 11l5.8-2.2z"/><path d="M19 3v3"/><path d="M20.5 4.5h-3"/>'),
  user:     S('<path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>'),
  users:    S('<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.9"/><path d="M16 3.1a4 4 0 0 1 0 7.8"/>'),
  team:     S('<circle cx="9" cy="7" r="3"/><circle cx="17" cy="9" r="2.4"/><path d="M2 20v-1.5A4.5 4.5 0 0 1 6.5 14h5a4.5 4.5 0 0 1 4.5 4.5V20"/><path d="M18 20v-1.6a3.4 3.4 0 0 0-1.2-2.6"/>'),
  building: S('<rect x="4" y="2" width="16" height="20" rx="2"/><path d="M9 22v-5h6v5"/><path d="M8.5 6.5h2"/><path d="M13.5 6.5h2"/><path d="M8.5 11h2"/><path d="M13.5 11h2"/>'),
  factory:  S('<path d="M2 22V10l6 4V10l6 4V6a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v16z"/><path d="M2 22h20"/>'),
  euro:     S('<path d="M4 10h11"/><path d="M4 14h9"/><path d="M19 5.5A7.4 7.4 0 0 0 14 4c-4.3 0-7.5 3.6-7.5 8s3.2 8 7.5 8a7.4 7.4 0 0 0 5-1.5"/>'),
  chart:    S('<path d="M3 3v16a2 2 0 0 0 2 2h16"/><rect x="7" y="12" width="3" height="6" rx="1"/><rect x="13" y="8" width="3" height="10" rx="1"/>'),
  rise:     S('<path d="M3 3v16a2 2 0 0 0 2 2h16"/><path d="m7 15 4-4 3 3 5-6"/><path d="M19 8h-3"/><path d="M19 8v3"/>'),
  gem:      S('<path d="M6 3h12l4 6-10 12L2 9z"/><path d="M2 9h20"/><path d="m10 3-2 6 4 12 4-12-2-6"/>'),
  lock:     S('<rect x="4" y="10.5" width="16" height="10.5" rx="2.5"/><path d="M8 10.5V7a4 4 0 0 1 8 0v3.5"/>'),
  mail:     S('<rect x="2" y="4.5" width="20" height="15" rx="2.5"/><path d="m3 7 8.2 5.6a1.5 1.5 0 0 0 1.6 0L21 7"/>'),
  receipt:  S('<path d="M5 2.5v19l2.3-1.3 2.3 1.3 2.4-1.3 2.3 1.3 2.4-1.3 2.3 1.3v-19l-2.3 1.3-2.4-1.3L12 4.1 9.6 2.8 7.3 4.1z"/><path d="M9 9h6"/><path d="M9 13h4"/>'),
  calendar: S('<rect x="3" y="4.5" width="18" height="17" rx="2.5"/><path d="M16 2.5v4"/><path d="M8 2.5v4"/><path d="M3 10h18"/>'),
  chat:     S('<path d="M21 14.5a2.5 2.5 0 0 1-2.5 2.5H8l-5 4V5.5A2.5 2.5 0 0 1 5.5 3h13A2.5 2.5 0 0 1 21 5.5z"/><path d="M8 9h8"/><path d="M8 12.5h5"/>'),
  megaphone:S('<path d="m3 11 15-6v14L3 14z"/><path d="M11.5 16.6a3 3 0 0 1-5.6-1.5"/><path d="M20.5 9.5a3 3 0 0 1 0 5"/>'),
  calc:     S('<rect x="4" y="2.5" width="16" height="19" rx="2.5"/><path d="M8 7h8"/><path d="M8 12h8"/><path d="M8 16.5h8"/>'),
  userplus: S('<path d="M15 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><path d="M19 8v6"/><path d="M22 11h-6"/>'),
  box:      S('<path d="M21 8.2a2 2 0 0 0-1-1.7l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8.2v7.6a2 2 0 0 0 1 1.7l7 4a2 2 0 0 0 2 0l7-4a2 2 0 0 0 1-1.7z"/><path d="m3.4 7.3 8.6 5 8.6-5"/><path d="M12 22v-9.7"/>'),
  clock:    S('<circle cx="12" cy="12" r="9"/><path d="M12 7v5.3l3.4 2"/>'),
  eye:      S('<path d="M2 12s3.8-7 10-7 10 7 10 7-3.8 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/>'),
  wallet:   S('<path d="M19 7V5.5A2.5 2.5 0 0 0 16.5 3H5a2 2 0 0 0 0 4h14a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5"/><path d="M17.5 12.5h.01"/>'),
  feather:  S('<path d="M20.2 12.2a6 6 0 0 0-8.5-8.5L5 10.5V19h8.5z"/><path d="M16 8 3 21"/><path d="M17.5 15H9"/>'),
  rocket:   S('<path d="M4.5 16.5c-1.5 1.3-2 5-2 5s3.7-.5 5-2a2.2 2.2 0 0 0-3-3z"/><path d="m12 15-3-3a22 22 0 0 1 2-4A12.9 12.9 0 0 1 22 2c0 2.7-.8 7.5-6 11a22 22 0 0 1-4 2z"/><path d="M9 12H4s.6-3 2-4c1.6-1.1 5 0 5 0"/><path d="M12 15v5s3-.6 4-2c1.1-1.6 0-5 0-5"/>'),
  bot:      S('<rect x="3" y="10" width="18" height="11" rx="3"/><circle cx="12" cy="5" r="2"/><path d="M12 7v3"/><path d="M8.5 15h.01"/><path d="M15.5 15h.01"/>'),
  file:     S('<path d="M14.5 2H7a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6.5z"/><path d="M14 2v5h5"/><path d="M9 13h6"/><path d="M9 17h4"/>'),
  image:    S('<rect x="3" y="3" width="18" height="18" rx="3"/><circle cx="8.8" cy="9" r="1.8"/><path d="m4 17.5 4.5-4a2 2 0 0 1 2.7 0L16 18"/><path d="m14 15 1.7-1.6a2 2 0 0 1 2.7 0L21 16"/>'),
  none:     S('<circle cx="12" cy="12" r="9"/><path d="m9 9 6 6"/><path d="M15 9l-6 6"/>'),
  globe:    S('<circle cx="12" cy="12" r="9"/><path d="M3 12h18"/><path d="M12 3a15 15 0 0 1 0 18 15 15 0 0 1 0-18z"/>'),
  arrowR:   S('<path d="M5 12h13"/><path d="m12 5 7 7-7 7"/>'),
  arrowL:   S('<path d="M19 12H6"/><path d="m12 19-7-7 7-7"/>'),
  unlock:   S('<rect x="4" y="10.5" width="16" height="10.5" rx="2.5"/><path d="M8 10.5V7a4 4 0 0 1 7.5-2"/>')
};

/* ════════ Ce que Milo sait avant de parler ════════ */
var KNOWN_URL = (window.ABYS && ABYS.get && ABYS.get('audit_url')) || '';
var HOST = KNOWN_URL ? String(KNOWN_URL).replace(/^https?:\/\//, '').replace(/^www\./, '').split('/')[0] : '';

/* ════════ L'entretien ════════ */
var PHASES = ['Votre entreprise', 'Votre quotidien', 'Vos priorités'];

var STEPS = [
  {
    key: 'Secteur', ph: 0, type: 'single', required: true, cols: 2,
    q: HOST ? 'Sur ' + HOST + ', vous faites quoi exactement ?' : 'Quel est votre métier ?',
    sub: HOST
      ? "Je n'ai pas pu tout lire sur votre site, et je préfère partir de votre réponse plutôt que d'une supposition."
      : "Tout part de là : je ne recommande pas les mêmes outils à un couvreur et à un cabinet de conseil.",
    opts: [
      [I.wrench, 'Artisanat, bâtiment, travaux'], [I.plate, 'Restauration, café, traiteur'],
      [I.bag, 'Commerce, boutique, e-commerce'],  [I.briefcase, 'Services aux entreprises, conseil'],
      [I.pulse, 'Santé, soin, bien-être'],        [I.bed, 'Hôtellerie, tourisme, location'],
      [I.truck, 'Transport, livraison, logistique'], [I.leaf, 'Agriculture, viticulture, élevage'],
      [I.house, 'Immobilier, gestion, syndic'],   [I.spark, 'Autre activité']
    ]
  },
  {
    key: 'Taille', ph: 0, type: 'single', required: true, cols: 3,
    q: 'Vous êtes combien à faire tourner tout ça ?',
    sub: "Seul, on cherche à se dégager du temps. À plusieurs, on cherche surtout à ne plus faire deux fois la même chose.",
    opts: [ [I.user, 'Je suis seul'], [I.users, '2 à 5 personnes'], [I.team, '6 à 20 personnes'],
            [I.building, '21 à 50 personnes'], [I.factory, 'Plus de 50'] ]
  },
  {
    key: "Chiffre d'affaires", ph: 0, type: 'single', cols: 3,
    q: "Votre chiffre d'affaires annuel, en ordre de grandeur ?",
    sub: "Il ne me sert qu'à convertir vos heures gagnées en euros. Sans lui, je vous donne du temps sans savoir ce qu'il vaut chez vous.",
    opts: [ [I.euro, 'Moins de 100 k€'], [I.chart, '100 à 300 k€'], [I.rise, '300 k€ à 1 M€'],
            [I.gem, 'Plus de 1 M€'], [I.lock, 'Je préfère ne pas dire'] ]
  },
  {
    key: 'Tâches chronophages', ph: 1, type: 'multi', required: true, cols: 2,
    q: 'Sur quoi passez-vous du temps sans que ça rapporte ?',
    sub: "Plusieurs réponses possibles. C'est exactement là que je vais chercher vos heures.",
    opts: [
      [I.mail, 'Devis, relances, emails'], [I.receipt, 'Factures et impayés'],
      [I.calendar, 'Planning et rendez-vous'], [I.chat, 'Répondre aux mêmes questions clients'],
      [I.megaphone, 'Communication et réseaux sociaux'], [I.calc, 'Comptabilité et paperasse'],
      [I.userplus, 'Recrutement et gestion du personnel'], [I.box, 'Stocks, achats, fournisseurs']
    ]
  },
  {
    key: 'Temps admin/semaine', ph: 1, type: 'single', cols: 2,
    q: "Sur une semaine normale, ça représente combien d'heures ?",
    sub: 'Une estimation au doigt mouillé suffit. La moyenne des dirigeants de TPE tourne autour de 8 heures.',
    opts: [ [BARS(1), 'Moins de 5 heures'], [BARS(2), '5 à 10 heures'],
            [BARS(3), '10 à 20 heures'], [BARS(4), 'Plus de 20 heures'] ]
  },
  {
    key: 'Outils déjà utilisés', ph: 1, type: 'multi', cols: 2,
    q: "Qu'est-ce qui est déjà en place chez vous ?",
    sub: "Si la réponse est rien, tant mieux : je pars d'une page blanche et les premiers gains arrivent plus vite.",
    opts: [
      [I.bot, 'ChatGPT ou une autre IA'], [I.file, 'Un logiciel de facturation'],
      [I.calendar, 'Un agenda ou une prise de rendez-vous en ligne'], [I.image, 'Canva ou un outil de visuels'],
      [I.calc, 'Un logiciel de comptabilité'], [I.globe, 'Un site qui reçoit des demandes'],
      [I.none, 'Rien de tout ça']
    ]
  },
  {
    key: 'Objectifs prioritaires', ph: 2, type: 'multi', required: true, cols: 2,
    q: "Si vous ne deviez régler qu'une chose cette année ?",
    sub: 'Une ou deux réponses. Je classerai mes recommandations dans cet ordre.',
    opts: [
      [I.clock, 'Arrêter de courir après le temps'], [I.rise, 'Trouver plus de clients'],
      [I.eye, 'Être visible quand on cherche mon métier'], [I.wallet, 'Sécuriser ma trésorerie'],
      [I.feather, 'Souffler, sortir la tête de l\'eau'], [I.rocket, 'Lancer une nouvelle offre']
    ]
  },
  {
    key: 'Appétence numérique', ph: 2, type: 'single', cols: 2,
    q: 'Avec les outils informatiques, vous vous situez où ?',
    sub: "Je calibre la difficulté de ce que je vais vous proposer. Personne n'est jugé ici.",
    opts: [ [BARS(1), "Ce n'est vraiment pas mon truc"], [BARS(2), 'Je me débrouille sur les bases'],
            [BARS(3), 'Plutôt à l\'aise'], [BARS(4), 'Très à l\'aise, curieux de tout tester'] ]
  },
  {
    key: 'Adoption équipe', ph: 2, type: 'single', cols: 3,
    skipIf: function (a) { return (a['Taille'] || '').indexOf('seul') > -1; },
    q: 'Et votre équipe, face à un nouvel outil ?',
    sub: "Un outil que personne n'ouvre ne sert à rien. Autant en tenir compte tout de suite.",
    opts: [ [BARS(1), 'Plutôt réticente'], [BARS(2), "Prête à essayer si c'est simple"], [BARS(3), 'Motivée et curieuse'] ]
  },
  { key: '_contact', ph: 2, type: 'contact', required: true }
];

/* ════════ Estimation vivante ════════ */
var HOURS = { 'Moins de 5 heures': 4, '5 à 10 heures': 7.5, '10 à 20 heures': 14.5, 'Plus de 20 heures': 23 };
var RATE  = { 'Moins de 100 k€': 26, '100 à 300 k€': 36, '300 k€ à 1 M€': 52, 'Plus de 1 M€': 72 };
var TEAM  = { 'Je suis seul': 6, '2 à 5 personnes': 9, '6 à 20 personnes': 13, '21 à 50 personnes': 18, 'Plus de 50': 22 };

function estimate() {
  var base = HOURS[answers['Temps admin/semaine']] || TEAM[answers['Taille']] || 0;
  if (!base) return null;
  var ratio = 0.5;
  var nbT = (answers['Tâches chronophages'] || '').split(', ').filter(Boolean).length;
  if (nbT >= 3) ratio += 0.06;
  if (nbT >= 5) ratio += 0.06;
  var ap = answers['Appétence numérique'] || '';
  if (ap.indexOf('pas mon truc') > -1) ratio -= 0.10;
  if (ap.indexOf('Très à l') > -1) ratio += 0.07;
  if ((answers['Outils déjà utilisés'] || '').indexOf('Rien de tout ça') > -1) ratio += 0.05;
  var h = base * Math.max(0.28, Math.min(0.68, ratio));
  var rate = RATE[answers["Chiffre d'affaires"]] || 34;
  return { lo: Math.max(1, Math.round(h * 0.85)), hi: Math.round(h * 1.18), eur: Math.round(h * 4.33 * rate / 10) * 10 };
}

function paintSide() {
  var chips = document.getElementById('qt-chips');
  var empty = document.getElementById('qt-empty');
  var rows = [
    ['Métier', answers['Secteur'], I.briefcase],
    ['Équipe', answers['Taille'], I.users],
    ['Volume', answers["Chiffre d'affaires"], I.chart],
    ['Temps perdu', answers['Tâches chronophages'], I.clock],
    ['Charge', answers['Temps admin/semaine'], I.calendar],
    ['Déjà en place', answers['Outils déjà utilisés'], I.bot],
    ['Priorité', answers['Objectifs prioritaires'], I.rocket]
  ].filter(function (r) { return r[1]; });

  empty.style.display = rows.length ? 'none' : '';
  var html = rows.map(function (r) {
    return '<div class="qt-chip">' + r[2] + '<div><span class="lb">' + r[0] + '</span><span class="vl">' + r[1] + '</span></div></div>';
  }).join('');
  if (chips.getAttribute('data-h') !== html) { chips.setAttribute('data-h', html); chips.innerHTML = html; }

  var e = estimate();
  if (e) {
    document.getElementById('qt-est').classList.remove('idle');
    document.getElementById('qt-estv').textContent = e.lo + ' à ' + e.hi + ' h';
    document.getElementById('qt-estu').textContent = 'par semaine, soit environ ' + e.eur.toLocaleString('fr-FR') + ' € par mois';
  }
}

/* ════════ Rendu ════════ */
var answers = {};
var cur = 0;
var $steps = document.getElementById('qt-steps');
function seqOf() { return STEPS.filter(function (s) { return !s.skipIf || !s.skipIf(answers); }); }

function paintRail(st, idx, total) {
  document.getElementById('qt-rail').innerHTML =
    PHASES.map(function (p, i) {
      var c = i === st.ph ? 'on' : (i < st.ph ? 'done' : '');
      return '<div class="qt-phase ' + c + '"><i></i><span>' + p + '</span></div>';
    }).join('<div class="sep"></div>') +
    '<div class="sep"></div><div class="qt-num">' + (idx + 1) + ' / ' + total + '</div>';
  document.getElementById('qt-bar').style.width = Math.round((idx / total) * 100) + '%';
}

function questionHTML(st) {
  var multi = st.type === 'multi';
  var cls = st.cols === 3 ? ' three' : (st.cols === 1 ? ' one' : '');
  var opts = st.opts.map(function (o, i) {
    return '<button class="qt-opt" style="--i:' + i + '" onclick="pick(' + i + ')">' +
      '<span class="ic">' + o[0] + '</span><span class="tx">' + o[1] + '</span>' +
      '<span class="kb">' + (i < 9 ? (i + 1) : 0) + '</span>' + '</button>';
  }).join('');

  return '<div class="qt-ask"><h1>' + st.q + '</h1>' + (st.sub ? '<p>' + st.sub + '</p>' : '') + '</div>' +
    '<div class="qt-opts' + cls + '">' + opts + '</div>' +
    '<div class="qt-nav">' +
      (cur > 0 ? '<button class="qt-back" onclick="go(-1)">' + I.arrowL + 'Retour</button>' : '<span></span>') +
      (multi
        ? '<button class="qt-go" id="qt-next" onclick="go(1)"' + (answers[st.key] ? '' : ' disabled') + '>Continuer' + I.arrowR + '</button>'
        : (st.required ? '' : '<button class="qt-skip" onclick="go(1)">Passer cette question</button>')) +
    '</div>' +
    '<div class="qt-hint">' + (multi ? 'Plusieurs réponses possibles. Le clavier fonctionne aussi.' : 'Un clic suffit. Le clavier fonctionne aussi.') + '</div>';
}

function contactHTML() {
  var e = estimate();
  var big = e ? e.lo + ' à ' + e.hi + ' h' : 'Votre plan';
  var cap = e
    ? "par semaine, récupérables d'après vos réponses, soit environ " + e.eur.toLocaleString('fr-FR') + ' € par mois. Je vérifie ce chiffre outil par outil dans votre audit.'
    : 'Votre plan personnalisé est prêt à être calculé.';
  return '<div class="qt-ask"><h1>C\'est fini. Votre plan est prêt.</h1>' +
      "<p>Il s'affiche à l'écran dans une trentaine de secondes, et je vous l'envoie par email pour que vous puissiez le relire au calme.</p></div>" +
    '<div class="qt-unlock">' +
      '<div class="qt-teaser">' +
        '<div class="big">' + big + '</div><div class="cap">' + cap + '</div>' +
        '<div class="row"><span>Les outils exacts pour votre métier</span><span class="blur">5 outils</span></div>' +
        '<div class="row"><span>Votre score de maturité IA</span><span class="blur">72 / 100</span></div>' +
        '<div class="row"><span>Le plan d\'installation, étape par étape</span><span class="lk">' + I.lock + 'verrouillé</span></div>' +
        '<div class="row"><span>Ce que vos concurrents utilisent déjà</span><span class="lk">' + I.lock + 'verrouillé</span></div>' +
      '</div>' +
      '<div class="qt-form">' +
        '<label for="qt-email">Où je vous envoie tout ça</label>' +
        '<input type="text" id="qt-prenom" placeholder="Votre prénom (facultatif)" autocomplete="given-name">' +
        '<input type="email" id="qt-email" placeholder="vous@votre-entreprise.fr" autocomplete="email" inputmode="email">' +
        '<div class="qt-nav" style="margin-top:4px">' +
          '<button class="qt-back" onclick="go(-1)">' + I.arrowL + 'Retour</button>' +
          '<button class="qt-go" id="qt-submit" onclick="submitTunnel()">' + I.unlock + 'Voir mon audit</button>' +
        '</div>' +
        '<div class="qt-hint">Gratuit, sans carte bancaire. Aucune revente de vos données, aucun appel commercial.</div>' +
      '</div>' +
    '</div>';
}

function render(idx) {
  var seq = seqOf();
  cur = Math.max(0, Math.min(idx, seq.length - 1));
  var st = seq[cur];
  paintRail(st, cur, seq.length);
  paintSide();
  ABYS.track(st.type === 'contact' ? 'tunnel_contact' : 'tunnel_q' + Math.min(9, cur + 1));

  var old = $steps.querySelector('.qt-step.on');
  if (old) { old.classList.remove('on'); old.classList.add('out'); setTimeout(function () { old.remove(); }, 460); }

  var el = document.createElement('div');
  el.className = 'qt-step';
  el.innerHTML = st.type === 'contact' ? contactHTML() : questionHTML(st);
  $steps.appendChild(el);
  requestAnimationFrame(function () { requestAnimationFrame(function () { el.classList.add('on'); }); });

  var prev = answers[st.key];
  if (prev && st.type !== 'contact') {
    var list = st.type === 'multi' ? prev.split(', ') : [prev];
    el.querySelectorAll('.qt-opt').forEach(function (b, i) {
      if (list.indexOf(st.opts[i][1]) > -1) b.classList.add('sel');
    });
    var nx = el.querySelector('#qt-next'); if (nx) nx.disabled = false;
  }
  if (st.type === 'contact') setTimeout(function () {
    var f = el.querySelector('#qt-email'); if (f) f.focus();
  }, 520);
}

/* ════════ Interaction ════════ */
function pick(i) {
  var st = seqOf()[cur];
  var btns = $steps.querySelectorAll('.qt-step.on .qt-opt');
  if (st.type === 'multi') {
    btns[i].classList.toggle('sel');
    var sel = [];
    btns.forEach(function (b, k) { if (b.classList.contains('sel')) sel.push(st.opts[k][1]); });
    answers[st.key] = sel.join(', ');
    var nx = $steps.querySelector('.qt-step.on #qt-next');
    if (nx) nx.disabled = sel.length === 0;
    paintSide();
  } else {
    btns.forEach(function (b) { b.classList.remove('sel'); });
    btns[i].classList.add('sel');
    answers[st.key] = st.opts[i][1];
    paintSide();
    setTimeout(function () { go(1); }, 280);
  }
}

function go(dir) {
  var seq = seqOf(), st = seq[cur];
  if (dir > 0 && st.required && st.type !== 'contact' && !answers[st.key]) return;
  if (cur + dir >= seq.length || cur + dir < 0) return;
  render(cur + dir);
}

document.addEventListener('keydown', function (e) {
  var st = seqOf()[cur];
  if (!st) return;
  if (st.type === 'contact') {
    if (e.key === 'Enter') { var b = document.getElementById('qt-submit'); if (b) b.click(); }
    return;
  }
  var len = st.opts ? st.opts.length : 0;
  if (e.key === '0' && len >= 10) pick(9);
  var n = parseInt(e.key, 10);
  if (n >= 1 && n <= len) pick(n - 1);
  if (e.key === 'Enter' && st.type === 'multi' && answers[st.key]) go(1);
  if (e.key === 'Backspace' && cur > 0) { e.preventDefault(); go(-1); }
});

/* ════════ Analyse ════════ */
var LOAD_MSGS = [
  'Je croise votre profil avec plus de 300 outils IA.',
  'Je garde uniquement ceux qui tiennent la route dans votre métier.',
  'Je chiffre vos gains en heures, puis en euros.',
  'Je regarde ce que vos concurrents ont déjà mis en place.',
  'Je rédige votre plan. Encore quelques secondes.'
];

async function submitTunnel() {
  var email  = (document.getElementById('qt-email').value || '').trim();
  var prenom = (document.getElementById('qt-prenom').value || '').trim();
  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
    var f = document.getElementById('qt-email'); f.classList.add('err'); f.focus(); return;
  }

  document.getElementById('qt-load').classList.add('on');
  var mi = 0;
  var t = setInterval(function () {
    mi = (mi + 1) % LOAD_MSGS.length;
    var p = document.getElementById('qt-loadmsg');
    p.style.opacity = 0;
    setTimeout(function () { p.textContent = LOAD_MSGS[mi]; p.style.opacity = 1; }, 320);
  }, 3400);

  var payload = {};
  STEPS.forEach(function (s) { if (s.key !== '_contact' && answers[s.key]) payload[s.key] = answers[s.key]; });

  try {
    var lead = await ABYS.api('leads.php', {
      action: 'create', url: KNOWN_URL || '', email: email,
      sector: answers['Secteur'] || '', source: 'questionnaire'
    });
    ABYS.store('lead_id', lead.lead_id);
    if (prenom) ABYS.store('prenom', prenom);
    ABYS.track('tunnel_email');
    await Audit.runFromQuestionnaire(payload);
  } catch (err) {
    clearInterval(t);
    document.getElementById('qt-load').classList.remove('on');
    var fe = document.getElementById('qt-email');
    if (fe) fe.classList.add('err');
    alert("Je n'ai pas réussi à lancer l'analyse : " + err.message);
  }
}

ABYS.track('tunnel_ouvert');
render(0);
</script>
