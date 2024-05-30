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
        Schema::connection('tenant')->table('refunds', function (Blueprint $table) {
            $table->foreign(['order_id'])->references(['id'])->on('orders')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['refund_reason_id'])->references(['id'])->on('refund_reasons')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['status_id'])->references(['id'])->on('statuses')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('tenant')->table('refunds', function (Blueprint $table) {
            $table->dropForeign('refunds_order_id_foreign');
            $table->dropForeign('refunds_refund_reason_id_foreign');
            $table->dropForeign('refunds_status_id_foreign');
        });
    }
};
