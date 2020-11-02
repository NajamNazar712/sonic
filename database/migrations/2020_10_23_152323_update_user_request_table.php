<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUserRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('admin_user_requests', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
            $table->string('phone_number')->nullable()->change();
            $table->string('cnic')->nullable()->change();
            $table->string('forwarded_by')->nullable();
            $table->string('forwarded_at')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('admin_user_requests', function (Blueprint $table) {
           $table->dropColumn('forwarded_at');
           $table->dropColumn('forwarded_by');
            $table->string('email')->change();
            $table->string('phone_number')->change();
            $table->string('cnic')->change();
        });
    }
}
