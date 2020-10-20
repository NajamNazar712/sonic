<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAddressesFor255CharTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->string('consignee_address', 255)->change();
        });
        Schema::table('misrouted_history', function (Blueprint $table) {
            $table->string('old_consignee_address', 255)->change();
            $table->string('new_consignee_address', 255)->change();
        });
        Schema::table('consignee_infos', function (Blueprint $table) {
            $table->string('address', 255)->change();
        });
        Schema::table('shipment_information_logs', function (Blueprint $table) {
            $table->string('old_consignee_address', 255)->change();
            $table->string('new_consignee_address', 255)->change();
        });
        Schema::table('consignee_informations', function (Blueprint $table) {
            $table->string('address', 255)->change();
        });
        Schema::table('intercept_re_book_request_histories', function (Blueprint $table) {
            $table->string('old_consignee_address', 255)->change();
            $table->string('new_consignee_address', 255)->change();
        });
        Schema::table('intercept_re_book_requests', function (Blueprint $table) {
            $table->string('consignee_address', 255)->change();
        });
        Schema::table('daily_visits', function (Blueprint $table) {
            $table->string('customer_address', 255)->change();
        });
        Schema::table('consignee_information_logs', function (Blueprint $table) {
            $table->string('address', 255)->change();
        });
        Schema::table('consignee_locations', function (Blueprint $table) {
            $table->string('address', 255)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->string('consignee_address', 191)->change();
        });
        Schema::table('misrouted_history', function (Blueprint $table) {
            $table->string('old_consignee_address', 191)->change();
            $table->string('new_consignee_address', 191)->change();
        });
        Schema::table('consignee_infos', function (Blueprint $table) {
            $table->string('address', 191)->change();
        });
        Schema::table('shipment_information_logs', function (Blueprint $table) {
            $table->string('old_consignee_address', 191)->change();
            $table->string('new_consignee_address', 191)->change();
        });
        Schema::table('consignee_informations', function (Blueprint $table) {
            $table->string('address', 191)->change();
        });
        Schema::table('intercept_re_book_request_histories', function (Blueprint $table) {
            $table->string('old_consignee_address', 191)->change();
            $table->string('new_consignee_address', 191)->change();
        });
        Schema::table('intercept_re_book_requests', function (Blueprint $table) {
            $table->string('consignee_address', 191)->change();
        });
        Schema::table('daily_visits', function (Blueprint $table) {
            $table->string('customer_address', 191)->change();
        });
        Schema::table('consignee_information_logs', function (Blueprint $table) {
            $table->string('address', 191)->change();
        });
        Schema::table('consignee_locations', function (Blueprint $table) {
            $table->string('address', 191)->change();
        });
    }
}
