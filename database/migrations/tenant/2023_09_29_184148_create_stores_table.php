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
        Schema::connection('tenant')->create('stores', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('image')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('username')->nullable();
            $table->string('address')->nullable();
            $table->unsignedBigInteger('seller_id')->nullable()->index('stores_seller_id_foreign');
            $table->unsignedBigInteger('city_id')->nullable()->index('stores_city_id_foreign');
            $table->string('bank_name')->nullable();
            $table->string('iban_no')->nullable();
            $table->string('swift_code')->nullable();
            $table->string('bank_account_no')->nullable();
            $table->string('commercial_registry_no')->nullable();
            $table->string('commercial_registry_photo')->nullable();
            $table->boolean('status')->default(false);
            $table->boolean('is_active')->default(true);
            $table->text('deactivation_reason')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->enum('type', ['stores', 'centers'])->default('stores');
            $table->boolean('is_featured')->default(false);
            $table->boolean('has_delivery_service')->default(false);
            $table->decimal('delivery_charge', 13, 3)->default(0);
            $table->decimal('application_dues', 12)->default(0);
            $table->softDeletes();
            $table->timestamps();
            $table->string('bank_user_name')->nullable();
            $table->string('bank_country_id')->nullable();
            $table->enum('bank_type', ['local', 'global'])->nullable()->default('local');
            $table->unsignedBigInteger('country_id')->default(194)->index('stores_country_id_foreign');
            $table->unsignedBigInteger('state_id')->default(2848)->index('stores_state_id_foreign');
            $table->string('bank_address')->nullable();
            $table->string('bank_user_phone')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('tenant')->dropIfExists('stores');
    }
};
