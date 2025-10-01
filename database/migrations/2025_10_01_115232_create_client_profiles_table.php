<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('client_profiles', function (Blueprint $table) {
            $table->id();

            // De client zelf
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();

            // 1 coach per client
            $table->foreignId('coach_id')->nullable()->constrained('users')->nullOnDelete()->index();

            // Basis
            $table->date('birthdate')->nullable();
            $table->enum('gender', ['m','f'])->nullable();

            // Adres als JSON om flexibel te blijven: {street, zipcode, city, country}
            $table->json('address')->nullable();

            // Metingen
            $table->decimal('height_cm', 5, 2)->nullable();
            $table->decimal('weight_kg', 5, 2)->nullable();

            // Intake-velden
            $table->json('goals')->nullable();        // array van strings
            $table->json('injuries')->nullable();     // array van strings

            $table->unsignedSmallInteger('period_weeks')->default(12);

            // { sessions_per_week, minutes_per_session }
            $table->json('frequency')->nullable();

            $table->text('background')->nullable();
            $table->text('facilities')->nullable();
            $table->text('materials')->nullable();
            $table->text('work_hours')->nullable();

            // { resting, max }
            $table->json('heartrate')->nullable();

            // { meters }
            $table->json('test_12min')->nullable();

            // { minutes, seconds }
            $table->json('test_5k')->nullable();

            // Intake keuze
            $table->enum('coach_preference', ['eline','nicky','roy','none'])->default('none');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_profiles');
    }
};
