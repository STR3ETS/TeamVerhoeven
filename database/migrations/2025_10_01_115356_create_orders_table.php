<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('intake_id')->nullable()->constrained('intakes')->nullOnDelete();

            $table->unsignedSmallInteger('period_weeks'); // 12 of 24

            // prijzen in centen, currency EUR
            $table->integer('amount_cents');
            $table->string('currency', 3)->default('EUR');

            $table->string('provider')->default('stripe');
            $table->string('provider_ref')->nullable();

            $table->enum('status', ['pending','paid','failed','canceled'])->default('pending')->index();
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
