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
        Schema::connection('tenant')->table('property_option_translations', function (Blueprint $table) {
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
        Schema::connection('tenant')->table('property_option_translations', function (Blueprint $table) {
            $table->dropForeign('property_option_translations_property_option_id_foreign');
        });
    }
};
