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
        Schema::create('user_phones', function (Blueprint $table) {
            $table->uuid();
            $table->string(column: 'region_iso_code', length: 2);
            $table->string(column: 'phone_number', length: 30);
            $table->foreignUuid('user_id')->nullable()->constrained()->on('users')->cascadeOnDelete();
            $table->string(column: 'confirmation_code', length: 10)->nullable();
            $table->dateTime(column: 'send_at')->nullable();
            $table->timestamps();

            $table->unique(['region_iso_code', 'phone_number']);
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_phones');
    }
};
