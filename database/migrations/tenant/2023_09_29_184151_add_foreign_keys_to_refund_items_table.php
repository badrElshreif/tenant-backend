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
        Schema::connection('tenant')->table('refund_items', function (Blueprint $table) {
            $table->foreign(['order_item_id'])->references(['id'])->on('order_items')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['refund_id'])->references(['id'])->on('refunds')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('tenant')->table('refund_items', function (Blueprint $table) {
            $table->dropForeign('refund_items_order_item_id_foreign');
            $table->dropForeign('refund_items_refund_id_foreign');
        });
    }
};
