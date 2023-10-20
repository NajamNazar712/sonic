<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexesToManifestBagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('manifest_bags', function (Blueprint $table) {
            $table->index('cargo_manifest_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('manifest_bags', function (Blueprint $table) {
            $table->dropIndex(['cargo_manifest_id']);
        });
    }
}
