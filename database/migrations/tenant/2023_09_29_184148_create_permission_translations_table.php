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
        Schema::connection('tenant')->create('permission_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('display_name');
            $table->string('key_name')->nullable();
            $table->string('locale');
            $table->unsignedBigInteger('permission_id')->index('permission_translations_permission_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('tenant')->dropIfExists('permission_translations');
    }
};
