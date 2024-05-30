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
        Schema::connection('tenant')->create('categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('parent_id')->nullable()->index('categories_parent_id_foreign');
            $table->unsignedBigInteger('order')->default(1);
            $table->string('image')->nullable();
            $table->double('tax_percentage', 8, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->enum('type', ['stores', 'centers'])->default('stores');
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
        Schema::connection('tenant')->dropIfExists('categories');
    }
};
