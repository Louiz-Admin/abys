<?php
// Page connexion / inscription client
session_start();
if (!empty($_SESSION['client_id'])) {
    header('Location: /compte/assistant.php'); exit;
}

$reset_token = $_GET['reset'] ?? '';
$page_title  = 'Mon espace · ABYS AI';
include __DIR__ . '/../includes/head.php';
include __DIR__ . '/../includes/nav.php';
?>
<style>
.auth-wrap{max-width:440px;margin:80px auto;padding:0 24px 80px}
.auth-card{background:var(--white);border:1px solid var(--border);border-radius:var(--r-xl);padding:40px;box-shadow:var(--shadow-lg)}
.auth-logo{text-align:center;margin-bottom:28px}
.auth-logo span{font-size:15px;font-weight:200;letter-spacing:.18em;text-transform:uppercase;color:var(--ink-2)}
.auth-tabs{display:flex;border-bottom:1px solid var(--border);margin-bottom:28px;gap:0}
.auth-tab{flex:1;text-align:center;padding:10px;font-size:14px;font-weight:500;color:var(--ink-4);cursor:pointer;border-bottom:2px solid transparent;transition:all 150ms}
.auth-tab.active{color:var(--green-deep);border-bottom-color:var(--green)}
.auth-panel{display:none}
.auth-panel.active{display:block}
.field{margin-bottom:16px}
.field label{display:block;font-size:13px;font-weight:500;color:var(--ink-2);margin-bottom:6px}
.field input{width:100%;padding:11px 14px;border:1px solid var(--border-2);border-radius:var(--r-md);font-family:var(--font);font-size:14px;color:var(--ink);background:var(--bg);transition:border-color 150ms;box-sizing:border-box}
.field input:focus{outline:none;border-color:var(--green)}
.auth-btn{width:100%;padding:13px;background:linear-gradient(135deg,#059669,#064E3B);color:#fff;border:none;border-radius:var(--r-md);font-size:15px;font-weight:600;cursor:pointer;font-family:var(--font);transition:opacity 150ms}
.auth-btn:hover{opacity:.9}
.auth-btn:disabled{opacity:.6;cursor:not-allowed}
.auth-error{background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);color:#DC2626;padding:10px 14px;border-radius:var(--r-md);font-size:13px;margin-bottom:16px;display:none}
.auth-success{background:rgba(16,185,129,.08);border:1px solid rgba(16,185,129,.2);color:#065F46;padding:10px 14px;border-radius:var(--r-md);font-size:13px;margin-bottom:16px;display:none}
.auth-forgot{text-align:right;margin-top:-8px;margin-bottom:16px}
.auth-forgot a{font-size:12px;color:var(--ink-4);cursor:pointer}
.auth-forgot a:hover{color:var(--green)}
</style>

<div class="auth-wrap">
  <div class="auth-card">
    <div class="auth-logo">
      <svg width="32" height="32" viewBox="0 0 34 34" fill="none" style="display:block;margin:0 auto 10px">
        <circle cx="17" cy="17" r="16.5" stroke="var(--border-2)" stroke-width="0.8"/>
        <circle cx="17" cy="17" r="14.8" fill="var(--bg)"/>
        <path d="M17 6.5 L25.5 25" stroke="var(--green-deep)" stroke-width="2.4" stroke-linecap="round"/>
        <path d="M17 6.5 L8.5 25" stroke="var(--green-deep)" stroke-width="2.4" stroke-linecap="round"/>
      </svg>
      <span>Mon espace ABYS<sup style="font-size:9px;opacity:.5">AI</sup></span>
    </div>

    <?php if ($reset_token): ?>
    <!-- Reset password panel -->
    <h2 style="font-size:18px;font-weight:600;margin-bottom:20px;color:var(--ink-2)">Nouveau mot de passe</h2>
    <div class="auth-error" id="reset-error"></div>
    <div class="auth-success" id="reset-success"></div>
    <div class="field"><label>Nouveau mot de passe</label><input type="password" id="new-pass" placeholder="8 caractères minimum"></div>
    <button class="auth-btn" onclick="doReset()">Enregistrer le nouveau mot de passe →</button>

    <?php else: ?>
    <div class="auth-tabs">
      <div class="auth-tab active" onclick="switchTab('login')">Connexion</div>
      <div class="auth-tab" onclick="switchTab('register')">Créer un compte</div>
    </div>

    <!-- LOGIN -->
    <div class="auth-panel active" id="panel-login">
      <div class="auth-error" id="login-error"></div>
      <div class="field"><label>Email</label><input type="email" id="login-email" placeholder="vous@entreprise.fr" autocomplete="email"></div>
      <div class="field"><label>Mot de passe</label><input type="password" id="login-pass" placeholder="••••••••" autocomplete="current-password"></div>
      <div class="auth-forgot"><a onclick="showForgot()">Mot de passe oublié ?</a></div>
      <button class="auth-btn" id="btn-login" onclick="doLogin()">Se connecter →</button>
    </div>

    <!-- REGISTER -->
    <div class="auth-panel" id="panel-register">
      <div class="auth-error" id="reg-error"></div>
      <div class="field"><label>Prénom / Nom</label><input type="text" id="reg-name" placeholder="Marie Dupont"></div>
      <div class="field"><label>Email</label><input type="email" id="reg-email" placeholder="vous@entreprise.fr" autocomplete="email"></div>
      <div class="field"><label>Mot de passe</label><input type="password" id="reg-pass" placeholder="8 caractères minimum" autocomplete="new-password"></div>
      <button class="auth-btn" id="btn-reg" onclick="doRegister()">Créer mon espace →</button>
      <p style="font-size:12px;color:var(--ink-4);text-align:center;margin-top:16px">Réservé aux clients ABYS ayant un audit ou un abonnement.</p>
    </div>

    <!-- FORGOT -->
    <div class="auth-panel" id="panel-forgot">
      <div class="auth-error" id="forgot-error"></div>
      <div class="auth-success" id="forgot-success"></div>
      <p style="font-size:14px;color:var(--ink-3);margin-bottom:16px">Entrez votre email pour recevoir un lien de réinitialisation.</p>
      <div class="field"><label>Email</label><input type="email" id="forgot-email" placeholder="vous@entreprise.fr"></div>
      <button class="auth-btn" onclick="doForgot()">Envoyer le lien →</button>
      <div style="text-align:center;margin-top:12px"><a onclick="switchTab('login')" style="font-size:13px;color:var(--ink-4);cursor:pointer">← Retour à la connexion</a></div>
    </div>
    <?php endif; ?>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
<script>
function switchTab(tab) {
  document.querySelectorAll('.auth-tab').forEach((t,i) => t.classList.toggle('active', (i===0&&tab==='login')||(i===1&&tab==='register')));
  document.querySelectorAll('.auth-panel').forEach(p => p.classList.remove('active'));
  document.getElementById('panel-' + tab).classList.add('active');
}
function showForgot() {
  document.querySelectorAll('.auth-tab').forEach(t => t.classList.remove('active'));
  document.querySelectorAll('.auth-panel').forEach(p => p.classList.remove('active'));
  document.getElementById('panel-forgot').classList.add('active');
}
function showErr(id, msg) {
  const el = document.getElementById(id);
  el.textContent = msg; el.style.display = 'block';
}
function hideErr(id) { document.getElementById(id).style.display = 'none'; }

async function doLogin() {
  hideErr('login-error');
  const btn = document.getElementById('btn-login');
  btn.disabled = true; btn.textContent = 'Connexion…';
  try {
    const r = await ABYS.api('auth-client.php', {
      action: 'login',
      email: document.getElementById('login-email').value,
      password: document.getElementById('login-pass').value
    });
    if (r.success) window.location.href = r.redirect || '/compte/assistant.php';
  } catch(e) {
    showErr('login-error', e.message || 'Erreur de connexion');
    btn.disabled = false; btn.textContent = 'Se connecter →';
  }
}

async function doRegister() {
  hideErr('reg-error');
  const btn = document.getElementById('btn-reg');
  btn.disabled = true; btn.textContent = 'Création…';
  try {
    const r = await ABYS.api('auth-client.php', {
      action: 'register',
      name: document.getElementById('reg-name').value,
      email: document.getElementById('reg-email').value,
      password: document.getElementById('reg-pass').value,
      lead_id: parseInt(ABYS.get('lead_id') || 0)
    });
    if (r.success) window.location.href = r.redirect || '/compte/assistant.php';
  } catch(e) {
    showErr('reg-error', e.message || 'Erreur');
    btn.disabled = false; btn.textContent = 'Créer mon espace →';
  }
}

async function doForgot() {
  hideErr('forgot-error');
  try {
    const r = await ABYS.api('auth-client.php', {
      action: 'reset_request',
      email: document.getElementById('forgot-email').value
    });
    const el = document.getElementById('forgot-success');
    el.textContent = r.message; el.style.display = 'block';
  } catch(e) { showErr('forgot-error', e.message); }
}

async function doReset() {
  hideErr('reset-error');
  try {
    const r = await ABYS.api('auth-client.php', {
      action: 'reset_confirm',
      token: '<?= htmlspecialchars($reset_token, ENT_QUOTES) ?>',
      password: document.getElementById('new-pass').value
    });
    const el = document.getElementById('reset-success');
    el.textContent = r.message + ' Redirection…'; el.style.display = 'block';
    setTimeout(() => window.location.href = '/compte/', 2000);
  } catch(e) { showErr('reset-error', e.message); }
}

document.querySelectorAll('.field input').forEach(i => {
  i.addEventListener('keypress', e => { if(e.key==='Enter') { const btn = document.querySelector('.auth-btn:not([disabled])'); if(btn) btn.click(); } });
});
</script>
