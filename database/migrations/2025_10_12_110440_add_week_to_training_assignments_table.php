<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('training_assignments', function (Blueprint $table) {
            $table->unsignedSmallInteger('week')->default(1)->after('user_id');
            $table->index(['user_id', 'week', 'day']);
        });
    }

    public function down(): void
    {
        Schema::table('training_assignments', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'week', 'day']);
            $table->dropColumn('week');
        });
    }
};
