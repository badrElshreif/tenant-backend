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
        Schema::connection('tenant')->create('transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->index('transactions_user_id_foreign');
            $table->enum('type', ['pay_in', 'pay_out']);
            $table->decimal('amount', 13, 3);
            $table->decimal('wallet_total', 13, 3)->nullable();
            $table->string('transactable_type');
            $table->unsignedBigInteger('transactable_id');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['transactable_type', 'transactable_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('tenant')->dropIfExists('transactions');
    }
};
