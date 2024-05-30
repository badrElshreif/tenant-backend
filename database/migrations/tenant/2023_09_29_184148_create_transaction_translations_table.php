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
        Schema::connection('tenant')->create('transaction_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('reason');
            $table->string('locale')->index();
            $table->unsignedBigInteger('transaction_id');

            $table->unique(['transaction_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('tenant')->dropIfExists('transaction_translations');
    }
};
