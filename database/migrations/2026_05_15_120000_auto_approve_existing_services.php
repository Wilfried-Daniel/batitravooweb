<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('services')
            ->whereIn('status', ['pending', 'rejected'])
            ->update(['status' => 'approved']);
    }

    public function down(): void
    {
        // Irréversible sans historique des statuts précédents.
    }
};
