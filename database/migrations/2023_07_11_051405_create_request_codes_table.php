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
        Schema::create('accounts-request_codes', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->uuid('account_uuid')->unique();
            $table->foreign('account_uuid')->references('uuid')->on('accounts')->cascadeOnDelete();
            $table->string('email', 255)->nullable();
            $table->string('phone', 255)->nullable();
            $table->string('code', 10);
            $table->timestamp('sendAt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts-request_codes');
    }
};
