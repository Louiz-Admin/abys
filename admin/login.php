<?php
// Fichier: abys-ai/admin/login.php

session_start();
if (!empty($_SESSION['abys_admin'])) {
    header('Location: /abys-ai/admin/index.php');
    exit;
}

require_once __DIR__ . '/../api/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $stmt = get_db()->prepare("SELECT id, password_hash FROM admin_users WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['abys_admin'] = $user['id'];
            $_SESSION['last_activity'] = time();
            header('Location: /abys-ai/admin/index.php');
            exit;
        }
    }
    $error = 'Identifiants incorrects.';
    sleep(1); // Anti brute-force
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>ABYS Admin — Connexion</title>
  <link rel="stylesheet" href="/abys-ai/assets/css/style.css"/>
  <style>
    body { display:flex; align-items:center; justify-content:center; min-height:100vh; background:var(--bg); }
    .login-box { width:100%; max-width:400px; padding:40px; background:var(--white); border:1px solid var(--border); border-radius:var(--r-xl); box-shadow:var(--shadow-lg); }
    .login-logo { display:flex; align-items:center; gap:10px; justify-content:center; margin-bottom:32px; }
    .login-logo-mark { width:36px; height:36px; border-radius:10px; background:var(--gradient); display:flex; align-items:center; justify-content:center; }
    .login-logo-mark span { width:15px; height:15px; border-radius:50%; background:#fff; display:block; }
    .login-title { font-size:22px; font-weight:700; color:var(--ink-2); letter-spacing:-0.04em; }
    .error { background:rgba(239,68,68,0.08); border:1px solid rgba(239,68,68,0.2); color:#DC2626; padding:10px 14px; border-radius:var(--r-md); font-size:14px; margin-bottom:20px; }
  </style>
</head>
<body>
<div class="login-box">
  <div class="login-logo">
    <div class="login-logo-mark"><span></span></div>
    <span class="login-title">ABYS Admin</span>
  </div>
  <?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <?php if (isset($_GET['timeout'])): ?>
    <div class="error">Session expirée. Reconnectez-vous.</div>
  <?php endif; ?>
  <form method="POST">
    <div class="mb-16">
      <label class="label">Identifiant</label>
      <input class="input" type="text" name="username" autocomplete="username" required/>
    </div>
    <div class="mb-24">
      <label class="label">Mot de passe</label>
      <input class="input" type="password" name="password" autocomplete="current-password" required/>
    </div>
    <button type="submit" class="btn w-full btn-lg">Se connecter</button>
  </form>
</div>
</body>
</html>
