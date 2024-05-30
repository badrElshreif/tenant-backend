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
        Schema::connection('tenant')->table('ad_translations', function (Blueprint $table) {
            $table->foreign(['ad_id'])->references(['id'])->on('ads')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('tenant')->table('ad_translations', function (Blueprint $table) {
            $table->dropForeign('ad_translations_ad_id_foreign');
        });
    }
};
