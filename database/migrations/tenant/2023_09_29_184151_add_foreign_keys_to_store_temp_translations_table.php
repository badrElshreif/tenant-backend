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
        Schema::connection('tenant')->table('store_temp_translations', function (Blueprint $table) {
            $table->foreign(['store_temp_id'])->references(['id'])->on('store_temps')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('tenant')->table('store_temp_translations', function (Blueprint $table) {
            $table->dropForeign('store_temp_translations_store_temp_id_foreign');
        });
    }
};
