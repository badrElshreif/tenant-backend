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
        Schema::connection('tenant')->table('bank_transfers', function (Blueprint $table) {
            $table->foreign(['bank_account_id'])->references(['id'])->on('bank_accounts')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['order_id'])->references(['id'])->on('orders')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('tenant')->table('bank_transfers', function (Blueprint $table) {
            $table->dropForeign('bank_transfers_bank_account_id_foreign');
            $table->dropForeign('bank_transfers_order_id_foreign');
        });
    }
};
