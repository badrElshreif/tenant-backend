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
        Schema::connection('tenant')->create('order_statuses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('status_id')->index('order_statuses_status_id_foreign');
            $table->string('statusable_type');
            $table->unsignedBigInteger('statusable_id');
            $table->text('reason')->nullable();
            $table->enum('type', ['order', 'refund'])->default('order');
            $table->softDeletes();
            $table->timestamps();

            $table->index(['statusable_type', 'statusable_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('tenant')->dropIfExists('order_statuses');
    }
};
