<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('intakes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();

            $table->enum('status', ['draft','active','completed'])->default('draft')->index();

            // step => value opslag zoals je UI nu doet
            $table->json('payload')->nullable();

            $table->timestamp('completed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('intakes');
    }
};
