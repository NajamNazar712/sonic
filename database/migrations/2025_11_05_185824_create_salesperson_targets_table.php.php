<?php 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('salesperson_target_segments', function (Blueprint $table) {
            $table->id();
            $table->string('salesperson_id'); // store Trax ID or user id as string
            $table->foreignId('segment_id')->nullable();
            $table->date('start_date')->nullable()->index();
            $table->date('end_date')->nullable()->index();
            $table->boolean('is_active')->default(1);

            // targets
            $table->integer('target_shipments_day')->nullable();
            $table->decimal('revenue_target_day', 15, 2)->nullable();
            $table->integer('target_shipments_month')->nullable();
            $table->decimal('revenue_target_month', 15, 2)->nullable();
            $table->decimal('avg_rps', 12, 2)->nullable();
            $table->decimal('avg_rpk', 12, 2)->nullable();

            // achieved (optional)
            $table->integer('achieved_shipments_day')->nullable();
            $table->decimal('achieved_revenue_day', 15, 2)->nullable();
            $table->integer('achieved_shipments_month')->nullable();
            $table->decimal('achieved_revenue_month', 15, 2)->nullable();
            $table->decimal('achieved_rps', 12, 2)->nullable();
            $table->decimal('achieved_rpk', 12, 2)->nullable();

            $table->timestamps();

            // unique constraint
            $table->unique(['salesperson_id', 'segment_id', 'start_date', 'end_date'], 'salesperson_segment_month_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('salesperson_target_segments');
    }
};
