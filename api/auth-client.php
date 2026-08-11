<?php
// Fichier: abys-ai/api/auth-client.php
header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/email.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); die(json_encode(['error' => 'Method not allowed']));
}

$input  = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';
$db     = get_db();

// ── REGISTER ──────────────────────────────────────────────────────────────────
if ($action === 'register') {
    $email   = filter_var(trim($input['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $password = $input['password'] ?? '';
    $name     = substr(trim($input['name'] ?? ''), 0, 100);
    $lead_id  = intval($input['lead_id'] ?? 0);

    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die(json_encode(['error' => 'Email invalide']));
    }
    if (strlen($password) < 8) {
        die(json_encode(['error' => 'Mot de passe trop court (8 caractères minimum)']));
    }

    // Vérifier si compte déjà existant
    $exists = $db->prepare("SELECT id FROM client_accounts WHERE email = ?");
    $exists->execute([$email]);
    if ($exists->fetch()) {
        die(json_encode(['error' => 'Un compte existe déjà avec cet email', 'code' => 'exists']));
    }

    $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    $stmt = $db->prepare("INSERT INTO client_accounts (email, password_hash, name, lead_id) VALUES (?, ?, ?, ?)");
    $stmt->execute([$email, $hash, $name ?: null, $lead_id ?: null]);
    $client_id = $db->lastInsertId();

    // Session
    $_SESSION['client_id']    = $client_id;
    $_SESSION['client_email'] = $email;
    $_SESSION['client_name']  = $name;

    // Email de bienvenue
    $body = "<h2>Votre espace ABYS est créé ✅</h2>
    <p>Bienvenue{$name_str}, votre assistant IA personnel est prêt.</p>
    <a class='btn' href='https://abys.ai/compte/assistant.php'>Accéder à mon assistant IA →</a>
    <p style='font-size:13px;color:#6B7280'>Connectez-vous à tout moment sur <a href='https://abys.ai/compte/'>abys.ai/compte/</a></p>";
    send_email($email, 'Votre espace ABYS AI est activé', $body);

    echo json_encode(['success' => true, 'client_id' => $client_id, 'redirect' => '/compte/assistant.php']);
}

// ── LOGIN ─────────────────────────────────────────────────────────────────────
elseif ($action === 'login') {
    $email    = filter_var(trim($input['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $password = $input['password'] ?? '';

    $stmt = $db->prepare("SELECT * FROM client_accounts WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $client = $stmt->fetch();

    if (!$client || !password_verify($password, $client['password_hash'])) {
        sleep(1); // anti brute-force
        die(json_encode(['error' => 'Email ou mot de passe incorrect']));
    }

    $db->prepare("UPDATE client_accounts SET last_login=NOW() WHERE id=?")->execute([$client['id']]);

    $_SESSION['client_id']    = $client['id'];
    $_SESSION['client_email'] = $client['email'];
    $_SESSION['client_name']  = $client['name'] ?? '';

    echo json_encode(['success' => true, 'redirect' => '/compte/assistant.php']);
}

// ── LOGOUT ────────────────────────────────────────────────────────────────────
elseif ($action === 'logout') {
    session_destroy();
    echo json_encode(['success' => true, 'redirect' => '/compte/']);
}

// ── CHECK SESSION ─────────────────────────────────────────────────────────────
elseif ($action === 'check') {
    if (!empty($_SESSION['client_id'])) {
        echo json_encode(['logged_in' => true, 'email' => $_SESSION['client_email']]);
    } else {
        echo json_encode(['logged_in' => false]);
    }
}

// ── RESET PASSWORD REQUEST ────────────────────────────────────────────────────
elseif ($action === 'reset_request') {
    $email = filter_var(trim($input['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $stmt  = $db->prepare("SELECT id FROM client_accounts WHERE email = ?");
    $stmt->execute([$email]);
    $client = $stmt->fetch();

    if ($client) {
        $token   = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', time() + 3600);
        $db->prepare("UPDATE client_accounts SET reset_token=?, reset_expires=? WHERE id=?")->execute([$token, $expires, $client['id']]);

        $link = "https://abys.ai/compte/?reset=" . urlencode($token);
        $body = "<h2>Réinitialisation de mot de passe</h2>
        <p>Cliquez sur le lien ci-dessous pour choisir un nouveau mot de passe (valable 1h) :</p>
        <a class='btn' href='{$link}'>Réinitialiser mon mot de passe →</a>";
        send_email($email, 'Réinitialisation mot de passe ABYS AI', $body);
    }
    // Toujours répondre OK (sécurité)
    echo json_encode(['success' => true, 'message' => 'Si cet email existe, un lien vous a été envoyé.']);
}

// ── RESET PASSWORD CONFIRM ────────────────────────────────────────────────────
elseif ($action === 'reset_confirm') {
    $token    = $input['token'] ?? '';
    $password = $input['password'] ?? '';

    if (strlen($password) < 8) die(json_encode(['error' => 'Mot de passe trop court']));

    $stmt = $db->prepare("SELECT id FROM client_accounts WHERE reset_token=? AND reset_expires > NOW()");
    $stmt->execute([$token]);
    $client = $stmt->fetch();

    if (!$client) die(json_encode(['error' => 'Lien invalide ou expiré']));

    $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    $db->prepare("UPDATE client_accounts SET password_hash=?, reset_token=NULL, reset_expires=NULL WHERE id=?")->execute([$hash, $client['id']]);

    echo json_encode(['success' => true, 'message' => 'Mot de passe mis à jour. Vous pouvez vous connecter.']);
}

else {
    http_response_code(400); echo json_encode(['error' => 'Action inconnue']);
}
