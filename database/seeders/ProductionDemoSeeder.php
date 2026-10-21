<?php

namespace Database\Seeders;

use App\Models\AlerteMeteo;
use App\Models\Avis;
use App\Models\CommentairePublication;
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
use RuntimeException;

class ProductionDemoSeeder extends Seeder
{
    private const LEGACY_PRIMARY_USER_EMAIL = 'habitant-demo-1@heatalert.demo';

    public function run(): void
    {
        $credentials = $this->demoCredentials();

        DB::transaction(function () use ($credentials): void {
            $quartiers = $this->seedQuartiers();
            $users = $this->seedUsers($quartiers, $credentials);
            $equipements = $this->seedEquipements($users);

            $this->seedConseils($equipements);
            $this->seedEntretiens($equipements);

            $coupures = $this->seedCoupures($quartiers);
            $this->seedAlertes($quartiers);
            $this->seedSignalements($users, $quartiers, $coupures);

            $points = $this->seedPointsFraicheur($quartiers);
            $this->seedAvis($users, $points);
            $this->seedEtablissementsSensibles($quartiers);
            $this->seedPublications($users, $quartiers);
            $this->seedPrimaryHabitantScenario($users, $quartiers['manzah_1']);
        });
    }

    /**
     * @return array{admin_email: string, admin_password: string, agent_password: string, user_password: string, primary_user_email: string}
     */
    private function demoCredentials(): array
    {
        return [
            'admin_email' => $this->requiredConfigurationValue('demo.admin_email', 'DEMO_ADMIN_EMAIL'),
            'admin_password' => $this->requiredConfigurationValue('demo.admin_password', 'DEMO_ADMIN_PASSWORD'),
            'agent_password' => $this->requiredConfigurationValue('demo.agent_password', 'DEMO_AGENT_PASSWORD'),
            'user_password' => $this->requiredConfigurationValue('demo.user_password', 'DEMO_USER_PASSWORD'),
            'primary_user_email' => $this->requiredConfigurationValue('demo.primary_user_email', 'DEMO_PRIMARY_USER_EMAIL'),
        ];
    }

    private function requiredConfigurationValue(string $key, string $environmentName): string
    {
        $value = config($key);

        if (! is_string($value) || trim($value) === '') {
            throw new RuntimeException("La variable d'environnement {$environmentName} est obligatoire pour ProductionDemoSeeder.");
        }

        return $value;
    }

    /** @return array<string, Quartier> */
    private function seedQuartiers(): array
    {
        $definitions = [
            'manzah_1' => ['Manzah 1', 'Tunis', '2091', 36.8270000, 10.1800000],
            'manzah_4' => ['El Menzah 4', 'Tunis', '2091', 36.8357000, 10.1657000],
            'ennasr_2' => ['Ennasr 2', 'Ariana', '2037', 36.8625000, 10.1705000],
            'bardo' => ['Le Bardo', 'Tunis', '2000', 36.8093000, 10.1406000],
            'marsa' => ['La Marsa', 'Tunis', '2078', 36.8782000, 10.3248000],
            'carthage' => ['Carthage', 'Tunis', '2016', 36.8527000, 10.3233000],
            'kram' => ['Le Kram', 'Tunis', '2015', 36.8374000, 10.3143000],
            'lac_1' => ['Les Berges du Lac 1', 'Tunis', '1053', 36.8351000, 10.2313000],
            'ariana' => ['Ariana Centre', 'Ariana', '2080', 36.8625000, 10.1956000],
            'bab_souika' => ['Bab Souika', 'Tunis', '1006', 36.8101000, 10.1712000],
        ];

        $quartiers = [];

        foreach ($definitions as $key => [$nom, $ville, $codePostal, $latitude, $longitude]) {
            $quartiers[$key] = Quartier::updateOrCreate(
                ['nom' => $nom, 'ville' => $ville],
                [
                    'code_postal' => $codePostal,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'actif' => true,
                ],
            );
        }

        return $quartiers;
    }

