<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ShipperAccountScreenColumnIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->index('rates_updated_by');
            $table->index('account_type_id');
            $table->index('email_verified');
        });

        Schema::table('sale_person_tags', function (Blueprint $table) {
            $table->index('admin_id');
            $table->index('user_id');
            $table->index('status');
            $table->index('created_at');
            $table->index('updated_at');
        });

        Schema::table('duplicate_users', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('created_at');
            $table->index('updated_at');
        });

        Schema::table('international_users_informations', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('status');
            $table->index('created_at');
            $table->index('updated_at');
        });

        Schema::table('user_document_attachments', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('uploaded_at');
            $table->index('uploaded_by');
            $table->index('approved_at');
            $table->index('approved_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['rates_updated_by']);
            $table->dropIndex(['account_type_id']);
            $table->dropIndex(['email_verified']);
        });

        Schema::table('sale_person_tags', function (Blueprint $table) {
            $table->dropIndex(['admin_id']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
        });

        Schema::table('duplicate_users', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
        });

        Schema::table('international_users_informations', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
        });

        Schema::table('user_document_attachments', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['uploaded_at']);
            $table->dropIndex(['uploaded_by']);
            $table->dropIndex(['approved_at']);
            $table->dropIndex(['approved_by']);
        });
    }
}
