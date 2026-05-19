@extends('admin.layout', ['title' => 'À traiter'])

@section('content')

<div class="admin-page-head">
    <div>
        <h1>File d’attente</h1>
        <p class="admin-page-head__sub">Raccourcis vers les éléments en attente de traitement.</p>
    </div>
</div>

<div class="admin-kpi-grid">
    <article class="admin-kpi">
        <div class="admin-kpi__head">
            <div class="admin-kpi__ico admin-kpi__ico--orange">
                <svg width="20" height="20" aria-hidden="true"><use href="#admin-ico-check" xlink:href="#admin-ico-check"/></svg>
            </div>
            @if ($pendingProfiles > 0)
                <span class="admin-kpi__delta admin-kpi__delta--down">À traiter</span>
            @endif
        </div>
        <p class="admin-kpi__label">Profils à valider</p>
        <p class="admin-kpi__value">{{ $pendingProfiles }}</p>
        <p class="admin-kpi__sub" style="margin-top:0.5rem">
            <a href="{{ route('admin.profile-validation.index', ['status' => 'pending']) }}" class="admin-link">Ouvrir →</a>
        </p>
    </article>

    <article class="admin-kpi">
        <div class="admin-kpi__head">
            <div class="admin-kpi__ico admin-kpi__ico--violet">
                <svg width="20" height="20" aria-hidden="true"><use href="#admin-ico-cube" xlink:href="#admin-ico-cube"/></svg>
            </div>
            @if ($pendingProducts > 0)
                <span class="admin-kpi__delta admin-kpi__delta--down">À traiter</span>
            @endif
        </div>
        <p class="admin-kpi__label">Produits en attente</p>
        <p class="admin-kpi__value">{{ $pendingProducts }}</p>
        <p class="admin-kpi__sub" style="margin-top:0.5rem">
            <a href="{{ route('admin.products.index', ['status' => 'pending']) }}" class="admin-link">Liste produits →</a>
        </p>
    </article>

    <article class="admin-kpi">
        <div class="admin-kpi__head">
            <div class="admin-kpi__ico admin-kpi__ico--navy">
                <svg width="20" height="20" aria-hidden="true"><use href="#admin-ico-doc" xlink:href="#admin-ico-doc"/></svg>
            </div>
            @if ($supportActive > 0)
                <span class="admin-kpi__delta admin-kpi__delta--down">À traiter</span>
            @endif
        </div>
        <p class="admin-kpi__label">Tickets support actifs</p>
        <p class="admin-kpi__value">{{ $supportActive }}</p>
        <p class="admin-kpi__sub">Ouverts et en cours</p>
        <p class="admin-kpi__sub" style="margin-top:0.35rem">
            <a href="{{ route('admin.support.tickets.index') }}" class="admin-link">Ouvrir le support →</a>
        </p>
    </article>
</div>

<div class="card" style="margin-top:1rem; padding:1rem 1.25rem">
    <p style="margin:0; color:var(--text-3); font-size:0.95rem">
        Vue détaillée de la modération catalogue :
        <a href="{{ route('admin.moderation') }}" class="admin-link">page Modération →</a>
    </p>
</div>

@endsection
