<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCargoManifestBagsAddRemarks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cargo_manifest_bags', function (Blueprint $table) {
            $table->timestamp('remarks_created_at')->nullable();
            $table->timestamp('remarks_updated_at')->nullable();
            $table->integer('remarks_added_by')->nullable()->index();
            $table->text('remarks')->nullable();
            $table->integer('pieces_count')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cargo_manifest_bags', function (Blueprint $table) {
            $table->dropColumn('pieces_count');
            $table->dropColumn('remarks');
            $table->dropColumn('remarks_added_by');
            $table->dropColumn('remarks_created_at');
            $table->dropColumn('remarks_updated_at');
        });
    }
}
