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
        Schema::connection('tenant')->table('state_translations', function (Blueprint $table) {
            $table->foreign(['state_id'])->references(['id'])->on('states')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('tenant')->table('state_translations', function (Blueprint $table) {
            $table->dropForeign('state_translations_state_id_foreign');
        });
    }
};
