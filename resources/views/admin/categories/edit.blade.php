@extends('admin.layout', ['title' => 'Catégorie — '.$category->name])

@section('content')
<div class="admin-page-head">
    <div>
        <h1>{{ $category->name }}</h1>
        <p class="admin-page-head__sub">Slug : <code>{{ $category->slug }}</code></p>
    </div>
    <div class="admin-page-head__actions">
        <a href="{{ route('admin.categories.index') }}" class="admin-btn admin-btn--ghost">
            <svg width="16" height="16" aria-hidden="true"><use href="#admin-ico-arrow-left" xlink:href="#admin-ico-arrow-left"/></svg>
            Retour
        </a>
    </div>
</div>

<div class="card" style="max-width:560px">
    <form method="post" action="{{ route('admin.categories.update', $category) }}">
        @csrf
        @method('PUT')

        <x-admin.field name="name" label="Nom" :value="$category->name" required maxWidth="100%" />

        <x-admin.field
            name="applies_to"
            type="select"
            label="Applique à"
            :value="$category->applies_to"
            :options="['product' => 'Produits uniquement', 'service' => 'Services uniquement', 'both' => 'Produits & services']"
            required
            maxWidth="100%"
        />

        <x-admin.field name="sort_order" type="number" label="Ordre d’affichage" :value="$category->sort_order" maxWidth="100%" min="0" />

        <x-admin.field
            name="is_active"
            type="select"
            label="Actif"
            :value="(string) (int) $category->is_active"
            :options="['1' => 'Oui', '0' => 'Non']"
            required
            maxWidth="100%"
        />

        <div class="admin-form-actions">
            <button type="submit" class="admin-btn admin-btn--orange">
                <svg width="16" height="16" aria-hidden="true"><use href="#admin-ico-check" xlink:href="#admin-ico-check"/></svg>
                Enregistrer
            </button>
            <a href="{{ route('admin.categories.index') }}" class="admin-btn admin-btn--ghost">Annuler</a>
        </div>
    </form>
</div>
@endsection
