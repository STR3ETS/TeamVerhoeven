<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('client_profiles', function (Blueprint $table) {
            // E.164 formaat (bijv. +31612345678), max 25 is ruim zat
            $table->string('phone_e164', 25)->nullable()->after('address');
        });
    }

    public function down(): void
    {
        Schema::table('client_profiles', function (Blueprint $table) {
            $table->dropColumn('phone_e164');
        });
    }
};
