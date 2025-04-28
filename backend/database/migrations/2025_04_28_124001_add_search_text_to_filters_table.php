<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('filters', function (Blueprint $table) {
            $table->string('search_text')->nullable()->after('category');
        });
    }

    public function down(): void
    {
        Schema::table('filters', function (Blueprint $table) {
            $table->dropColumn('search_text');
        });
    }
};
