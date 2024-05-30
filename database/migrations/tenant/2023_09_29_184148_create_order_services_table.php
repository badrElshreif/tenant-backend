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
        Schema::connection('tenant')->create('order_services', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id')->index('order_services_order_id_foreign');
            $table->unsignedBigInteger('service_id')->index('order_services_service_id_foreign');
            $table->unsignedBigInteger('subcategory_id')->nullable()->index('order_services_subcategory_id_foreign');
            $table->text('problem_description')->nullable();
            $table->decimal('service_price', 13, 3);
            $table->decimal('preview_fees', 13, 3);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('tenant')->dropIfExists('order_services');
    }
};
