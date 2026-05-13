@php
    $safe = preg_replace('/[^a-z0-9-]/', '', strtolower((string) ($name ?? '')));
    $id = 'app-nav-ico-'.($safe !== '' ? $safe : 'home');
@endphp
<svg class="app-nav-ico" width="18" height="18" aria-hidden="true" focusable="false"><use href="#{{ $id }}" xlink:href="#{{ $id }}"/></svg>
