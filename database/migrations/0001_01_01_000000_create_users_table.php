<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();

            $table->string('password');

            // Rollen: client, coach, admin
            $table->enum('role', ['client', 'coach', 'admin'])->default('client')->index();

            $table->rememberToken();
            $table->timestamps();

            // Als je multi-tenant gebruikt, voeg hier tenant_id toe en index
            // $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
