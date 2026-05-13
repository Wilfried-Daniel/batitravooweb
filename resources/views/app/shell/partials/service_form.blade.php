@php
    /** @var string $serviceFormMode */
    /** @var \App\Models\Service|null $formService */
    $mp = $profileSlug ?? 'batiment';
    $isEdit = ($serviceFormMode ?? '') === 'edit' && isset($formService) && $formService instanceof \App\Models\Service;
    $action = $isEdit
        ? route('app.'.$mp.'.services.update', ['service' => $formService->getKey()])
        : route('app.'.$mp.'.services.store');
@endphp

<form method="post" action="{{ $action }}" enctype="multipart/form-data" class="app-card app-form-stack">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="app-field">
        <label for="srv-title">Intitulé <span class="app-muted">*</span></label>
        <input type="text" name="title" id="srv-title" required maxlength="255" value="{{ old('title', $isEdit ? $formService->title : '') }}">
        @error('title')<span class="app-error">{{ $message }}</span>@enderror
    </div>

    <div class="app-field">
        <label for="srv-cat">Catégorie</label>
        <select name="category_id" id="srv-cat">
            <option value="">—</option>
            @foreach ($categories ?? [] as $cat)
                @php $cid = (int) ($cat['id'] ?? 0); @endphp
                <option value="{{ $cid }}" @selected(old('category_id', $isEdit ? $formService->category_id : null) == $cid)>{{ $cat['name'] ?? '—' }}</option>
            @endforeach
        </select>
        @error('category_id')<span class="app-error">{{ $message }}</span>@enderror
    </div>

    <div class="app-field">
        <label for="srv-desc">Description</label>
        <textarea name="description" id="srv-desc" rows="5" maxlength="10000">{{ old('description', $isEdit ? ($formService->description ?? '') : '') }}</textarea>
        @error('description')<span class="app-error">{{ $message }}</span>@enderror
    </div>

    <div class="app-field">
        <label for="srv-loc">Lieu / zone d’intervention</label>
        <input type="text" name="location" id="srv-loc" maxlength="255" value="{{ old('location', $isEdit ? ($formService->location ?? '') : '') }}">
        @error('location')<span class="app-error">{{ $message }}</span>@enderror
    </div>

    <div class="app-field">
        <label class="app-checkbox-label">
            <input type="checkbox" name="price_variables" value="1" @checked(old('price_variables', $isEdit ? $formService->price_variables : false))>
            Tarification variable / sur devis
        </label>
    </div>

    <div class="app-field">
        <label for="srv-price">Libellé tarif (ex. « À partir de 500 000 FCFA »)</label>
        <input type="text" name="price_fixed_label" id="srv-price" maxlength="255" value="{{ old('price_fixed_label', $isEdit ? ($formService->price_fixed_label ?? '') : '') }}">
        @error('price_fixed_label')<span class="app-error">{{ $message }}</span>@enderror
    </div>

    <div class="app-field">
        <label for="srv-img">Visuel</label>
        @if ($isEdit && $formService->image_path)
            <p class="app-muted app-mb-sm">Image actuelle enregistrée. Choisissez un fichier pour la remplacer.</p>
        @endif
        <input type="file" name="image" id="srv-img" accept="image/*">
        @error('image')<span class="app-error">{{ $message }}</span>@enderror
    </div>

    <div class="app-form-actions">
        <button type="submit" class="app-btn app-btn--inline">{{ $isEdit ? 'Enregistrer' : 'Publier la prestation' }}</button>
        <a href="{{ route('app.'.$mp.'.services') }}" class="app-btn app-btn--secondary">Annuler</a>
    </div>
</form>
