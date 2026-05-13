@if (! empty($documentsList) && count($documentsList))
    <div class="app-card app-mt">
        <ul class="app-doc-list">
            @foreach ($documentsList as $doc)
                <li class="app-doc-list__item">
                    <div>
                        <strong>{{ $doc['title'] ?? 'Document' }}</strong>
                        @if (! empty($doc['subtitle']))
                            <span class="app-muted app-text-sm">{{ $doc['subtitle'] }}</span>
                        @endif
                    </div>
                    @if (! empty($doc['has_file']) && ! empty($doc['file_url']))
                        <a href="{{ $doc['file_url'] }}" class="app-text-link" target="_blank" rel="noopener">Télécharger</a>
                    @else
                        <span class="app-muted">—</span>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
@else
    <div class="app-card app-mt">
        <p class="app-muted app-mb-0">Aucun document pour l’instant. Les pièces déposées depuis le mobile ou ajoutées par l’équipe apparaîtront ici avec un lien de téléchargement lorsque disponible.</p>
    </div>
@endif
