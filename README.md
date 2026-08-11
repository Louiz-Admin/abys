# ABYS AI

Plateforme d'audit IA pour PME/TPE (abys.ai).

## Stack
PHP 8.x sans framework, MySQL, Stripe (Checkout), SMTP Brevo.

## Configuration
Copier `api/config.example.php` vers `api/config.php` et renseigner les valeurs.
Le fichier `api/config.php` n'est jamais versionné (secrets).

## Dépendances
`composer install` installe le SDK Stripe dans `vendor/`.

## Déploiement (VPS)
Le VPS tire ce dépôt via `git pull` (cron). Voir `deploy/` pour l'amorçage.
