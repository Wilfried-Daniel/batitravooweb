<?php

namespace App\Http\Controllers\Api\Concerns;

use App\Models\Service;

trait FormatsServiceApi
{
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
