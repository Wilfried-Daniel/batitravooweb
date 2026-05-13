@if ($profileSlug === 'fournisseur')
    @include('app.shell.partials.home_fournisseur')
@elseif ($profileSlug === 'batiment')
    @include('app.shell.partials.home_batiment')
@elseif ($profileSlug === 'particulier')
    @include('app.shell.partials.home_particulier')
@elseif ($profileSlug === 'artisan')
    @include('app.shell.partials.home_artisan')
@else
    @include('app.shell.partials.dashboard_metrics')
@endif

@if (! empty($notificationsPreview['data']))
    <div class="app-card app-mt">
        <h2 class="app-section-title">Notifications récentes</h2>
        <ul class="app-notif-list">
            @foreach ($notificationsPreview['data'] as $n)
                <li class="app-notif-list__item {{ empty($n['read']) ? 'is-unread' : '' }}">
                    <strong>{{ $n['title'] ?? '—' }}</strong>
                    <span class="app-muted">{{ $n['body'] ?? '' }}</span>
                </li>
            @endforeach
        </ul>
        @if (! empty($notificationsPreview['meta']['unread_count']))
            <p class="app-muted app-mt-sm">{{ $notificationsPreview['meta']['unread_count'] }} non lue(s)</p>
        @endif
        <p class="app-mt-sm">
            <a href="{{ route('app.'.$profileSlug.'.notifications') }}" class="app-text-link">Voir toutes les notifications</a>
        </p>
    </div>
@endif
