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
        Schema::create('user_emails', function (Blueprint $table) {
            $table->uuid();
            $table->string('email', 255)->unique();
            $table->string('confirmation_code', 6)->nullable();
            $table->timestamp('send_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->foreignUuid('user_uuid')->unique()->constrained('users', 'uuid')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_emails');
    }
};
