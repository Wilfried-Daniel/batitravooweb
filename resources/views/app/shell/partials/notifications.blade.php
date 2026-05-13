@php
    $notifUrl = route('app.'.$profileSlug.'.notifications');
@endphp

<div class="app-card app-card--flush" style="display:flex;flex-wrap:wrap;align-items:center;justify-content:flex-end;gap:0.75rem;">
        <form method="get" action="{{ $notifUrl }}" style="margin:0;display:flex;align-items:center;gap:0.5rem;">
            <label for="notif_per_page" class="app-muted" style="font-size:0.85rem;">Afficher</label>
            <select name="per_page" id="notif_per_page" class="mp-select" style="min-width:5rem;" onchange="this.form.submit()">
                @foreach ([15, 30, 50] as $pp)
                    <option value="{{ $pp }}" @selected((int) request('per_page', 30) === $pp)>{{ $pp }}</option>
                @endforeach
            </select>
        </form>
        @if (! empty($notificationsFull['meta']['unread_count']) && (int) $notificationsFull['meta']['unread_count'] > 0)
            <form method="post" action="{{ route('app.'.$profileSlug.'.notifications.read_all') }}" style="margin:0;">
                @csrf
                <button type="submit" class="app-btn app-btn--inline app-btn--ghost">Tout marquer comme lu</button>
            </form>
        @endif
</div>

<div class="app-card app-mt">
    @if (! empty($notificationsFull['data']) && count($notificationsFull['data']))
        <ul class="app-notif-list">
            @foreach ($notificationsFull['data'] as $n)
                <li class="app-notif-list__item {{ empty($n['read']) ? 'is-unread' : '' }}">
                    <strong>{{ $n['title'] ?? '—' }}</strong>
                    <span class="app-muted">{{ $n['body'] ?? '' }}</span>
                    <span class="app-muted" style="font-size:0.75rem;">{{ $n['created_at'] ?? '' }}</span>
                </li>
            @endforeach
        </ul>
        @if (! empty($notificationsFull['meta']['unread_count']))
            <p class="app-muted app-mt-sm">{{ $notificationsFull['meta']['unread_count'] }} non lue(s)</p>
        @endif
    @else
        <p class="app-muted app-mb-0">Aucune notification pour le moment. Les alertes liées à vos devis, commandes et messages apparaîtront ici.</p>
    @endif
</div>
