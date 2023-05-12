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
        Schema::create('categories', static function (Blueprint $table) {
            $table->uuid()->primary();
            $table->string('slug', 255)->unique();
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->string('icon', 100)->nullable();
            $table->uuid('parent_uuid')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::table('categories', static function(Blueprint $table) {
            $table->foreign('parent_uuid')->references('uuid')->on('categories')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
