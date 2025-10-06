<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_profiles', function (Blueprint $table) {
            // bestaand: test_12min (json), test_5k (json)
            $table->json('test_10k')->nullable()->after('test_5k');          // { minutes, seconds }
            $table->json('test_marathon')->nullable()->after('test_10k');    // { hours, minutes, seconds }
        });
    }

    public function down(): void
    {
        Schema::table('client_profiles', function (Blueprint $table) {
            $table->dropColumn(['test_10k', 'test_marathon']);
        });
    }
};
