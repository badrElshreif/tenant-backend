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
        Schema::connection('tenant')->create('payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('total', 13, 3)->default(0);
            $table->decimal('application_dues_percentage', 12)->default(0);
            $table->decimal('application_dues', 13, 3)->default(0);
            $table->decimal('amount', 13, 3)->default(0);
            $table->string('receipt')->nullable();
            $table->unsignedBigInteger('store_id')->nullable()->index('payments_store_id_foreign');
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
        Schema::connection('tenant')->dropIfExists('payments');
    }
};
