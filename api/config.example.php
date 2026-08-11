<?php
// Fichier: abys-ai/api/config.php
// NE PAS committer avec de vraies valeurs · utiliser des variables d'environnement en prod

define('DB_HOST', 'localhost');         // IONOS : souvent 'db5xxxxx.hosting-data.io'
define('DB_NAME', 'abysai_db');
define('DB_USER', 'abysai_user');
define('DB_PASS', 'VOTRE_MOT_DE_PASSE_ICI');
define('DB_CHARSET', 'utf8mb4');

// Clé de chiffrement pour les clés API stockées en BDD (32 octets)
define('ENCRYPTION_KEY', 'VOTRE_CLE_32_CARACTERES_ALEATOIRES');

// URL de base du site
define('SITE_URL', 'https://votre-domaine.fr/abys-ai');

// Environnement
define('APP_ENV', 'production'); // 'development' pour debug
define('DEBUG', false);
