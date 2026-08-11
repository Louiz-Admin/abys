<?php
$page_title = 'Formations IA pour PME · ABYS AI';
$page_description = 'Formations pratiques pour apprendre à utiliser les outils IA dans votre entreprise. Débutant à avancé.';
include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/nav.php';
?>
<style>
.page-hero { padding: 72px 0 48px; text-align: center; }
.page-hero h1 { font-size: 48px; font-weight: 300; letter-spacing: -0.04em; margin-bottom: 16px; }
.page-hero h1 strong { font-weight: 700; background: var(--gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
.page-hero p { font-size: 18px; color: var(--ink-3); max-width: 560px; margin: 0 auto 40px; line-height: 1.65; }
.formations-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 24px; padding-bottom: 80px; }
.formation-card { background: var(--white); border: 1px solid var(--border); border-radius: var(--r-xl); overflow: hidden; box-shadow: var(--shadow-sm); transition: transform 150ms, box-shadow 150ms; }
.formation-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-md); }
.formation-header { padding: 28px 28px 20px; }
.formation-badge { display: inline-block; padding: 4px 12px; border-radius: var(--r-pill); font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 14px; }
.badge-debutant { background: rgba(16,185,129,0.1); color: var(--green-deep); }
.badge-intermediaire { background: rgba(14,165,233,0.1); color: var(--blue); }
.badge-avance { background: rgba(99,102,241,0.1); color: #6366F1; }
.formation-title { font-size: 19px; font-weight: 600; color: var(--ink-2); margin-bottom: 10px; line-height: 1.3; }
.formation-desc { font-size: 14px; color: var(--ink-3); line-height: 1.65; }
.formation-footer { padding: 20px 28px; border-top: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
.formation-meta { font-size: 12px; color: var(--ink-4); }
.formation-price { font-size: 20px; font-weight: 700; color: var(--ink-2); }
.coming-soon { text-align: center; padding: 80px 24px; }
.coming-soon h2 { font-size: 32px; font-weight: 300; letter-spacing: -0.04em; margin-bottom: 16px; }
@media(max-width:768px){ .formations-grid{grid-template-columns:1fr} }
</style>

<div class="page-hero">
  <div class="container">
    <div class="badge" style="margin:0 auto 20px">Formations</div>
    <h1>Maîtrisez <strong>l'IA</strong><br>à votre rythme</h1>
    <p>Des formations pratiques, concrètes, adaptées aux PME et TPE · sans jargon technique.</p>
    <div style="display:inline-flex;align-items:center;gap:10px;background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.2);border-radius:40px;padding:10px 20px;margin-top:8px">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--green)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
      <span style="font-size:14px;font-weight:600;color:var(--green-deep)">30 minutes par semaine = une expertise en 6 mois</span>
    </div>
  </div>
</div>

<div class="container">
  <div class="formations-grid">
    <div class="formation-card reveal">
      <div class="formation-header">
        <div class="formation-badge badge-debutant">Débutant</div>
        <div class="formation-title">ChatGPT pour votre entreprise en 3h</div>
        <p class="formation-desc">Apprenez à rédiger emails, devis, posts réseaux sociaux et réponses clients avec ChatGPT. Cas pratiques sur votre secteur.</p>
      </div>
      <div class="formation-footer">
        <div class="formation-meta">3h · En ligne · Accès à vie</div>
        <div class="formation-price">Inclus <span style="font-size:13px;color:var(--ink-4)">rapport</span></div>
      </div>
    </div>

    <div class="formation-card reveal">
      <div class="formation-header">
        <div class="formation-badge badge-debutant">Débutant</div>
        <div class="formation-title">Automatiser avec Zapier · Zéro code</div>
        <p class="formation-desc">Connectez vos applications et automatisez les tâches répétitives : emails, facturation, CRM. Gagnez 5h par semaine.</p>
      </div>
      <div class="formation-footer">
        <div class="formation-meta">4h · En ligne · Accès à vie</div>
        <div class="formation-price">Inclus <span style="font-size:13px;color:var(--ink-4)">rapport</span></div>
      </div>
    </div>

    <div class="formation-card reveal">
      <div class="formation-header">
        <div class="formation-badge badge-intermediaire">Intermédiaire</div>
        <div class="formation-title">Notion AI · Votre base de connaissances IA</div>
        <p class="formation-desc">Organisez votre entreprise avec Notion AI : processus, documents, FAQ interne. Votre équipe trouve l'info en 3 secondes.</p>
      </div>
      <div class="formation-footer">
        <div class="formation-meta">5h · En ligne · Accès à vie</div>
        <div class="formation-price">Inclus <span style="font-size:13px;color:var(--ink-4)">rapport</span></div>
      </div>
    </div>
  </div>

  <div class="coming-soon reveal" style="background:var(--white);border-radius:var(--r-xl);border:1px solid var(--border);margin-bottom:80px">
    <div style="margin-bottom:16px"><svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--green)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg></div>
    <h2>D'autres formations <strong style="background:var(--gradient);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text">arrivent bientôt</strong></h2>
    <p style="font-size:16px;color:var(--ink-3);margin-bottom:28px">Canva AI, Make, Fireflies, référencement IA... Laissez votre email pour être prévenu.</p>
    <div class="url-input-wrap" style="max-width:420px;margin:0 auto">
      <input type="email" id="notif-email" placeholder="votre@email.fr">
      <button class="btn-url" onclick="ABYS.toast('Inscrit ! On vous prévient dès la sortie.','success')">Me prévenir →</button>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
