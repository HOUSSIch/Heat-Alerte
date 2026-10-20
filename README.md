# HeatAlert

HeatAlert est une application Laravel de prévention locale face aux fortes chaleurs et aux coupures électriques. Elle permet aux habitants de suivre les alertes de leur quartier, signaler une coupure, protéger leurs équipements et trouver des espaces frais. Le back office permet l’administration des données territoriales, des signalements et des avis.

## Technologies

- Laravel 12 et PHP 8.2+
- MySQL en environnement local (SQLite est utilisé par la suite de tests)
- Vite, JavaScript et CSS
- Eloquent, migrations, factories, seeders et tests PHPUnit
- Leaflet / OpenStreetMap pour les cartes
- Open-Meteo, Nominatim, Geoapify et les services IA configurés dans l’environnement

## Templates utilisés

L’audit du code constate les éléments suivants :

- FrontOffice actif : [Workfolio de BootstrapMade](https://bootstrapmade.com/workfolio-bootstrap-portfolio-template/), personnalisé aux couleurs et parcours HeatAlert dans resources/views/layouts/front.blade.php.
- BackOffice actif : [Gentelella](https://github.com/ColorlibHQ/gentelella), personnalisé dans resources/views/admin/layouts/gentelella.blade.php.
- Le package [Tabler Free](https://tabler.io/) est présent dans package.json et le layout historique resources/views/layouts/user.blade.php est conservé. Il n’est cependant pas le layout actif de l’ensemble des vues front actuelles.
- AdminLTE n’est pas présent dans les assets/layouts actifs observés. Il ne serait donc pas honnête de le déclarer comme template effectivement utilisé sans une migration visuelle séparée.

Les layouts actifs centralisent le menu, le pied de page, les assets et @yield('content'); les pages enfant utilisent @extends et @section. Le thème HeatAlert est visible par le logo, les menus métier, les couleurs d’alerte, les parcours météo, coupures, équipements, assistance, cartes et avis.

## Installation locale

    git clone <URL_DU_DEPOT>
    cd "projet meteo"
    composer install
    npm install
    copy .env.example .env
    php artisan key:generate

Configurez ensuite la connexion MySQL dans .env, puis :

    php artisan migrate
    php artisan db:seed
    npm run build
    php artisan serve

L’application est alors disponible sur http://127.0.0.1:8000.

Ne versionnez jamais .env et ne placez aucune clé API réelle dans cette documentation.

## Comptes de démonstration

Après php artisan db:seed, le scénario de démonstration est créé ou mis à jour de manière idempotente.

| Rôle | Adresse e-mail | Mot de passe local |
| --- | --- | --- |
| Administrateur | admin@heatalert.test | password |
| Habitant | habitant@heatalert.test | password |
| Agent | agent@heatalert.test | password |

Ces identifiants sont exclusivement réservés à une démonstration locale. Le seeder conserve également les comptes de scénario existants admin@gmail.com et test@gmail.com lorsqu’ils sont utilisés dans la base locale.

## Fonctionnalités

- Authentification, profils et rôles admin, agent et habitant
- Gestion des quartiers, alertes météo et coupures électriques
- Signalements géolocalisés avec cartes
- Équipements d’un habitant, entretiens et conseils liés par une table pivot
- Points de fraîcheur, avis et modération
- Structures d’assistance et discussions de quartier
- Assistant HeatAlert et synthèses IA lorsque les clés correspondantes sont configurées

## APIs externes

| Service | Usage |
| --- | --- |
| [Open-Meteo](https://open-meteo.com/) | Données météo et vigilance |
| [Nominatim / OpenStreetMap](https://nominatim.openstreetmap.org/) | Géocodage et affichage cartographique |
| [Geoapify](https://www.geoapify.com/) | Recherche de lieux et points de fraîcheur |
| Gemini / Groq | Fonctions d’assistance et de synthèse IA selon la configuration |

Les variables documentées dans .env.example sont notamment GEMINI_API_KEY, GEMINI_MODEL, GROQ_API_KEY, GROQ_MODEL et GEOAPIFY_API_KEY. Elles doivent rester vides dans le dépôt.

## Données de démonstration, factories et seeders

HeatAlertDemoSeeder crée un scénario cohérent, réexécutable et relationnel :

- quartier Le Bardo;
- utilisateurs admin, habitant et agent;
- équipements, conseils associés, alerte, coupure et signalement;
- point de fraîcheur et avis publié.

Les factories suivantes sont disponibles pour les tests et les démonstrations : User, Quartier, Equipement, Conseil, AlerteMeteo, CoupureElectrique, Signalement, PointFraicheur et Avis.

Pour lancer explicitement le scénario :

    php artisan db:seed --class=HeatAlertDemoSeeder

## Tests et vérifications

    php artisan optimize:clear
    php artisan route:list
    npm run build
    php artisan test

La suite teste notamment les autorisations, les CRUD, les relations, les validations, les factories et le seeder idempotent.

## Scénario de démonstration

Le déroulé de soutenance de 5 à 10 minutes se trouve dans docs/SCENARIO_DEMO.md.

## Git

Avant un commit, vérifiez l’état réel du dépôt :

    git status
    git log --oneline --decorate -10

Les changements de cette mission peuvent être séparés de façon lisible, par exemple :

- feat: add relational factories and demo seeder
- test: cover factories and demo scenario
- docs: document demo setup and templates

Ne réécrivez pas l’historique et n’effectuez pas de force-push pour une démonstration académique.

## CI/CD

La branche de préparation DevOps est `feature/devops-pipeline`. Le workflow GitHub Actions
`.github/workflows/ci.yml` s’exécute sur les pull requests vers `main`, ainsi que sur les
push vers `main` et la branche DevOps. Il installe PHP 8.2 et Node 22, utilise une base MySQL
temporaire, construit les assets Vite, applique les migrations de test, exécute PHPUnit avec
PCOV, puis vérifie Pint et Larastan.

L’analyse SonarQube Cloud est activée lorsque les variables GitHub `SONAR_PROJECT_KEY` et
`SONAR_ORGANIZATION`, ainsi que le secret `SONAR_TOKEN`, ont été configurés. Le scanner attend
le Quality Gate : un échec rend le job rouge. Railway doit être connecté au dépôt avec l’option
**Wait for CI** activée afin de ne déployer `main` qu’après la réussite de tous les checks.

Les détails de configuration, les variables de production et la procédure de déploiement sont
documentés dans [docs/DEVOPS.md](docs/DEVOPS.md).

## Authentification et sécurité

HeatAlert utilise l’authentification Laravel existante : inscription, connexion, déconnexion, option « Se souvenir de moi », récupération de mot de passe par Password Broker, vérification d’adresse e-mail signée et rôles côté serveur.

Les comptes nouvellement inscrits doivent vérifier leur adresse e-mail avant d’accéder aux fonctions métier. Les comptes de démonstration sont déjà vérifiés afin de ne pas bloquer la soutenance.

Le profil permet la mise à jour du nom, de l’e-mail, du quartier, du mot de passe, de la photo de profil et la suppression sécurisée du compte. Le changement de mot de passe et la suppression demandent le mot de passe actuel. Pour utiliser l’affichage public des photos en local, créer le lien de stockage une seule fois :

    php artisan storage:link

La 2FA est TOTP standard, compatible avec Google Authenticator, Microsoft Authenticator et Authy. Elle se configure dans Profil, demande la confirmation d’un code à six chiffres et propose huit codes de récupération à usage unique.

## Configuration e-mail

Ne renseignez les variables suivantes que dans `.env`, jamais dans Git. Laravel utilise `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS` et `MAIL_FROM_NAME`; Laravel 12 utilise `MAIL_SCHEME` pour le schéma SMTP. Une valeur historique `MAIL_ENCRYPTION=ssl` est aussi reconnue et associée à `smtps`; pour TLS/port 587, le transport SMTP Laravel négocie STARTTLS automatiquement.

    MAIL_MAILER=smtp
    MAIL_HOST=
    MAIL_PORT=
    MAIL_USERNAME=
    MAIL_PASSWORD=
    MAIL_ENCRYPTION=
    MAIL_FROM_ADDRESS=
    MAIL_FROM_NAME=HeatAlert
    MAIL_SCHEME=

Le mailer `log` suffit pour le développement : les liens sont écrits dans les logs Laravel. Pour une démonstration avec e-mail réel, configurez un SMTP valide dans `.env`, puis exécutez `php artisan optimize:clear` afin que Laravel relise la configuration. Aucune valeur réelle ne doit apparaître dans le dépôt ou dans la documentation.

## E-mails HeatAlert

Tous les e-mails utilisateur utilisent l’expéditeur configuré par `MAIL_FROM_ADDRESS` et `MAIL_FROM_NAME` (HeatAlert) ainsi que le template commun `resources/views/emails/layout.blade.php`.

- Vérification d’adresse e-mail, via URL temporaire signée Laravel ;
- Réinitialisation de mot de passe, via Password Broker et son token à durée de vie configurée ;
- Confirmation de modification de mot de passe ;
- Confirmation d’activation ou de désactivation de la 2FA.

Les liens de vérification et de réinitialisation sont générés à partir de `APP_URL`. Les secrets SMTP, tokens, secrets TOTP et codes de récupération ne sont jamais insérés dans les e-mails de sécurité.
