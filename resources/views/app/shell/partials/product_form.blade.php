@php
    /** @var string $productFormMode */
    /** @var \App\Models\Product|null $formProduct */
    /** @var array<int, array<string, mixed>> $categories */
    $isEdit = ($productFormMode ?? '') === 'edit' && $formProduct instanceof \App\Models\Product;
    $action = $isEdit
        ? route('app.fournisseur.products.update', ['product' => $formProduct->getKey()])
        : route('app.fournisseur.products.store');
@endphp

<form method="post" action="{{ $action }}" enctype="multipart/form-data" class="app-card app-form-stack">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="app-field">
        <label for="prod-title">Nom du produit</label>
        <input type="text" name="title" id="prod-title" required maxlength="255" value="{{ old('title', $isEdit ? $formProduct->title : '') }}">
        @error('title')
            <span class="app-error">{{ $message }}</span>
        @enderror
    </div>

    <div class="app-field">
        <label for="prod-cat">Catégorie</label>
        <select name="category_id" id="prod-cat">
            <option value="">—</option>
            @foreach ($categories ?? [] as $cat)
                @php $cid = (int) ($cat['id'] ?? 0); @endphp
                <option value="{{ $cid }}" @selected(old('category_id', $isEdit ? $formProduct->category_id : null) == $cid)>{{ $cat['name'] ?? '—' }}</option>
            @endforeach
        </select>
        @error('category_id')
            <span class="app-error">{{ $message }}</span>
        @enderror
    </div>

    <div class="app-field">
        <label for="prod-desc">Description</label>
        <textarea name="description" id="prod-desc" rows="4" maxlength="10000">{{ old('description', $isEdit ? ($formProduct->description ?? '') : '') }}</textarea>
        @error('description')
            <span class="app-error">{{ $message }}</span>
        @enderror
    </div>

    <div class="app-field-row">
        <div class="app-field">
            <label for="prod-price">Prix (FCFA)</label>
            <input type="number" name="price_amount" id="prod-price" required min="0" step="1" value="{{ old('price_amount', $isEdit ? $formProduct->price_amount : '') }}">
            @error('price_amount')
                <span class="app-error">{{ $message }}</span>
            @enderror
        </div>
        <div class="app-field">
            <label for="prod-stock">Stock (unités)</label>
            <input type="number" name="stock_units" id="prod-stock" required min="0" step="1" value="{{ old('stock_units', $isEdit ? $formProduct->stock_units : '') }}">
            @error('stock_units')
                <span class="app-error">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="app-field">
        <label for="prod-img">Photo</label>
        @if ($isEdit && $formProduct->image_path)
            <p class="app-muted app-mb-sm">Image actuelle enregistrée. Choisissez un fichier pour la remplacer.</p>
        @endif
        <input type="file" name="image" id="prod-img" accept="image/*">
        @error('image')
            <span class="app-error">{{ $message }}</span>
        @enderror
    </div>

    <div class="app-form-actions">
        <button type="submit" class="app-btn app-btn--inline">{{ $isEdit ? 'Enregistrer' : 'Créer le produit' }}</button>
        <a href="{{ route('app.fournisseur.products') }}" class="app-btn app-btn--secondary">Annuler</a>
    </div>
</form>
