<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateInternationalRatesTableForMargins extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('international_user_rates', function (Blueprint $table) {
            $table->renameColumn('margin', 'margin_1');
            $table->decimal('margin_2', 20,2);
            $table->decimal('margin_3', 20,2);
            $table->decimal('margin_4', 20,2);
            $table->decimal('margin_5', 20,2);
            $table->decimal('margin_6', 20,2);
            $table->decimal('margin_7', 20,2);
            $table->decimal('margin_8', 20,2);
            $table->decimal('margin_9', 20,2);
            $table->decimal('margin_10', 20,2);
            $table->decimal('margin_11', 20,2);
        });
        Schema::table('pending_international_user_rates', function (Blueprint $table) {
            $table->renameColumn('margin', 'margin_1');
            $table->decimal('margin_2', 20,2);
            $table->decimal('margin_3', 20,2);
            $table->decimal('margin_4', 20,2);
            $table->decimal('margin_5', 20,2);
            $table->decimal('margin_6', 20,2);
            $table->decimal('margin_7', 20,2);
            $table->decimal('margin_8', 20,2);
            $table->decimal('margin_9', 20,2);
            $table->decimal('margin_10', 20,2);
            $table->decimal('margin_11', 20,2);
        });
        Schema::table('history_international_user_rates', function (Blueprint $table) {
            $table->renameColumn('margin', 'margin_1');
            $table->decimal('margin_2', 20,2);
            $table->decimal('margin_3', 20,2);
            $table->decimal('margin_4', 20,2);
            $table->decimal('margin_5', 20,2);
            $table->decimal('margin_6', 20,2);
            $table->decimal('margin_7', 20,2);
            $table->decimal('margin_8', 20,2);
            $table->decimal('margin_9', 20,2);
            $table->decimal('margin_10', 20,2);
            $table->decimal('margin_11', 20,2);
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
            $table->renameColumn('margin_1', 'margin');
            $table->dropColumn('margin_2');
            $table->dropColumn('margin_3');
            $table->dropColumn('margin_4');
            $table->dropColumn('margin_5');
            $table->dropColumn('margin_6');
            $table->dropColumn('margin_7');
            $table->dropColumn('margin_8');
            $table->dropColumn('margin_9');
            $table->dropColumn('margin_10');
            $table->dropColumn('margin_11');
        });
        Schema::table('pending_international_user_rates', function (Blueprint $table) {
            $table->renameColumn('margin_1', 'margin');
            $table->dropColumn('margin_2');
            $table->dropColumn('margin_3');
            $table->dropColumn('margin_4');
            $table->dropColumn('margin_5');
            $table->dropColumn('margin_6');
            $table->dropColumn('margin_7');
            $table->dropColumn('margin_8');
            $table->dropColumn('margin_9');
            $table->dropColumn('margin_10');
            $table->dropColumn('margin_11');

        });
        Schema::table('history_international_user_rates', function (Blueprint $table) {
            $table->renameColumn('margin_1', 'margin');
            $table->dropColumn('margin_2');
            $table->dropColumn('margin_3');
            $table->dropColumn('margin_4');
            $table->dropColumn('margin_5');
            $table->dropColumn('margin_6');
            $table->dropColumn('margin_7');
            $table->dropColumn('margin_8');
            $table->dropColumn('margin_9');
            $table->dropColumn('margin_10');
            $table->dropColumn('margin_11');

        });
    }
}
