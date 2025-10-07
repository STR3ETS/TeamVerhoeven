<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('client_todo_items', function (Blueprint $table) {
            $table->id();

            // Voor wie is de to-do (de klant)
            $table->foreignId('client_user_id')
                ->constrained('users')->cascadeOnDelete();

            // Wie heeft het aangemaakt (coach of systeem)
            $table->foreignId('created_by_user_id')
                ->nullable()
                ->constrained('users')->nullOnDelete();

            // Wie heeft het afgevinkt (coach)
            $table->foreignId('completed_by_user_id')
                ->nullable()
                ->constrained('users')->nullOnDelete();

            // Inhoud
            $table->string('label', 200);             // bv. "30 min call met coach"
            $table->boolean('is_optional')->default(false);
            $table->unsignedInteger('position')->default(0); // sortering binnen lijst

            // Status
            $table->timestamp('completed_at')->nullable();
            $table->date('due_date')->nullable();
            $table->text('notes')->nullable();

            // Herkomst (handig voor automatische generatie en filtering)
            $table->enum('source', ['system', 'manual'])->default('system');
            $table->enum('package', ['pakket_a','pakket_b','pakket_c'])->nullable(); // alleen bij system
            $table->unsignedSmallInteger('duration_weeks')->nullable();              // 12 of 24 (alleen bij system)

            $table->timestamps();

            // Handige indexen
            $table->index(['client_user_id', 'completed_at']);
            $table->index(['client_user_id', 'position']);
            $table->index(['package', 'duration_weeks']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_todo_items');
    }
};
