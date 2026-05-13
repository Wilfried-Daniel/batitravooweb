@php
    $mp = $profileSlug;
    $headTitle = match ($mp) {
        'artisan' => 'Mes prestations artisan',
        'batiment' => 'Prestations entreprise BTP',
        default => 'Mes services',
    };
    $productStatus = static function (?string $s): string {
        return match ($s) {
            'draft' => 'Brouillon',
            'pending' => 'En validation',
            'approved' => 'Publié',
            'rejected' => 'Refusé',
            default => $s ?? '—',
        };
    };
    $priceShort = static function (array $row): string {
        $pr = $row['pricing'] ?? [];
        $line = $pr['detail_fr'] ?? $pr['title_fr'] ?? '';
        if ($line === '' && ! empty($row['price_fixed_label'])) {
            $line = (string) $row['price_fixed_label'];
        }
        return $line !== '' ? \Illuminate\Support\Str::limit(strip_tags($line), 48) : '—';
    };
@endphp

<div class="app-manage-toolbar app-card">
    @if (in_array($mp, ['batiment', 'artisan'], true))
        <a href="{{ route('app.'.$mp.'.services.create') }}" class="app-btn app-btn--inline app-btn--sm">Nouvelle prestation</a>
    @endif
    <a href="{{ route('app.'.$mp.'.marketplace', ['tab' => 'services']) }}" class="app-btn app-btn--inline app-btn--sm">Marketplace services</a>
    <a href="{{ route('app.'.$mp.'.marketplace', ['tab' => 'besoins']) }}" class="app-btn app-btn--secondary app-btn--sm">Besoins à pourvoir</a>
    <a href="{{ route('app.'.$mp.'.devis') }}" class="app-btn app-btn--secondary app-btn--sm">Mes devis</a>
    @if ($mp === 'batiment')
        <a href="{{ route('app.'.$mp.'.besoins') }}" class="app-btn app-btn--secondary app-btn--sm">Mes besoins publiés</a>
    @endif
</div>

<div class="app-card app-mt">
    <div class="app-flex-between app-mb-sm" style="flex-wrap:wrap;gap:0.65rem;">
        <h2 class="app-section-title" style="margin:0;">{{ $headTitle }}</h2>
        @if (in_array($mp, ['batiment', 'artisan'], true))
            <a href="{{ route('app.'.$mp.'.services.create') }}" class="app-btn app-btn--inline app-btn--sm">Nouvelle prestation</a>
        @else
            <span class="app-muted app-mb-0" style="font-size:0.9rem;">Ajouter une prestation → app mobile</span>
        @endif
    </div>
    @if (! empty($servicesList) && count($servicesList))
        <div class="app-table-wrap">
            <table class="app-table app-table--bordered">
                <thead>
                    <tr>
                        <th>Prestation</th>
                        <th>Catégorie</th>
                        <th>Type</th>
                        <th>Prix / tarif</th>
                        <th>Lieu</th>
                        <th>Statut</th>
                        <th class="app-table__col-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($servicesList as $row)
                        @php
                            $kind = $row['service_kind'] ?? '';
                            $kindLabel = match ($kind) {
                                'artisan' => 'Artisan',
                                'entrepreneur' => 'Entrepreneur BTP',
                                default => $kind,
                            };
                            $sid = (int) ($row['id'] ?? 0);
                        @endphp
                        <tr>
                            <td><strong>{{ $row['title'] ?? '—' }}</strong></td>
                            <td class="app-muted">{{ $row['category']['name'] ?? '—' }}</td>
                            <td>{{ $kindLabel }}</td>
                            <td class="app-muted">{{ $priceShort($row) }}</td>
                            <td class="app-muted">{{ $row['location'] ?? '—' }}</td>
                            <td><span class="app-pill">{{ $productStatus($row['status'] ?? null) }}</span></td>
                            <td class="app-table__col-actions">
                                @if ($sid > 0)
                                    <span class="app-action-links">
                                        <a href="{{ route('app.'.$mp.'.marketplace.service', ['service' => $sid]) }}">Voir annonce</a>
                                        @if (in_array($mp, ['batiment', 'artisan'], true))
                                            <span class="app-muted"> · </span>
                                            <a href="{{ route('app.'.$mp.'.services.edit', ['service' => $sid]) }}">Modifier</a>
                                            <form action="{{ route('app.'.$mp.'.services.destroy', ['service' => $sid]) }}" method="post" class="app-inline-form" onsubmit="return confirm('Supprimer cette prestation ?');" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="app-text-link app-text-link--danger">Supprimer</button>
                                            </form>
                                        @endif
                                        @if (($row['rating'] ?? 0) > 0)
                                            <span class="app-muted" title="Note">{{ number_format((float) $row['rating'], 1, ',', ' ') }} ★</span>
                                        @endif
                                    </span>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="app-muted app-mb-sm">
            @if ($mp === 'artisan')
                Aucune prestation : créez votre première offre ci-dessus ou depuis « Nouvelle prestation » pour apparaître dans les annonces.
            @elseif ($mp === 'batiment')
                Aucune prestation : utilisez « Nouvelle prestation » pour publier vos savoir-faire BTP sur le marketplace.
            @else
                Suivi des prestations listées ci-dessous après publication depuis votre espace.
            @endif
        </p>
        <div style="display:flex;flex-wrap:wrap;gap:0.5rem;">
            <a href="{{ route('app.'.$mp.'.marketplace', ['tab' => 'besoins']) }}" class="app-btn app-btn--inline">Voir les besoins publiés</a>
            <a href="{{ route('app.'.$mp.'.candidatures') }}" class="app-btn app-btn--secondary app-btn--inline">Mes candidatures</a>
        </div>
    @endif
</div>
