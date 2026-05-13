<?php

namespace App\Http\Controllers\Api\Me;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserDocument;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Documents utilisateur (pièces entreprise BTP / fournisseur, etc.).
 */
class UserDocumentController extends Controller
{
    /**
     * @return list<string>
     */
    public static function companyComplianceKinds(): array
    {
        return [
            UserDocument::KIND_COMMERCE_REGISTER,
            UserDocument::KIND_DFE,
            UserDocument::KIND_MANAGER_CNI,
        ];
    }

    /**
     * @return list<string>
     */
    private function uploadableKindsFor(User $user): array
    {
        return match ($user->profile_type) {
            User::PROFILE_ENTREPRENEUR_BATIMENT,
            User::PROFILE_ENTREPRISE_FOURNISSEUR => self::companyComplianceKinds(),
            default => [],
        };
    }

    public function index(Request $request): JsonResponse
    {
        $docs = $request->user()->documents()->orderBy('kind')->get()->map(function (UserDocument $d) {
            $url = $d->storage_path ? storage_public_url($d->storage_path) : null;

            return [
                'id' => $d->id,
                'title' => UserDocument::labelForKind($d->kind),
                'subtitle' => $d->original_filename,
                'kind' => $d->kind,
                'file_url' => $url,
                'has_file' => $url !== null,
            ];
        });

        return response()->json([
            'data' => $docs,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        $allowed = $this->uploadableKindsFor($user);
        if ($allowed === []) {
            return response()->json(['message' => 'Dépôt non disponible pour ce type de profil.'], 403);
        }

        $validated = $request->validate([
            'kind' => ['required', 'string', Rule::in($allowed)],
            'file' => ['required', 'file', 'max:10240'],
        ]);

        $doc = UserDocument::storeUploaded($user, $request->file('file'), $validated['kind']);
        $url = $doc->storage_path ? storage_public_url($doc->storage_path) : null;

        return response()->json([
            'data' => [
                'id' => $doc->id,
                'title' => UserDocument::labelForKind($doc->kind),
                'subtitle' => $doc->original_filename,
                'kind' => $doc->kind,
                'file_url' => $url,
                'has_file' => $url !== null,
            ],
        ], 201);
    }
}
