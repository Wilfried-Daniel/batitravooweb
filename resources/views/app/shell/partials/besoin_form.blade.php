@php
    $mp = $profileSlug;
    $isEdit = ($besoinFormMode ?? '') === 'edit' && isset($routeBesoin) && $routeBesoin instanceof \App\Models\Besoin;
    $action = $isEdit
        ? route('app.'.$mp.'.besoins.update', ['besoin' => $routeBesoin->getKey()])
        : route('app.'.$mp.'.besoins.store');
    $b = $isEdit ? $routeBesoin : null;
@endphp

<div class="app-card app-card--flush app-flex-between-wrap">
    <a href="{{ route('app.'.$mp.'.besoins') }}" class="app-text-link">← Retour à mes besoins</a>
</div>

<div class="app-card app-mt">
    <form method="post" action="{{ $action }}" enctype="multipart/form-data" class="app-form-stack">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div class="app-field">
            <label for="b_title">Titre du projet <span class="app-muted">*</span></label>
            <input type="text" name="title" id="b_title" value="{{ old('title', $isEdit ? $b->title : '') }}" required maxlength="255" autocomplete="off">
            @error('title')<div class="app-error">{{ $message }}</div>@enderror
        </div>
        @if ($isEdit)
            <div class="app-field">
                <label for="b_status">Statut</label>
                <select name="status" id="b_status">
                    @foreach (['open' => 'Ouvert', 'in_progress' => 'En cours', 'closed' => 'Clôturé', 'cancelled' => 'Annulé'] as $val => $lab)
                        <option value="{{ $val }}" @selected(old('status', $b->status) === $val)>{{ $lab }}</option>
                    @endforeach
                </select>
                @error('status')<div class="app-error">{{ $message }}</div>@enderror
            </div>
        @endif
        <div class="app-field">
            <label for="b_budget">Budget (indicatif)</label>
            <input type="text" name="budget" id="b_budget" value="{{ old('budget', $isEdit ? ($b->budget ?? '') : '') }}" maxlength="128" placeholder="Ex. 2–5 M FCFA">
            @error('budget')<div class="app-error">{{ $message }}</div>@enderror
        </div>
        <div class="app-field">
            <label for="b_place">Lieu / zone</label>
            <input type="text" name="place" id="b_place" value="{{ old('place', $isEdit ? ($b->place ?? '') : '') }}" maxlength="255">
            @error('place')<div class="app-error">{{ $message }}</div>@enderror
        </div>
        <div class="app-field">
            <label for="b_start">Démarrage souhaité</label>
            <input type="text" name="start_label" id="b_start" value="{{ old('start_label', $isEdit ? ($b->start_label ?? '') : '') }}" maxlength="128">
            @error('start_label')<div class="app-error">{{ $message }}</div>@enderror
        </div>
        <div class="app-field">
            <label for="b_duration">Durée estimée</label>
            <input type="text" name="duration" id="b_duration" value="{{ old('duration', $isEdit ? ($b->duration ?? '') : '') }}" maxlength="128">
            @error('duration')<div class="app-error">{{ $message }}</div>@enderror
        </div>
        <div class="app-field">
            <label for="b_short_date">Échéance</label>
            <input type="text" name="short_date" id="b_short_date" value="{{ old('short_date', $isEdit ? ($b->short_date ?? '') : '') }}" maxlength="64">
            @error('short_date')<div class="app-error">{{ $message }}</div>@enderror
        </div>
        <div class="app-field">
            <label for="b_desc">Description</label>
            <textarea name="description" id="b_desc" rows="7" maxlength="10000">{{ old('description', $isEdit ? ($b->description ?? '') : '') }}</textarea>
            @error('description')<div class="app-error">{{ $message }}</div>@enderror
        </div>
        <div class="app-field">
            <label for="b_img">Photo illustrative @if ($isEdit)(nouvelle image remplace l’actuelle) @endif</label>
            <input type="file" name="image" id="b_img" accept="image/*">
            @error('image')<div class="app-error">{{ $message }}</div>@enderror
        </div>
        <div class="app-form-actions">
            <button type="submit" class="app-btn app-btn--inline">{{ $isEdit ? 'Enregistrer' : 'Publier le besoin' }}</button>
            <a href="{{ route('app.'.$mp.'.besoins') }}" class="app-btn app-btn--secondary">Annuler</a>
        </div>
    </form>
</div>
