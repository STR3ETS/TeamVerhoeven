<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void {
        Schema::create('access_keys', function (Blueprint $table) {
            $table->id();
            $table->string('key', 128)->unique();              // bv. dw821jhdwauhuidaw124
            $table->enum('package', ['pakket_a','pakket_b','pakket_c']);
            $table->unsignedTinyInteger('duration_weeks');      // 12 of 24
            $table->boolean('active')->default(true);
            $table->unsignedInteger('uses_limit')->nullable();  // null = onbeperkt
            $table->unsignedInteger('uses_count')->default(0);
            $table->timestamp('valid_until')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('access_keys');
    }
};
