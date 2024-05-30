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
        Schema::connection('tenant')->table('setting_translations', function (Blueprint $table) {
            $table->foreign(['setting_id'])->references(['id'])->on('settings')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('tenant')->table('setting_translations', function (Blueprint $table) {
            $table->dropForeign('setting_translations_setting_id_foreign');
        });
    }
};
