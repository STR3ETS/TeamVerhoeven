<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void {
        Schema::table('client_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('client_profiles','ftp')) {
                $table->json('ftp')->nullable()->after('materials');
            }
            // (optioneel) als je ‘test_10k’ / ‘test_marathon’ nog mist:
            if (!Schema::hasColumn('client_profiles','test_10k')) {
                $table->json('test_10k')->nullable()->after('test_5k');
            }
            if (!Schema::hasColumn('client_profiles','test_marathon')) {
                $table->json('test_marathon')->nullable()->after('test_10k');
            }
            if (!Schema::hasColumn('client_profiles','goal')) {
                $table->json('goal')->nullable()->after('materials');
            }
        });
    }
    public function down(): void {
        Schema::table('client_profiles', function (Blueprint $table) {
            $table->dropColumn(['ftp','test_10k','test_marathon','goal']);
        });
    }
};
