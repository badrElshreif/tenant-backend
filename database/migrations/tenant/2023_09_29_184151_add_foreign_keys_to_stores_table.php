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
        Schema::connection('tenant')->table('stores', function (Blueprint $table) {
            $table->foreign(['city_id'])->references(['id'])->on('cities')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['country_id'])->references(['id'])->on('countries')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['seller_id'])->references(['id'])->on('admins')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['state_id'])->references(['id'])->on('states')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('tenant')->table('stores', function (Blueprint $table) {
            $table->dropForeign('stores_city_id_foreign');
            $table->dropForeign('stores_country_id_foreign');
            $table->dropForeign('stores_seller_id_foreign');
            $table->dropForeign('stores_state_id_foreign');
        });
    }
};
