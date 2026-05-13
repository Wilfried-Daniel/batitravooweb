<div class="app-card app-mt">
    @include('app.partials.profile-form-heading', ['profileKey' => 'particulier'])
    <div class="app-field">
        <label for="name">Nom complet</label>
        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required maxlength="255">
    </div>
    <div class="app-field">
        <label for="bio">Présentation</label>
        <textarea name="bio" id="bio" rows="4" required maxlength="5000">{{ old('bio', $user->bio) }}</textarea>
    </div>
    <div class="app-field">
        <label for="ville">Ville</label>
        <input type="text" name="ville" id="ville" value="{{ old('ville', $user->city) }}" required maxlength="255">
    </div>
    <div class="app-field">
        <label for="commune">Commune</label>
        <input type="text" name="commune" id="commune" value="{{ old('commune', $user->commune) }}" required maxlength="255">
    </div>
    <div class="app-field">
        <label for="company_address">Adresse complète</label>
        <textarea name="company_address" id="company_address" rows="2" required maxlength="2000">{{ old('company_address', $user->company_address) }}</textarea>
    </div>
    <div class="app-field">
        <label for="phone">Téléphone</label>
        <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" required maxlength="32">
    </div>
    <div class="app-field">
        <label for="contact_email">E-mail de contact</label>
        <input type="email" name="contact_email" id="contact_email" value="{{ old('contact_email', $user->contact_email) }}" required maxlength="255">
    </div>
    <div class="app-field">
        <label for="document_cni">Pièce d’identité (CNI)</label>
        <input type="file" name="document_cni" id="document_cni" accept=".pdf,image/*" required>
        <span class="app-muted app-field-hint">PDF ou image, max 10 Mo.</span>
    </div>
    <div class="app-field">
        <label for="document_other">Justificatif complémentaire</label>
        <input type="file" name="document_other" id="document_other" accept=".pdf,image/*" required>
    </div>
</div>