    /**
     * @param  array<string, Quartier>  $quartiers
     * @param  array{admin_email: string, admin_password: string, agent_password: string, user_password: string, primary_user_email: string}  $credentials
     * @return array<string, User>
     */
    private function seedUsers(array $quartiers, array $credentials): array
    {
        $users = [];
        $quartierKeys = array_keys($quartiers);

        $users['admin'] = User::updateOrCreate(
            ['email' => $credentials['admin_email']],
            [
                'name' => 'Administration HeatAlert Démo',
                'password' => Hash::make($credentials['admin_password']),
                'role' => 'admin',
                'quartier_id' => $quartiers['manzah_1']->id,
                'email_verified_at' => now(),
            ],
        );

        foreach (['Amel Gharbi', 'Sami Trabelsi', 'Yasmine Ben Salem'] as $index => $name) {
            $key = 'agent_'.($index + 1);
            $users[$key] = User::updateOrCreate(
                ['email' => 'agent-demo-'.($index + 1).'@heatalert.demo'],
                [
                    'name' => $name,
                    'password' => Hash::make($credentials['agent_password']),
                    'role' => 'agent',
                    'quartier_id' => $quartiers[$quartierKeys[($index + 2) % count($quartierKeys)]]->id,
                    'email_verified_at' => now(),
                ],
            );
        }

        $inhabitants = [
            'Houssem Chérif', 'Meriem Ben Amor', 'Nour Ben Youssef', 'Ahmed Jlassi',
            'Sarra Dridi', 'Walid Kefi', 'Inès Ayadi', 'Rami Fakhfakh',
            'Leïla Ghedira', 'Karim Baccouche', 'Sonia Mzali', 'Fares Hachicha',
            'Amina Kallel', 'Mourad Jaziri', 'Rania Bensalah', 'Youssef Chouchane',
            'Nadia Ben Saïd', 'Mehdi Chaabane', 'Sabrine Khélifi', 'Hatem Ktari',
            'Sana Khemiri', 'Omar Ben Hmida', 'Maha Bouaziz', 'Tarek Messaoud',
        ];

        foreach ($inhabitants as $index => $name) {
            $key = 'habitant_'.($index + 1);

            if ($index === 0) {
                $users[$key] = $this->resolvePrimaryHabitant(
                    $name,
                    $quartiers[$quartierKeys[$index % count($quartierKeys)]],
                    $credentials,
                );

                continue;
            }

            $users[$key] = User::updateOrCreate(
                ['email' => 'habitant-demo-'.($index + 1).'@heatalert.demo'],
                [
                    'name' => $name,
                    'password' => Hash::make($credentials['user_password']),
                    'role' => 'habitant',
                    'quartier_id' => $quartiers[$quartierKeys[$index % count($quartierKeys)]]->id,
                    'email_verified_at' => now(),
                ],
            );
        }

        return $users;
    }

    /**
     * Finds the configured primary user before migrating the historical account.
     *
     * @param  array{admin_email: string, admin_password: string, agent_password: string, user_password: string, primary_user_email: string}  $credentials
     */
    private function resolvePrimaryHabitant(string $name, Quartier $quartier, array $credentials): User
    {
        $primaryUser = User::query()
            ->where('email', $credentials['primary_user_email'])
            ->first();

        if (! $primaryUser instanceof User) {
            $primaryUser = User::query()
                ->where('email', self::LEGACY_PRIMARY_USER_EMAIL)
                ->first();
        }

        if ($primaryUser instanceof User) {
            $primaryUser->forceFill([
                'email' => $credentials['primary_user_email'],
                'password' => Hash::make($credentials['user_password']),
            ])->save();

            return $primaryUser;
        }

        return User::create([
            'name' => $name,
            'email' => $credentials['primary_user_email'],
            'password' => Hash::make($credentials['user_password']),
            'role' => 'habitant',
            'quartier_id' => $quartier->id,
            'email_verified_at' => now(),
        ]);
    }

