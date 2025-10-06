<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('threads', function (Blueprint $table) {
            $table->id();

            // client en coach als users.*
            $table->foreignIdFor(User::class, 'client_user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->foreignIdFor(User::class, 'coach_user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->string('subject')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('threads');
    }
};
