<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('training_plan_templates', function (Blueprint $table) {
            $table->string('level', 30)->default('beginner')->after('slug');
        });

        // Bestaande templates zijn allemaal beginner-niveau
        DB::table('training_plan_templates')
            ->whereNull('level')
            ->orWhere('level', '')
            ->update(['level' => 'beginner']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('training_plan_templates', function (Blueprint $table) {
            $table->dropColumn('level');
        });
    }
};
