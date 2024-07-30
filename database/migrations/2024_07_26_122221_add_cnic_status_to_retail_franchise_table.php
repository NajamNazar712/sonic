<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCnicStatusToRetailFranchiseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('retail_franchises', function (Blueprint $table) {
            $table->tinyInteger('cnic_status')->nullable()->after('cnic');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('retail_franchises', function (Blueprint $table) {
            $table->dropColumn('cnic_status');
        });
    }
}
