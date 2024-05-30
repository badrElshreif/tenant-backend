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
        Schema::connection('tenant')->create('ads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ad_url')->nullable();
            $table->string('image');
            $table->unsignedBigInteger('category_id')->nullable()->index('ads_category_id_foreign');
            $table->boolean('is_active')->default(true);
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
        Schema::connection('tenant')->dropIfExists('ads');
    }
};
