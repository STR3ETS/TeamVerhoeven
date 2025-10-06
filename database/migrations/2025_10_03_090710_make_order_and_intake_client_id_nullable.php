<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ----- INTAKES -----
        // 1) FK tijdelijk droppen (naam meestal 'intakes_client_id_foreign')
        Schema::table('intakes', function (Blueprint $table) {
            try {
                $table->dropForeign(['client_id']);
            } catch (\Throwable $e) {
                // ignore if already dropped or not present
            }
        });

        // 2) client_id nullable maken (zonder doctrine/dbal via raw SQL)
        DB::statement('ALTER TABLE `intakes` MODIFY `client_id` BIGINT UNSIGNED NULL');

        // 3) FK opnieuw toevoegen, nullOnDelete
        Schema::table('intakes', function (Blueprint $table) {
            $table->foreign('client_id')
                ->references('id')->on('users')
                ->nullOnDelete();
        });

        // ----- ORDERS -----
        // 1) FK tijdelijk droppen (naam meestal 'orders_client_id_foreign')
        Schema::table('orders', function (Blueprint $table) {
            try {
                $table->dropForeign(['client_id']);
            } catch (\Throwable $e) {
                // ignore if already dropped or not present
            }
        });

        // 2) client_id nullable maken
        DB::statement('ALTER TABLE `orders` MODIFY `client_id` BIGINT UNSIGNED NULL');

        // 3) FK opnieuw toevoegen, nullOnDelete
        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('client_id')
                ->references('id')->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        // LET OP: down() faalt als er rijen met NULL client_id bestaan.
        // Maak ze eerst non-null of verwijder ze vóór je down runt.

        // ----- INTAKES -----
        Schema::table('intakes', function (Blueprint $table) {
            try {
                $table->dropForeign(['client_id']);
            } catch (\Throwable $e) {}
        });
        DB::statement('ALTER TABLE `intakes` MODIFY `client_id` BIGINT UNSIGNED NOT NULL');
        Schema::table('intakes', function (Blueprint $table) {
            $table->foreign('client_id')
                ->references('id')->on('users')
                ->cascadeOnDelete();
        });

        // ----- ORDERS -----
        Schema::table('orders', function (Blueprint $table) {
            try {
                $table->dropForeign(['client_id']);
            } catch (\Throwable $e) {}
        });
        DB::statement('ALTER TABLE `orders` MODIFY `client_id` BIGINT UNSIGNED NOT NULL');
        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('client_id')
                ->references('id')->on('users')
                ->cascadeOnDelete();
        });
    }
};
