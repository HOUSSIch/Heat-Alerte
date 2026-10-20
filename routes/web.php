<?php

use App\Http\Controllers\Admin\AlerteMeteoController as AdminAlerteMeteoController;
use App\Http\Controllers\Admin\AssistantController as AdminAssistantController;
use App\Http\Controllers\Admin\AvisController as AdminAvisController;
use App\Http\Controllers\Admin\CoupureController as AdminCoupureController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EntretienController as AdminEntretienController;
use App\Http\Controllers\Admin\EquipementController as AdminEquipementController;
use App\Http\Controllers\Admin\EtablissementSensibleController as AdminEtablissementSensibleController;
use App\Http\Controllers\Admin\PointFraicheurController as AdminPointFraicheurController;
use App\Http\Controllers\Admin\PublicationQuartierController as AdminPublicationQuartierController;
use App\Http\Controllers\Admin\QuartierController as AdminQuartierController;
use App\Http\Controllers\Admin\SignalementController as AdminSignalementController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\WeatherVigilanceController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\ConseilController;
use App\Http\Controllers\CoupureController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EntretienEquipementController;
use App\Http\Controllers\EquipementController;
use App\Http\Controllers\EquipementPdfController;
use App\Http\Controllers\EquipementQrController;
use App\Http\Controllers\EtablissementSensibleController;
use App\Http\Controllers\MeteoController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PointFraicheurController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicationQuartierController;
use App\Http\Controllers\SignalementController;
use App\Http\Controllers\TwoFactorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ==========================
// PAGE D'ACCUEIL
// ==========================

Route::get('/', function () {
    return view('welcome');
});

// ==========================
// ROUTES UTILISATEUR CONNECTÉ
// ==========================

