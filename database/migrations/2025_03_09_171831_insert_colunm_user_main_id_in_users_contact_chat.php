<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users_contact_chat', function (Blueprint $table) {
            $table->foreignId('user_main_id')->nullable(true)->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users_contact_chat', function (Blueprint $table) {
            $table->dropForeign(['user_main_id']);
            $table->dropColumn('user_main_id');
        });
    }
};
