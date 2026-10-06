<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EquipementController;
use App\Http\Controllers\ConseilController;
use App\Http\Controllers\ChatbotController;
use App\Models\User;
use App\Models\Equipement;
use App\Models\Conseil;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\EquipementController as AdminEquipementController;
use App\Http\Controllers\Admin\QuartierController as AdminQuartierController;
use App\Http\Controllers\Admin\AlerteMeteoController as AdminAlerteMeteoController;
use App\Http\Controllers\MeteoController;
use App\Http\Controllers\CoupureController;
use App\Http\Controllers\SignalementController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\CoupureController as AdminCoupureController;
use App\Http\Controllers\Admin\SignalementController as AdminSignalementController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PointFraicheurController as AdminPointFraicheurController;
use App\Http\Controllers\Admin\AvisController as AdminAvisController;
use App\Http\Controllers\PointFraicheurController;

// ==========================
// PAGE D'ACCUEIL
// ==========================

Route::get('/', function () {
    return view('welcome');
});


// ==========================
// AUTHENTIFICATION
// ==========================

// Register
Route::get('/register', [AuthController::class, 'showRegister'])
    ->name('register');

Route::post('/register', [AuthController::class, 'register']);

// Login
Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login']);

// Logout
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');


// ==========================
// ROUTES UTILISATEUR CONNECTÉ
// ==========================

Route::middleware('auth')->group(function () {
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::resource('users', AdminUserController::class);
        Route::resource('equipements', AdminEquipementController::class)->only(['index','show']);
        Route::resource('quartiers', AdminQuartierController::class);
        Route::post('quartiers/{quartier}/generer-alerte', [AdminQuartierController::class,'generate'])->name('quartiers.generate');
        Route::resource('alertes-meteo', AdminAlerteMeteoController::class)->parameters(['alertes-meteo'=>'alerte_meteo']);
        Route::resource('coupures', AdminCoupureController::class);
        Route::get('signalements', [AdminSignalementController::class,'index'])->name('signalements.index');
        Route::get('signalements/{signalement}', [AdminSignalementController::class,'show'])->name('signalements.show');
        Route::put('signalements/{signalement}', [AdminSignalementController::class,'update'])->name('signalements.update');
        Route::resource('conseils', ConseilController::class)->names('conseils');
        Route::get('points-fraicheur/geoapify', [AdminPointFraicheurController::class, 'geoapify'])->name('points-fraicheur.geoapify');
        Route::post('points-fraicheur/geoapify/search', [AdminPointFraicheurController::class, 'searchGeoapify'])->name('points-fraicheur.geoapify.search');
        Route::post('points-fraicheur/geoapify/import', [AdminPointFraicheurController::class, 'importGeoapify'])->name('points-fraicheur.geoapify.import');
        Route::resource('points-fraicheur', AdminPointFraicheurController::class)->parameters(['points-fraicheur' => 'pointFraicheur']);
        Route::resource('avis', AdminAvisController::class)->only(['index', 'show', 'destroy'])->parameters(['avis' => 'avis']);
        Route::put('avis/{avis}/publier', [AdminAvisController::class, 'publish'])->name('avis.publish');
        Route::put('avis/{avis}/rejeter', [AdminAvisController::class, 'reject'])->name('avis.reject');
        Route::get('/profile', fn (\Illuminate\Http\Request $request) => view('admin.profile', ['user' => $request->user()]))->name('profile');
        Route::get('/profile/edit', fn (\Illuminate\Http\Request $request) => view('admin.profile-edit', ['user' => $request->user()]))->name('profile.edit');
    });

    // Dashboard
    Route::get('/dashboard', [DashboardController::class,'index'])->name('dashboard');
    Route::get('/alertes-meteo', [MeteoController::class, 'index'])->name('meteo.index');
    Route::get('/coupures', [CoupureController::class,'index'])->name('coupures.index');
    Route::get('/coupures/{coupure}', [CoupureController::class,'show'])->name('coupures.show');
    Route::get('/mes-signalements', [SignalementController::class,'index'])->name('signalements.index');
    Route::get('/mes-signalements/{signalement}', [SignalementController::class,'show'])->name('signalements.show');
    Route::get('/signalements/create', [SignalementController::class,'create'])->name('signalements.create');
    Route::post('/signalements', [SignalementController::class,'store'])->name('signalements.store');


    // Profil - READ
    Route::get('/profile', [ProfileController::class, 'show'])
        ->name('profile.show');

    // Profil - formulaire UPDATE
    Route::get('/profile/edit', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    // Profil - UPDATE
    Route::put('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    // Profil - DELETE
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    Route::resource('equipements', EquipementController::class);
    Route::get('/mes-conseils', [ConseilController::class, 'mes'])->name('conseils.mes');
    Route::get('/assistant', [ChatbotController::class, 'index'])->name('chatbot.index');
    Route::post('/assistant', [ChatbotController::class, 'send'])->name('chatbot.send');
    Route::delete('/assistant', [ChatbotController::class, 'clear'])->name('chatbot.clear');
    Route::get('/points-fraicheur', [PointFraicheurController::class, 'index'])->name('points-fraicheur.index');
    Route::get('/points-fraicheur/{pointFraicheur}', [PointFraicheurController::class, 'show'])->name('points-fraicheur.show');
    Route::post('/points-fraicheur/{pointFraicheur}/avis', [PointFraicheurController::class, 'storeAvis'])->name('points-fraicheur.avis.store');
    Route::put('/points-fraicheur/{pointFraicheur}/avis/{avis}', [PointFraicheurController::class, 'updateAvis'])->name('points-fraicheur.avis.update');
    Route::delete('/points-fraicheur/{pointFraicheur}/avis/{avis}', [PointFraicheurController::class, 'destroyAvis'])->name('points-fraicheur.avis.destroy');
    Route::middleware('role:admin')->group(function () { Route::resource('conseils', ConseilController::class); });

});
