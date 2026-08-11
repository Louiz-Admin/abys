<?php
$page_title = 'Contact · ABYS AI';
$page_description = 'Contactez l\'équipe ABYS AI · Thomas et son équipe vous répondent personnellement sous 24h.';
include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/nav.php';
?>
<style>

/* ── Layout ── */
.contact-page {
  max-width: 1100px;
  margin: 0 auto;
  padding: 72px 40px 96px;
}

/* ── Header ── */
.contact-header { margin-bottom: 64px; }
.contact-eyebrow {
  display: inline-flex; align-items: center; gap: 8px;
  font-size: 12px; font-weight: 700; letter-spacing: 0.1em;
  text-transform: uppercase; color: var(--green-deep);
  margin-bottom: 20px;
}
.contact-eyebrow svg { stroke: var(--green); }
.contact-header h1 {
  font-size: clamp(36px, 5vw, 54px);
  font-weight: 300; letter-spacing: -0.04em;
  margin: 0 0 16px; line-height: 1.1;
}
.contact-header h1 strong { font-weight: 800; }
.contact-header-sub {
  font-size: 17px; color: var(--ink-3); max-width: 500px;
  line-height: 1.7;
}

/* ── Two-column layout ── */
.contact-grid {
  display: grid;
  grid-template-columns: 1fr 440px;
  gap: 64px;
  align-items: start;
}

/* ── Thomas intro card ── */
.contact-thomas {
  display: flex; gap: 20px; align-items: flex-start;
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: 20px;
  padding: 28px;
  box-shadow: var(--shadow-sm);
  margin-bottom: 32px;
}
.contact-thomas-photo {
  width: 72px; height: 72px; border-radius: 50%;
  object-fit: cover; object-position: top;
  flex-shrink: 0;
  border: 2px solid var(--border);
}
.contact-thomas-name {
  font-size: 15px; font-weight: 700; color: var(--ink-2);
  margin-bottom: 2px;
}
.contact-thomas-role {
  font-size: 12px; color: var(--ink-4);
  margin-bottom: 12px;
}
.contact-thomas-msg {
  font-size: 14.5px; color: var(--ink-2);
  line-height: 1.7;
}
.contact-thomas-msg::before {
  content: open-quote;
  font-size: 20px; color: var(--green);
  font-family: Georgia, serif;
  line-height: 0; vertical-align: -4px;
  margin-right: 2px;
}
.contact-thomas-msg::after {
  content: close-quote;
  font-size: 20px; color: var(--green);
  font-family: Georgia, serif;
  line-height: 0; vertical-align: -4px;
  margin-left: 2px;
}

