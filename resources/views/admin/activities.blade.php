@extends('admin.layout', ['title' => 'Activités'])

@section('content')

<div class="admin-page-head">
    <div>
        <h1>Activités récentes</h1>
        <p class="admin-page-head__sub">Derniers besoins, services et produits publiés.</p>
    </div>
</div>

<div class="admin-chart-grid" style="grid-template-columns: 1fr;">
    <div class="card">
        <div class="admin-card-h">
            <h3 class="admin-card-title">Besoins</h3>
            <a href="{{ route('admin.besoins.index') }}" class="admin-btn admin-btn--soft admin-btn--sm">Tout voir</a>
        </div>
        <ul style="margin:0; padding-left:1.1rem">
            @forelse($recentBesoins as $b)
                <li style="margin-bottom:0.35rem">
                    <a href="{{ route('admin.besoins.show', $b) }}" class="admin-link">{{ \Illuminate\Support\Str::limit($b->title ?? 'Sans titre', 80) }}</a>
                    <span style="color:var(--text-3); font-size:0.9em"> — {{ $b->user?->name }}</span>
                </li>
            @empty
                <li style="color:var(--text-3)">Aucun.</li>
            @endforelse
        </ul>
    </div>
    <div class="card">
        <div class="admin-card-h">
            <h3 class="admin-card-title">Services</h3>
            <a href="{{ route('admin.services.index') }}" class="admin-btn admin-btn--soft admin-btn--sm">Tout voir</a>
        </div>
        <ul style="margin:0; padding-left:1.1rem">
            @forelse($recentServices as $s)
                <li style="margin-bottom:0.35rem">
                    <a href="{{ route('admin.services.show', $s) }}" class="admin-link">{{ \Illuminate\Support\Str::limit($s->title ?? 'Sans titre', 80) }}</a>
                    <span style="color:var(--text-3); font-size:0.9em"> — {{ $s->user?->name }}</span>
                </li>
            @empty
                <li style="color:var(--text-3)">Aucun.</li>
            @endforelse
        </ul>
    </div>
    <div class="card">
        <div class="admin-card-h">
            <h3 class="admin-card-title">Produits</h3>
            <a href="{{ route('admin.products.index') }}" class="admin-btn admin-btn--soft admin-btn--sm">Tout voir</a>
        </div>
        <ul style="margin:0; padding-left:1.1rem">
            @forelse($recentProducts as $p)
                <li style="margin-bottom:0.35rem">
                    <a href="{{ route('admin.products.show', $p) }}" class="admin-link">{{ \Illuminate\Support\Str::limit($p->title ?? 'Sans titre', 80) }}</a>
                    <span style="color:var(--text-3); font-size:0.9em"> — {{ $p->user?->name }}</span>
                </li>
            @empty
                <li style="color:var(--text-3)">Aucun.</li>
            @endforelse
        </ul>
    </div>
</div>

@endsection
