<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('filters', function (Blueprint $table) {
            $table->string('category')->change();
        });

        Schema::table('offers', function (Blueprint $table) {
            $table->string('offer_url', 512)->change();
        });
    }

    public function down(): void
    {
        Schema::table('filters', function (Blueprint $table) {
            $table->enum('category', ['ciezarowe', 'budowlane'])->change();
        });

        Schema::table('offers', function (Blueprint $table) {
            $table->string('offer_url')->change();
        });
    }
};
