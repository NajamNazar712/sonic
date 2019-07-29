<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateDiscountChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pending_discount_charges', function (Blueprint $table) {
            $table->string('weight')->change();
            $table->string('cash')->change();
            $table->string('insurance')->change();
            $table->string('return')->change();
            $table->string('packaging')->change();
        });

        Schema::table('history_discount_charges', function (Blueprint $table) {
            $table->string('weight')->change();
            $table->string('cash')->change();
            $table->string('insurance')->change();
            $table->string('return')->change();
            $table->string('packaging')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pending_discount_charges', function (Blueprint $table) {
            $table->decimal('weight',8,2)->nullable()->change();
            $table->decimal('cash',8,2)->nullable()->change();
            $table->decimal('insurance',8,2)->nullable()->change();
            $table->decimal('return',8,2)->nullable()->change();
            $table->decimal('packaging',8,2)->nullable()->change();
        });

        Schema::table('history_discount_charges', function (Blueprint $table) {
            $table->decimal('weight',8,2)->nullable()->change();
            $table->decimal('cash',8,2)->nullable()->change();
            $table->decimal('insurance',8,2)->nullable()->change();
            $table->decimal('return',8,2)->nullable()->change();
            $table->decimal('packaging',8,2)->nullable()->change();
        });
    }
}
