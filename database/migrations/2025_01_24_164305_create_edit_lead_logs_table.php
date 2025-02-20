<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('edit_lead_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('lead_id')->nullable()->index();
            $table->string('trax_id')->nullable();
            $table->string('admin_name')->nullable();
            $table->text('edited_fields')->nullable();
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
        Schema::dropIfExists('edit_lead_logs');
    }
};
