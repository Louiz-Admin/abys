<?php
$page_title = 'Rapport Premium — ABYS AI';
include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/nav.php';
?>

<div class="container-sm" style="padding-top:60px;padding-bottom:80px;text-align:center">

  <?php if (isset($_GET['cancelled'])): ?>
  <div style="background:rgba(245,158,11,0.08);border:1px solid rgba(245,158,11,0.2);border-radius:var(--r-md);padding:14px;margin-bottom:32px;font-size:14px;color:#B45309">
    Paiement annulé. Votre audit est toujours disponible.
  </div>
  <?php endif; ?>

  <div class="badge" style="margin:0 auto 20px">Rapport Premium</div>
  <h1 style="font-size:42px;font-weight:300;letter-spacing:-0.04em;margin-bottom:14px">
    Votre plan d'action IA <strong style="font-weight:700">complet</strong>
  </h1>
  <p style="font-size:17px;color:var(--ink-3);margin-bottom:48px;line-height:1.65">
    Tutoriels personnalisés · Simulation chiffrée · Plan 12 mois · Liens d'affiliation vérifiés
  </p>

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:48px;text-align:left">
    <?php
    $included = [
      ['✅','Toutes les opportunités IA (7+)','avec gains calculés précisément pour votre secteur'],
      ['✅','Tutoriels pas-à-pas personnalisés','un guide pour chaque outil, adapté à votre métier'],
      ['✅','Plan d\'action sur 12 mois','priorisé : quoi faire en premier pour un impact maximal'],
      ['✅','Simulation interactive','ajustez selon votre réalité et voyez vos gains en temps réel'],
      ['✅','Outils vérifiés avec liens directs','les meilleures offres du marché, négociées pour vous'],
      ['✅','Analyse concurrentielle','où en sont vos concurrents, comment les dépasser'],
    ];
    foreach($included as $item): ?>
    <div style="display:flex;gap:12px;padding:16px;background:var(--white);border:1px solid var(--border);border-radius:var(--r-md)">
      <span style="font-size:18px"><?= $item[0] ?></span>
      <div>
        <div style="font-weight:600;font-size:14px;color:var(--ink-2);margin-bottom:2px"><?= htmlspecialchars($item[1]) ?></div>
        <div style="font-size:12px;color:var(--ink-4)"><?= htmlspecialchars($item[2]) ?></div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <div style="background:linear-gradient(135deg,var(--ink-2),#064E3B);border-radius:var(--r-xl);padding:48px">
    <div style="font-size:48px;font-weight:700;color:#fff;margin-bottom:4px"><span style="font-size:26px;opacity:.45;text-decoration:line-through;font-weight:500;margin-right:10px">249€</span>99€</div>
    <div style="color:rgba(255,255,255,0.6);font-size:15px;margin-bottom:24px">Paiement unique — accès à vie · Offre de lancement</div>
    <div>
      <label style="color:rgba(255,255,255,0.8);font-size:14px;display:block;margin-bottom:8px">Votre email pour recevoir le rapport</label>
      <input type="email" id="email-input" placeholder="vous@votreentreprise.fr"
        style="padding:12px 16px;border-radius:var(--r-md);border:none;font-size:15px;width:100%;max-width:360px;margin-bottom:16px;font-family:inherit"/>
    </div>
    <a href="/facturation.php?plan=report" class="btn btn-primary btn-lg" style="font-size:16px;display:inline-block">
      Obtenir mon rapport — 99€ →
    </a>
    <p style="color:rgba(255,255,255,0.4);font-size:12px;margin-top:12px">🔒 Paiement sécurisé Stripe · Accès immédiat après paiement · <a href="/cgv.php" style="color:rgba(255,255,255,0.5)">CGV</a></p>
  </div>
</div>


<?php include __DIR__ . '/includes/footer.php'; ?>
<script>
document.getElementById('btn-pay').addEventListener('click', async () => {
  const email   = document.getElementById('email-input').value;
  const leadId  = ABYS.get('lead_id') || 0;
  const auditId = ABYS.get('audit_id') || 0;
  const btn = document.getElementById('btn-pay');
  btn.textContent = 'Redirection…'; btn.disabled = true;

  try {
    const res = await ABYS.api('stripe.php', {
      action: 'create_checkout_report',
      lead_id: leadId, audit_id: auditId, email
    });
    if (res.url) window.location.href = res.url;
  } catch (e) {
    btn.textContent = 'Obtenir mon rapport — 99€ →';
    btn.disabled = false;
    ABYS.toast('Erreur de paiement, réessayez', 'error');
  }
});
</script>