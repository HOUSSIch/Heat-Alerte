@extends('layouts.front')

@section('title', 'Comparer les points de fraîcheur')

@section('content')
    @include('partials.front-page-title', [
        'title' => 'Comparer les points de fraîcheur',
        'subtitle' => 'Critères objectifs issus des informations enregistrées.',
    ])

    <section class="portfolio section">
        <div class="container">
            <div class="row gy-4 mb-4">
                @foreach ($comparisons as $item)
                    <div class="col-lg-4">
                        <div class="heatalert-card">
                            <i class="bi bi-geo-alt heatalert-icon"></i>
                            <h3>{{ $item['point']->nom }}</h3>
                            <p>{{ $item['point']->type }}</p>
                            <span class="badge text-bg-warning">
                                <i class="bi bi-star-fill"></i>
                                {{ $item['point']->note_moyenne ? number_format($item['point']->note_moyenne, 1, ',', ' ') : '—' }}/5
                            </span>
                            <p class="mt-2 mb-0">
                                <i class="bi bi-signpost-2"></i>
                                {{ number_format($item['distance'], 2, ',', ' ') }} km
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="heatalert-panel compare-table">
                <div class="compare-table-heading">
                    <div>
                        <span class="points-compare-kicker"><i class="bi bi-table"></i> Comparatif</span>
                        <h3>Comparer les services et l’accessibilité</h3>
                    </div>
                    <span class="compare-table-help">Les informations affichées proviennent des fiches des points.</span>
                </div>

                <div class="table-responsive">
                    <table class="table comparison-table mb-0">
                        <thead>
                            <tr>
                                <th scope="col">Critère</th>
                                @foreach ($comparisons as $item)
                                    <th scope="col">{{ $item['point']->nom }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ([
                                'Distance' => 'distance',
                                'Horaires' => 'horaires',
                                'Climatisation' => 'climatise',
                                'Eau disponible' => 'eau_disponible',
                                'Accessible PMR' => 'accessible_pmr',
                                'Note moyenne' => 'note_moyenne',
                                'Avis publiés' => 'avis_publies_count',
                            ] as $label => $field)
                                <tr>
                                    <th scope="row">{{ $label }}</th>
                                    @foreach ($comparisons as $item)
                                        <td>
                                            @if ($field === 'distance')
                                                <span class="comparison-distance">
                                                    <i class="bi bi-signpost-2"></i>
                                                    {{ number_format($item['distance'], 2, ',', ' ') }} km
                                                </span>
                                            @elseif (in_array($field, ['climatise', 'eau_disponible', 'accessible_pmr']))
                                                <span class="comparison-value comparison-value--{{ $item['point']->$field ? 'yes' : 'no' }}">
                                                    <i class="bi bi-{{ $item['point']->$field ? 'check-lg' : 'dash-lg' }}"></i>
                                                    {{ $item['point']->$field ? 'Oui' : 'Non' }}
                                                </span>
                                            @elseif ($field === 'note_moyenne')
                                                <span class="comparison-rating">
                                                    <i class="bi bi-star-fill"></i>
                                                    {{ $item['point']->$field ? number_format($item['point']->$field, 1, ',', ' ') : '—' }}/5
                                                </span>
                                            @else
                                                <span class="comparison-count">{{ $item['point']->$field ?? '—' }}</span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <a class="compare-back-link mt-4" href="{{ route('points-fraicheur.index') }}">
                <i class="bi bi-arrow-left"></i>
                Retour aux points de fraîcheur
            </a>
        </div>
    </section>
@endsection
