<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_validation_status', 32)->default('approved');
            $table->text('profile_validation_note')->nullable();
            $table->timestamp('profile_validated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'profile_validation_status',
                'profile_validation_note',
                'profile_validated_at',
            ]);
        });
    }
};
