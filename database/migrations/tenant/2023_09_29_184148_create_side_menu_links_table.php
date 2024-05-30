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
        Schema::connection('tenant')->create('side_menu_links', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('order');
            $table->string('url')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable()->index('side_menu_links_parent_id_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('tenant')->dropIfExists('side_menu_links');
    }
};
