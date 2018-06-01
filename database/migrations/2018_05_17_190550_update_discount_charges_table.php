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
        Schema::table('discount_charges', function (Blueprint $table) {
            $table->string('title');
            $table->integer('weight')->default(0)->change();
            $table->integer('cash')->default(0)->change();
            $table->integer('insurance')->default(0)->change();
            $table->integer('return')->default(0)->change();
            $table->integer('packaging')->default(0)->change();
            $table->integer('added_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('discount_charges', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->decimal('weight',8,2)->nullable();
            $table->decimal('cash',8,2)->nullable();
            $table->decimal('insurance',8,2)->nullable();
            $table->decimal('return',8,2)->nullable();
            $table->decimal('packaging',8,2)->nullable();
            $table->dropColumn('added_by');
        });
    }
}
