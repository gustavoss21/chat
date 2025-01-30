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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->text('message');
            $table->char('uploud_message',255)->nullable();
            $table->foreignId('sender_user_id')->references('id')->on('users');
            $table->foreignId('recipient_user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages',function (Blueprint $table) {
            $table->dropForeign(['sender_user_id'],['recipient_user_id']);
        });

        Schema::dropIfExists('messages');
    }
};
