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
        Schema::connection('tenant')->table('property_type_translations', function (Blueprint $table) {
            $table->foreign(['property_type_id'])->references(['id'])->on('property_types')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('tenant')->table('property_type_translations', function (Blueprint $table) {
            $table->dropForeign('property_type_translations_property_type_id_foreign');
        });
    }
};
