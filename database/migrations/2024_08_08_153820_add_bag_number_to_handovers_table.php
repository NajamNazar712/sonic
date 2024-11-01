<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBagNumberToHandoversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('handovers', function (Blueprint $table) {
            $table->unsignedBigInteger('bag_number')->nullable()->after('received');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('handovers', function (Blueprint $table) {
            $table->dropColumn('bag_number');
        });
    }
}
