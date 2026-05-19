@php
    $tk = $ticketDetail['data'] ?? [];
    $closed = in_array($tk['status'] ?? '', ['closed', 'resolved'], true);
    $initials = static function (?string $name): string {
        $name = trim((string) $name);
        if ($name === '') {
            return '?';
        }
        $parts = preg_split('/\s+/u', $name) ?: [];
        if (count($parts) >= 2) {
            return mb_strtoupper(mb_substr($parts[0], 0, 1).mb_substr($parts[1], 0, 1));
        }

        return mb_strtoupper(mb_substr($name, 0, 1));
    };
    $formatDate = function ($iso) {
        if (empty($iso)) return '—';
        try {
            $c = \Carbon\Carbon::parse($iso)->locale('fr');
            if ($c->isToday()) return $c->translatedFormat('H:i');
            if ($c->isYesterday()) return 'Hier, ' . $c->translatedFormat('H:i');
            return $c->translatedFormat('d M, H:i');
        } catch (\Throwable) { return (string) $iso; }
    };
@endphp

<div class="app-card app-card--flush app-flex-between-wrap app-mb-sm">
    <a href="{{ route('app.'.$profileSlug.'.support') }}" class="msn__back app-text-link">← Retour aux tickets</a>
</div>

@if (! empty($tk))
    <div class="msn msn--ticket app-mt">
        <section class="msn__panel msn__panel--ticket" aria-label="Conversation support">
            <header class="msn__chatbar msn__chatbar--ticket">
                <a href="{{ route('app.'.$profileSlug.'.support') }}" class="msn__back-mobile app-text-link" aria-label="Retour">←</a>
                <div class="msn__ticket-head-text">
                    <h2 class="msn__ticket-subject">{{ $tk['subject'] ?? 'Ticket' }}</h2>
                    <div class="msn__ticket-badges">
                        <span class="msn__badge-status">{{ $tk['status'] ?? '—' }}</span>
                        <span class="app-muted">Priorité {{ $tk['priority'] ?? '—' }}</span>
                        @if (filled(data_get($tk, 'assigned_to.name')))
                            <span class="app-muted">· {{ data_get($tk, 'assigned_to.name') }}</span>
                        @endif
                    </div>
                </div>
            </header>

            <div class="msn__stream msn__stream--ticket" id="msn-ticket-stream" role="log">
                @foreach ($tk['messages'] ?? [] as $msg)
                    @php $isStaff = ! empty($msg['is_staff']); @endphp
                    <div class="msn__row {{ $isStaff ? 'msn__row--support' : 'msn__row--me' }}">
                        @if ($isStaff)
                            <span class="msn__avatar msn__avatar--support" aria-hidden="true">BT</span>
                        @endif
                        <div class="msn__bubble {{ $isStaff ? 'msn__bubble--support' : 'msn__bubble--sent' }}">
                            <div class="msn__bubble-label">
                                {{ $msg['user']['name'] ?? '—' }}
                                @if ($isStaff)
                                    <span class="msn__pill-support">Support</span>
                                @endif
                            </div>
                            <div class="msn__bubble-text">{{ $msg['body'] ?? '' }}</div>
                            @if (! empty($msg['attachment_url']))
                                <a href="{{ $msg['attachment_url'] }}" class="msn__attach" target="_blank" rel="noopener">@include('app.partials.app-nav-icon', ['name' => 'paperclip'])<span>Pièce jointe</span></a>
                            @endif
                            <time class="msn__time">{{ $formatDate($msg['created_at'] ?? '') }}</time>
                        </div>
                    </div>
                @endforeach
            </div>

            @if (! $closed)
                <form method="post" action="{{ route('app.'.$profileSlug.'.support.reply', ['ticket' => $routeTicket->id ?? $tk['id']]) }}" enctype="multipart/form-data" class="msn__composer">
                    @csrf
                    <div class="msn__composer-inner">
                        <label class="msn__visually-hidden" for="reply_body">Votre réponse</label>
                        <textarea name="body" id="reply_body" rows="1" required maxlength="20000" placeholder="Écrire une réponse…" class="msn__input">{{ old('body') }}</textarea>
                        <label class="msn__clip" title="Joindre un fichier">
                            <input type="file" name="attachment" id="reply_attachment" class="msn__clip-input">
                            <span class="msn__clip-icon" aria-hidden="true">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/></svg>
                            </span>
                        </label>
                        <button type="submit" class="msn__send" aria-label="Envoyer">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                        </button>
                    </div>
                    @error('body')<div class="app-error msn__composer-error">{{ $message }}</div>@enderror
                    @error('attachment')<div class="app-error msn__composer-error">{{ $message }}</div>@enderror
                </form>
            @else
                <p class="msn__closed app-muted">Ce ticket est clos — vous ne pouvez plus répondre.</p>
            @endif
        </section>
    </div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var el = document.getElementById('msn-ticket-stream');
    if (el) el.scrollTop = el.scrollHeight;
});
</script>
@endpush