    /**
     * @param  array<string, User>  $users
     * @return array<int, Equipement>
     */
    private function seedEquipements(array $users): array
    {
        $definitions = [
            ['Climatiseur salon', 'Climatisation', 'LG', true, true],
            ['Réfrigérateur familial', 'Électroménager', 'Samsung', true, true],
            ['PC portable', 'Informatique', 'Lenovo', true, true],
            ['Ventilateur sur pied', 'Ventilation', 'Moulinex', true, false],
            ['Congélateur', 'Électroménager', 'Beko', true, true],
            ['Téléviseur principal', 'Électronique', 'Hisense', false, true],
            ['Routeur internet', 'Informatique', 'TP-Link', false, true],
            ['Climatiseur chambre', 'Climatisation', 'Toshiba', true, true],
            ['Pompe à eau', 'Équipement domestique', 'Pedrollo', false, true],
            ['Micro-ondes', 'Électroménager', 'Sharp', true, true],
            ['Ordinateur de bureau', 'Informatique', 'HP', true, true],
            ['Purificateur d’air', 'Santé', 'Xiaomi', true, false],
            ['Réfrigérateur médicaments', 'Santé', 'Haier', true, true],
            ['Ventilateur plafond', 'Ventilation', 'Soler & Palau', true, false],
            ['Box internet', 'Informatique', 'Huawei', false, true],
            ['Lave-linge', 'Électroménager', 'Whirlpool', true, true],
            ['Écran de travail', 'Informatique', 'Dell', true, true],
            ['Climatiseur bureau', 'Climatisation', 'Gree', true, true],
            ['Machine à café', 'Électroménager', 'DeLonghi', true, true],
            ['Batterie de secours', 'Énergie', 'APC', false, true],
        ];

        $equipements = [];
        $primaryHabitantEquipmentNames = [
            'Climatiseur salon',
            'Réfrigérateur familial',
            'PC portable',
            'Ventilateur sur pied',
            'Box internet',
            'Batterie de secours',
        ];

        foreach ($definitions as $index => [$nom, $type, $marque, $sensibleChaleur, $sensibleCoupure]) {
            $owner = in_array($nom, $primaryHabitantEquipmentNames, true)
                ? $users['habitant_1']
                : $users['habitant_'.(($index % 20) + 1)];
            $equipements[] = Equipement::updateOrCreate(
                ['user_id' => $owner->id, 'nom' => $nom],
                [
                    'type' => $type,
                    'marque' => $marque,
                    'sensible_chaleur' => $sensibleChaleur,
                    'sensible_coupure' => $sensibleCoupure,
                    'description' => "Équipement de démonstration suivi par HeatAlert pour {$owner->name}.",
                ],
            );
        }

        return $equipements;
    }

    /** @param array<int, Equipement> $equipements */
    private function seedConseils(array $equipements): void
    {
        $definitions = [
            ['Préparer le climatiseur avant une canicule', 'Chaleur', 'Important'],
            ['Nettoyer les filtres avant l’été', 'Prévention', 'Information'],
            ['Fermer les volets aux heures chaudes', 'Chaleur', 'Information'],
            ['Conserver le froid du réfrigérateur', 'Coupure électrique', 'Important'],
            ['Éviter les ouvertures répétées du congélateur', 'Coupure électrique', 'Important'],
            ['Débrancher les appareils sensibles', 'Coupure électrique', 'Urgent'],
            ['Utiliser un parasurtenseur', 'Coupure électrique', 'Important'],
            ['Sauvegarder son travail régulièrement', 'Coupure électrique', 'Information'],
            ['Aérer le logement tôt le matin', 'Chaleur', 'Information'],
            ['Boire de l’eau régulièrement', 'Chaleur', 'Urgent'],
            ['Prévoir une solution de secours internet', 'Coupure électrique', 'Information'],
            ['Surveiller les médicaments au frais', 'Santé', 'Urgent'],
            ['Ne pas surcharger les multiprises', 'Prévention', 'Important'],
            ['Vérifier les joints du réfrigérateur', 'Prévention', 'Information'],
            ['Nettoyer les grilles de ventilation', 'Prévention', 'Information'],
            ['Limiter les usages énergivores', 'Coupure électrique', 'Information'],
            ['Éteindre les écrans inutilisés', 'Prévention', 'Information'],
            ['Préserver la batterie de secours', 'Coupure électrique', 'Important'],
            ['Protéger les équipements de l’humidité', 'Prévention', 'Information'],
            ['Anticiper une maintenance annuelle', 'Prévention', 'Important'],
            ['Vérifier la circulation d’air', 'Chaleur', 'Information'],
            ['Éviter l’exposition directe au soleil', 'Chaleur', 'Important'],
            ['Tester les alarmes de sécurité', 'Prévention', 'Important'],
            ['Planifier les entretiens nécessaires', 'Prévention', 'Information'],
            ['Consulter les alertes locales HeatAlert', 'Prévention', 'Important'],
        ];

        foreach ($definitions as $index => [$titre, $categorie, $niveau]) {
            $conseil = Conseil::updateOrCreate(
                ['titre' => $titre],
                [
                    'description' => "Conseil de démonstration : {$titre} afin de limiter les risques pour vos équipements.",
                    'categorie' => $categorie,
                    'niveau' => $niveau,
                    'actif' => $index !== 24,
                ],
            );
            $conseil->equipements()->syncWithoutDetaching([$equipements[$index % count($equipements)]->id]);
        }
    }

