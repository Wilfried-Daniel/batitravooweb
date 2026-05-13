@php
    $mp = $profileSlug;
    $sectionTitle = match ($mp) {
        'particulier' => 'Vos besoins & projets',
        'batiment' => 'Besoins & chantiers publiés',
        default => 'Vos besoins',
    };
    $candBase = route('app.'.$mp.'.candidatures');
    $mkBesoinCandLink = static function (int $besoinId) use ($mp, $candBase): string {
        if ($mp === 'batiment') {
            return $candBase.'?'.http_build_query(['vue' => 'recues', 'besoin_id' => $besoinId]);
        }

        return $candBase.'?'.http_build_query(['besoin_id' => $besoinId]);
    };
    $statusBesoin = static function (?string $s): string {
        return match ($s) {
            'open' => 'Ouvert',
            'in_progress' => 'En cours',
            'closed' => 'Clôturé',
            'cancelled' => 'Annulé',
            default => $s ?? '—',
        };
    };
@endphp

<div class="app-manage-toolbar app-card">
    <a href="{{ route('app.'.$mp.'.besoins.create') }}" class="app-btn app-btn--inline app-btn--sm">Créer un besoin</a>
    <a href="{{ route('app.'.$mp.'.candidatures') }}" class="app-btn app-btn--secondary app-btn--sm">Voir les candidatures</a>
    <a href="{{ route('app.'.$mp.'.marketplace', ['tab' => 'besoins']) }}" class="app-btn app-btn--secondary app-btn--sm">Marketplace besoins</a>
</div>

<div class="app-card app-mt">
    <div class="app-flex-between app-mb-sm" style="flex-wrap:wrap;gap:0.65rem;">
        <h2 class="app-section-title" style="margin:0;">{{ $sectionTitle }}</h2>
        <a href="{{ route('app.'.$mp.'.besoins.create') }}" class="app-btn app-btn--inline">Nouveau besoin</a>
    </div>
    @if (! empty($besoinsList) && count($besoinsList))
        <div class="app-table-wrap">
            <table class="app-table app-table--bordered">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Budget</th>
                        <th>Lieu</th>
                        <th>Réponses</th>
                        <th>Statut</th>
                        <th class="app-table__col-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($besoinsList as $row)
                        @php
                            $bid = (int) ($row['id'] ?? 0);
                            $nCand = (int) ($row['candidature_count'] ?? 0);
                        @endphp
                        <tr>
                            <td><strong>{{ $row['title'] ?? '—' }}</strong></td>
                            <td class="app-muted">{{ $row['budget'] ?? '—' }}</td>
                            <td class="app-muted">{{ $row['place'] ?? '—' }}</td>
                            <td>
                                @if ($nCand > 0 && $bid > 0)
                                    <a href="{{ $mkBesoinCandLink($bid) }}" class="app-text-link">{{ $nCand }}</a>
                                @else
                                    {{ $nCand }}
                                @endif
                            </td>
                            <td><span class="app-pill">{{ $statusBesoin($row['status'] ?? null) }}</span></td>
                            <td class="app-table__col-actions">
                                <span class="app-action-links">
                                    <a href="{{ route('app.'.$mp.'.marketplace.besoin', ['besoin' => $bid]) }}">Fiche publique</a>
                                    @if ($bid > 0 && $nCand > 0)
                                        <span class="app-muted"> · </span>
                                        <a href="{{ $mkBesoinCandLink($bid) }}">Candidatures</a>
                                    @endif
                                    @if ($bid > 0 && in_array($mp, ['particulier', 'batiment'], true))
                                        <span class="app-muted"> · </span>
                                        <a href="{{ route('app.'.$mp.'.besoins.edit', ['besoin' => $bid]) }}" class="app-text-link">Modifier</a>
                                        <form action="{{ route('app.'.$mp.'.besoins.destroy', ['besoin' => $bid]) }}" method="post" class="app-inline-form" onsubmit="return confirm('Supprimer ce besoin ?');" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="app-text-link app-text-link--danger">Supprimer</button>
                                        </form>
                                    @endif
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="app-muted app-mb-sm">Aucun besoin publié pour le moment.</p>
        <div style="display:flex;flex-wrap:wrap;gap:0.5rem;">
            <a href="{{ route('app.'.$mp.'.besoins.create') }}" class="app-btn app-btn--inline">Publier un besoin</a>
            <a href="{{ route('app.'.$mp.'.marketplace', ['tab' => 'besoins']) }}" class="app-btn app-btn--secondary app-btn--inline">Voir le marketplace</a>
        </div>
    @endif
</div>
