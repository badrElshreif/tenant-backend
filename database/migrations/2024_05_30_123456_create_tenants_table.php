<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::connection('main')->create('tenants', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 255);
            $table->string('slug', 255);
            $table->unsignedBigInteger('user_id')->index('tenants_user_id_foreign');
            $table->string('domain', 255)->nullable()->unique();
            $table->string('default_lang', 2)->nullable();
            $table->text('api_key')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('main')->dropIfExists('tenants');
    }
};
