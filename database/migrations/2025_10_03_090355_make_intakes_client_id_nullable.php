<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('intakes', function (Blueprint $table) {
            // haal constraint weg als hij bestaat (afhankelijk van jouw FK-naam)
            try { $table->dropForeign(['client_id']); } catch (\Throwable $e) {}

            // maak kolom nullable
            $table->unsignedBigInteger('client_id')->nullable()->change();

            // zet FK terug, ON DELETE SET NULL is handig
            $table->foreign('client_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('intakes', function (Blueprint $table) {
            try { $table->dropForeign(['client_id']); } catch (\Throwable $e) {}
            // let op: alleen terugzetten naar notNullable als je zeker weet dat er geen nulls meer zijn
            $table->unsignedBigInteger('client_id')->nullable(false)->change();
            $table->foreign('client_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