    /** @param array<int, Equipement> $equipements */
    private function seedEntretiens(array $equipements): void
    {
        foreach ($equipements as $index => $equipement) {
            $datePrevue = match ($index % 4) {
                0 => now()->subDays($index + 2),
                1 => now()->addDays($index + 1),
                2 => now()->addWeeks(2),
                default => now()->subMonths(2),
            };
            $dateRealisee = $index % 4 === 3 ? now()->subMonths(2)->addDay() : null;

            EntretienEquipement::updateOrCreate(
                ['equipement_id' => $equipement->id, 'titre' => 'Entretien démonstration '.($index + 1)],
                [
                    'type' => $index % 2 === 0 ? 'Maintenance préventive' : 'Contrôle',
                    'description' => "Contrôle périodique de {$equipement->nom}.",
                    'date_prevue' => $datePrevue->toDateString(),
                    'date_realisee' => $dateRealisee?->toDateString(),
                    'rappel_avant_jours' => $index % 4 === 2 ? 14 : 7,
                    'notes' => $dateRealisee ? 'Entretien effectué et validé.' : 'Entretien à suivre dans HeatAlert.',
                ],
            );
        }
    }

    /**
     * @param  array<string, Quartier>  $quartiers
     * @return array<int, CoupureElectrique>
     */
    private function seedCoupures(array $quartiers): array
    {
        $quartierKeys = array_keys($quartiers);
        $statuts = ['terminée', 'prévue', 'en_cours'];
        $coupures = [];

        for ($index = 0; $index < 15; $index++) {
            $quartier = $quartiers[$quartierKeys[$index % count($quartierKeys)]];
            $statut = $statuts[$index % count($statuts)];
            $dateDebut = match ($statut) {
                'terminée' => now()->subDays($index + 1)->setTime(9, 0),
                'prévue' => now()->addDays($index + 1)->setTime(9, 0),
                default => now()->subHours($index + 1),
            };

            $coupures[] = CoupureElectrique::updateOrCreate(
                ['quartier_id' => $quartier->id, 'titre' => 'Intervention réseau '.($index + 1).' — '.$quartier->nom],
                [
                    'description' => "Intervention de démonstration sur le réseau électrique de {$quartier->nom}.",
                    'type' => $index % 2 === 0 ? 'maintenance' : 'panne',
                    'cause' => $index % 2 === 0 ? 'Maintenance préventive planifiée' : 'Incident réseau localisé',
                    'statut' => $statut,
                    'date_debut' => $dateDebut,
                    'date_fin_estimee' => $dateDebut->copy()->addHours(3),
                    'date_fin_reelle' => $statut === 'terminée' ? $dateDebut->copy()->addHours(2) : null,
                    'adresse' => "{$quartier->nom}, {$quartier->ville}",
                    'latitude' => $quartier->latitude,
                    'longitude' => $quartier->longitude,
                    'source' => $index % 3 === 0 ? 'agent' : 'admin',
                    'actif' => $statut !== 'terminée' || $index % 2 === 0,
                ],
            );
        }

        return $coupures;
    }

    /** @param array<string, Quartier> $quartiers */
    private function seedAlertes(array $quartiers): void
    {
        $quartierKeys = array_keys($quartiers);
        $niveaux = ['Normal', 'Vigilance', 'Alerte', 'Critique', 'Danger'];

        for ($index = 0; $index < 15; $index++) {
            $quartier = $quartiers[$quartierKeys[$index % count($quartierKeys)]];
            $start = now()->addDays($index - 7)->startOfDay();

            AlerteMeteo::updateOrCreate(
                ['quartier_id' => $quartier->id, 'titre' => 'Surveillance météo '.($index + 1).' — '.$quartier->nom],
                [
                    'description' => "Alerte de démonstration pour informer les habitants de {$quartier->nom}.",
                    'temperature' => 28 + ($index % 10),
                    'temperature_min' => 20 + ($index % 6),
                    'temperature_max' => 33 + ($index % 10),
                    'niveau' => $niveaux[$index % count($niveaux)],
                    'source' => $index % 2 === 0 ? 'open-meteo' : 'manuel',
                    'date_debut' => $start,
                    'date_fin' => $start->copy()->addDays(2)->endOfDay(),
                    'actif' => $index >= 7,
                ],
            );
        }
    }

