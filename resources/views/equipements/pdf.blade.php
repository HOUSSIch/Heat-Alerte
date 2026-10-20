<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 28px 32px; }
        body { color: #1d2939; font-family: DejaVu Sans, sans-serif; font-size: 10px; line-height: 1.5; }
        .header { border-bottom: 3px solid #f15a24; margin-bottom: 20px; padding-bottom: 12px; }
        .brand { color: #e54a16; font-size: 20px; font-weight: bold; }
        .logo { float: right; height: 42px; max-width: 100px; object-fit: contain; }
        h1 { font-size: 20px; margin: 4px 0; }
        h2 { border-bottom: 1px solid #e5e7eb; color: #d94813; font-size: 13px; margin: 20px 0 8px; padding-bottom: 5px; }
        .reference { color: #64748b; font-size: 11px; }
        .photo { max-height: 150px; max-width: 220px; margin: 0 0 14px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border-bottom: 1px solid #e5e7eb; padding: 7px; text-align: left; vertical-align: top; }
        th { background: #fff4ed; color: #8c2d0b; width: 35%; }
        .history th { width: auto; }
        .footer { color: #64748b; font-size: 9px; margin-top: 28px; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        @if ($logoDataUri)
            <img src="{{ $logoDataUri }}" class="logo" alt="HeatAlert">
        @endif
        <div class="brand">HeatAlert</div>
        <h1>Fiche équipement</h1>
        <div class="reference">Identifiant : {{ $reference }}</div>
    </div>

    @if ($photoDataUri)
        <img src="{{ $photoDataUri }}" class="photo" alt="Photo de {{ $equipement->nom }}">
    @endif

    <table>
        <tr><th>Nom</th><td>{{ $equipement->nom }}</td></tr>
        <tr><th>Type</th><td>{{ $equipement->type }}</td></tr>
        <tr><th>Marque</th><td>{{ $equipement->marque ?: 'Non renseignée' }}</td></tr>
        <tr><th>Description</th><td>{{ $equipement->description ?: 'Non renseignée' }}</td></tr>
        <tr><th>Sensibilité chaleur</th><td>{{ $equipement->sensible_chaleur ? 'Oui' : 'Non' }}</td></tr>
        <tr><th>Sensibilité coupure</th><td>{{ $equipement->sensible_coupure ? 'Oui' : 'Non' }}</td></tr>
    </table>

    <h2>Conseils associés</h2>
    <ul>
        @forelse ($equipement->conseils as $conseil)
            <li><strong>{{ $conseil->titre }}</strong> — {{ $conseil->description }}</li>
        @empty
            <li>Aucun conseil associé.</li>
        @endforelse
    </ul>

    <h2>Suivi entretien</h2>
    <table>
        <tr><th>Dernier entretien</th><td>{{ $dernierEntretien?->date_realisee?->format('d/m/Y') ?? 'Aucun entretien réalisé' }}</td></tr>
        <tr><th>Prochain entretien</th><td>{{ $prochainEntretien?->date_prevue?->format('d/m/Y') ?? 'Aucun entretien planifié' }}</td></tr>
        <tr><th>Statut</th><td>{{ $equipement->entretiens->contains(fn ($entretien) => $entretien->statut() === 'en_retard') ? 'En retard' : ($prochainEntretien ? 'Prévu' : 'À jour') }}</td></tr>
    </table>

    <h2>Historique récent</h2>
    <table class="history">
        <thead><tr><th>Date</th><th>Titre</th><th>Type</th><th>Statut</th></tr></thead>
        <tbody>
            @forelse ($equipement->entretiens->take(5) as $entretien)
                <tr><td>{{ $entretien->date_prevue->format('d/m/Y') }}</td><td>{{ $entretien->titre }}</td><td>{{ $entretien->type }}</td><td>{{ ucfirst(str_replace('_', ' ', $entretien->statut())) }}</td></tr>
            @empty
                <tr><td colspan="4">Aucun entretien enregistré.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Généré le {{ now()->format('d/m/Y à H:i') }} — HeatAlert</div>
</body>
</html>
