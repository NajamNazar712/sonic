<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePackagingMaterialRequestHistoryUpdatedByUserIdTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packaging_material_request_histories', function (Blueprint $table) {
            $table->integer('updated_by_user_id')->nullable();
            $table->integer('updated_by')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('packaging_material_request_histories', function (Blueprint $table) {
            $table->dropColumn('updated_by_user_id');
            $table->integer('updated_by')->nullable(false)->change();
        });
    }
}