    /**
     * @param  array<string, User>  $users
     * @param  array<string, Quartier>  $quartiers
     * @param  array<int, CoupureElectrique>  $coupures
     */
    private function seedSignalements(array $users, array $quartiers, array $coupures): void
    {
        $statuses = ['en_attente', 'validé', 'rejeté', 'résolu'];
        $quartiersById = [];

        foreach ($quartiers as $quartier) {
            $quartiersById[$quartier->id] = $quartier;
        }

        for ($index = 0; $index < 30; $index++) {
            $user = $users['habitant_'.(($index % 24) + 1)];
            $coupure = $coupures[$index % count($coupures)];
            $quartier = $quartiersById[$coupure->quartier_id];

            Signalement::updateOrCreate(
                ['user_id' => $user->id, 'titre' => 'Signalement réseau '.($index + 1)],
                [
                    'coupure_electrique_id' => $coupure->id,
                    'quartier_id' => $quartier->id,
                    'description' => "L'habitant signale une situation liée à {$coupure->titre}.",
                    'adresse' => "{$quartier->nom}, {$quartier->ville}",
                    'latitude' => $quartier->latitude,
                    'longitude' => $quartier->longitude,
                    'statut' => $statuses[$index % count($statuses)],
                    'date_signalement' => now()->subHours($index * 3),
                ],
            );
        }
    }

    /**
     * @param  array<string, Quartier>  $quartiers
     * @return array<int, PointFraicheur>
     */
    private function seedPointsFraicheur(array $quartiers): array
    {
        $definitions = [
            ['Centre municipal Manzah 1', 'Centre climatisé', 'manzah_1'], ['Bibliothèque El Menzah', 'Bibliothèque', 'manzah_1'],
            ['Maison de jeunes El Menzah 4', 'Centre climatisé', 'manzah_4'], ['Parc El Menzah 4', 'Parc ombragé', 'manzah_4'],
            ['Centre culturel Ennasr', 'Centre culturel', 'ennasr_2'], ['Jardin Ennasr', 'Parc ombragé', 'ennasr_2'],
            ['Médiathèque du Bardo', 'Bibliothèque', 'bardo'], ['Parc du Bardo', 'Parc ombragé', 'bardo'],
            ['Maison de la culture La Marsa', 'Centre culturel', 'marsa'], ['Jardin Safsaf', 'Parc ombragé', 'marsa'],
            ['Centre de quartier Carthage', 'Centre climatisé', 'carthage'], ['Parc archéologique Carthage', 'Parc ombragé', 'carthage'],
            ['Maison des associations Le Kram', 'Centre climatisé', 'kram'], ['Jardin public du Kram', 'Parc ombragé', 'kram'],
            ['Espace citoyen Lac 1', 'Centre climatisé', 'lac_1'], ['Parc du Lac 1', 'Parc ombragé', 'lac_1'],
            ['Bibliothèque Ariana Centre', 'Bibliothèque', 'ariana'], ['Jardin de Bab Souika', 'Parc ombragé', 'bab_souika'],
        ];

        $points = [];

        foreach ($definitions as $index => [$nom, $type, $quartierKey]) {
            $quartier = $quartiers[$quartierKey];
            $points[] = PointFraicheur::updateOrCreate(
                ['quartier_id' => $quartier->id, 'nom' => $nom],
                [
                    'type' => $type,
                    'adresse' => "{$nom}, {$quartier->ville}",
                    'latitude' => (float) $quartier->latitude + (($index % 3) * 0.0004),
                    'longitude' => (float) $quartier->longitude + (($index % 3) * 0.0004),
                    'description' => "Lieu de fraîcheur accessible aux habitants de {$quartier->nom}.",
                    'horaires' => $index % 2 === 0 ? '08:00 - 20:00' : '09:00 - 18:00',
                    'telephone' => '71 '.str_pad((string) (110000 + $index), 6, '0', STR_PAD_LEFT),
                    'climatise' => $index % 2 === 0,
                    'eau_disponible' => $index % 3 !== 0,
                    'accessible_pmr' => $index % 4 !== 0,
                    'actif' => $index !== 17,
                    'source' => 'manuel',
                ],
            );
        }

        return $points;
    }

