<div class="app-card app-mt">
    @include('app.partials.profile-form-heading', ['profileKey' => 'fournisseur'])
    <div class="app-field">
        <label for="company_name">Raison sociale</label>
        <input type="text" name="company_name" id="company_name" value="{{ old('company_name', $user->company_name) }}" required maxlength="255">
    </div>
    <div class="app-field">
        <label for="company_description">Description</label>
        <textarea name="company_description" id="company_description" rows="4" required maxlength="5000">{{ old('company_description', $user->company_description) }}</textarea>
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
        <label for="manager_name">Nom du responsable</label>
        <input type="text" name="manager_name" id="manager_name" value="{{ old('manager_name', $user->manager_name) }}" required maxlength="255">
    </div>
    <div class="app-field">
        <label for="manager_contact">Contact du responsable</label>
        <input type="text" name="manager_contact" id="manager_contact" value="{{ old('manager_contact', $user->manager_contact) }}" required maxlength="255">
    </div>
    <div class="app-field">
        <label for="avatar">Logo / photo (optionnel)</label>
        <input type="file" name="avatar" id="avatar" accept="image/*">
    </div>
    <div class="app-field">
        <label for="document_manager_cni">CNI du responsable (optionnel)</label>
        <input type="file" name="document_manager_cni" id="document_manager_cni" accept=".pdf,image/*">
    </div>
    <div class="app-field">
        <label for="document_commerce_register">Registre de commerce (optionnel)</label>
        <input type="file" name="document_commerce_register" id="document_commerce_register" accept=".pdf,image/*">
    </div>
</div>
