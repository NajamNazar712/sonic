<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSalePersonNumbersColumnsSize extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sale_person_numbers', function (Blueprint $table) {
            $table->decimal('revenue', 20,2)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sale_person_numbers', function (Blueprint $table) {
            $table->decimal('revenue', 8,2)->change();
        });
    }
}
