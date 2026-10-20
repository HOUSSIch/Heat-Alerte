@extends('layouts.front')

@section('title', 'Mes notifications - HeatAlert')

@section('content')
    @include('partials.front-page-title', ['title' => 'Mes notifications', 'subtitle' => 'Alertes et informations concernant votre quartier.'])
    <section class="services section"><div class="container">
        @if ($notifications->contains(fn ($notification) => ! $notification->read_at))<form method="POST" action="{{ route('notifications.read-all') }}" class="mb-4">@csrf @method('PATCH')<button class="btn-main" type="submit">Tout marquer comme lu</button></form>@endif
        @forelse ($notifications as $notification)
            <article class="heatalert-card mb-3 {{ $notification->read_at ? '' : 'border border-warning' }}"><div class="d-flex justify-content-between gap-3"><div><p class="post-category">{{ $notification->data['niveau'] ?? 'Information' }}</p><h3>{{ $notification->data['title'] ?? 'Notification HeatAlert' }}</h3><p>{{ $notification->data['message'] ?? '' }}</p><small>{{ $notification->created_at->format('d/m/Y H:i') }}</small></div>@if (! $notification->read_at)<form method="POST" action="{{ route('notifications.read', $notification->id) }}">@csrf @method('PATCH')<button class="btn-line" type="submit">Marquer comme lu</button></form>@endif</div></article>
        @empty
            <div class="heatalert-panel text-center"><h3>Aucune notification</h3><p class="mb-0">Vous serez informé des vigilances importantes dans votre quartier.</p></div>
        @endforelse
        {{ $notifications->links() }}
    </div></section>
@endsection
