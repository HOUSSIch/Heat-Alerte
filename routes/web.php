<?php

use Illuminate\Support\Facades\Route;

// Heat-Alerte controllers
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EquipementController;
use App\Http\Controllers\ConseilController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\MeteoController;
use App\Http\Controllers\CoupureController;
use App\Http\Controllers\SignalementController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PointFraicheurController;

// Admin controllers
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\EquipementController as AdminEquipementController;
use App\Http\Controllers\Admin\QuartierController as AdminQuartierController;
use App\Http\Controllers\Admin\AlerteMeteoController as AdminAlerteMeteoController;
use App\Http\Controllers\Admin\CoupureController as AdminCoupureController;
use App\Http\Controllers\Admin\SignalementController as AdminSignalementController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PointFraicheurController as AdminPointFraicheurController;
use App\Http\Controllers\Admin\AvisController as AdminAvisController;


// ==========================
// PAGE D'ACCUEIL
// ==========================

Route::get('/', function () {
    return view('welcome');
});


// ==========================
// AUTHENTIFICATION
// ==========================

Route::get('/register', [AuthController::class, 'showRegister'])
    ->name('register');

Route::post('/register', [AuthController::class, 'register']);

Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');


// ==========================
// ROUTES UTILISATEUR CONNECTÉ
// ==========================

Route::middleware('auth')->group(function () {

    // ==========================
    // ADMIN
    // ==========================

    Route::middleware('role:admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            Route::get('/dashboard', [AdminDashboardController::class, 'index'])
                ->name('dashboard');

            Route::resource('users', AdminUserController::class);

            Route::resource('equipements', AdminEquipementController::class)
                ->only(['index', 'show']);

            Route::resource('quartiers', AdminQuartierController::class);

            Route::post(
                'quartiers/{quartier}/generer-alerte',
                [AdminQuartierController::class, 'generate']
            )->name('quartiers.generate');

            Route::resource('alertes-meteo', AdminAlerteMeteoController::class)
                ->parameters(['alertes-meteo' => 'alerte_meteo']);

            Route::resource('coupures', AdminCoupureController::class);

            Route::get(
                'signalements',
                [AdminSignalementController::class, 'index']
            )->name('signalements.index');

            Route::get(
                'signalements/{signalement}',
                [AdminSignalementController::class, 'show']
            )->name('signalements.show');

            Route::put(
                'signalements/{signalement}',
                [AdminSignalementController::class, 'update']
            )->name('signalements.update');

            Route::resource('conseils', ConseilController::class)
                ->names('conseils');

            Route::get(
                'points-fraicheur/geoapify',
                [AdminPointFraicheurController::class, 'geoapify']
            )->name('points-fraicheur.geoapify');

            Route::post(
                'points-fraicheur/geoapify/search',
                [AdminPointFraicheurController::class, 'searchGeoapify']
            )->name('points-fraicheur.geoapify.search');

            Route::post(
                'points-fraicheur/geoapify/import',
                [AdminPointFraicheurController::class, 'importGeoapify']
            )->name('points-fraicheur.geoapify.import');

            Route::resource(
                'points-fraicheur',
                AdminPointFraicheurController::class
            )->parameters([
                'points-fraicheur' => 'pointFraicheur'
            ]);

            Route::resource('avis', AdminAvisController::class)
                ->only(['index', 'show', 'destroy'])
                ->parameters(['avis' => 'avis']);

            Route::put(
                'avis/{avis}/publier',
                [AdminAvisController::class, 'publish']
            )->name('avis.publish');

            Route::put(
                'avis/{avis}/rejeter',
                [AdminAvisController::class, 'reject']
            )->name('avis.reject');

            Route::get('/profile', function (\Illuminate\Http\Request $request) {
                return view('admin.profile', [
                    'user' => $request->user()
                ]);
            })->name('profile');

            Route::get('/profile/edit', function (\Illuminate\Http\Request $request) {
                return view('admin.profile-edit', [
                    'user' => $request->user()
                ]);
            })->name('profile.edit');
        });


    // ==========================
    // USER DASHBOARD
    // ==========================

    Route::get(
        '/dashboard',
        [DashboardController::class, 'index']
    )->name('dashboard');


    // ==========================
    // WEATHER
    // ==========================

    Route::get(
        '/alertes-meteo',
        [MeteoController::class, 'index']
    )->name('meteo.index');


    // ==========================
    // POWER OUTAGES
    // ==========================

    Route::get(
        '/coupures',
        [CoupureController::class, 'index']
    )->name('coupures.index');

    Route::get(
        '/coupures/{coupure}',
        [CoupureController::class, 'show']
    )->name('coupures.show');


    // ==========================
    // REPORTS
    // ==========================

    Route::get(
        '/mes-signalements',
        [SignalementController::class, 'index']
    )->name('signalements.index');

    Route::get(
        '/mes-signalements/{signalement}',
        [SignalementController::class, 'show']
    )->name('signalements.show');

    Route::get(
        '/signalements/create',
        [SignalementController::class, 'create']
    )->name('signalements.create');

    Route::post(
        '/signalements',
        [SignalementController::class, 'store']
    )->name('signalements.store');


    // ==========================
    // PROFILE
    // ==========================

    Route::get(
        '/profile',
        [ProfileController::class, 'show']
    )->name('profile.show');

    Route::get(
        '/profile/edit',
        [ProfileController::class, 'edit']
    )->name('profile.edit');

    Route::put(
        '/profile',
        [ProfileController::class, 'update']
    )->name('profile.update');

    Route::delete(
        '/profile',
        [ProfileController::class, 'destroy']
    )->name('profile.destroy');


    // ==========================
    // EQUIPMENT
    // ==========================

    Route::resource(
        'equipements',
        EquipementController::class
    );


    // ==========================
    // ADVICE
    // ==========================

    Route::get(
        '/mes-conseils',
        [ConseilController::class, 'mes']
    )->name('conseils.mes');

    Route::middleware('role:admin')->group(function () {
        Route::resource(
            'conseils',
            ConseilController::class
        );
    });


    // ==========================
    // CHATBOT
    // ==========================

    Route::get(
        '/assistant',
        [ChatbotController::class, 'index']
    )->name('chatbot.index');

    Route::post(
        '/assistant',
        [ChatbotController::class, 'send']
    )->name('chatbot.send');

    Route::delete(
        '/assistant',
        [ChatbotController::class, 'clear']
    )->name('chatbot.clear');


    // ==========================
    // COOLING POINTS
    // ==========================

    Route::get(
        '/points-fraicheur',
        [PointFraicheurController::class, 'index']
    )->name('points-fraicheur.index');

    Route::get(
        '/points-fraicheur/{pointFraicheur}',
        [PointFraicheurController::class, 'show']
    )->name('points-fraicheur.show');

    Route::post(
        '/points-fraicheur/{pointFraicheur}/avis',
        [PointFraicheurController::class, 'storeAvis']
    )->name('points-fraicheur.avis.store');

    Route::put(
        '/points-fraicheur/{pointFraicheur}/avis/{avis}',
        [PointFraicheurController::class, 'updateAvis']
    )->name('points-fraicheur.avis.update');

    Route::delete(
        '/points-fraicheur/{pointFraicheur}/avis/{avis}',
        [PointFraicheurController::class, 'destroyAvis']
    )->name('points-fraicheur.avis.destroy');
});