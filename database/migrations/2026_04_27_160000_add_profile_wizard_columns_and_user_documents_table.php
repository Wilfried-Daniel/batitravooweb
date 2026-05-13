<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('bio')->nullable()->after('commune');
            $table->string('contact_email')->nullable()->after('bio');
            $table->text('company_description')->nullable()->after('contact_email');
            $table->string('years_experience', 64)->nullable()->after('company_description');
            $table->string('activity_type', 128)->nullable()->after('years_experience');
            $table->string('company_size', 128)->nullable()->after('activity_type');
            $table->string('manager_name')->nullable()->after('company_size');
            $table->string('manager_contact', 128)->nullable()->after('manager_name');
            $table->string('artisan_availability', 32)->nullable()->after('manager_contact');
            $table->timestamp('profile_completed_at')->nullable()->after('artisan_availability');
        });

        Schema::create('user_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('kind', 64);
            $table->string('storage_path', 512);
            $table->string('original_filename', 512)->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'kind']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_documents');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'bio',
                'contact_email',
                'company_description',
                'years_experience',
                'activity_type',
                'company_size',
                'manager_name',
                'manager_contact',
                'artisan_availability',
                'profile_completed_at',
            ]);
        });
    }
};
