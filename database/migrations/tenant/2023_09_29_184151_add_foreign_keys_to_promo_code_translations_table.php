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
        Schema::connection('tenant')->table('promo_code_translations', function (Blueprint $table) {
            $table->foreign(['promo_code_id'])->references(['id'])->on('promo_codes')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('tenant')->table('promo_code_translations', function (Blueprint $table) {
            $table->dropForeign('promo_code_translations_promo_code_id_foreign');
        });
    }
};
