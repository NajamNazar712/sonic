<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateinternationalUserRatesaddcolumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('international_user_rates', function (Blueprint $table) {
            $table->decimal('margin_1b', 20,2);
            $table->decimal('margin_8b', 20,2);
        });
        Schema::table('pending_international_user_rates', function (Blueprint $table) {
            $table->decimal('margin_1b', 20,2);
            $table->decimal('margin_8b', 20,2);
        });
        Schema::table('history_international_user_rates', function (Blueprint $table) {
            $table->decimal('margin_1b', 20,2);
            $table->decimal('margin_8b', 20,2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('international_user_rates', function (Blueprint $table) {
            $table->dropColumn('margin_12');
            $table->dropColumn('margin_13');
        });
        Schema::table('pending_international_user_rates', function (Blueprint $table) {
            $table->dropColumn('margin_12');
            $table->dropColumn('margin_13');

        });
        Schema::table('history_international_user_rates', function (Blueprint $table) {
            $table->dropColumn('margin_12');
            $table->dropColumn('margin_13');

        });
    }

}
