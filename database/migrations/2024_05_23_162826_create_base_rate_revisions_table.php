<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBaseRateRevisionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('base_rate_revisions', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('rate_type_id')->index();
            $table->integer('added_by_admin_id')->index();
            $table->integer('approval1_by_admin_id')->nullable()->index();
            $table->timestamp('approval1_at')->nullable();
            $table->tinyInteger('approval1_status')->default(1);
            $table->integer('approval2_by_admin_id')->nullable()->index();
            $table->timestamp('approval2_at')->nullable();
            $table->tinyInteger('approval2_status')->default(1);
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
        Schema::dropIfExists('base_rate_revisions');
    }
}
