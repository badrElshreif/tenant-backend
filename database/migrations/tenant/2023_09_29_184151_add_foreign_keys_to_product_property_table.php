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
        Schema::connection('tenant')->table('product_property', function (Blueprint $table) {
            $table->foreign(['product_id'])->references(['id'])->on('products')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['property_id'])->references(['id'])->on('properties')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['property_option_id'])->references(['id'])->on('property_options')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('tenant')->table('product_property', function (Blueprint $table) {
            $table->dropForeign('product_property_product_id_foreign');
            $table->dropForeign('product_property_property_id_foreign');
            $table->dropForeign('product_property_property_option_id_foreign');
        });
    }
};
