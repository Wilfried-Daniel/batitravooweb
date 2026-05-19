<?php

namespace App\Http\Controllers\Api\Concerns;

use App\Models\Service;
use App\Models\User;

trait FormatsServiceApi
{
    /**
     * Type catalogue aligné sur le profil du prestataire (source de vérité pour la marketplace).
     */
    protected function effectiveServiceKind(Service $s): string
    {
        $user = $s->relationLoaded('user') ? $s->user : null;
        if ($user instanceof User) {
            if ($user->profile_type === User::PROFILE_ENTREPRENEUR_BATIMENT) {
                return 'entrepreneur';
            }
            if ($user->profile_type === User::PROFILE_ARTISAN) {
                return 'artisan';
            }
        }

        $kind = $s->service_kind;

        return in_array($kind, ['artisan', 'entrepreneur'], true) ? $kind : 'artisan';
    }

    protected function serviceKindForProfileType(string $profileType): string
    {
        return $profileType === User::PROFILE_ENTREPRENEUR_BATIMENT ? 'entrepreneur' : 'artisan';
    }
    /**
     * Image affichée : upload disque public en priorité, sinon URL externe https.
     * Pas d’image factice : si vide → null (l’app peut afficher une icône).
     */
    protected function resolveServiceImageUrl(Service $s): ?string
    {
        if ($s->image_path) {
            return storage_public_url($s->image_path);
        }

        $url = $s->image_url;
        if (! is_string($url) || $url === '') {
            return null;
        }

        if (str_starts_with($url, 'https://') || str_starts_with($url, 'http://')) {
            return $url;
        }

        return null;
    }

    /**
     * @return array{mode: string, title_fr: string, detail_fr: ?string, hint_fr: ?string}
     */
    protected function servicePricingPayload(Service $s): array
    {
        $variable = (bool) $s->price_variables;
        $detail = trim((string) ($s->price_fixed_label ?? ''));
        $detailOrNull = $detail !== '' ? $detail : null;

        if ($variable) {
            return [
                'mode' => 'variable',
                'title_fr' => 'Prix variable',
                'detail_fr' => $detailOrNull,
                'hint_fr' => $detailOrNull ?? 'Sur devis ou selon le périmètre du projet.',
            ];
        }

        return [
            'mode' => 'fixed',
            'title_fr' => 'Prix fixe',
            'detail_fr' => $detailOrNull,
            'hint_fr' => $detailOrNull,
        ];
    }
}
