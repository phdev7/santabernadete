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
        if (! Schema::hasColumn('configuracoes', 'sync_version')) {
            Schema::table('configuracoes', function (Blueprint $table) {
                $table->unsignedBigInteger('sync_version')->default(1)->after('google_maps_link');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('configuracoes', 'sync_version')) {
            Schema::table('configuracoes', function (Blueprint $table) {
                $table->dropColumn('sync_version');
            });
        }
    }
};
