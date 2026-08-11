<?php
$page_title = 'Visibilité IA — Apparaissez dans ChatGPT & Google AI — ABYS AI';
$page_description = 'Optimisez votre présence sur les moteurs de recherche IA : ChatGPT, Perplexity, Google AI Overviews. SEO nouvelle génération pour PME.';
include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/nav.php';
?>
<style>
.page-hero { padding: 72px 0 56px; text-align: center; }
.page-hero h1 { font-size: 48px; font-weight: 300; letter-spacing: -0.04em; margin-bottom: 16px; }
.page-hero h1 strong { font-weight: 700; background: var(--gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
.page-hero p { font-size: 18px; color: var(--ink-3); max-width: 580px; margin: 0 auto 40px; line-height: 1.65; }
.platforms { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; margin-bottom: 24px; }
.platform-pill { display: flex; align-items: center; gap: 8px; padding: 10px 20px; background: var(--white); border: 1px solid var(--border); border-radius: var(--r-pill); font-size: 14px; font-weight: 500; color: var(--ink-2); box-shadow: var(--shadow-sm); }
.llm-grid { display: grid; grid-template-columns: repeat(5,1fr); gap: 16px; max-width: 860px; margin: 40px auto 0; }
.llm-card { display: flex; flex-direction: column; align-items: center; gap: 10px; padding: 20px 12px; background: var(--white); border: 1px solid var(--border); border-radius: var(--r-xl); box-shadow: var(--shadow-sm); transition: box-shadow 150ms, transform 150ms; }
.llm-card:hover { box-shadow: var(--shadow-md); transform: translateY(-2px); }
.llm-logo { width: 48px; height: 48px; border-radius: 12px; object-fit: contain; }
.llm-name { font-size: 13px; font-weight: 600; color: var(--ink-2); text-align: center; }
.llm-desc { font-size: 11px; color: var(--ink-4); text-align: center; line-height: 1.4; }
@media(max-width:640px) { .llm-grid { grid-template-columns: repeat(3,1fr); } }
.why-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin: 48px 0; }
.why-card { background: var(--white); border: 1px solid var(--border); border-radius: var(--r-xl); padding: 32px; box-shadow: var(--shadow-sm); }
.why-icon { font-size: 36px; margin-bottom: 16px; }
.why-title { font-size: 20px; font-weight: 600; color: var(--ink-2); margin-bottom: 10px; }
.why-desc { font-size: 15px; color: var(--ink-3); line-height: 1.65; }
.offer-box { background: linear-gradient(135deg,var(--ink-2),#064E3B); border-radius: var(--r-xl); padding: 56px; text-align: center; margin: 48px 0 80px; }
.offer-box h2 { font-size: 36px; font-weight: 300; color: #fff; letter-spacing: -0.04em; margin-bottom: 12px; }
.offer-box h2 strong { color: var(--green-2); font-weight: 700; }
.offer-box p { font-size: 16px; color: rgba(255,255,255,0.7); margin-bottom: 12px; }
.offer-box .price { font-size: 52px; font-weight: 700; color: #fff; letter-spacing: -0.04em; margin: 24px 0 8px; }
.offer-box .period { font-size: 16px; color: rgba(255,255,255,0.5); margin-bottom: 32px; }
@media(max-width:768px){ .why-grid{grid-template-columns:1fr} }
</style>

<div class="page-hero">
  <div class="container">
    <div class="badge" style="margin:0 auto 20px">Visibilité IA</div>
    <h1>Soyez trouvé sur<br><strong>les IA de demain</strong></h1>
    <p>En 2026, vos clients cherchent leurs prestataires sur ChatGPT et Perplexity. Êtes-vous présent ? Nous faisons en sorte que oui.</p>
    <p style="font-size:14px;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--ink-4);margin-bottom:8px">Tous les grands modèles IA</p>
    <div class="llm-grid">
      <div class="llm-card">
        <img class="llm-logo" src="https://www.google.com/s2/favicons?domain=openai.com&sz=128" alt="ChatGPT">
        <div class="llm-name">ChatGPT</div>
        <div class="llm-desc">OpenAI · 180M utilisateurs</div>
      </div>
      <div class="llm-card">
        <img class="llm-logo" src="https://www.google.com/s2/favicons?domain=claude.ai&sz=128" alt="Claude">
        <div class="llm-name">Claude</div>
        <div class="llm-desc">Anthropic · Rédaction & analyse</div>
      </div>
      <div class="llm-card">
        <img class="llm-logo" src="https://www.google.com/s2/favicons?domain=gemini.google.com&sz=128" alt="Gemini">
        <div class="llm-name">Gemini</div>
        <div class="llm-desc">Google · IA Overviews</div>
      </div>
      <div class="llm-card">
        <img class="llm-logo" src="https://www.google.com/s2/favicons?domain=perplexity.ai&sz=128" alt="Perplexity">
        <div class="llm-name">Perplexity</div>
        <div class="llm-desc">Moteur de recherche IA</div>
      </div>
      <div class="llm-card">
        <img class="llm-logo" src="https://www.google.com/s2/favicons?domain=microsoft.com&sz=128" alt="Copilot">
        <div class="llm-name">Copilot</div>
        <div class="llm-desc">Microsoft · Bing IA</div>
      </div>
      <div class="llm-card">
        <img class="llm-logo" src="https://www.google.com/s2/favicons?domain=mistral.ai&sz=128" alt="Mistral">
        <div class="llm-name">Mistral</div>
        <div class="llm-desc">IA française · Le Chat</div>
      </div>
      <div class="llm-card">
        <img class="llm-logo" src="https://www.google.com/s2/favicons?domain=meta.ai&sz=128" alt="Meta AI">
        <div class="llm-name">Meta AI</div>
        <div class="llm-desc">Llama · Facebook & Instagram</div>
      </div>
      <div class="llm-card">
        <img class="llm-logo" src="https://www.google.com/s2/favicons?domain=deepseek.com&sz=128" alt="DeepSeek">
        <div class="llm-name">DeepSeek</div>
        <div class="llm-desc">Modèle open source</div>
      </div>
      <div class="llm-card">
        <img class="llm-logo" src="https://www.google.com/s2/favicons?domain=grok.com&sz=128" alt="Grok">
        <div class="llm-name">Grok</div>
        <div class="llm-desc">xAI · Intégré à X</div>
      </div>
      <div class="llm-card">
        <img class="llm-logo" src="https://www.google.com/s2/favicons?domain=you.com&sz=128" alt="You.com">
        <div class="llm-name">You.com</div>
        <div class="llm-desc">Recherche IA multimodale</div>
      </div>
    </div>
  </div>
</div>

<div class="container">
  <div class="why-grid">
    <div class="why-card reveal">
      <div class="why-icon"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--blue)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg></div>
      <div class="why-title">Le SEO traditionnel ne suffit plus</div>
      <p class="why-desc">40% des recherches Google se terminent sans clic depuis les AI Overviews. Votre position Google ne garantit plus le trafic. Les IA citent directement leurs sources — il faut en faire partie.</p>
    </div>
    <div class="why-card reveal">
      <div class="why-icon"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--green)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg></div>
      <div class="why-title">Les IA recommandent des entreprises</div>
      <p class="why-desc">"Quel plombier à Lyon ?" "Quelle agence comptable pour une PME ?" Les IA répondent avec des noms précis. Nous optimisons votre contenu pour que votre entreprise soit citée.</p>
    </div>
    <div class="why-card reveal">
      <div class="why-icon"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--ink-2)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg></div>
      <div class="why-title">Ce qu'on fait concrètement</div>
      <ul style="font-size:15px;color:var(--ink-3);line-height:1.8;padding-left:20px;margin-top:8px">
        <li>Audit de votre présence actuelle sur les IA</li>
        <li>Optimisation de vos contenus pour les citations IA</li>
        <li>Schema.org + données structurées</li>
        <li>Création de contenus optimisés "réponse directe"</li>
        <li>Rapport mensuel de citations détectées</li>
      </ul>
    </div>
    <div class="why-card reveal">
      <div class="why-icon"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--green)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></div>
      <div class="why-title">Résultats mesurables</div>
      <p class="why-desc">Nous mesurons chaque mois votre score de présence sur ChatGPT, Perplexity, Google AI et Bing Copilot. Vous voyez votre progression et les requêtes sur lesquelles vous apparaissez.</p>
    </div>
  </div>

  <div class="offer-box reveal">
    <h2>SEO & <strong>Visibilité IA</strong></h2>
    <p>Audit complet + optimisation + rapport mensuel de citations</p>
    <div class="price">49€</div>
    <div class="period">/ mois · Sans engagement</div>
    <a href="/assistant.php?plan=seo" class="btn btn-primary" style="background:#10B981;border-color:#10B981;font-size:17px;padding:14px 36px">Commencer maintenant →</a>
    <p style="margin-top:20px;font-size:13px">Premier mois : audit complet offert · Résiliation en 1 clic</p>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
