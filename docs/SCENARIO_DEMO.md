# Scénario de démonstration HeatAlert

Durée cible : 5 à 10 minutes. Avant la démonstration, exécuter php artisan db:seed, lancer Vite si nécessaire et vérifier que les services externes ont leurs clés configurées dans le fichier .env local.

## Préparation

1. Ouvrir l’application sur http://127.0.0.1:8000.
2. Utiliser les comptes créés par HeatAlertDemoSeeder :
   - habitant : habitant@heatalert.test / password;
   - administrateur : admin@heatalert.test / password.
3. Le scénario de données se concentre sur le quartier Le Bardo.

## Parcours habitant

1. Se connecter avec le compte habitant puis présenter le dashboard : météo, alertes, coupures et équipements.
2. Ouvrir la météo pour montrer les informations Open-Meteo et les bulletins administratifs disponibles.
3. Ouvrir Coupures puis la coupure Maintenance réseau Bardo.
4. Ouvrir Mes signalements : montrer le signalement relié à l’habitant, au quartier et à la coupure; consulter l’adresse et la carte.
5. Ouvrir Mes équipements : présenter Réfrigérateur principal et PC portable.
6. Ouvrir les conseils de l’équipement pour illustrer la relation plusieurs-à-plusieurs avec Limiter l’ouverture du réfrigérateur pendant une coupure.
7. Ouvrir Points de fraîcheur, puis Parc du Bardo : montrer quartier, services, accessibilité et l’avis publié.
8. Si la clé locale est configurée, ouvrir l’assistant et poser une question de prévention. En l’absence de clé, indiquer clairement que la fonctionnalité dépend de la configuration locale.

## Démonstration authentification et sécurité

1. Créer un nouvel habitant avec une adresse e-mail réelle : l’application affiche ensuite la page de vérification d’e-mail.
2. Montrer l’e-mail HeatAlert reçu : logo ou en-tête HeatAlert, texte français et bouton « Vérifier mon adresse e-mail ».
3. Cliquer le lien signé reçu par e-mail, puis montrer l’accès au dashboard.
4. Se déconnecter, ouvrir « Mot de passe oublié », saisir la même adresse et montrer l’e-mail HeatAlert de réinitialisation.
5. Cliquer le bouton « Réinitialiser mon mot de passe », définir un nouveau mot de passe puis se reconnecter.
6. En local sans SMTP réel, utiliser temporairement le mailer `log` et consulter le lien dans les logs Laravel ; ne jamais utiliser ce mode pour une démonstration d’envoi réel.
7. Dans Profil, modifier une information ou téléverser une photo JPG/PNG/WebP.
8. Changer le mot de passe en saisissant d’abord le mot de passe actuel et montrer l’e-mail de sécurité reçu.
9. Activer la 2FA : saisir le mot de passe actuel, scanner le QR code avec une application TOTP, puis confirmer le code à six chiffres. L’e-mail de confirmation ne contient ni secret ni QR code.
10. Conserver les codes de récupération affichés, se déconnecter et se reconnecter : le challenge TOTP apparaît entre le login et le dashboard.
11. Facultatif : utiliser un code de récupération, puis constater qu’il devient inutilisable.

## Parcours administrateur

1. Se connecter avec admin@heatalert.test.
2. Présenter le dashboard d’administration et les menus de gestion.
3. Ouvrir Quartiers et le détail de Le Bardo.
4. Ouvrir les alertes météo puis Vigilance chaleur au Bardo.
5. Ouvrir les coupures et les signalements afin de montrer la relation quartier → coupure → signalement.
6. Ouvrir les points de fraîcheur et l’avis associé à Parc du Bardo.
7. Ouvrir les équipements et les conseils : montrer les propriétaires et les associations conseil ↔ équipement.
8. Terminer par les contrôles d’accès : les routes admin sont protégées par authentification et rôle.

## Points à expliquer au professeur

- Les données sont créées de manière réexécutable avec updateOrCreate.
- Les relations Eloquent sont visibles dans les listes, détails et sélecteurs.
- Les validations serveur, la conservation de saisie old() et les retours d’erreur @error sont présents sur les formulaires audités.
- Les suppressions importantes demandent une confirmation dans l’interface.
- Les APIs et l’IA ne sont utilisées qu’avec des clés présentes dans .env; aucune clé n’est versionnée.
