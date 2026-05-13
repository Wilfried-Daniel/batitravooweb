<?php

declare(strict_types=1);

if (! function_exists('public_asset')) {
    /**
     * URL absolue vers un fichier sous public/, alignée sur le domaine et le préfixe de la requête courante.
     * Évite les assets cassés lorsque APP_URL sur le serveur ne correspond pas au domaine réel (ex. encore localhost).
     */
    function public_asset(string $path): string
    {
        $path = ltrim($path, '/');
        $base = rtrim(request()->getBasePath(), '/');

        return request()->getSchemeAndHttpHost().($base === '' ? '' : $base).'/'.$path;
    }
}

if (! function_exists('storage_public_url')) {
    /**
     * URL absolue vers un fichier du disque "public" (storage/app/public), via le lien symbolique public/storage.
     * Utilise le même domaine que la requête courante (indispensable pour l’app mobile sur le réseau local).
     * Ne pas enchaîner url(Storage::url()) : le disque peut déjà renvoyer une URL basée sur APP_URL incorrecte.
     */
    function storage_public_url(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        return public_asset('storage/'.ltrim($path, '/'));
    }
}
