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
        Schema::connection('tenant')->create('offers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('type', ['percentage', 'value', 'free_product'])->default('percentage');
            $table->decimal('value', 13, 3)->nullable();
            $table->unsignedBigInteger('free_product_id')->nullable()->index('offers_free_product_id_foreign');
            $table->unsignedBigInteger('store_id')->nullable()->index('offers_store_id_foreign');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable()->index('offers_created_by_foreign');
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
        Schema::connection('tenant')->dropIfExists('offers');
    }
};