Route::middleware(['auth', 'verified'])->group(function () {
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::resource('users', AdminUserController::class);
        Route::resource('equipements', AdminEquipementController::class)->only(['index', 'show']);
        Route::get('entretiens', [AdminEntretienController::class, 'index'])->name('entretiens.index');
        Route::get('assistant', [AdminAssistantController::class, 'index'])->name('assistant.index');
        Route::post('assistant', [AdminAssistantController::class, 'send'])->name('assistant.send');
        Route::get('vigilance-meteo', [WeatherVigilanceController::class, 'index'])->name('vigilance-meteo.index');
        Route::post('vigilance-meteo/{quartier}/actualiser', [WeatherVigilanceController::class, 'refresh'])->name('vigilance-meteo.refresh');
        Route::resource('quartiers', AdminQuartierController::class);
        Route::resource('etablissements-sensibles', AdminEtablissementSensibleController::class)
            ->parameters(['etablissements-sensibles' => 'etablissementSensible']);
        Route::post('quartiers/{quartier}/generer-alerte', [AdminQuartierController::class, 'generate'])->name('quartiers.generate');
        Route::resource('alertes-meteo', AdminAlerteMeteoController::class)->parameters(['alertes-meteo' => 'alerte_meteo']);
        Route::resource('coupures', AdminCoupureController::class);
        Route::get('coupures-planning', [AdminCoupureController::class, 'planning'])->name('coupures.planning');
        Route::get('signalements', [AdminSignalementController::class, 'index'])->name('signalements.index');
        Route::get('signalements/{signalement}', [AdminSignalementController::class, 'show'])->name('signalements.show');
        Route::put('signalements/{signalement}', [AdminSignalementController::class, 'update'])->name('signalements.update');
        Route::resource('conseils', ConseilController::class)->names('conseils');
        Route::get('points-fraicheur/geoapify', [AdminPointFraicheurController::class, 'geoapify'])->name('points-fraicheur.geoapify');
        Route::post('points-fraicheur/geoapify/search', [AdminPointFraicheurController::class, 'searchGeoapify'])->name('points-fraicheur.geoapify.search');
        Route::post('points-fraicheur/geoapify/import', [AdminPointFraicheurController::class, 'importGeoapify'])->name('points-fraicheur.geoapify.import');
        Route::resource('points-fraicheur', AdminPointFraicheurController::class)->parameters(['points-fraicheur' => 'pointFraicheur']);
        Route::get('couverture-points-fraicheur', [AdminPointFraicheurController::class, 'coverage'])->name('points-fraicheur.couverture');
        Route::post('points-fraicheur/{pointFraicheur}/analyse-avis', [AdminPointFraicheurController::class, 'analyseAvis'])->name('points-fraicheur.analyse-avis');
        Route::post('avis/analyse', [AdminAvisController::class, 'analyse'])->name('avis.analyse');
        Route::resource('avis', AdminAvisController::class)->only(['index', 'show', 'destroy'])->parameters(['avis' => 'avis']);
        Route::get('discussions-quartier', [AdminPublicationQuartierController::class, 'index'])->name('discussions-quartier.index');
        Route::get('discussions-quartier/{publication}', [AdminPublicationQuartierController::class, 'show'])->name('discussions-quartier.show');
        Route::put('discussions-quartier/{publication}', [AdminPublicationQuartierController::class, 'update'])->name('discussions-quartier.update');
        Route::delete('discussions-quartier/{publication}', [AdminPublicationQuartierController::class, 'destroy'])->name('discussions-quartier.destroy');
        Route::put('avis/{avis}/publier', [AdminAvisController::class, 'publish'])->name('avis.publish');
        Route::put('avis/{avis}/rejeter', [AdminAvisController::class, 'reject'])->name('avis.reject');
        Route::get('/profile', fn (Request $request) => view('admin.profile', ['user' => $request->user()]))->name('profile');
        Route::get('/profile/edit', fn (Request $request) => view('admin.profile-edit', ['user' => $request->user()]))->name('profile.edit');
    });

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/alertes-meteo', [MeteoController::class, 'index'])->name('meteo.index');
    Route::post('/alertes-meteo/brief-ia', [MeteoController::class, 'brief'])->name('meteo.brief');
    Route::get('/quartier/discussions', [PublicationQuartierController::class, 'index'])->name('discussions-quartier.index');
    Route::get('/quartier/discussions/create', [PublicationQuartierController::class, 'create'])->name('discussions-quartier.create');
    Route::post('/quartier/discussions', [PublicationQuartierController::class, 'store'])->name('discussions-quartier.store');
    Route::get('/quartier/discussions/{publication}', [PublicationQuartierController::class, 'show'])->name('discussions-quartier.show');
    Route::post('/quartier/discussions/{publication}/commentaires', [PublicationQuartierController::class, 'comment'])->name('discussions-quartier.comment');
    Route::post('/quartier/discussions/{publication}/confirmations', [PublicationQuartierController::class, 'toggleConfirmation'])->name('discussions-quartier.confirmation');
    Route::patch('/quartier/discussions/{publication}/statut', [PublicationQuartierController::class, 'updateStatus'])->name('discussions-quartier.status');
    Route::post('/quartier/discussions/{publication}/resume-ia', [PublicationQuartierController::class, 'summarize'])->name('discussions-quartier.resume-ia');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'read'])->name('notifications.read');
    Route::patch('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');
    Route::get('/coupures', [CoupureController::class, 'index'])->name('coupures.index');
    Route::get('/coupures/planning', [CoupureController::class, 'planning'])->name('coupures.planning');
    Route::get('/coupures/{coupure}', [CoupureController::class, 'show'])->name('coupures.show');
    Route::get('/mes-signalements', [SignalementController::class, 'index'])->name('signalements.index');
    Route::get('/mes-signalements/{signalement}', [SignalementController::class, 'show'])->name('signalements.show');
    Route::get('/signalements/create', [SignalementController::class, 'create'])->name('signalements.create');
    Route::post('/signalements', [SignalementController::class, 'store'])->name('signalements.store');
    Route::get('/signalements/adresses', [SignalementController::class, 'addressSearch'])->name('signalements.addresses');
    Route::post('/signalements/assistant-fill', [SignalementController::class, 'assistantFill'])->name('signalements.assistant-fill');

    // Profil - READ
    Route::get('/profile', [ProfileController::class, 'show'])
        ->name('profile.show');

    // Profil - formulaire UPDATE
    Route::get('/profile/edit', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    // Profil - UPDATE
    Route::put('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::patch('/profile', [ProfileController::class, 'update']);

    // Profil - DELETE
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    Route::post('/profile/two-factor/enable', [TwoFactorController::class, 'enable'])->name('two-factor.enable');
    Route::get('/profile/two-factor/setup', [TwoFactorController::class, 'setup'])->name('two-factor.setup');
    Route::post('/profile/two-factor/confirm', [TwoFactorController::class, 'confirm'])->name('two-factor.confirm');
    Route::post('/profile/two-factor/recovery-codes', [TwoFactorController::class, 'regenerate'])->name('two-factor.recovery-codes');
    Route::delete('/profile/two-factor', [TwoFactorController::class, 'disable'])->name('two-factor.disable');

    Route::get('equipements/{equipement}/qr', [EquipementQrController::class, 'show'])->name('equipements.qr');
    Route::get('equipements/{equipement}/qr/download', [EquipementQrController::class, 'download'])->name('equipements.qr.download');
    Route::get('equipements/{equipement}/fiche-pdf', [EquipementPdfController::class, 'download'])->name('equipements.pdf.download');
    Route::post('equipements/{equipement}/analyse-ia', [ChatbotController::class, 'analyseEquipement'])->name('equipements.ai-analysis');
    Route::resource('equipements.entretiens', EntretienEquipementController::class)->except(['index', 'show']);
    Route::patch('equipements/{equipement}/entretiens/{entretien}/realiser', [EntretienEquipementController::class, 'realiser'])->name('equipements.entretiens.realiser');
    Route::resource('equipements', EquipementController::class);
    Route::get('/mes-conseils', [ConseilController::class, 'mes'])->name('conseils.mes');
    Route::get('/assistant', [ChatbotController::class, 'index'])->name('chatbot.index');
    Route::post('/assistant', [ChatbotController::class, 'send'])->name('chatbot.send');
    Route::delete('/assistant', [ChatbotController::class, 'clear'])->name('chatbot.clear');
    Route::get('/points-fraicheur', [PointFraicheurController::class, 'index'])->name('points-fraicheur.index');
    Route::get('/points-fraicheur/comparer', [PointFraicheurController::class, 'compare'])->name('points-fraicheur.compare');
    Route::get('/points-fraicheur/{pointFraicheur}', [PointFraicheurController::class, 'show'])->name('points-fraicheur.show');
    Route::get('/etablissements-sensibles', [EtablissementSensibleController::class, 'index'])->name('etablissements-sensibles.index');
    Route::get('/etablissements-sensibles/{etablissementSensible}', [EtablissementSensibleController::class, 'show'])->name('etablissements-sensibles.show');
    Route::post('/points-fraicheur/{pointFraicheur}/avis', [PointFraicheurController::class, 'storeAvis'])->name('points-fraicheur.avis.store');
    Route::put('/points-fraicheur/{pointFraicheur}/avis/{avis}', [PointFraicheurController::class, 'updateAvis'])->name('points-fraicheur.avis.update');
    Route::delete('/points-fraicheur/{pointFraicheur}/avis/{avis}', [PointFraicheurController::class, 'destroyAvis'])->name('points-fraicheur.avis.destroy');
    Route::middleware('role:admin')->group(function () {
        Route::resource('conseils', ConseilController::class);
    });

});

require __DIR__.'/auth.php';
