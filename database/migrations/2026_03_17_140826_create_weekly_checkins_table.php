<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weekly_checkins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedSmallInteger('week_number');
            $table->unsignedTinyInteger('recovery');
            $table->string('recovery_note', 100)->nullable();
            $table->unsignedTinyInteger('training_feel');
            $table->string('training_feel_note', 100)->nullable();
            $table->unsignedTinyInteger('sleep_quality');
            $table->string('sleep_quality_note', 100)->nullable();
            $table->unsignedTinyInteger('stress');
            $table->string('stress_note', 100)->nullable();
            $table->unsignedTinyInteger('progression');
            $table->string('progression_note', 100)->nullable();
            $table->unsignedTinyInteger('soreness');
            $table->string('soreness_note', 100)->nullable();
            $table->unsignedTinyInteger('motivation');
            $table->string('motivation_note', 100)->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'week_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weekly_checkins');
    }
};
