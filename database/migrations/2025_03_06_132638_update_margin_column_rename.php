<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('international_user_rates', function (Blueprint $table) {
            $table->renameColumn('margin_1b', 'margin_12');
            $table->renameColumn('margin_8b', 'margin_13');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('international_user_rates', function (Blueprint $table) {
            $table->renameColumn('margin_12', 'margin_1b');
            $table->renameColumn('margin_13', 'margin_8b');
        });
    }
};
