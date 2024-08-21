<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToRetailUserFamilyInformationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('retail_user_family_informations', function (Blueprint $table) {
            $table->integer('salary')->nullable()->after('family_member_name');
            $table->string('agreement_start_date')->nullable()->after('salary');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('retail_user_family_informations', function (Blueprint $table) {
            $table->dropColumn('salary');
            $table->dropColumn('agreement_start_date');
        });
    }
}
