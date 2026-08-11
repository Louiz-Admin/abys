<?php
$plan = $_GET['plan'] ?? '';
if ($plan === 'essential') {
    header('Location: /audit-rapport-premium.php', true, 302);
} elseif ($plan === 'premium') {
    header('Location: /audit-abys-premium.php', true, 302);
} else {
    header('Location: /tarifs.php', true, 302);
}
exit;
