<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_profiles', function (Blueprint $table) {
            // Slaat doelwedstrijd op als { distance: string|null, time_hms: string|null, date: string|null(YYYY-MM-DD) }
            $table->json('goal')->nullable()->after('test_5k');
        });
    }

    public function down(): void
    {
        Schema::table('client_profiles', function (Blueprint $table) {
            $table->dropColumn('goal');
        });
    }
};
