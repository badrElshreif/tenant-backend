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
        Schema::connection('tenant')->create('ratings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('rate');
            $table->text('comment')->nullable();
            $table->unsignedBigInteger('product_id')->index('ratings_product_id_foreign');
            $table->unsignedBigInteger('user_id')->index('ratings_user_id_foreign');
            $table->boolean('is_active')->default(false);
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
        Schema::connection('tenant')->dropIfExists('ratings');
    }
};
