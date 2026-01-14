<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Simpele tabel om bij te houden of een user ooit verlengd heeft.
     * Geen count - alleen presence check voor "Verlenging" label in dashboard.
     */
    public function up(): void
    {
        Schema::create('subscription_renewals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->timestamp('first_renewed_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_renewals');
    }
};
