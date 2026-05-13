@php
    $devisStatusUrl = route('app.'.$profileSlug.'.devis');
    $devisStatuses = $devisList['meta']['status_filter'] ?? [['value' => '', 'label' => 'Tous les statuts']];
@endphp

<form method="get" action="{{ $devisStatusUrl }}" class="app-filter-bar app-card">
    <div class="app-field app-field--inline">
        <label for="devis_status">Statut</label>
        <select name="status" id="devis_status" class="mp-select" onchange="this.form.submit()">
            @foreach ($devisStatuses as $opt)
                <option value="{{ $opt['value'] }}" @selected((string) request('status') === (string) ($opt['value'] ?? ''))>{{ $opt['label'] }}</option>
            @endforeach
        </select>
    </div>
    @if (filled(request('status')))
        <a href="{{ $devisStatusUrl }}" class="app-text-link app-mt-sm" style="align-self:center;">Réinitialiser</a>
    @endif
</form>

    <div class="app-card app-mt">
    <div class="app-flex-between app-mb-sm" style="flex-wrap:wrap;gap:0.5rem;">
        <h2 class="app-section-title" style="margin:0;">{{ $profileSlug === 'fournisseur' ? 'Mes commandes' : 'Devis' }}</h2>
        <div style="display:flex;flex-wrap:wrap;gap:0.5rem;align-items:center;">
            @if (in_array($profileSlug, ['particulier', 'batiment', 'fournisseur'], true))
                <a href="{{ route('app.'.$profileSlug.'.devis.create') }}" class="app-btn app-btn--inline app-btn--sm">Nouveau devis</a>
            @endif
            <a href="{{ route('app.'.$profileSlug.'.marketplace') }}" class="app-text-link">Annonces</a>
        </div>
    </div>
    @if (! empty($devisList['data']) && count($devisList['data']))
        <div class="app-table-wrap">
            <table class="app-table app-table--bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Titre</th>
                        <th>Client</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($devisList['data'] as $row)
                        <tr>
                            <td>#{{ $row['id'] ?? '—' }}</td>
                            <td>{{ $row['title'] ?? '—' }}</td>
                            <td>{{ $row['client_name'] ?? '—' }}</td>
                            <td><span class="app-pill">{{ $row['status_label'] ?? ($row['status'] ?? '—') }}</span></td>
                            <td class="app-muted">{{ isset($row['created_at']) ? \Illuminate\Support\Str::limit($row['created_at'], 16) : '—' }}</td>
                            <td>
                                <a href="{{ route('app.'.$profileSlug.'.devis.show', ['devis' => $row['id'] ?? 0]) }}" class="app-text-link">Voir</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="app-muted">Aucun devis pour ces critères.</p>
    @endif
</div>
