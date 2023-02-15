<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSalePersonTargetSegmentIdTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sale_person_targets', function (Blueprint $table) {
            $table->integer('segment_id')->nullable()->index();
        });
        Schema::table('sale_person_target_deletes', function (Blueprint $table) {
            $table->integer('segment_id')->nullable()->index();
        });
        Schema::table('sale_person_target_logs', function (Blueprint $table) {
            $table->integer('segment_id')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sale_person_targets', function (Blueprint $table) {
            $table->dropIndex('segment_id');
            $table->dropColumn('segment_id');
        });
        Schema::table('sale_person_target_deletes', function (Blueprint $table) {
            $table->dropIndex('segment_id');
            $table->dropColumn('segment_id');
        });
        Schema::table('sale_person_target_logs', function (Blueprint $table) {
            $table->dropIndex('segment_id');
            $table->dropColumn('segment_id');
        });
    }
}
