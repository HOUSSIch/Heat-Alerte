# Heat Alerte

Heat Alerte est une application Laravel destinée à informer les habitants pendant les épisodes de forte chaleur. Elle centralise les alertes météo, les conseils de prévention, les équipements disponibles, les points de fraîcheur, les coupures électriques et les signalements des utilisateurs.
## Fonctionnalités

- Tableau de bord personnalisé après connexion.
- Consultation des alertes météo par quartier.
- Conseils de prévention et gestion des équipements personnels.
- Recherche de points de fraîcheur et dépôt d'avis.
- Consultation des coupures électriques.
- Création et suivi de signalements.
- Assistant conversationnel accessible depuis `/assistant`.
- Interface d'administration protégée par le rôle `admin` pour gérer les utilisateurs, quartiers, alertes, coupures, conseils, points de fraîcheur, avis et signalements.

## Prérequis
- PHP 8.2 ou supérieur avec les extensions nécessaires à Laravel.
- Composer.
- Node.js et npm.
- SQLite (configuration par défaut) ou une base de données compatible avec Laravel.

## Installation
Depuis la racine du projet :

```bash
composer install
npm install
copy .env.example .env
php artisan key:generate

```
Sous Linux ou macOS, remplacez la commande `copy` par :

```bash
cp .env.example .env

```
Le projet utilise SQLite par défaut. Créez le fichier de base s'il n'existe pas encore :

```bash
type nul > database/database.sqlite
php artisan migrate

```
Sous Linux ou macOS :

```bash
touch database/database.sqlite
php artisan migrate
```
## Configuration

Adaptez `.env` à votre environnement. Les variables facultatives suivantes activent les services externes :

```dotenv
GEMINI_API_KEY=
GEMINI_MODEL=gemini-2.5-flash
GEOAPIFY_API_KEY=
```
`GEMINI_API_KEY` est utilisée par l'assistant conversationnel. `GEOAPIFY_API_KEY` permet de rechercher et d'importer des points de fraîcheur depuis l'administration. Ne committez jamais de clé API dans le dépôt.

Pour utiliser MySQL ou PostgreSQL, remplacez les variables `DB_*` de `.env`, puis relancez les migrations.

## Données de démonstration
Le seeder principal crée un utilisateur de test. Pour charger les données de démonstration du projet, utilisez :

```bash
php artisan db:seed --class=DemoDataSeeder
```
Pour repartir d'une base vide et relancer les migrations avec les données de démonstration :

```bash
php artisan migrate:fresh --seed --seeder=DemoDataSeeder
```
## Lancement en développement

Dans un terminal, lancez Laravel :

```bash
php artisan serve
```
Dans un second terminal, lancez Vite :

```bash
npm run dev
```
L'application est alors disponible à l'adresse [http://localhost:8000](http://localhost:8000).

Le script Composer suivant lance simultanément le serveur Laravel, le worker de file d'attente, les logs et Vite :

```bash
composer run dev
```
## Tests et qualité

Exécuter la suite de tests :

```bash
php artisan test
```
Construire les assets pour la production :

```bash
npm run build
```
Avant une mise en production, configurez `APP_ENV=production`, désactivez `APP_DEBUG` et utilisez des secrets gérés par l'environnement d'exécution.

## Organisation du projet
- `app/Http/Controllers` : contrôleurs web et administration.
- `app/Models` : modèles Eloquent.
- `app/Services` : intégrations météo, géocodage et services externes.
- `database/migrations` : structure de la base de données.
- `database/seeders` : données initiales et de démonstration.
- `resources/views` : vues Blade.
- `resources/js` et `resources/css` : assets front-end.
- `routes/web.php` : routes publiques, utilisateur et administration.

## Technologies
- Laravel 12 et PHP 8.2+.
- Blade, Vite, Tailwind CSS et JavaScript.
- Eloquent ORM et migrations Laravel.
- Gemini pour l'assistant conversationnel.
- Geoapify pour la recherche de points de fraîcheur.

## Licence

Ce projet est distribué sous licence MIT.
<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