    /**
     * @param  array<string, User>  $users
     * @param  array<int, PointFraicheur>  $points
     */
    private function seedAvis(array $users, array $points): void
    {
        $comments = [
            'Endroit propre et bien climatisé.', 'Très accessible pour les personnes à mobilité réduite.',
            'Personnel accueillant et lieu agréable.', 'Beaucoup de monde l’après-midi.',
            'L’eau était indisponible lors de ma visite.', 'Les horaires affichés ne correspondaient pas exactement.',
        ];

        for ($index = 0; $index < 36; $index++) {
            $userNumber = $index < 24 ? $index + 1 : $index - 23;
            $pointIndex = $index < 24 ? $index % count($points) : ($index + 5) % count($points);

            Avis::updateOrCreate(
                ['user_id' => $users['habitant_'.$userNumber]->id, 'point_fraicheur_id' => $points[$pointIndex]->id],
                [
                    'note' => 2 + ($index % 4),
                    'commentaire' => $comments[$index % count($comments)],
                    'statut' => $index % 6 === 0 ? 'en_attente' : 'publie',
                ],
            );
        }
    }

    /** @param array<string, Quartier> $quartiers */
    private function seedEtablissementsSensibles(array $quartiers): void
    {
        $definitions = [
            ['Dispensaire El Menzah', 'Centre de santé', 'manzah_1', true], ['Pharmacie de garde El Menzah 4', 'Pharmacie', 'manzah_4', true],
            ['Centre de santé Ennasr', 'Centre de santé', 'ennasr_2', true], ['Hôpital du Bardo', 'Hôpital', 'bardo', true],
            ['Clinique La Marsa', 'Clinique', 'marsa', true], ['Poste de protection civile Carthage', 'Protection civile', 'carthage', false],
            ['Croissant-Rouge Le Kram', 'Association', 'kram', true], ['Centre d’écoute Lac 1', 'Centre social', 'lac_1', true],
            ['Maison des aînés Ariana', 'Centre social', 'ariana', true], ['Centre communal Bab Souika', 'Centre social', 'bab_souika', false],
            ['Service municipal Manzah 1', 'Service public', 'manzah_1', false], ['Association solidarité Bardo', 'Association', 'bardo', true],
        ];

        foreach ($definitions as $index => [$nom, $type, $quartierKey, $accueilPublic]) {
            $quartier = $quartiers[$quartierKey];

            EtablissementSensible::updateOrCreate(
                ['quartier_id' => $quartier->id, 'nom' => $nom],
                [
                    'type' => $type,
                    'adresse' => "{$nom}, {$quartier->ville}",
                    'telephone' => '71 '.(220000 + $index),
                    'description' => "Structure de démonstration référencée pour les habitants de {$quartier->nom}.",
                    'horaires' => $accueilPublic ? '08:00 - 18:00' : 'Sur rendez-vous',
                    'services' => $accueilPublic ? 'Information, orientation et assistance de proximité.' : 'Information et coordination des services locaux.',
                    'accueil_public' => $accueilPublic,
                    'actif' => true,
                ],
            );
        }
    }

