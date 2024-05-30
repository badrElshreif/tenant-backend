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
        Schema::connection('tenant')->table('online_payment_method_translations', function (Blueprint $table) {
            $table->foreign(['payment_id'])->references(['id'])->on('online_payment_methods')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('tenant')->table('online_payment_method_translations', function (Blueprint $table) {
            $table->dropForeign('online_payment_method_translations_payment_id_foreign');
        });
    }
};
