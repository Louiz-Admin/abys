<?php
// Fichier: abys-ai/admin/auth.php
// Inclure en haut de chaque page admin

session_start();

if (empty($_SESSION['abys_admin'])) {
    header('Location: /abys-ai/admin/login.php');
    exit;
}

// Renouveler la session toutes les 30 min
if (isset($_SESSION['last_activity']) && time() - $_SESSION['last_activity'] > 1800) {
    session_destroy();
    header('Location: /abys-ai/admin/login.php?timeout=1');
    exit;
}
$_SESSION['last_activity'] = time();