    /**
     * @param  array<string, User>  $users
     * @param  array<string, Quartier>  $quartiers
     */
    private function seedPublications(array $users, array $quartiers): void
    {
        $quartierKeys = array_keys($quartiers);
        $definitions = [
            ['Information fraîcheur du jour', 'Le centre municipal reste ouvert cet après-midi pour les personnes fragiles.', 'information', 'signale'],
            ['Besoin de glace pour un voisin', 'Une personne âgée cherche de la glace pour conserver ses médicaments.', 'entraide', 'en_cours'],
            ['Coupure signalée dans le secteur', 'Une interruption électrique est signalée, partagez vos informations utiles.', 'probleme', 'en_cours'],
            ['Éclairage public rétabli', 'L’éclairage fonctionne de nouveau dans la rue principale.', 'information', 'resolu'],
            ['Alerte chaleur locale', 'Pensez à vous hydrater et à limiter les sorties aux heures les plus chaudes.', 'alerte', 'signale'],
            ['Recherche d’aide pour un déplacement', 'Une famille cherche un accompagnement vers un point de fraîcheur.', 'entraide', 'signale'],
            ['Rue temporairement fermée', 'Une intervention réseau nécessite de limiter la circulation dans la rue.', 'information', 'en_cours'],
            ['Distribution d’eau annoncée', 'Une distribution d’eau est organisée devant le centre de quartier.', 'information', 'signale'],
            ['Ventilateur à prêter', 'Un habitant propose un ventilateur en bon état pour quelques jours.', 'entraide', 'resolu'],
            ['Température élevée attendue', 'La vigilance est maintenue demain, prenez des nouvelles des personnes isolées.', 'alerte', 'signale'],
            ['Panne d’ascenseur signalée', 'L’ascenseur de l’immeuble est indisponible pendant l’intervention électrique.', 'probleme', 'en_cours'],
            ['Merci aux bénévoles', 'Les habitants remercient les bénévoles présents pendant la dernière coupure.', 'information', 'resolu'],
        ];

        foreach ($definitions as $index => [$titre, $contenu, $type, $statut]) {
            $quartier = $quartiers[$quartierKeys[$index % count($quartierKeys)]];
            $author = $index % 3 === 0 ? $users['admin'] : $users['agent_'.(($index % 3) + 1)];
            $publication = PublicationQuartier::updateOrCreate(
                ['quartier_id' => $quartier->id, 'titre' => $titre],
                [
                    'user_id' => $author->id,
                    'contenu' => $contenu,
                    'type' => $type,
                    'statut' => $statut,
                    'resolved_at' => $statut === 'resolu' ? now()->subDays($index + 1) : null,
                ],
            );

            for ($commentIndex = 0; $commentIndex < 2; $commentIndex++) {
                $commentator = $users['habitant_'.((($index * 2) + $commentIndex) % 24 + 1)];
                CommentairePublication::updateOrCreate(
                    ['publication_id' => $publication->id, 'user_id' => $commentator->id],
                    ['contenu' => $commentIndex === 0 ? 'Merci pour cette information utile.' : 'Je confirme la situation dans notre quartier.'],
                );
            }

            $publication->confirmations()->syncWithoutDetaching([
                $users['habitant_'.(($index % 24) + 1)]->id,
                $users['habitant_'.((($index + 5) % 24) + 1)]->id,
            ]);
        }
    }

