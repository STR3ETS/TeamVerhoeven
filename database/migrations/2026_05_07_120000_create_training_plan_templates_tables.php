<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_plan_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('injury_type')->nullable(); // 'knee', null = general
            $table->unsignedSmallInteger('max_days'); // 5 or 6
            $table->unsignedSmallInteger('weeks')->default(12);
            $table->timestamps();
        });

        Schema::create('training_plan_template_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('training_plan_templates')->cascadeOnDelete();
            $table->unsignedSmallInteger('week');
            $table->string('day', 3); // mon, tue, wed, thu, fri, sat, sun
            $table->foreignId('training_card_id')->constrained('training_cards')->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['template_id', 'week', 'day']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_plan_template_items');
        Schema::dropIfExists('training_plan_templates');
    }
};
