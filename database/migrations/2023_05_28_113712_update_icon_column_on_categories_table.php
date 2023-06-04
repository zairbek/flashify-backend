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
        Schema::table('categories', static function(Blueprint $table) {
            $table->dropColumn('icon');
        });
        Schema::table('categories', static function(Blueprint $table) {
            $table->uuid('icon_uuid')->nullable();
            $table->foreign('icon_uuid')->references('uuid')->on('icons')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', static function(Blueprint $table) {
            $table->dropForeign('categories_icon_uuid_foreign');
            $table->dropColumn('icon_uuid');
            $table->string('icon', 100)->nullable();
        });
    }
};
