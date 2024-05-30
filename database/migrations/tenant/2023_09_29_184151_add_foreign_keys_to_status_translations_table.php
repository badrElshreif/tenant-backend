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
        Schema::connection('tenant')->table('status_translations', function (Blueprint $table) {
            $table->foreign(['status_id'])->references(['id'])->on('statuses')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('tenant')->table('status_translations', function (Blueprint $table) {
            $table->dropForeign('status_translations_status_id_foreign');
        });
    }
};
