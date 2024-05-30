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
        Schema::connection('tenant')->table('warranty_translations', function (Blueprint $table) {
            $table->foreign(['warranty_id'])->references(['id'])->on('warranties')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('tenant')->table('warranty_translations', function (Blueprint $table) {
            $table->dropForeign('warranty_translations_warranty_id_foreign');
        });
    }
};
