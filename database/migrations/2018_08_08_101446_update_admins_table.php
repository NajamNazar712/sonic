<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn('username');
            $table->dropColumn('city_id');
            $table->dropColumn('department');

            $table->string('phone_number')->after('email');
            $table->string('cnic')->after('phone_number');
            $table->integer('role_id')->after('cnic');
            $table->integer('updated_by')->nullable();
            $table->tinyinteger('status')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn('phone_number');
            $table->dropColumn('cnic');
            $table->dropColumn('role_id');
            $table->dropColumn('updated_by');
            $table->dropColumn('status');

            $table->string('username')->unique()->after('name');
            $table->integer('city_id')->after('password');
            $table->string('department')->after('city_id');
        });
    }
}
