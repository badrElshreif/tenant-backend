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
        Schema::connection('tenant')->table('offers', function (Blueprint $table) {
            $table->foreign(['created_by'])->references(['id'])->on('admins')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['free_product_id'])->references(['id'])->on('products')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['store_id'])->references(['id'])->on('stores')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('tenant')->table('offers', function (Blueprint $table) {
            $table->dropForeign('offers_created_by_foreign');
            $table->dropForeign('offers_free_product_id_foreign');
            $table->dropForeign('offers_store_id_foreign');
        });
    }
};
