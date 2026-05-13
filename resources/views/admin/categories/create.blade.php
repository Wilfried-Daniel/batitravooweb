@extends('admin.layout', ['title' => 'Nouvelle catégorie'])

@section('content')
<div class="admin-page-head">
    <div>
        <h1>Nouvelle catégorie</h1>
        <p class="admin-page-head__sub">Catégorie utilisée côté produits et/ou services dans la marketplace.</p>
    </div>
    <div class="admin-page-head__actions">
        <a href="{{ route('admin.categories.index') }}" class="admin-btn admin-btn--ghost">
            <svg width="16" height="16" aria-hidden="true"><use href="#admin-ico-arrow-left" xlink:href="#admin-ico-arrow-left"/></svg>
            Retour
        </a>
    </div>
</div>

<div class="card" style="max-width:560px">
    <form method="post" action="{{ route('admin.categories.store') }}">
        @csrf

        <x-admin.field name="name" label="Nom de la catégorie" required maxWidth="100%" />

        <x-admin.field
            name="applies_to"
            type="select"
            label="Applique à"
            value="both"
            :options="['product' => 'Produits uniquement', 'service' => 'Services uniquement', 'both' => 'Produits & services']"
            required
            maxWidth="100%"
        />

        <x-admin.field name="sort_order" type="number" label="Ordre d’affichage" value="0" maxWidth="100%" min="0" />

        <x-admin.field
            name="is_active"
            type="select"
            label="Actif"
            value="1"
            :options="['1' => 'Oui', '0' => 'Non']"
            required
            maxWidth="100%"
        />

        <div class="admin-form-actions">
            <button type="submit" class="admin-btn admin-btn--orange">
                <svg width="16" height="16" aria-hidden="true"><use href="#admin-ico-plus" xlink:href="#admin-ico-plus"/></svg>
                Créer la catégorie
            </button>
            <a href="{{ route('admin.categories.index') }}" class="admin-btn admin-btn--ghost">Annuler</a>
        </div>
    </form>
</div>
@endsection
