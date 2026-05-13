{{--
  Titres de section alignés sur les formulaires « compléter le profil ».
  @param string $profileKey particulier | artisan | batiment | fournisseur
--}}
@php
    $titles = [
        'particulier' => 'Profil particulier',
        'artisan' => 'Profil artisan',
        'batiment' => 'Entreprise — entrepreneur du bâtiment',
        'fournisseur' => 'Entreprise fournisseur',
    ];
@endphp
<h2 class="app-section-title">{{ $titles[$profileKey] ?? 'Profil' }}</h2>
