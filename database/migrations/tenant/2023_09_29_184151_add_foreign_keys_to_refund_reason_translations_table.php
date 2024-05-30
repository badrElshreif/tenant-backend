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
        Schema::connection('tenant')->table('refund_reason_translations', function (Blueprint $table) {
            $table->foreign(['refund_reason_id'])->references(['id'])->on('refund_reasons')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('tenant')->table('refund_reason_translations', function (Blueprint $table) {
            $table->dropForeign('refund_reason_translations_refund_reason_id_foreign');
        });
    }
};
