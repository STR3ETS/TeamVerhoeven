<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('intake_step_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('intake_id')->constrained('intakes')->cascadeOnDelete();

            $table->string('step');
            $table->json('value')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('intake_step_logs');
    }
};
