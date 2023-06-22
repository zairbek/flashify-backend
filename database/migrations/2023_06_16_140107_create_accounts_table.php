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
        Schema::create('accounts', static function (Blueprint $table) {
            $table->uuid()->unique()->primary();
            $table->string('login', 50)->unique();

            $table->string(column: 'region_iso_code', length: 2);
            $table->string(column: 'phone_number', length: 30);
            $table->unique(['region_iso_code', 'phone_number']);

            $table->string('email', 255)->unique()->nullable();
            $table->unique(['region_iso_code', 'phone_number', 'email']);

            $table->json('confirmation_code')->nullable()
                ->default(json_encode([
                    'email' => ['code' => null, 'sendAt' => null],
                    'phone' => ['code' => null, 'sendAt' => null]
                ], JSON_THROW_ON_ERROR));

            $table->string('first_name', 255)->nullable();
            $table->string('last_name', 255)->nullable();
            $table->enum('status', ['active', 'inactive', 'ban'])->default('inactive');
            $table->string('password')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
