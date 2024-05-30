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
        Schema::connection('tenant')->create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('quantity')->nullable();
            $table->decimal('price', 13, 3);
            $table->boolean('approved')->default(false);
            $table->unsignedBigInteger('category_id')->index('products_category_id_foreign');
            $table->unsignedBigInteger('brand_id')->nullable()->index('products_brand_id_foreign');
            $table->unsignedBigInteger('store_id')->index('products_store_id_foreign');
            $table->string('image')->nullable();
            $table->string('barcode')->nullable();
            $table->unsignedBigInteger('made_in')->nullable()->index('products_made_in_foreign');
            $table->decimal('preview_fees', 13, 3)->nullable();
            $table->unsignedBigInteger('max_purchase_quantity')->default(0);
            $table->boolean('is_active')->default(true);
            $table->date('deactivation_start_date')->nullable();
            $table->date('deactivation_end_date')->nullable();
            $table->unsignedBigInteger('created_by')->nullable()->index('products_created_by_foreign');
            $table->timestamps();
            $table->string('catalog')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('tenant')->dropIfExists('products');
    }
};
