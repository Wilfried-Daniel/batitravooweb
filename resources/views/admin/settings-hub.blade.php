@extends('admin.layout', ['title' => 'Paramétrage'])

@section('content')

<div class="admin-page-head">
    <div>
        <h1>Paramétrage</h1>
        <p class="admin-page-head__sub">Raccourcis vers les réglages disponibles dans cette version.</p>
    </div>
</div>

<div class="admin-kpi-grid" style="grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));">
    <a href="{{ route('admin.categories.index') }}" class="card" style="text-decoration:none; color:inherit; display:block">
        <h3 class="admin-card-title">Catégories</h3>
        <p class="admin-card-sub">Services et produits — gestion des catégories.</p>
        <span class="admin-link">Ouvrir →</span>
    </a>
    <a href="{{ route('admin.support.tickets.index') }}" class="card" style="text-decoration:none; color:inherit; display:block">
        <h3 class="admin-card-title">Support (tickets)</h3>
        <p class="admin-card-sub">Demandes utilisateurs et réponses équipe.</p>
        <span class="admin-link">Ouvrir →</span>
    </a>
    <a href="{{ route('admin.profile-validation.index', ['status' => 'pending']) }}" class="card" style="text-decoration:none; color:inherit; display:block">
        <h3 class="admin-card-title">Validation des profils</h3>
        <p class="admin-card-sub">Comptes en attente, validés ou rejetés — règles métier d’accès.</p>
        <span class="admin-link">Ouvrir →</span>
    </a>
    <div class="card">
        <h3 class="admin-card-title">Notifications in-app</h3>
        <p class="admin-card-sub">Les notifications côté app passent par l’API <code>GET /me/notifications</code> (candidatures, messages, devis, tickets support, etc.).</p>
        <p class="admin-card-sub" style="margin-top:0.75rem">
            <a href="{{ url('/api') }}" class="admin-link">Préfixe API (JSON) →</a>
            <span style="color:var(--text-3)"> · </span>
            <a href="{{ route('admin.support.tickets.index') }}" class="admin-link">Tickets support →</a>
        </p>
    </div>
</div>

@endsection