/* ── Channels ── */
.contact-channels { display: flex; flex-direction: column; gap: 14px; margin-bottom: 40px; }
.contact-channel {
  display: flex; align-items: center; gap: 18px;
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: 14px;
  padding: 18px 22px;
  box-shadow: var(--shadow-sm);
  text-decoration: none;
  transition: border-color 150ms, box-shadow 150ms, transform 150ms;
}
.contact-channel:hover {
  border-color: rgba(16,185,129,0.4);
  box-shadow: 0 4px 24px rgba(16,185,129,0.1);
  transform: translateX(4px);
}
.contact-channel-icon {
  width: 44px; height: 44px; border-radius: 12px;
  background: linear-gradient(135deg, rgba(16,185,129,0.1), rgba(14,165,233,0.07));
  border: 1px solid rgba(16,185,129,0.2);
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.contact-channel-icon svg { stroke: #10B981; }
.contact-channel-info { flex: 1; }
.contact-channel-title {
  font-size: 14px; font-weight: 700; color: var(--ink-2);
  margin-bottom: 2px;
}
.contact-channel-desc {
  font-size: 12px; color: var(--ink-4); line-height: 1.4;
}
.contact-channel-arrow {
  width: 28px; height: 28px;
  border-radius: 50%;
  background: var(--bg);
  border: 1px solid var(--border);
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.contact-channel-arrow svg { stroke: var(--ink-3); }

/* ── Steps ── */
.contact-steps-title {
  font-size: 13px; font-weight: 700; color: var(--ink-2);
  text-transform: uppercase; letter-spacing: 0.08em;
  margin-bottom: 16px;
}
.contact-steps { display: flex; flex-direction: column; gap: 14px; }
.contact-step {
  display: flex; align-items: flex-start; gap: 14px;
}
.contact-step-num {
  width: 28px; height: 28px; border-radius: 50%;
  background: linear-gradient(135deg, #10B981, #059669);
  color: #fff; font-size: 12px; font-weight: 700;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0; margin-top: 1px;
}
.contact-step-text { font-size: 14px; color: var(--ink-3); line-height: 1.6; }

/* ── Form card ── */
.contact-form-card {
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: 24px;
  padding: 40px;
  box-shadow: var(--shadow-md);
  position: sticky;
  top: 24px;
}
.contact-form-card h2 {
  font-size: 22px; font-weight: 700; color: var(--ink-2);
  margin: 0 0 4px;
}
.contact-form-sub {
  font-size: 14px; color: var(--ink-3);
  margin: 0 0 28px; line-height: 1.5;
}

.form-group { margin-bottom: 16px; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
label {
  display: block; font-size: 12px; font-weight: 700;
  color: var(--ink-2); margin-bottom: 6px;
  text-transform: uppercase; letter-spacing: 0.06em;
}
input[type=text], input[type=email], select, textarea {
  width: 100%; padding: 12px 14px;
  border: 1.5px solid var(--border-2); border-radius: 10px;
  font-family: var(--font); font-size: 14px; color: var(--ink);
  background: var(--bg);
  transition: border-color 150ms, box-shadow 150ms;
  box-sizing: border-box;
}
input:focus, select:focus, textarea:focus {
  outline: none; border-color: #10B981;
  box-shadow: 0 0 0 3px rgba(16,185,129,0.1);
}
textarea { resize: vertical; min-height: 120px; }

.form-submit {
  width: 100%; padding: 14px;
  background: var(--ink-2); color: #fff;
  border: none; border-radius: 10px;
  font-size: 15px; font-weight: 700; font-family: var(--font);
  cursor: pointer;
  transition: background 150ms, transform 150ms;
}
.form-submit:hover { background: #059669; transform: translateY(-1px); }

.form-note {
  display: flex; align-items: center; gap: 8px;
  font-size: 12px; color: var(--ink-4);
  margin-top: 12px;
}
.form-note svg { stroke: var(--ink-4); flex-shrink: 0; }

/* ── Success ── */
.contact-success {
  display: none; text-align: center; padding: 20px 0;
}
.contact-success-icon {
  width: 64px; height: 64px; border-radius: 50%;
  background: linear-gradient(135deg, rgba(16,185,129,0.15), rgba(5,150,105,0.1));
  border: 1.5px solid rgba(16,185,129,0.3);
  display: flex; align-items: center; justify-content: center;
  margin: 0 auto 16px;
}
.contact-success-icon svg { stroke: #10B981; }
.contact-success h3 {
  font-size: 18px; font-weight: 700; color: var(--ink-2);
  margin: 0 0 8px;
}
.contact-success p { font-size: 14px; color: var(--ink-3); line-height: 1.6; }

@media (max-width: 900px) {
  .contact-grid { grid-template-columns: 1fr; gap: 40px; }
  .contact-form-card { position: static; }
  .contact-page { padding: 48px 24px 72px; }
}
@media (max-width: 480px) {
  .form-row { grid-template-columns: 1fr; }
}
</style>

<div class="contact-page">

  <!-- Header -->
  <div class="contact-header">
    <div class="contact-eyebrow">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
      Contact
    </div>
    <h1>On est là,<br><strong>vraiment.</strong></h1>
    <p class="contact-header-sub">Une question, un doute, une critique · on lit tout et on répond à tout personnellement. Pas de bot, pas de template automatique.</p>
  </div>

  <div class="contact-grid">

    <!-- Colonne gauche -->
    <div>

      <!-- Thomas -->
      <div class="contact-thomas">
        <img src="/assets/img/thomas.jpg" alt="Thomas Capiten" class="contact-thomas-photo">
        <div>
          <div class="contact-thomas-name">Thomas Capiten</div>
          <div class="contact-thomas-role">Fondateur, ABYS AI</div>
          <div class="contact-thomas-msg">J'ai conçu ABYS AI pour que vous puissiez poser vos vraies questions. Mon équipe et moi lisons chaque message. Si vous avez un doute, une question, une critique · écrivez-nous. C'est comme ça qu'on s'améliore.</div>
        </div>
      </div>

      <!-- Channels -->
      <div class="contact-channels">

        <a href="mailto:contact@abys.ai" class="contact-channel">
          <div class="contact-channel-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          </div>
          <div class="contact-channel-info">
            <div class="contact-channel-title">contact@abys.ai</div>
            <div class="contact-channel-desc">Réponse humaine sous 24h ouvrées</div>
          </div>
          <div class="contact-channel-arrow">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
          </div>
        </a>

        <a href="/assistant.php" class="contact-channel">
          <div class="contact-channel-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
          </div>
          <div class="contact-channel-info">
            <div class="contact-channel-title">Assistant IA</div>
            <div class="contact-channel-desc">Questions rapides en temps réel</div>
          </div>
          <div class="contact-channel-arrow">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
          </div>
        </a>

        <a href="/audit-abys-premium.php" class="contact-channel">
          <div class="contact-channel-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 1-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          </div>
          <div class="contact-channel-info">
            <div class="contact-channel-title">Chat expert · ABYS Premium</div>
            <div class="contact-channel-desc">Accès direct à notre équipe, réponse sous 4h</div>
          </div>
          <div class="contact-channel-arrow">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
          </div>
        </a>

      </div>

      <!-- Steps -->
      <p class="contact-steps-title">Ce qui se passe après votre envoi</p>
      <div class="contact-steps">
        <div class="contact-step">
          <div class="contact-step-num">1</div>
          <div class="contact-step-text">On reçoit votre message et on le lit · toujours par une vraie personne.</div>
        </div>
        <div class="contact-step">
          <div class="contact-step-num">2</div>
          <div class="contact-step-text">On vous répond sous 24h ouvrées avec une réponse adaptée à votre situation.</div>
        </div>
        <div class="contact-step">
          <div class="contact-step-num">3</div>
          <div class="contact-step-text">Si besoin, on propose un échange en visio pour aller plus loin ensemble.</div>
        </div>
      </div>

    </div>

    <!-- Colonne droite · formulaire sticky -->
    <div class="contact-form-card">
      <h2>Envoyer un message</h2>
      <p class="contact-form-sub">On lit tout, on répond à tout. Vraiment.</p>

      <form id="contact-form" onsubmit="submitContact(event)">
        <div class="form-row form-group">
          <div>
            <label>Prénom</label>
            <input type="text" name="firstname" placeholder="Marie" required>
          </div>
          <div>
            <label>Nom</label>
            <input type="text" name="lastname" placeholder="Dupont" required>
          </div>
        </div>
        <div class="form-group">
          <label>Email</label>
          <input type="email" name="email" placeholder="marie@example.fr" required>
        </div>
        <div class="form-group">
          <label>Entreprise <span style="font-weight:400;color:var(--ink-4)">(optionnel)</span></label>
          <input type="text" name="company" placeholder="Boulangerie du Centre…">
        </div>
        <div class="form-group">
          <label>Sujet</label>
          <select name="subject">
            <option value="audit">Question sur mon audit</option>
            <option value="rapport">Mon rapport ou mon accompagnement</option>
            <option value="financement">Financement &amp; aides disponibles</option>
            <option value="partenariat">Partenariat ou revendeur</option>
            <option value="technique">Problème technique</option>
            <option value="autre">Autre</option>
          </select>
        </div>
        <div class="form-group">
          <label>Message</label>
          <textarea name="message" placeholder="Décrivez votre situation ou votre question. Plus c'est précis, mieux on peut vous aider." required></textarea>
        </div>
        <button type="submit" class="form-submit">Envoyer le message</button>
        <div class="form-note">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          Vos données ne sont pas partagées avec des tiers.
        </div>
      </form>

      <div class="contact-success" id="contact-success">
        <div class="contact-success-icon">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <h3>Message envoyé</h3>
        <p>On vous répond sous 24h ouvrées.<br>En attendant, découvrez <a href="/qui-sommes-nous.php" style="color:var(--green-deep);font-weight:600">notre équipe</a>.</p>
      </div>
    </div>

  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
<script>
function submitContact(e) {
  e.preventDefault();
  const form = e.target;
  const data = Object.fromEntries(new FormData(form));
  const name = [data.firstname, data.lastname].filter(Boolean).join(' ');
  const subject = '[ABYS] ' + data.subject + (data.company ? ' · ' + data.company : '');
  const body = `Nom: ${name}\nEntreprise: ${data.company || 'non renseignée'}\nSujet: ${data.subject}\n\n${data.message}`;
  window.location.href = `mailto:contact@abys.ai?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
  form.style.display = 'none';
  document.getElementById('contact-success').style.display = 'block';
}
</script>
