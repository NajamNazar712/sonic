<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSalesTierTagsTableForIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sale_tier_tags', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('poc');
            $table->index('kam');
            $table->index('ref');
        });

        Schema::table('sale_tier_tag_histories', function (Blueprint $table) {
            $table->index('sale_tier_tag_id');
            $table->index('user_id');
            $table->index('poc');
            $table->index('kam');
            $table->index('ref');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sale_tier_tags', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['poc']);
            $table->dropIndex(['kam']);
            $table->dropIndex(['ref']);
        });

        Schema::table('sale_tier_tag_histories', function (Blueprint $table) {
            $table->dropIndex(['sale_tier_tag_id']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['poc']);
            $table->dropIndex(['kam']);
            $table->dropIndex(['ref']);
        });

    }
}