    /**
     * Guarantees a complete, coherent scenario for habitant-demo-1@heatalert.demo.
     *
     * @param  array<string, User>  $users
     */
    private function seedPrimaryHabitantScenario(array $users, Quartier $quartier): void
    {
        $primaryHabitant = $users['habitant_1'];
        $equipements = Equipement::query()
            ->where('user_id', $primaryHabitant->id)
            ->orderBy('id')
            ->get();

        $conseilTitles = [
            'Préparer le climatiseur avant une canicule',
            'Nettoyer les filtres avant l’été',
            'Fermer les volets aux heures chaudes',
            'Conserver le froid du réfrigérateur',
            'Débrancher les appareils sensibles',
            'Utiliser un parasurtenseur',
            'Sauvegarder son travail régulièrement',
            'Prévoir une solution de secours internet',
            'Préserver la batterie de secours',
            'Anticiper une maintenance annuelle',
        ];

        foreach ($conseilTitles as $index => $title) {
            $conseil = Conseil::where('titre', $title)->firstOrFail();
            $conseil->equipements()->syncWithoutDetaching([$equipements[$index % $equipements->count()]->id]);
        }

        $coupures = CoupureElectrique::query()
            ->where('quartier_id', $quartier->id)
            ->orderBy('id')
            ->get();

        foreach ([
            ['Coupure constatée dans mon immeuble', 'en_attente', 'Le courant est instable depuis ce matin dans notre immeuble.'],
            ['Réfrigérateur à protéger pendant la coupure', 'validé', 'Je demande une vérification car la coupure met les aliments au frais en danger.'],
            ['Électricité rétablie dans la résidence', 'résolu', 'Le courant est revenu après intervention, la situation est maintenant résolue.'],
            ['Signalement doublon à vérifier', 'rejeté', 'Cette information a finalement été confirmée comme un doublon.'],
            ['Panne encore active près du centre municipal', 'en_attente', 'Plusieurs voisins signalent que la panne est toujours active.'],
        ] as $index => [$titre, $statut, $description]) {
            $coupure = $coupures[$index % $coupures->count()];

            Signalement::updateOrCreate(
                ['user_id' => $primaryHabitant->id, 'titre' => $titre],
                [
                    'coupure_electrique_id' => $coupure->id,
                    'quartier_id' => $quartier->id,
                    'description' => $description,
                    'adresse' => "{$quartier->nom}, {$quartier->ville}",
                    'latitude' => $quartier->latitude,
                    'longitude' => $quartier->longitude,
                    'statut' => $statut,
                    'date_signalement' => now()->subHours($index + 1),
                ],
            );
        }

        foreach ([
            ['Centre municipal Manzah 1', 5, 'Espace bien climatisé et très utile pendant les fortes chaleurs.'],
            ['Bibliothèque El Menzah', 4, 'Lieu calme, accessible et agréable pour travailler au frais.'],
            ['Maison de jeunes El Menzah 4', 4, 'Personnel accueillant et espaces propres.'],
            ['Parc du Bardo', 3, 'L’ombre est appréciable mais il y avait beaucoup de monde.'],
            ['Maison de la culture La Marsa', 5, 'Très bon accueil et informations claires sur les services disponibles.'],
            ['Espace citoyen Lac 1', 4, 'Point de fraîcheur pratique et bien indiqué.'],
        ] as [$pointName, $note, $commentaire]) {
            $point = PointFraicheur::where('nom', $pointName)->firstOrFail();

            Avis::updateOrCreate(
                ['user_id' => $primaryHabitant->id, 'point_fraicheur_id' => $point->id],
                ['note' => $note, 'commentaire' => $commentaire, 'statut' => 'publie'],
            );
        }

        $communityPublication = PublicationQuartier::updateOrCreate(
            ['quartier_id' => $quartier->id, 'titre' => 'Permanence d’information à Manzah 1'],
            [
                'user_id' => $users['agent_1']->id,
                'contenu' => 'Une permanence est disponible au centre municipal pour orienter les habitants pendant les fortes chaleurs.',
                'type' => 'information',
                'statut' => 'signale',
                'resolved_at' => null,
            ],
        );

        foreach ([
            ['Besoin de conseils pour préserver les aliments', 'Je cherche des recommandations pour conserver les aliments pendant la coupure.', 'entraide', 'signale'],
            ['Point de fraîcheur recommandé aux voisins', 'Le centre municipal est accessible et reste une bonne solution pour les personnes fragiles.', 'information', 'en_cours'],
            ['Retour après la coupure électrique', 'Merci aux voisins et aux agents pour les informations partagées pendant la panne.', 'information', 'resolu'],
        ] as $index => [$titre, $contenu, $type, $statut]) {
            $publication = PublicationQuartier::updateOrCreate(
                ['quartier_id' => $quartier->id, 'titre' => $titre],
                [
                    'user_id' => $primaryHabitant->id,
                    'contenu' => $contenu,
                    'type' => $type,
                    'statut' => $statut,
                    'resolved_at' => $statut === 'resolu' ? now()->subDay() : null,
                ],
            );

            CommentairePublication::updateOrCreate(
                ['publication_id' => $publication->id, 'user_id' => $users['habitant_'.($index + 2)]->id],
                ['contenu' => 'Merci pour ce partage, cette information est utile au quartier.'],
            );
        }

        $discussionTitles = [
            'Information fraîcheur du jour',
            'Panne d’ascenseur signalée',
            $communityPublication->titre,
        ];

        foreach ($discussionTitles as $index => $title) {
            $publication = PublicationQuartier::query()
                ->where('quartier_id', $quartier->id)
                ->where('titre', $title)
                ->firstOrFail();

            CommentairePublication::updateOrCreate(
                ['publication_id' => $publication->id, 'user_id' => $primaryHabitant->id],
                ['contenu' => 'Je confirme la situation et je reste disponible pour aider les voisins.'],
            );
            $publication->confirmations()->syncWithoutDetaching([$primaryHabitant->id]);
        }
    }
}
