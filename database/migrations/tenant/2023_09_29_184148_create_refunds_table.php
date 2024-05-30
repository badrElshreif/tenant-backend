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
        Schema::connection('tenant')->create('refunds', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id')->index('refunds_order_id_foreign');
            $table->unsignedBigInteger('status_id')->index('refunds_status_id_foreign');
            $table->unsignedBigInteger('refund_reason_id')->index('refunds_refund_reason_id_foreign');
            $table->enum('refund_type', ['order', 'items'])->default('items');
            $table->decimal('subtotal', 13, 3);
            $table->decimal('promo_code_discount', 13, 3);
            $table->decimal('total', 13, 3);
            $table->text('note')->nullable();
            $table->text('reason')->nullable();
            $table->unsignedBigInteger('creatable_id')->nullable();
            $table->string('creatable_type')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('tenant')->dropIfExists('refunds');
    }
};
