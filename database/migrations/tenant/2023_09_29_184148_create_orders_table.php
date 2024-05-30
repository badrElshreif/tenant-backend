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
        Schema::connection('tenant')->create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('status_id')->index('orders_status_id_foreign');
            $table->unsignedBigInteger('user_address_id')->index('orders_user_address_id_foreign');
            $table->unsignedBigInteger('payment_method_id')->index('orders_payment_method_id_foreign');
            $table->unsignedBigInteger('store_id')->index('orders_store_id_foreign');
            $table->unsignedBigInteger('user_id')->index('orders_user_id_foreign');
            $table->date('service_wanted_date')->nullable();
            $table->decimal('subtotal', 13, 3);
            $table->decimal('offer_discount', 13, 3)->default(0);
            $table->decimal('delivery_charge', 13, 3)->default(0);
            $table->decimal('order_added_tax', 13, 3)->default(0);
            $table->decimal('warranties_amount', 13, 3)->default(0);
            $table->decimal('promo_code_discount', 13, 3)->default(0);
            $table->decimal('wallet_payout', 13, 3)->default(0);
            $table->decimal('remain', 13, 3)->default(0);
            $table->decimal('total', 13, 3);
            $table->decimal('application_dues', 13, 3)->default(0);
            $table->string('promo_code_id')->nullable();
            $table->string('address')->nullable();
            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->enum('type', ['stores', 'centers'])->default('stores');
            $table->boolean('is_paid')->default(true);
            $table->string('invoice_file')->nullable();
            $table->string('qr_code')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->decimal('application_dues_percentage', 12)->default(0);
            $table->unsignedBigInteger('payment_id')->nullable()->index('orders_payment_id_foreign');
            $table->boolean('store_has_delivery_service')->default(false);
            $table->string('transaction_id')->nullable();
            $table->string('payment_method_brand')->nullable();
            $table->string('deliveryOptionId', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('tenant')->dropIfExists('orders');
    }
};
