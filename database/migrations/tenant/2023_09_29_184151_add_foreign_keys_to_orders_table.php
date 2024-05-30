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
        Schema::connection('tenant')->table('orders', function (Blueprint $table) {
            $table->foreign(['payment_id'])->references(['id'])->on('payments')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['payment_method_id'])->references(['id'])->on('payment_methods')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['status_id'])->references(['id'])->on('statuses')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['store_id'])->references(['id'])->on('stores')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['user_address_id'])->references(['id'])->on('user_addresses')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['user_id'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('tenant')->table('orders', function (Blueprint $table) {
            $table->dropForeign('orders_payment_id_foreign');
            $table->dropForeign('orders_payment_method_id_foreign');
            $table->dropForeign('orders_status_id_foreign');
            $table->dropForeign('orders_store_id_foreign');
            $table->dropForeign('orders_user_address_id_foreign');
            $table->dropForeign('orders_user_id_foreign');
        });
    }
};
