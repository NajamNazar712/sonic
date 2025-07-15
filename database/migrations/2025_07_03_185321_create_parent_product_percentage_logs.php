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
        Schema::create('parent_product_percentage_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('parent_product_id')->nullable();
            $table->string('field_changed', 20); // 'tax_percentage' or 'sst_percentage'
            $table->decimal('old_value', 10, 2)->nullable();
            $table->decimal('new_value', 10, 2)->nullable();
            $table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('parent_product_percentage_logs');
    }
};
