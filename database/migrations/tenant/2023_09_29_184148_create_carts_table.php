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
        Schema::connection('tenant')->create('carts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('store_id')->nullable()->index('carts_store_id_foreign');
            $table->unsignedBigInteger('user_id')->nullable()->index('carts_user_id_foreign');
            $table->unsignedBigInteger('product_id')->index('carts_product_id_foreign');
            $table->unsignedBigInteger('warranty_id')->nullable()->index('carts_warranty_id_foreign');
            $table->integer('quantity')->default(1);
            $table->string('device_id')->nullable();
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
        Schema::connection('tenant')->dropIfExists('carts');
    }
};
