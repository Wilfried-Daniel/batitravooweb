<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserDocument extends Model
{
    public const KIND_CNI = 'cni';

    public const KIND_OTHER = 'other';

    public const KIND_CERTIFICATE = 'certificate';

    public const KIND_COMMERCE_REGISTER = 'commerce_register';

    public const KIND_MANAGER_CNI = 'manager_cni';

    /** Déclaration fiscale d’établissement (DFE). */
    public const KIND_DFE = 'dfe';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'kind',
        'storage_path',
        'original_filename',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function labelForKind(string $kind): string
    {
        return match ($kind) {
            self::KIND_CNI => 'Pièce d’identité',
            self::KIND_OTHER => 'Document complémentaire',
            self::KIND_CERTIFICATE => 'Certificat / qualification',
            self::KIND_COMMERCE_REGISTER => 'RCCM (registre du commerce)',
            self::KIND_MANAGER_CNI => 'CNI du dirigeant',
            self::KIND_DFE => 'DFE (déclaration fiscale d’établissement)',
            default => $kind,
        };
    }

    public static function storeUploaded(User $user, UploadedFile $file, string $kind): self
    {
        $dir = 'user_documents/'.$user->id;
        $ext = strtolower((string) ($file->getClientOriginalExtension() ?: pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION)));
        if ($ext === '') {
            $ext = 'bin';
        }
        $storedName = Str::uuid()->toString().'.'.$ext;
        $storagePath = $file->storeAs($dir, $storedName, 'public');

        $existing = self::query()
            ->where('user_id', $user->id)
            ->where('kind', $kind)
            ->first();

        if ($existing !== null && $existing->storage_path) {
            Storage::disk('public')->delete($existing->storage_path);
        }

        return self::updateOrCreate(
            [
                'user_id' => $user->id,
                'kind' => $kind,
            ],
            [
                'storage_path' => $storagePath,
                'original_filename' => $file->getClientOriginalName(),
            ]
        );
    }
}
