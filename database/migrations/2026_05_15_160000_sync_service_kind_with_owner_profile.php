<?php

use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Service::query()
            ->whereHas('user', fn ($q) => $q->where(
                'profile_type',
                User::PROFILE_ENTREPRENEUR_BATIMENT
            ))
            ->where('service_kind', '!=', 'entrepreneur')
            ->update(['service_kind' => 'entrepreneur']);

        Service::query()
            ->whereHas('user', fn ($q) => $q->where('profile_type', User::PROFILE_ARTISAN))
            ->where('service_kind', '!=', 'artisan')
            ->update(['service_kind' => 'artisan']);
    }

    public function down(): void
    {
        // Non réversible sans historique.
    }
};
