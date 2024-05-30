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
        Schema::connection('tenant')->create('bank_transfers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('depositor_name')->nullable();
            $table->decimal('deposit_amount', 13, 3)->default(0);
            $table->string('deposit_receipt')->nullable();
            $table->unsignedBigInteger('order_id')->index('bank_transfers_order_id_foreign');
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('bank_account_id')->nullable()->index('bank_transfers_bank_account_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('tenant')->dropIfExists('bank_transfers');
    }
};
