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
        Schema::connection('tenant')->create('order_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id')->index('order_items_order_id_foreign');
            $table->unsignedBigInteger('product_id')->index('order_items_product_id_foreign');
            $table->unsignedBigInteger('free_product_id')->nullable()->index('order_items_free_product_id_foreign');
            $table->unsignedBigInteger('offer_id')->nullable()->index('order_items_offer_id_foreign');
            $table->unsignedBigInteger('warranty_id')->nullable()->index('order_items_warranty_id_foreign');
            $table->integer('quantity');
            $table->decimal('product_price', 13, 3);
            $table->decimal('warranty_price', 13, 3);
            $table->decimal('offer_discount', 13, 3);
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
        Schema::connection('tenant')->dropIfExists('order_items');
    }
};
