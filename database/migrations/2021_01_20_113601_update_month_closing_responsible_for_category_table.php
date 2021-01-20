<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMonthClosingResponsibleForCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('month_closing_responsibles', function (Blueprint $table) {
            $table->integer('admin')->after('month_closing_id')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('month_closing_responsibles', function (Blueprint $table) {
            $table->dropColumn('admin');
        });
    }
}
