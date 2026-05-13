<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 32)->default('user')->after('password');
            $table->string('profile_type', 64)->nullable()->after('role');
            $table->string('phone', 32)->nullable()->after('profile_type');
            $table->boolean('is_active')->default(true)->after('phone');
            $table->string('company_name')->nullable()->after('is_active');
            $table->text('company_address')->nullable()->after('company_name');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role',
                'profile_type',
                'phone',
                'is_active',
                'company_name',
                'company_address',
            ]);
        });
    }
};
