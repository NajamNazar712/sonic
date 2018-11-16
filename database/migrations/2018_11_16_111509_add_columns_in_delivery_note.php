<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsInDeliveryNote extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('delivery_notes', function (Blueprint $table) {
            $table->renameColumn('last_updated','last_updated_at');
            $table->timestamp('status_updated_at')->nullable();
            $table->timestamp('status_verified_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('delivery_notes', function (Blueprint $table) {
            $table->renameColumn('last_updated_at','last_updated');
            $table->dropColumn('status_updated_at');
            $table->dropColumn('status_verified_at');
        });
    }
}
