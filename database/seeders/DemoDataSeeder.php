<?php

namespace Database\Seeders;

use App\Models\AlerteMeteo;
use App\Models\Conseil;
use App\Models\CoupureElectrique;
use App\Models\Equipement;
use App\Models\Quartier;
use App\Models\Signalement;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $bardo = Quartier::firstOrCreate(['nom' => 'Bardo', 'ville' => 'Tunis'], ['code_postal' => '2000', 'latitude' => 36.8090, 'longitude' => 10.1400, 'actif' => true]);
        $lacroix = Quartier::firstOrCreate(['nom' => 'La Marsa', 'ville' => 'Tunis'], ['code_postal' => '2070', 'latitude' => 36.8782, 'longitude' => 10.3247, 'actif' => true]);

        $admin = User::firstOrCreate(['email' => 'admin@gmail.com'], ['name' => 'Administrateur HeatAlert', 'password' => Hash::make('admin123'), 'role' => 'admin', 'quartier_id' => $bardo->id]);
        $admin->update(['role' => 'admin', 'quartier_id' => $bardo->id]);
        $habitant = User::firstOrCreate(['email' => 'habitant@heatalert.test'], ['name' => 'Amine Bardo', 'password' => Hash::make('password'), 'role' => 'habitant', 'quartier_id' => $bardo->id]);
        $habitant->update(['quartier_id' => $bardo->id]);
        $testUser = User::firstOrCreate(['email' => 'test@gmail.com'], ['name' => 'Utilisateur scénario', 'password' => Hash::make('password'), 'role' => 'habitant', 'quartier_id' => $bardo->id]);
        $testUser->update(['role' => 'habitant', 'quartier_id' => $bardo->id]);
        $agent = User::firstOrCreate(['email' => 'agent@heatalert.test'], ['name' => 'Sana Marsa', 'password' => Hash::make('password'), 'role' => 'agent', 'quartier_id' => $lacroix->id]);

        $frigo = Equipement::firstOrCreate(['user_id' => $habitant->id, 'nom' => 'Réfrigérateur'], ['type' => 'Électroménager', 'marque' => 'Samsung', 'sensible_chaleur' => true, 'sensible_coupure' => true, 'description' => 'Conservation des aliments.']);
        $pc = Equipement::firstOrCreate(['user_id' => $habitant->id, 'nom' => 'Ordinateur portable'], ['type' => 'Informatique', 'marque' => 'Lenovo', 'sensible_chaleur' => true, 'sensible_coupure' => true, 'description' => 'Poste de télétravail.']);
        $testFrigo = Equipement::firstOrCreate(['user_id' => $testUser->id, 'nom' => 'Réfrigérateur scénario'], ['type' => 'Électroménager', 'marque' => 'Whirlpool', 'sensible_chaleur' => true, 'sensible_coupure' => true, 'description' => 'Équipement principal du scénario.']);
        $testPc = Equipement::firstOrCreate(['user_id' => $testUser->id, 'nom' => 'PC portable scénario'], ['type' => 'Informatique', 'marque' => 'Dell', 'sensible_chaleur' => true, 'sensible_coupure' => true, 'description' => 'Poste informatique du scénario.']);

        $conseil = Conseil::firstOrCreate(['titre' => 'Limiter les ouvertures du réfrigérateur'], ['description' => 'Gardez la porte fermée pendant une coupure.', 'categorie' => 'Coupure électrique', 'niveau' => 'Important', 'actif' => true]);
        $conseil->equipements()->syncWithoutDetaching([$frigo->id, $pc->id]);
        $conseil->equipements()->syncWithoutDetaching([$testFrigo->id, $testPc->id]);

        AlerteMeteo::updateOrCreate(['quartier_id' => $bardo->id, 'source' => 'open-meteo', 'actif' => true], ['titre' => 'Forte chaleur à Bardo', 'description' => 'Hydratez-vous et évitez les sorties aux heures chaudes.', 'temperature' => 41, 'temperature_max' => 43, 'temperature_min' => 29, 'niveau' => 'Alerte', 'date_debut' => now(), 'date_fin' => now()->endOfDay()]);

        $coupure = CoupureElectrique::updateOrCreate(['quartier_id' => $bardo->id, 'titre' => 'Maintenance réseau électrique Bardo'], ['description' => 'Intervention technique programmée sur le réseau local.', 'type' => 'maintenance', 'cause' => 'Maintenance préventive', 'statut' => 'prévue', 'date_debut' => now()->addDay()->setTime(9, 0), 'date_fin_estimee' => now()->addDay()->setTime(12, 0), 'adresse' => 'Avenue Habib Bourguiba, Bardo', 'latitude' => 36.8090, 'longitude' => 10.1400, 'source' => 'admin', 'actif' => true]);

        Signalement::firstOrCreate(['user_id' => $habitant->id, 'titre' => 'Coupure dans mon immeuble'], ['coupure_electrique_id' => $coupure->id, 'quartier_id' => $bardo->id, 'description' => 'Coupure intermittente depuis ce matin.', 'adresse' => 'Rue de la République, Bardo', 'latitude' => 36.8100, 'longitude' => 10.1420, 'statut' => 'validé', 'date_signalement' => now()->subHour()]);
        Signalement::firstOrCreate(['user_id' => $testUser->id, 'titre' => 'Coupure signalée pour le scénario'], ['coupure_electrique_id' => $coupure->id, 'quartier_id' => $bardo->id, 'description' => 'Signalement de démonstration lié à la coupure de maintenance.', 'adresse' => 'Bardo, Tunis, Tunisia', 'latitude' => 36.8090, 'longitude' => 10.1400, 'statut' => 'en_attente', 'date_signalement' => now()->subMinutes(15)]);
        Signalement::firstOrCreate(['user_id' => $agent->id, 'titre' => 'Panne signalée près du marché'], ['quartier_id' => $lacroix->id, 'description' => 'Plusieurs commerces sont concernés.', 'adresse' => 'Marché de La Marsa', 'latitude' => 36.8780, 'longitude' => 10.3250, 'statut' => 'en_attente', 'date_signalement' => now()->subMinutes(30)]);
    }
}
