<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_sections', function (Blueprint $table) {
            $table->id();
            $table->string('name');                 // bijv. "Warming-up", "Threshold"
            $table->unsignedInteger('sort_order');  // volgorde in de bibliotheek
            $table->timestamps();
        });

        Schema::create('training_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_section_id')->constrained()->cascadeOnDelete();
            $table->string('title');                // bijv. "Recovery", "Endurance", "Treshold"
            $table->unsignedInteger('sort_order');
            $table->timestamps();
        });

        Schema::create('training_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_card_id')->constrained()->cascadeOnDelete();
            $table->string('label');                // bijv. "Zone 1", "Raise"
            $table->string('badge_classes')->nullable();
            $table->unsignedInteger('sort_order');
            $table->timestamps();
        });

        Schema::create('training_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_block_id')->constrained()->cascadeOnDelete();
            $table->text('left_html');              // HTML toegestaan (zoals in jouw blade)
            $table->string('right_text')->nullable();
            $table->unsignedInteger('sort_order');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_items');
        Schema::dropIfExists('training_blocks');
        Schema::dropIfExists('training_cards');
        Schema::dropIfExists('training_sections');
    }
};
