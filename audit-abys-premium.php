<?php
$page_title = 'ABYS Premium · Accompagnement IA · ABYS AI';
include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/nav.php';
?>

<style>
/* ── Page Premium ── */
.prem-hero {
  background: linear-gradient(160deg, #0A0F1A 0%, #05150F 50%, #071A12 100%);
  padding: 80px 24px 60px;
  text-align: center;
  position: relative;
  overflow: hidden;
}
.prem-hero::before {
  content: '';
  position: absolute;
  top: -120px; left: 50%; transform: translateX(-50%);
  width: 600px; height: 600px;
  background: radial-gradient(circle, rgba(16,185,129,0.18) 0%, transparent 70%);
  pointer-events: none;
}
.prem-badge {
  display: inline-flex; align-items: center; gap: 8px;
  background: linear-gradient(90deg, rgba(16,185,129,0.2), rgba(5,150,105,0.15));
  border: 1px solid rgba(16,185,129,0.4);
  border-radius: 100px;
  padding: 6px 18px;
  font-size: 13px; font-weight: 600;
  color: #10B981;
  letter-spacing: 0.05em; text-transform: uppercase;
  margin-bottom: 28px;
}
.prem-hero h1 {
  font-size: clamp(32px, 5vw, 52px);
  font-weight: 300;
  letter-spacing: -0.04em;
  color: #fff;
  margin: 0 0 16px;
  line-height: 1.2;
}
.prem-hero h1 strong { font-weight: 800; color: #10B981; }
.prem-hero p {
  font-size: 18px; color: rgba(255,255,255,0.55);
  max-width: 560px; margin: 0 auto 48px; line-height: 1.6;
}

/* ── Features grid ── */
.prem-features {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 16px;
  max-width: 960px;
  margin: 0 auto 60px;
  padding: 0 24px;
}
.prem-feat {
  display: flex; gap: 14px; align-items: flex-start;
  background: rgba(255,255,255,0.04);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 16px;
  padding: 20px;
}
.prem-feat-icon {
  width: 40px; height: 40px; border-radius: 10px;
  background: linear-gradient(135deg, rgba(16,185,129,0.25), rgba(5,150,105,0.15));
  border: 1px solid rgba(16,185,129,0.3);
  display: flex; align-items: center; justify-content: center;
  font-size: 18px; flex-shrink: 0;
}
.prem-feat-title {
  font-weight: 700; font-size: 14px; color: #fff; margin-bottom: 4px;
}
.prem-feat-desc {
  font-size: 13px; color: rgba(255,255,255,0.5); line-height: 1.5;
}

/* ── Pricing card ── */
.prem-pricing-wrap {
  max-width: 520px; margin: 0 auto; padding: 0 24px 80px;
}
.prem-card {
  position: relative;
  background: linear-gradient(160deg, rgba(16,185,129,0.12) 0%, rgba(5,150,105,0.06) 100%);
  border: 1px solid rgba(16,185,129,0.35);
  border-radius: 24px;
  padding: 48px 40px;
  text-align: center;
  overflow: hidden;
}
.prem-card::before {
  content: '';
  position: absolute; inset: 0;
  background: linear-gradient(105deg, transparent 40%, rgba(16,185,129,0.06) 50%, transparent 60%);
  background-size: 200% 100%;
  animation: prem-gloss 4s ease-in-out infinite;
  pointer-events: none;
}
@keyframes prem-gloss {
  0%   { background-position: -100% 0; }
  60%  { background-position: 250% 0; }
  100% { background-position: 250% 0; }
}
.prem-card-price {
  font-size: 72px; font-weight: 800;
  color: #fff; line-height: 1;
  margin-bottom: 6px;
}
.prem-card-price span {
  font-size: 32px; font-weight: 400;
  color: rgba(255,255,255,0.5);
  vertical-align: super;
}
.prem-card-sub {
  font-size: 14px; color: rgba(255,255,255,0.45);
  margin-bottom: 32px;
}
.prem-includes {
  text-align: left; margin-bottom: 36px;
}
.prem-includes li {
  display: flex; align-items: flex-start; gap: 10px;
  font-size: 14px; color: rgba(255,255,255,0.75);
  padding: 8px 0;
  border-bottom: 1px solid rgba(255,255,255,0.06);
  list-style: none;
}
.prem-includes li:last-child { border-bottom: none; }
.prem-includes li::before {
  content: ''; flex-shrink: 0; width: 16px; height: 16px; margin-top: 2px;
  background: #10B981;
  -webkit-mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='2.6' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M20 6 9.5 17 4 11.5'/%3E%3C/svg%3E") center/14px no-repeat;
          mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='2.6' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M20 6 9.5 17 4 11.5'/%3E%3C/svg%3E") center/14px no-repeat;
}
.prem-email-label {
  font-size: 13px; color: rgba(255,255,255,0.6);
  display: block; text-align: left; margin-bottom: 8px;
}
.prem-email-input {
  width: 100%; padding: 12px 16px;
  border-radius: 10px;
  border: 1px solid rgba(255,255,255,0.15);
  background: rgba(255,255,255,0.06);
  color: #fff; font-size: 15px; font-family: inherit;
  margin-bottom: 20px;
  box-sizing: border-box;
  outline: none;
  transition: border-color 200ms;
}
.prem-email-input::placeholder { color: rgba(255,255,255,0.3); }
.prem-email-input:focus { border-color: rgba(16,185,129,0.5); }

.prem-btn {
  display: block; width: 100%;
  padding: 16px 24px;
  border: none; border-radius: 12px; cursor: pointer;
  font-size: 16px; font-weight: 700; font-family: inherit;
  color: #fff;
  background: linear-gradient(90deg, #059669 0%, #0EA5E9 30%, #10B981 50%, #0EA5E9 70%, #059669 100%);
  background-size: 300% 100%;
  animation: prem-shine 3s linear infinite;
  box-shadow: 0 6px 28px rgba(16,185,129,0.45);
  position: relative; overflow: hidden;
  transition: transform 150ms, box-shadow 150ms;
}
.prem-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 36px rgba(16,185,129,0.55);
}
.prem-btn::after {
  content: '';
  position: absolute; inset: 0;
  background: linear-gradient(105deg, transparent 40%, rgba(255,255,255,0.2) 50%, transparent 60%);
  background-size: 200% 100%;
  animation: prem-gloss-btn 2.5s ease-in-out infinite;
}
@keyframes prem-shine {
  0%   { background-position: 0% 0%; }
  100% { background-position: 300% 0%; }
}
@keyframes prem-gloss-btn {
  0%   { background-position: -100% 0; }
  60%  { background-position: 250% 0; }
  100% { background-position: 250% 0; }
}
.prem-trust {
  font-size: 12px; color: rgba(255,255,255,0.3);
  margin-top: 16px; line-height: 1.6;
}
.prem-trust a { color: rgba(255,255,255,0.4); }

/* ── Cancelled notice ── */
.prem-cancelled {
  background: rgba(245,158,11,0.08);
  border: 1px solid rgba(245,158,11,0.25);
  border-radius: 12px; padding: 14px 20px;
  font-size: 14px; color: #D97706;
  margin-bottom: 28px;
  max-width: 520px; margin-left: auto; margin-right: auto;
}

/* ── Dark page background ── */
body { background: #060D14; }
</style>

<!-- Annulation notice -->
<?php if (isset($_GET['cancelled'])): ?>
<div class="prem-cancelled" style="margin-top:32px;text-align:center">
  Paiement annulé. Votre audit est toujours disponible · reprenez quand vous voulez.
</div>
<?php endif; ?>

<!-- Hero -->
<div class="prem-hero">
  <div class="prem-badge"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3 2.6 5.9 6.4.6-4.8 4.3 1.4 6.2L12 16.8 6.4 20l1.4-6.2L3 9.5l6.4-.6z"/></svg> ABYS Premium</div>
  <h1>L'accompagnement IA<br><strong>de A à Z</strong></h1>
  <p>Rapport complet + tutoriels vidéo pas-à-pas + déploiement assisté + chat expert dédié et suivi 30 jours.</p>
</div>

<!-- Features -->
<div style="background:#060D14;padding:40px 0 0">
  <div class="prem-features">

    <div class="prem-feat">
      <div class="prem-feat-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v16a2 2 0 0 0 2 2h16"/><rect x="7" y="12" width="3" height="6" rx="1"/><rect x="13" y="8" width="3" height="10" rx="1"/></svg></div>
      <div>
        <div class="prem-feat-title">Rapport IA complet &amp; détaillé</div>
        <div class="prem-feat-desc">Toutes les opportunités identifiées avec gains calculés précisément, comparatif outils, simulation ROI et plan 12 mois.</div>
      </div>
    </div>

    <div class="prem-feat">
      <div class="prem-feat-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="2.5" y="4" width="19" height="16" rx="2.5"/><path d="M8 4v16"/><path d="M16 4v16"/><path d="M2.5 12h19"/></svg></div>
      <div>
        <div class="prem-feat-title">Tutoriels vidéo pas-à-pas</div>
        <div class="prem-feat-desc">Pour chaque outil recommandé, une vidéo de mise en place personnalisée selon votre secteur et votre usage.</div>
      </div>
    </div>

    <div class="prem-feat">
      <div class="prem-feat-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4.5 16.5c-1.5 1.3-2 5-2 5s3.7-.5 5-2a2.2 2.2 0 0 0-3-3z"/><path d="m12 15-3-3a22 22 0 0 1 2-4A12.9 12.9 0 0 1 22 2c0 2.7-.8 7.5-6 11a22 22 0 0 1-4 2z"/></svg></div>
      <div>
        <div class="prem-feat-title">Accompagnement au déploiement</div>
        <div class="prem-feat-desc">On vous guide de l'installation à la première automatisation opérationnelle · sans laisser de côté.</div>
      </div>
    </div>

    <div class="prem-feat">
      <div class="prem-feat-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M21 14.5a2.5 2.5 0 0 1-2.5 2.5H8l-5 4V5.5A2.5 2.5 0 0 1 5.5 3h13A2.5 2.5 0 0 1 21 5.5z"/></svg></div>
      <div>
        <div class="prem-feat-title">Espace client ABYS + chat expert</div>
        <div class="prem-feat-desc">Accès à votre espace personnel avec toutes vos ressources et un expert disponible sous 4h pour toutes vos questions.</div>
      </div>
    </div>

    <div class="prem-feat">
      <div class="prem-feat-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4.5" width="18" height="17" rx="2.5"/><path d="M16 2.5v4"/><path d="M8 2.5v4"/><path d="M3 10h18"/></svg></div>
      <div>
        <div class="prem-feat-title">Suivi personnalisé 30 jours</div>
        <div class="prem-feat-desc">Un point de suivi à J+7, J+15 et J+30 pour s'assurer que vous avancez et débloquer les éventuels obstacles.</div>
      </div>
    </div>

    <div class="prem-feat">
      <div class="prem-feat-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M7 4h10v5a5 5 0 0 1-10 0z"/><path d="M7 6H4v1a3 3 0 0 0 3 3"/><path d="M17 6h3v1a3 3 0 0 1-3 3"/><path d="M10 14h4v3h-4z"/><path d="M8 20h8"/><path d="M12 17v3"/></svg></div>
      <div>
        <div class="prem-feat-title">Analyse concurrentielle</div>
        <div class="prem-feat-desc">Où en sont vos concurrents dans l'adoption de l'IA, et comment prendre une longueur d'avance sur eux.</div>
      </div>
    </div>

  </div>
</div>

<!-- Pricing card -->
<div style="background:#060D14;padding:20px 0 0">
  <div class="prem-pricing-wrap">
    <div class="prem-card">
      <div class="prem-card-price"><span>€</span>499</div>
      <div class="prem-card-sub">Paiement unique · Accès à vie + suivi 30 jours</div>

      <ul class="prem-includes">
        <li>Toutes les opportunités IA (7+ identifiées)</li>
        <li>Tutoriels vidéo personnalisés par outil</li>
        <li>Plan d'action sur 12 mois priorisé</li>
        <li>Simulation ROI interactive</li>
        <li>Accompagnement au déploiement</li>
        <li>Espace client ABYS + chat expert 4h</li>
        <li>Suivi 30 jours (J+7, J+15, J+30)</li>
        <li>Analyse concurrentielle sectorielle</li>
      </ul>

      <label class="prem-email-label" for="prem-email">Votre email pour accéder à l'espace client</label>
      <input type="email" id="prem-email" class="prem-email-input" placeholder="vous@votreentreprise.fr" />

      <button id="prem-btn" class="prem-btn">
        Démarrer avec ABYS Premium · 499€ →
      </button>

      <p class="prem-trust">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="10.5" width="16" height="10.5" rx="2.5"/><path d="M8 10.5V7a4 4 0 0 1 8 0v3.5"/></svg> Paiement sécurisé Stripe · Accès immédiat après paiement<br>
        <a href="/cgv.php">CGV</a> · <a href="/confidentialite.php">Confidentialité</a>
      </p>
    </div>

    <div style="text-align:center;margin-top:20px">
      <a href="/checkout.php?plan=essential" style="font-size:13px;color:rgba(255,255,255,0.3);text-decoration:none">
        Voir le Rapport Essentiel à 249€ →
      </a>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

<script>
document.getElementById('prem-btn').addEventListener('click', async function() {
  const email   = document.getElementById('prem-email').value.trim();
  const leadId  = ABYS.get('lead_id')  || 0;
  const auditId = ABYS.get('audit_id') || 0;
  const btn     = this;

  if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
    ABYS.toast('Veuillez entrer un email valide', 'error');
    document.getElementById('prem-email').focus();
    return;
  }

  btn.textContent = 'Redirection vers le paiement…';
  btn.disabled    = true;

  try {
    const res = await ABYS.api('stripe.php', {
      action:   'create_checkout_pack',
      lead_id:  leadId,
      audit_id: auditId,
      email:    email
    });
    if (res.url) {
      window.location.href = res.url;
    } else {
      throw new Error(res.error || 'Réponse invalide');
    }
  } catch (e) {
    btn.textContent = 'Démarrer avec ABYS Premium · 499€ →';
    btn.disabled    = false;
    ABYS.toast('Erreur de paiement, réessayez', 'error');
  }
});
</script>
