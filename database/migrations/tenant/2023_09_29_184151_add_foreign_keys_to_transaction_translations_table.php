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
        Schema::connection('tenant')->table('transaction_translations', function (Blueprint $table) {
            $table->foreign(['transaction_id'])->references(['id'])->on('transactions')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('tenant')->table('transaction_translations', function (Blueprint $table) {
            $table->dropForeign('transaction_translations_transaction_id_foreign');
        });
    }
};
