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
            $table->char('uploud_message',255);
            $table->foreignId('index_message_id')->references('id')->on('index_messages');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages',function (Blueprint $table) {
            $table->dropForeign(['index_message_id']);
        });

        Schema::dropIfExists('messages');
    }
};
