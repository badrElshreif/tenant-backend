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
        Schema::connection('tenant')->table('offer_product', function (Blueprint $table) {
            $table->foreign(['offer_id'])->references(['id'])->on('offers')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['product_id'])->references(['id'])->on('products')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('tenant')->table('offer_product', function (Blueprint $table) {
            $table->dropForeign('offer_product_offer_id_foreign');
            $table->dropForeign('offer_product_product_id_foreign');
        });
    }
};
