<div class="app-card app-card--flush">
    <a href="{{ route('app.'.$profileSlug.'.support') }}" class="app-text-link">← Retour à la liste</a>
</div>

<div class="app-card app-mt">
    <form method="post" action="{{ route('app.'.$profileSlug.'.support.store') }}" enctype="multipart/form-data" class="app-form-stack">
        @csrf
        <div class="app-field">
            <label for="subject">Sujet</label>
            <input type="text" name="subject" id="subject" value="{{ old('subject') }}" required maxlength="255">
            @error('subject')<div class="app-error">{{ $message }}</div>@enderror
        </div>
        <div class="app-field">
            <label for="body">Message</label>
            <textarea name="body" id="body" rows="6" required maxlength="20000">{{ old('body') }}</textarea>
            @error('body')<div class="app-error">{{ $message }}</div>@enderror
        </div>
        <div class="app-field">
            <label for="priority">Priorité</label>
            <select name="priority" id="priority">
                <option value="">Normale</option>
                <option value="low" @selected(old('priority') === 'low')>Basse</option>
                <option value="normal" @selected(old('priority') === 'normal')>Normale</option>
                <option value="high" @selected(old('priority') === 'high')>Haute</option>
            </select>
            @error('priority')<div class="app-error">{{ $message }}</div>@enderror
        </div>
        <div class="app-field">
            <label for="attachment">Pièce jointe (optionnel)</label>
            <input type="file" name="attachment" id="attachment" accept="image/*,.pdf,.doc,.docx">
            @error('attachment')<div class="app-error">{{ $message }}</div>@enderror
        </div>
        <button type="submit" class="app-btn app-btn--inline">Envoyer le ticket</button>
    </form>
</div>
