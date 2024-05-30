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
        Schema::connection('tenant')->table('side_menu_link_translations', function (Blueprint $table) {
            $table->foreign(['side_menu_link_id'])->references(['id'])->on('side_menu_links')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('tenant')->table('side_menu_link_translations', function (Blueprint $table) {
            $table->dropForeign('side_menu_link_translations_side_menu_link_id_foreign');
        });
    }
};
