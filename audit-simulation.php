<?php
// Ancien formulaire remplace par le tunnel plein ecran /audit-questionnaire.php
$qs = $_SERVER['QUERY_STRING'] ?? '';
header('Location: /audit-questionnaire.php' . ($qs ? '?' . $qs : ''), true, 301);
exit;
