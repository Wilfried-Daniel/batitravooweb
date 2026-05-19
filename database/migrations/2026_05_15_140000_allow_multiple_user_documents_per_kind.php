<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_documents', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('user_documents', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'kind']);
            $table->index(['user_id', 'kind']);
        });

        Schema::table('user_documents', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('user_documents', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('user_documents', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'kind']);
            $table->unique(['user_id', 'kind']);
        });

        Schema::table('user_documents', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
