<?php
$page_title = 'Comment ça marche — ABYS AI';
$page_desc  = 'Découvrez comment ABYS analyse votre entreprise, calcule vos gains IA et vous accompagne pas à pas dans la mise en place des outils.';
include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/nav.php';
?>

<style>
.ccm-step {
  display: grid;
  grid-template-columns: 88px 1fr;
  gap: 32px;
  align-items: start;
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: var(--r-xl);
  padding: 32px;
  margin-bottom: 20px;
  box-shadow: var(--shadow-sm);
  transition: box-shadow 150ms var(--ease);
}
.ccm-step:hover { box-shadow: var(--shadow-md); }
.ccm-icon-wrap {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 10px;
}
.ccm-icon-circle {
  width: 56px;
  height: 56px;
  border-radius: 16px;
  background: linear-gradient(135deg, rgba(16,185,129,0.1), rgba(14,165,233,0.1));
  border: 1px solid rgba(16,185,129,0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.ccm-num {
  font-size: 11px;
  font-weight: 700;
  color: var(--ink-5);
  letter-spacing: 0.12em;
  text-transform: uppercase;
}
.ccm-title {
  font-size: 20px;
  font-weight: 600;
  color: var(--ink-2);
  margin-bottom: 10px;
  line-height: 1.3;
}
.ccm-desc {
  font-size: 15px;
  color: var(--ink-3);
  line-height: 1.65;
  margin-bottom: 10px;
}
.ccm-detail {
  font-size: 13px;
  color: var(--ink-4);
  line-height: 1.6;
  padding: 10px 14px;
  background: rgba(16,185,129,0.04);
  border-left: 2px solid var(--green);
  border-radius: 0 8px 8px 0;
}
@media(max-width:768px){
  .ccm-step { grid-template-columns: 1fr; }
  .ccm-icon-wrap { flex-direction: row; }
}
</style>

<div class="container" style="padding-top:60px;padding-bottom:80px;max-width:800px">
  <div class="text-center mb-48">
    <div class="badge" style="margin:0 auto 16px">Transparent & Simple</div>
    <h1 style="font-size:44px;font-weight:300;letter-spacing:-0.04em;margin-bottom:14px">
      Comment <strong style="font-weight:700;background:var(--gradient);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text">fonctionne ABYS</strong> ?
    </h1>
    <p style="font-size:17px;color:var(--ink-3);max-width:540px;margin:0 auto;line-height:1.65">
      De l'entrée de votre URL à la mise en place de vos premiers outils IA — voici exactement ce qui se passe.
    </p>
  </div>

  <!-- Étape 01 -->
  <div class="ccm-step reveal">
    <div class="ccm-icon-wrap">
      <div class="ccm-icon-circle">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="url(#g1)" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
          <defs><linearGradient id="g1" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#10B981"/><stop offset="100%" stop-color="#0EA5E9"/></linearGradient></defs>
          <circle cx="12" cy="12" r="10"/>
          <line x1="2" y1="12" x2="22" y2="12"/>
          <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
        </svg>
      </div>
      <span class="ccm-num">01</span>
    </div>
    <div>
      <div class="ccm-title">Vous entrez votre URL</div>
      <p class="ccm-desc">Notre système analyse automatiquement votre site web : secteur d'activité, services proposés, zone géographique, présence en ligne. Si vous n'avez pas de site, un questionnaire de 5 questions prend le relais.</p>
      <p class="ccm-detail">Technologie : scraping respectueux (lecture seule, sans ralentir votre site) + analyse IA en temps réel.</p>
    </div>
  </div>

  <!-- Étape 02 -->
  <div class="ccm-step reveal">
    <div class="ccm-icon-wrap">
      <div class="ccm-icon-circle">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="url(#g2)" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
          <defs><linearGradient id="g2" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#10B981"/><stop offset="100%" stop-color="#0EA5E9"/></linearGradient></defs>
          <path d="M12 2a10 10 0 1 0 0 20A10 10 0 0 0 12 2z"/>
          <path d="M8 12h.01M12 12h.01M16 12h.01"/>
          <path d="M9 7l-1 5h8l-1-5"/>
          <path d="M10 17h4"/>
        </svg>
      </div>
      <span class="ccm-num">02</span>
    </div>
    <div>
      <div class="ccm-title">L'IA analyse votre potentiel</div>
      <p class="ccm-desc">En moins de 30 secondes, notre IA calcule un score d'adoption IA de 0 à 100, identifie les 5 à 10 tâches que vous faites encore manuellement et qui pourraient être automatisées, et estime les gains en temps et en argent pour chacune.</p>
      <p class="ccm-detail">Données basées sur 3 247 audits d'entreprises similaires à la vôtre.</p>
    </div>
  </div>

  <!-- Étape 03 -->
  <div class="ccm-step reveal">
    <div class="ccm-icon-wrap">
      <div class="ccm-icon-circle">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="url(#g3)" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
          <defs><linearGradient id="g3" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#10B981"/><stop offset="100%" stop-color="#0EA5E9"/></linearGradient></defs>
          <line x1="18" y1="20" x2="18" y2="10"/>
          <line x1="12" y1="20" x2="12" y2="4"/>
          <line x1="6"  y1="20" x2="6"  y2="14"/>
          <rect x="3" y="20" width="18" height="1" rx="0.5" fill="url(#g3)"/>
        </svg>
      </div>
      <span class="ccm-num">03</span>
    </div>
    <div>
      <div class="ccm-title">Vous recevez vos résultats</div>
      <p class="ccm-desc">Score global, top 3 des opportunités, simulation de gains — 100% gratuit, immédiatement. Pour aller plus loin, le rapport premium à 249€ détaille chaque opportunité avec l'outil recommandé, un tutoriel personnalisé et le plan d'action complet.</p>
      <p class="ccm-detail">Le rapport est généré spécifiquement pour votre entreprise, pas un rapport générique.</p>
    </div>
  </div>

  <!-- Étape 04 -->
  <div class="ccm-step reveal">
    <div class="ccm-icon-wrap">
      <div class="ccm-icon-circle">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="url(#g4)" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
          <defs><linearGradient id="g4" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#10B981"/><stop offset="100%" stop-color="#0EA5E9"/></linearGradient></defs>
          <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
        </svg>
      </div>
      <span class="ccm-num">04</span>
    </div>
    <div>
      <div class="ccm-title">Vous mettez en place à votre rythme</div>
      <p class="ccm-desc">Chaque outil recommandé vient avec un tutoriel pas-à-pas adapté à votre secteur. Notre assistant IA répond à toutes vos questions (WhatsApp/email) pendant la mise en place. Aucune compétence technique requise.</p>
      <p class="ccm-detail">95% de nos clients implémentent leur premier outil en moins d'une semaine.</p>
    </div>
  </div>

  <div class="text-center mt-48">
    <a href="/" class="btn btn-primary btn-lg">Démarrer mon audit gratuit →</a>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
