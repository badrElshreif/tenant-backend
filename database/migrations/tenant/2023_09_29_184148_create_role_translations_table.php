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
        Schema::connection('tenant')->create('role_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('display_name');
            $table->string('locale')->index();
            $table->unsignedBigInteger('role_id');

            $table->unique(['role_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('tenant')->dropIfExists('role_translations');
    }
};
