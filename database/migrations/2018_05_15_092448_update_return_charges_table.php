<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateReturnChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('return_charges', function (Blueprint $table) {
            $table->integer('local')->change();
            $table->integer('national')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('return_charges', function (Blueprint $table) {
            $table->decimal('local', 8, 2);
            $table->decimal('national', 8, 2);
        });
    }
}
