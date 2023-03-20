<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSalesPersonTragetForAchievementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sale_person_targets', function (Blueprint $table) {
            $table->integer('achieved_shipments')->nullable();
            $table->decimal('achieved_shipments_percentage', 20,2)->nullable();
            $table->integer('achieved_revenue')->nullable();
            $table->decimal('achieved_revenue_percentage', 20,2)->nullable();
        });

        Schema::table('sale_person_target_deletes', function (Blueprint $table) {
            $table->integer('achieved_shipments')->nullable();
            $table->decimal('achieved_shipments_percentage', 20,2)->nullable();
            $table->integer('achieved_revenue')->nullable();
            $table->decimal('achieved_revenue_percentage', 20,2)->nullable();
        });

        Schema::table('sale_person_target_logs', function (Blueprint $table) {
            $table->integer('achieved_shipments')->nullable();
            $table->decimal('achieved_shipments_percentage', 20,2)->nullable();
            $table->integer('achieved_revenue')->nullable();
            $table->decimal('achieved_revenue_percentage', 20,2)->nullable();
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
            $table->dropColumn('achieved_shipments');
            $table->dropColumn('achieved_shipments_percentage');
            $table->dropColumn('achieved_revenue');
            $table->dropColumn('achieved_revenue_percentage');
        });

        Schema::table('sale_person_target_deletes', function (Blueprint $table) {
            $table->dropColumn('achieved_shipments');
            $table->dropColumn('achieved_shipments_percentage');
            $table->dropColumn('achieved_revenue');
            $table->dropColumn('achieved_revenue_percentage');
        });

        Schema::table('sale_person_target_logs', function (Blueprint $table) {
            $table->dropColumn('achieved_shipments');
            $table->dropColumn('achieved_shipments_percentage');
            $table->dropColumn('achieved_revenue');
            $table->dropColumn('achieved_revenue_percentage');
        });
    }
}
