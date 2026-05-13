@php
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
    $peerName = null;
    if (($peerId ?? 0) > 0) {
        foreach ($conversations as $p) {
            if ((int) ($p['id'] ?? 0) === (int) ($peerId ?? 0)) {
                $peerName = $p['name'] ?? null;
                break;
            }
        }
    }
@endphp

<div class="msn">
    <aside class="msn__sidebar" aria-label="Conversations">
        <div class="msn__sidebar-head">
            <h2 class="msn__sidebar-title">Discussions</h2>
        </div>
        <div class="msn__thread-list">
            @forelse ($conversations as $p)
                @php
                    $pid = (int) ($p['id'] ?? 0);
                    $isActive = ($peerId ?? 0) === $pid;
                @endphp
                <a href="{{ route('app.'.$profileSlug.'.messages') }}?peer_id={{ $pid }}"
                   class="msn__thread {{ $isActive ? 'is-active' : '' }}">
                    <span class="msn__avatar" aria-hidden="true">{{ $initials($p['name'] ?? '') }}</span>
                    <span class="msn__thread-body">
                        <span class="msn__thread-name">{{ $p['name'] ?? '—' }}</span>
                        <span class="msn__thread-meta">{{ $p['profile_type'] ?? '' }}</span>
                    </span>
                </a>
            @empty
                <p class="msn__empty-sidebar app-muted">Aucune conversation. Ouvrez une fiche prestataire ou une annonce pour démarrer un échange.</p>
            @endforelse
        </div>
    </aside>

    <section class="msn__panel" aria-label="Fil de messages">
        @if (($peerId ?? 0) > 0)
            <header class="msn__chatbar">
                <span class="msn__avatar msn__avatar--lg" aria-hidden="true">{{ $initials($peerName) }}</span>
                <div class="msn__chatbar-info">
                    <span class="msn__chatbar-name">{{ $peerName ?? 'Contact' }}</span>
                    <span class="msn__chatbar-sub">Message privé BatiTravoo</span>
                </div>
            </header>

            <div class="msn__stream" id="msn-stream" role="log" aria-live="polite">
                @if (! empty($thread['data']))
                    @foreach ($thread['data'] as $m)
                        @php $isMe = (int) ($m['sender_id'] ?? 0) === (int) auth()->id(); @endphp
                        <div class="msn__row {{ $isMe ? 'msn__row--me' : 'msn__row--them' }}">
                            @unless ($isMe)
                                <span class="msn__avatar msn__avatar--xs" aria-hidden="true">{{ $initials($peerName) }}</span>
                            @endunless
                            <div class="msn__bubble {{ $isMe ? 'msn__bubble--sent' : 'msn__bubble--recv' }}">
                                <div class="msn__bubble-text">{{ $m['body'] ?? '—' }}</div>
                                @if (! empty($m['attachment_url']))
                                    <a href="{{ $m['attachment_url'] }}" class="msn__attach" target="_blank" rel="noopener">@include('app.partials.app-nav-icon', ['name' => 'paperclip'])<span>Pièce jointe</span></a>
                                @endif
                                <time class="msn__time" datetime="{{ $m['created_at'] ?? '' }}">{{ \Illuminate\Support\Str::limit($m['created_at'] ?? '', 19) }}</time>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="msn__empty-stream app-muted">Pas encore de message. Envoyez le premier.</p>
                @endif
            </div>

            <form method="post" action="{{ route('app.'.$profileSlug.'.messages.send') }}" enctype="multipart/form-data" class="msn__composer">
                @csrf
                <input type="hidden" name="receiver_id" value="{{ $peerId }}">
                <div class="msn__composer-inner">
                    <label class="msn__visually-hidden" for="msg_body">Message</label>
                    <textarea name="body" id="msg_body" rows="1" required maxlength="20000" placeholder="Écrire un message…" class="msn__input">{{ old('body') }}</textarea>
                    <label class="msn__clip" title="Joindre un fichier">
                        <input type="file" name="attachment" id="msg_attachment" class="msn__clip-input" accept="image/*,.pdf,.doc,.docx">
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
            <div class="msn__placeholder">
                <div class="msn__placeholder-icon" aria-hidden="true">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                </div>
                <p class="msn__placeholder-title">Sélectionnez une discussion</p>
                <p class="app-muted">Choisissez un contact dans la liste pour afficher vos messages.</p>
            </div>
        @endif
    </section>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var el = document.getElementById('msn-stream');
    if (el) el.scrollTop = el.scrollHeight;
});
</script>
@endpush
