{{--
  Logo graphique : image PNG si présente (public/images/logo.png), sinon SVG inline
  (les polices s’appliquent, contrairement à &lt;img src="logo.svg"&gt;).
  @param string|null $variant « inverse » = colonne gauche login (fond marine)
--}}
@php
    $inverse = ($variant ?? '') === 'inverse';
    $png = public_path('images/logo.png');
    $hasPng = file_exists($png);
    $alt = config('app.name', 'BATITRAVOO');
@endphp
@if ($hasPng)
    <img
        src="{{ public_asset('images/logo.png') }}"
        alt="{{ $alt }}"
        class="admin-brand-logo-img {{ $inverse ? 'admin-brand-logo-img--on-hero' : '' }}"
        width="200"
        height="80"
        decoding="async"
        loading="eager"
    >
@else
    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 260 52"
        class="admin-brand-logo-svg {{ $inverse ? 'admin-brand-logo-svg--inverse' : '' }}"
        role="img"
        aria-label="{{ $alt }}"
    >
        <text x="2" y="38" font-family="Inter, system-ui, sans-serif" font-size="32" font-weight="700">BATI</text>
        <text x="98" y="38" font-family="Inter, system-ui, sans-serif" font-size="32" font-weight="700">TRAV</text>
        <text x="196" y="38" font-family="Inter, system-ui, sans-serif" font-size="32" font-weight="700">OO</text>
    </svg>
@endif
