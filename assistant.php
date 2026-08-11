<?php
$page_title = 'Assistant IA · ABYS AI';
$plan = $_GET['plan'] ?? 'assistant';
include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/nav.php';
?>

<div class="container-sm" style="padding-top:60px;padding-bottom:80px;text-align:center">

  <div class="badge" style="margin:0 auto 20px">Assistant IA Personnel</div>
  <h1 style="font-size:42px;font-weight:300;letter-spacing:-0.04em;margin-bottom:14px">
    Votre expert IA <strong style="font-weight:700">disponible 24h/24</strong>
  </h1>
  <p style="font-size:17px;color:var(--ink-3);margin-bottom:48px;line-height:1.65">
    Posez toutes vos questions sur les outils IA, la mise en place, votre secteur.<br>
    Votre assistant répond dans votre espace personnel · clairement, sans jargon.
  </p>

  <!-- Exemples de questions -->
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:48px;text-align:left">
    <?php
    $questions = [
      '« Comment configurer Notion AI pour ma comptabilité ? »',
      '« Quel outil IA pour automatiser mes devis ? »',
      '« ChatGPT peut-il répondre à mes emails clients ? »',
      '« Comment créer des posts réseaux sociaux avec l\'IA ? »',
      '« Quel logiciel de gestion avec IA pour une boulangerie ? »',
      '« Comment former mes employés à l\'IA ? »',
    ];
    foreach($questions as $q): ?>
    <div style="padding:14px 16px;background:var(--white);border:1px solid var(--border);border-radius:var(--r-md);font-size:13px;color:var(--ink-3);font-style:italic">
      <?= htmlspecialchars($q) ?>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Prix + CTA -->
  <div style="background:linear-gradient(135deg,var(--ink-2),#064E3B);border-radius:var(--r-xl);padding:48px;margin-bottom:32px">
    <div style="font-size:13px;font-weight:500;color:var(--green-2);text-transform:uppercase;letter-spacing:0.12em;margin-bottom:16px">
      <?= $plan === 'seo' ? 'SEO & Visibilité IA' : 'Assistant IA' ?>
    </div>
    <div style="font-size:48px;font-weight:700;color:#fff;margin-bottom:4px">
      <?= $plan === 'seo' ? '49€' : '29€' ?>
    </div>
    <div style="color:rgba(255,255,255,0.6);font-size:15px;margin-bottom:24px">/mois · sans engagement</div>

    <div>
      <label style="color:rgba(255,255,255,0.8);font-size:14px;display:block;margin-bottom:8px">Votre email</label>
      <input type="email" id="email-input" placeholder="vous@votreentreprise.fr"
        style="padding:12px 16px;border-radius:var(--r-md);border:none;font-size:15px;width:100%;max-width:340px;margin-bottom:16px;font-family:inherit"/>
    </div>
    <a href="/facturation.php?plan=<?= htmlspecialchars($plan, ENT_QUOTES) ?>" class="btn btn-primary btn-lg" style="display:inline-block">
      S'abonner maintenant →
    </a>
    <p style="color:rgba(255,255,255,0.4);font-size:12px;margin-top:12px">
      🔒 Paiement sécurisé Stripe · Résiliable à tout moment · Accès immédiat à votre espace
    </p>
  </div>

  <p style="font-size:14px;color:var(--ink-4)">
    Déjà abonné ? Contactez-nous sur <a href="mailto:support@abys.ai" style="color:var(--blue)">support@abys.ai</a>
  </p>
</div>


<?php include __DIR__ . '/includes/footer.php'; ?>
<script>
const plan = '<?= htmlspecialchars($plan, ENT_QUOTES) ?>';
document.getElementById('btn-subscribe').addEventListener('click', async () => {
  const email = document.getElementById('email-input').value;
  const leadId = ABYS.get('lead_id') || 0;
  const btn = document.getElementById('btn-subscribe');
  btn.textContent = 'Redirection…'; btn.disabled = true;

  try {
    const res = await ABYS.api('stripe.php', {
      action: 'create_checkout_subscription',
      plan, lead_id: leadId, email
    });
    if (res.url) window.location.href = res.url;
  } catch (e) {
    btn.textContent = 'S\'abonner maintenant →';
    btn.disabled = false;
    ABYS.toast('Erreur, réessayez', 'error');
  }
});
</script>