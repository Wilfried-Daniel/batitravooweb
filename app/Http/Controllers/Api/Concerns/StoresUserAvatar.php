<?php

namespace App\Http\Controllers\Api\Concerns;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait StoresUserAvatar
{
    protected function replaceUserAvatar(User $user, UploadedFile $file): void
    {
        $dir = 'avatars/'.$user->id;
        $ext = strtolower((string) $file->getClientOriginalExtension());
        if ($ext === '') {
            $ext = (string) ($file->guessExtension() ?: 'jpg');
        }
        $storedName = Str::uuid()->toString().'.'.$ext;
        $path = $file->storeAs($dir, $storedName, 'public');

        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        $user->avatar_path = $path;
        $user->save();
    }
}
