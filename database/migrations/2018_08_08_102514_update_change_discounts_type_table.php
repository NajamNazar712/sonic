<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateChangeDiscountsTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('discount_charges', function (Blueprint $table) {
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
        Schema::table('discount_charges', function (Blueprint $table) {
            $table->integer('weight')->change();
            $table->integer('cash')->change();
            $table->integer('insurance')->change();
            $table->integer('return')->change();
            $table->integer('packaging')->change();
        });
    }
}
