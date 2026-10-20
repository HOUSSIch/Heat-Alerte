<?php

namespace Database\Seeders;

use App\Models\AlerteMeteo;
use App\Models\Avis;
use App\Models\Conseil;
use App\Models\CoupureElectrique;
use App\Models\EntretienEquipement;
use App\Models\Equipement;
use App\Models\EtablissementSensible;
use App\Models\PointFraicheur;
use App\Models\PublicationQuartier;
use App\Models\Quartier;
use App\Models\Signalement;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class HeatAlertDemoSeeder extends Seeder
{
    public function run(): void
    {
        $quartier = Quartier::updateOrCreate(
            ['nom' => 'Manzah 1', 'ville' => 'tunis'],
            [
                'code_postal' => '2091',
                'latitude' => 36.8270000,
                'longitude' => 10.1800000,
                'actif' => true,
            ],
        );

        $testUser = User::updateOrCreate(
            ['email' => 'test@gmail.com'],
            [
                'name' => 'Utilisateur scénario HeatAlert',
                'password' => Hash::make('password'),
                'role' => 'habitant',
                'quartier_id' => $quartier->id,
                'email_verified_at' => now(),
            ],
        );

        $admin = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Administrateur HeatAlert',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'quartier_id' => $quartier->id,
                'email_verified_at' => now(),
            ],
        );

        DB::transaction(function () use ($testUser, $admin, $quartier): void {
            $testUser->update([
                'name' => 'Utilisateur scénario HeatAlert',
                'role' => 'habitant',
                'quartier_id' => $quartier->id,
            ]);
            $admin->update(['role' => 'admin']);

            $equipements = $this->createDemoEquipements($testUser);
            $this->createDemoConseils($equipements);
            $this->createDemoEntretiens($equipements);
            $coupure = $this->createDemoCoupure($quartier);
            $this->createDemoSignalements($testUser, $quartier, $coupure);
            $this->createDemoAvis($testUser, $quartier);
            $this->createDemoAlerte($quartier);
            $this->ensureDemoStructures($quartier);
            $this->createDemoDiscussions($testUser, $admin, $quartier);
            $this->createAcademicScenario();
        });
    }

    /**
     * Adds the concise, professional scenario documented for the academic demo.
     * All fixed records use updateOrCreate so the command stays rerunnable.
     */
    private function createAcademicScenario(): void
    {
        $bardo = Quartier::updateOrCreate(
            ['nom' => 'Le Bardo', 'ville' => 'Tunis'],
            [
                'code_postal' => '2000',
                'latitude' => 36.8093000,
                'longitude' => 10.1406000,
                'actif' => true,
            ],
        );

        $admin = User::updateOrCreate(
            ['email' => 'admin@heatalert.test'],
            [
                'name' => 'Nadia Trabelsi',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'quartier_id' => $bardo->id,
                'email_verified_at' => now(),
            ],
        );
        $habitant = User::updateOrCreate(
            ['email' => 'habitant@heatalert.test'],
            [
                'name' => 'Amine Ben Salah',
                'password' => Hash::make('password'),
                'role' => 'habitant',
                'quartier_id' => $bardo->id,
                'email_verified_at' => now(),
            ],
        );
        User::updateOrCreate(
            ['email' => 'agent@heatalert.test'],
            [
                'name' => 'Sana Ben Ali',
                'password' => Hash::make('password'),
                'role' => 'agent',
                'quartier_id' => $bardo->id,
                'email_verified_at' => now(),
            ],
        );

        $refrigerateur = Equipement::updateOrCreate(
            ['user_id' => $habitant->id, 'nom' => 'Réfrigérateur principal'],
            [
                'type' => 'Électroménager',
                'marque' => 'Samsung',
                'sensible_chaleur' => true,
                'sensible_coupure' => true,
                'description' => 'Réfrigérateur familial contenant les aliments frais.',
            ],
        );
        $pc = Equipement::updateOrCreate(
            ['user_id' => $habitant->id, 'nom' => 'PC portable'],
            [
                'type' => 'Informatique',
                'marque' => 'Lenovo',
                'sensible_chaleur' => true,
                'sensible_coupure' => true,
                'description' => 'Ordinateur utilisé pour les études et le travail à domicile.',
            ],
        );

        $conseil = Conseil::updateOrCreate(
            ['titre' => 'Limiter l’ouverture du réfrigérateur pendant une coupure'],
            [
                'description' => 'Conservez le froid en ouvrant le réfrigérateur le moins possible jusqu’au retour de l’électricité.',
                'categorie' => 'Coupure électrique',
                'niveau' => 'Important',
                'actif' => true,
            ],
        );
        $conseil->equipements()->sync([$refrigerateur->id, $pc->id]);

        $alerte = AlerteMeteo::updateOrCreate(
            ['quartier_id' => $bardo->id, 'titre' => 'Vigilance chaleur au Bardo'],
            [
                'description' => 'Une forte chaleur est attendue : hydratez-vous et privilégiez les lieux frais.',
                'temperature' => 38,
                'temperature_min' => 27,
                'temperature_max' => 41,
                'niveau' => 'Alerte',
                'source' => 'manuel',
                'date_debut' => now()->startOfDay(),
                'date_fin' => now()->addDay()->endOfDay(),
                'actif' => true,
            ],
        );

        $coupure = CoupureElectrique::updateOrCreate(
            ['quartier_id' => $bardo->id, 'titre' => 'Maintenance réseau Bardo'],
            [
                'description' => 'Intervention planifiée pour sécuriser le réseau électrique du quartier.',
                'type' => 'maintenance',
                'cause' => 'Maintenance préventive',
                'statut' => 'prévue',
                'date_debut' => now()->addDay()->setTime(9, 0),
                'date_fin_estimee' => now()->addDay()->setTime(12, 0),
                'adresse' => 'Le Bardo, Tunis',
                'latitude' => $bardo->latitude,
                'longitude' => $bardo->longitude,
                'source' => 'admin',
                'actif' => true,
            ],
        );

        Signalement::updateOrCreate(
            ['user_id' => $habitant->id, 'titre' => 'Vérification d’une coupure au Bardo'],
            [
                'coupure_electrique_id' => $coupure->id,
                'quartier_id' => $bardo->id,
                'description' => 'Une interruption de courant a été constatée dans notre immeuble.',
                'adresse' => 'Le Bardo, Tunis',
                'latitude' => $bardo->latitude,
                'longitude' => $bardo->longitude,
                'statut' => 'en_attente',
                'date_signalement' => now()->subMinutes(30),
            ],
        );

        $point = PointFraicheur::updateOrCreate(
            ['quartier_id' => $bardo->id, 'nom' => 'Parc du Bardo'],
            [
                'type' => 'Parc ombragé',
                'adresse' => 'Avenue Habib Bourguiba, Le Bardo',
                'latitude' => 36.8101000,
                'longitude' => 10.1410000,
                'description' => 'Espace ombragé avec bancs et point d’eau.',
                'horaires' => '08:00 - 20:00',
                'telephone' => '71 000 020',
                'climatise' => false,
                'eau_disponible' => true,
                'accessible_pmr' => true,
                'actif' => true,
                'source' => 'manuel',
            ],
        );
        Avis::updateOrCreate(
            ['user_id' => $habitant->id, 'point_fraicheur_id' => $point->id],
            [
                'note' => 5,
                'commentaire' => 'Lieu agréable, ombragé et facilement accessible.',
                'statut' => 'publie',
            ],
        );
    }

    /**
     * @return array<string, Equipement>
     */
    private function createDemoEquipements(User $testUser): array
    {
        $definitions = [
            'climatiseur' => [
                'nom' => 'Climatiseur du salon',
                'type' => 'Climatisation',
                'marque' => 'LG',
                'description' => 'Climatiseur principal utilisé pendant les fortes chaleurs estivales.',
                'sensible_chaleur' => false,
                'sensible_coupure' => true,
                'photo' => 'equipements/demo/climatiseur.jpg',
            ],
            'refrigerateur' => [
                'nom' => 'Réfrigérateur cuisine',
                'type' => 'Électroménager',
                'marque' => 'Whirlpool',
                'description' => 'Réfrigérateur principal contenant les aliments et produits frais.',
                'sensible_chaleur' => true,
                'sensible_coupure' => true,
                'photo' => 'equipements/demo/refrigerateur.jpg',
            ],
            'pc' => [
                'nom' => 'PC portable',
                'type' => 'Informatique',
                'marque' => 'Dell',
                'description' => 'Ordinateur portable utilisé quotidiennement pour le travail et les études.',
                'sensible_chaleur' => true,
                'sensible_coupure' => true,
                'photo' => 'equipements/demo/pc-portable.jpg',
            ],
            'televiseur' => [
                'nom' => 'Téléviseur salon',
                'type' => 'Électroménager',
                'marque' => 'LG',
                'description' => 'Téléviseur principal du foyer.',
                'sensible_chaleur' => true,
                'sensible_coupure' => true,
                'photo' => 'equipements/demo/televiseur.jpg',
            ],
        ];

        $equipements = [];

        foreach ($definitions as $key => $attributes) {
            $equipements[$key] = Equipement::updateOrCreate(
                ['user_id' => $testUser->id, 'nom' => $attributes['nom']],
                $attributes,
            );
        }

        return $equipements;
    }

    /**
     * @param  array<string, Equipement>  $equipements
     */
    private function createDemoConseils(array $equipements): void
    {
        $conseils = [
            [
                'attributes' => [
                    'titre' => 'Limiter les ouvertures du réfrigérateur',
                    'description' => 'Pendant une coupure, évitez d’ouvrir fréquemment le réfrigérateur afin de conserver le froid plus longtemps.',
                    'categorie' => 'Coupure électrique',
                    'niveau' => 'Important',
                    'actif' => true,
                ],
                'equipement' => $equipements['refrigerateur'],
            ],
            [
                'attributes' => [
                    'titre' => 'Contrôler la température après une coupure',
                    'description' => 'Après le retour du courant, contrôlez que les aliments sont restés à une température adaptée.',
                    'categorie' => 'Coupure électrique',
                    'niveau' => 'Important',
                    'actif' => true,
                ],
                'equipement' => $equipements['refrigerateur'],
            ],
            [
                'attributes' => [
                    'titre' => 'Nettoyer régulièrement les filtres',
                    'description' => 'Des filtres propres améliorent le fonctionnement du climatiseur pendant les fortes chaleurs.',
                    'categorie' => 'Prévention',
                    'niveau' => 'Important',
                    'actif' => true,
                ],
                'equipement' => $equipements['climatiseur'],
            ],
            [
                'attributes' => [
                    'titre' => 'Éviter une température excessivement basse',
                    'description' => 'Réglez le climatiseur à une température raisonnable afin de limiter la consommation.',
                    'categorie' => 'Chaleur',
                    'niveau' => 'Information',
                    'actif' => true,
                ],
                'equipement' => $equipements['climatiseur'],
            ],
            [
                'attributes' => [
                    'titre' => 'Éteindre correctement lors d’une coupure',
                    'description' => 'Débranchez l’appareil si l’électricité est instable afin de le protéger au redémarrage.',
                    'categorie' => 'Coupure électrique',
                    'niveau' => 'Important',
                    'actif' => true,
                ],
                'equipement' => $equipements['climatiseur'],
            ],
            [
                'attributes' => [
                    'titre' => 'Éviter la surchauffe du PC',
                    'description' => 'Placez l’ordinateur dans un espace ventilé et évitez l’exposition directe au soleil.',
                    'categorie' => 'Chaleur',
                    'niveau' => 'Important',
                    'actif' => true,
                ],
                'equipement' => $equipements['pc'],
            ],
            [
                'attributes' => [
                    'titre' => 'Vérifier le climatiseur avant les fortes chaleurs',
                    'description' => 'Nettoyez les filtres et vérifiez le fonctionnement du climatiseur avant une période de canicule.',
                    'categorie' => 'Prévention',
                    'niveau' => 'Information',
                    'actif' => true,
                ],
                'equipement' => $equipements['climatiseur'],
            ],
            [
                'attributes' => [
                    'titre' => 'Sauvegarder le travail avant une coupure',
                    'description' => 'Enregistrez régulièrement vos documents et utilisez une batterie chargée si possible.',
                    'categorie' => 'Coupure électrique',
                    'niveau' => 'Important',
                    'actif' => true,
                ],
                'equipement' => $equipements['pc'],
            ],
            [
                'attributes' => [
                    'titre' => 'Débrancher le téléviseur en cas d’instabilité',
                    'description' => 'Protégez le téléviseur des surtensions lors du retour de l’électricité.',
                    'categorie' => 'Coupure électrique',
                    'niveau' => 'Important',
                    'actif' => true,
                ],
                'equipement' => $equipements['televiseur'],
            ],
        ];

        foreach ($conseils as $definition) {
            $conseil = Conseil::updateOrCreate(
                ['titre' => $definition['attributes']['titre']],
                $definition['attributes'],
            );
            $conseil->equipements()->syncWithoutDetaching([$definition['equipement']->id]);
        }
    }

    /** @param array<string, Equipement> $equipements */
    private function createDemoEntretiens(array $equipements): void
    {
        $entretiens = [
            [$equipements['climatiseur'], 'Nettoyage des filtres', 'Maintenance préventive', now()->subMonths(2), now()->subMonths(2)->addDay(), null, 'Filtres nettoyés et contrôlés.'],
            [$equipements['climatiseur'], 'Contrôle avant saison chaude', 'Contrôle', now()->addDays(5), null, 5, 'Vérifier le fonctionnement général avant les fortes chaleurs.'],
            [$equipements['refrigerateur'], 'Nettoyage arrière et ventilation', 'Maintenance préventive', now()->subMonth(), now()->subMonth()->addDay(), null, 'Ventilation nettoyée.'],
            [$equipements['refrigerateur'], 'Contrôle des joints de porte', 'Contrôle', now()->addWeeks(3), null, 5, 'Contrôler l’étanchéité des joints.'],
            [$equipements['pc'], 'Nettoyage ventilation', 'Maintenance préventive', now()->subDays(10), null, 3, 'Entretien à programmer rapidement.'],
            [$equipements['televiseur'], 'Contrôle branchement électrique', 'Contrôle', now()->subMonths(3), now()->subMonths(3)->addDay(), null, 'Branchement et câble contrôlés.'],
        ];

        foreach ($entretiens as [$equipement, $titre, $type, $datePrevue, $dateRealisee, $rappel, $notes]) {
            EntretienEquipement::updateOrCreate(
                ['equipement_id' => $equipement->id, 'titre' => $titre],
                [
                    'type' => $type,
                    'date_prevue' => $datePrevue->toDateString(),
                    'date_realisee' => $dateRealisee?->toDateString(),
                    'rappel_avant_jours' => $rappel,
                    'description' => 'Suivi de démonstration HeatAlert.',
                    'notes' => $notes,
                ],
            );
        }
    }

    private function createDemoCoupure(Quartier $quartier): CoupureElectrique
    {
        return CoupureElectrique::updateOrCreate(
            ['quartier_id' => $quartier->id, 'titre' => 'Maintenance électrique programmée'],
            [
                'description' => 'Intervention programmée sur le réseau électrique du quartier.',
                'type' => 'maintenance',
                'cause' => 'Maintenance préventive du réseau',
                'statut' => 'prévue',
                'date_debut' => now()->addDay()->setTime(9, 0),
                'date_fin_estimee' => now()->addDay()->setTime(12, 0),
                'date_fin_reelle' => null,
                'adresse' => 'Manzah 1, Tunis',
                'latitude' => $quartier->latitude,
                'longitude' => $quartier->longitude,
                'source' => 'admin',
                'actif' => true,
            ],
        );
    }

    private function createDemoSignalements(User $testUser, Quartier $quartier, CoupureElectrique $coupure): void
    {
        $signalements = [
            [
                'titre' => 'Coupure électrique dans la rue',
                'description' => 'Une coupure électrique est signalée dans le quartier depuis quelques minutes.',
                'adresse' => 'Manzah 1, Tunis',
                'latitude' => $quartier->latitude,
                'longitude' => $quartier->longitude,
                'statut' => 'en_attente',
                'date_signalement' => now()->subMinutes(20),
            ],
            [
                'titre' => 'Coupure brève dans l’immeuble',
                'description' => 'Une interruption brève a été constatée dans l’immeuble et nécessite une vérification.',
                'adresse' => 'Manzah 1, Tunis',
                'latitude' => $quartier->latitude,
                'longitude' => $quartier->longitude,
                'statut' => 'validé',
                'date_signalement' => now()->subHour(),
            ],
        ];

        foreach ($signalements as $attributes) {
            Signalement::updateOrCreate(
                ['user_id' => $testUser->id, 'titre' => $attributes['titre']],
                $attributes + [
                    'coupure_electrique_id' => $coupure->id,
                    'quartier_id' => $quartier->id,
                ],
            );
        }
    }

    private function createDemoAvis(User $testUser, Quartier $quartier): void
    {
        $definitions = [
            ['El Menzah 4', 'Centre / espace climatisé', '08:00 - 20:00', true, true, true, 'Climatisation, eau potable, sanitaires, espace de repos', 5, 'Endroit propre, calme et bien climatisé.'],
            ['Zone Verte', 'Espace ombragé', '09:00 - 18:00', false, true, true, 'Eau potable, espace ombragé, bancs', 4, 'Très agréable mais il n’y a pas de climatisation.'],
            ['El Menzah 1', 'Centre / espace climatisé', '10:00 - 22:00', true, false, false, 'Climatisation, espace de repos', 3, 'Bien climatisé mais l’accès est difficile pour une personne à mobilité réduite.'],
        ];
        foreach ($definitions as $index => [$nom,$type,$horaires,$climatise,$eau,$pmr,$services,$note,$commentaire]) {
            $point = PointFraicheur::updateOrCreate(['quartier_id' => $quartier->id, 'nom' => $nom], ['type' => $type, 'adresse' => $nom.', '.$quartier->ville, 'latitude' => (float) $quartier->latitude + ($index + 1) / 10000, 'longitude' => (float) $quartier->longitude + ($index + 1) / 10000, 'horaires' => $horaires, 'description' => $services, 'climatise' => $climatise, 'eau_disponible' => $eau, 'accessible_pmr' => $pmr, 'actif' => true, 'source' => 'manuel']);
            Avis::updateOrCreate(['user_id' => $testUser->id, 'point_fraicheur_id' => $point->id], ['note' => $note, 'commentaire' => $commentaire, 'statut' => 'publie']);

            if ($nom === 'El Menzah 4') {
                $avisGroq = [
                    [5, 'Endroit propre et bien climatisé.'],
                    [5, 'Très accessible pour une personne à mobilité réduite.'],
                    [4, 'Le personnel est accueillant et l’endroit est agréable.'],
                    [3, 'Beaucoup de monde l’après-midi.'],
                    [2, 'L’eau était indisponible lors de ma visite.'],
                    [3, 'Les horaires affichés ne correspondaient pas exactement.'],
                ];

                foreach ($avisGroq as $avisIndex => [$noteGroq, $commentaireGroq]) {
                    $user = User::updateOrCreate(
                        ['email' => 'demo-avis-'.($avisIndex + 1).'@heatalert.test'],
                        ['name' => 'Avis démo '.($avisIndex + 1), 'password' => Hash::make('demo-password'), 'role' => 'habitant', 'quartier_id' => $quartier->id],
                    );
                    Avis::updateOrCreate(
                        ['user_id' => $user->id, 'point_fraicheur_id' => $point->id],
                        ['note' => $noteGroq, 'commentaire' => $commentaireGroq, 'statut' => 'publie'],
                    );
                }
            }
        }
    }

    private function createDemoAlerte(Quartier $quartier): void
    {
        AlerteMeteo::updateOrCreate(
            ['quartier_id' => $quartier->id, 'titre' => 'Alerte chaleur à Manzah 1'],
            [
                'description' => 'Températures élevées attendues : hydratez-vous et évitez les sorties aux heures les plus chaudes.',
                'temperature' => 39,
                'temperature_max' => 41,
                'temperature_min' => 28,
                'niveau' => 'Alerte',
                'source' => 'open-meteo',
                'date_debut' => now()->startOfDay(),
                'date_fin' => now()->endOfDay(),
                'actif' => true,
            ],
        );
    }

    private function ensureDemoStructures(Quartier $quartier): void
    {
        $structures = [
            [
                'nom' => 'Centre de santé Manzah 1',
                'type' => 'Centre de santé',
                'adresse' => 'Manzah 1, Tunis',
                'telephone' => '71 000 000',
                'description' => 'Information, orientation et soins de proximité.',
                'horaires' => '08:00 - 17:00',
                'services' => 'Consultation, orientation et soins de proximité.',
                'accueil_public' => true,
                'actif' => true,
            ],
            [
                'nom' => 'Maison de retraite Manzah 1',
                'type' => 'Maison de retraite',
                'adresse' => 'Manzah 1, Tunis',
                'telephone' => '71 000 001',
                'description' => 'Accompagnement des personnes âgées.',
                'horaires' => '08:00 - 17:00',
                'services' => 'Accompagnement et information des personnes âgées.',
                'accueil_public' => false,
                'actif' => true,
            ],
        ];

        foreach ($structures as $attributes) {
            EtablissementSensible::updateOrCreate(
                ['quartier_id' => $quartier->id, 'nom' => $attributes['nom']],
                $attributes,
            );
        }
    }

    private function createDemoDiscussions(User $testUser, User $admin, Quartier $quartier): void
    {
        $discussion = PublicationQuartier::updateOrCreate(
            ['quartier_id' => $quartier->id, 'titre' => 'Coupure électrique depuis 14h près de Manzah 1'],
            [
                'user_id' => $testUser->id,
                'contenu' => 'Nous n’avons plus d’électricité depuis environ 14h dans notre immeuble.',
                'type' => 'probleme',
                'statut' => 'en_cours',
                'resolved_at' => null,
            ],
        );

        $commentators = [];
        foreach ([
            'Même problème dans notre rue.',
            'Toujours coupé chez moi.',
            'Le courant est revenu vers 17h dans notre immeuble.',
            'Chez nous ce n’est pas encore revenu.',
            'Le problème concerne plusieurs bâtiments de la zone.',
        ] as $index => $contenu) {
            $commentators[] = User::updateOrCreate(
                ['email' => 'demo-discussion-'.($index + 1).'@heatalert.test'],
                ['name' => 'Habitant démo '.($index + 1), 'password' => Hash::make('demo-password'), 'role' => 'habitant', 'quartier_id' => $quartier->id],
            );
            $discussion->commentaires()->updateOrCreate(
                ['user_id' => $commentators[$index]->id],
                ['contenu' => $contenu],
            );
        }
        $discussion->confirmations()->syncWithoutDetaching(collect($commentators)->take(3)->pluck('id')->all());

        foreach ([
            ['Informations utiles ce soir', 'Le centre communautaire reste ouvert jusqu’à 20h pour les habitants qui souhaitent se rafraîchir.', 'information', 'signale', null],
            ['Besoin de glace pour conserver des médicaments', 'Un voisin peut-il aider une personne âgée à conserver des médicaments au frais ?', 'entraide', 'signale', null],
            ['Éclairage rétabli dans la rue principale', 'L’éclairage public fonctionne de nouveau depuis ce matin.', 'information', 'resolu', now()->subDay()],
        ] as [$titre, $contenu, $type, $statut, $resolvedAt]) {
            PublicationQuartier::updateOrCreate(
                ['quartier_id' => $quartier->id, 'titre' => $titre],
                ['user_id' => $admin->id, 'contenu' => $contenu, 'type' => $type, 'statut' => $statut, 'resolved_at' => $resolvedAt],
            );
        }
    }
}
