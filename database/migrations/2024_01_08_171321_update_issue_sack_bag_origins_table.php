<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateIssueSackBagOriginsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::table('issue_sack_bag_origins',function(Blueprint $table){
            $table->integer('sack_destination_id')->nullable()->after('origin'); 
            $table->integer('sack_status_id')->default(1)->after('sack_destination_id');
            $table->integer('bag_count')->nullable()->after('remarks');
            $table->timestamp('reporting_date')->nullable();
       });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('issue_sack_bag_origins', function (Blueprint $table) {
            $table->dropColumn('sack_destination_id');
            $table->dropColumn('sack_status_id');
            $table->dropColumn('bag_count');
            $table->dropColumn('reporting_date');

        });
    }
}
