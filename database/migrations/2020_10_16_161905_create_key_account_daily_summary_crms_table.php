<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKeyAccountDailySummaryCrmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('key_account_daily_summary_crms', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('admin_id');
            $table->integer('case_nature_type_id');
            $table->integer('channel_id');
            $table->integer('count');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('key_account_daily_summary_crms');
    }
}
