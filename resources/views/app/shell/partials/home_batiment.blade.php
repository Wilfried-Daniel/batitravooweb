@php
    $u = $profileData['user'] ?? [];
    $company = trim((string) ($u['company_name'] ?? ''));
    $displayName = $company !== '' ? $company : (string) ($u['name'] ?? 'Entreprise');
@endphp

<div class="app-card app-card--flush app-fournisseur-home-head">
    <div class="app-fournisseur-home-head__row">
        <div class="app-fournisseur-home-head__greet app-fournisseur-home-head__greet--grow">
            <span class="app-muted app-fournisseur-home-head__hi">Bonjour,</span>
            <strong class="app-fournisseur-home-head__name">{{ $displayName }}</strong>
        </div>
    </div>
</div>

<form method="get" action="{{ route('app.batiment.marketplace') }}" class="app-card app-mt">
    <label for="batiment-home-search" class="app-muted app-text-sm">Recherche marketplace</label>
    <div class="app-mt-sm" style="display:flex;gap:0.5rem;flex-wrap:wrap;align-items:center;">
        <input type="search" name="q" id="batiment-home-search" class="app-input app-input--rounded" style="flex:1;min-width:12rem;" placeholder="Rechercher annonces, prestations…" value="{{ request('q') }}">
        <button type="submit" class="app-btn app-btn--inline app-btn--sm">Rechercher</button>
    </div>
</form>

@include('app.shell.partials.home_shortcuts')

<div class="app-mt">
    @include('app.shell.partials.dashboard_metrics')
</div>
