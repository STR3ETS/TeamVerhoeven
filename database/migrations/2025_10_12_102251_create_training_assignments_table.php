<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void {
        Schema::create('training_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();            // client
            $table->foreignId('training_card_id')->constrained('training_cards')->cascadeOnDelete();
            $table->string('day', 3);         // mon|tue|wed|thu|fri|sat|sun
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['user_id','day']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('training_assignments');
    }
};
