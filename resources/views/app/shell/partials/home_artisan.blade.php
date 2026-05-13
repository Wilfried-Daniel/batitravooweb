@php
    $u = $profileData['user'] ?? [];
    $displayName = trim((string) ($u['name'] ?? '')) !== '' ? trim((string) $u['name']) : 'Artisan';
@endphp

<div class="app-card app-card--flush app-fournisseur-home-head">
    <div class="app-fournisseur-home-head__row">
        <div class="app-fournisseur-home-head__greet app-fournisseur-home-head__greet--grow">
            <span class="app-muted app-fournisseur-home-head__hi">Bonjour,</span>
            <strong class="app-fournisseur-home-head__name">{{ $displayName }}</strong>
        </div>
    </div>
</div>

<form method="get" action="{{ route('app.artisan.marketplace') }}" class="app-card app-mt">
    <label for="artisan-home-search" class="app-muted app-text-sm">Recherche marketplace</label>
    <div class="app-mt-sm" style="display:flex;gap:0.5rem;flex-wrap:wrap;align-items:center;">
        <input type="search" name="q" id="artisan-home-search" class="app-input app-input--rounded" style="flex:1;min-width:12rem;" placeholder="Besoins, prestations, matériaux…" value="{{ request('q') }}">
        <button type="submit" class="app-btn app-btn--inline app-btn--sm">Rechercher</button>
    </div>
</form>

@include('app.shell.partials.home_shortcuts')
