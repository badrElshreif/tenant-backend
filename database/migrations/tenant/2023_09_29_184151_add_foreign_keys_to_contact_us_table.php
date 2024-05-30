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
        Schema::connection('tenant')->table('contact_us', function (Blueprint $table) {
            $table->foreign(['contact_type_id'])->references(['id'])->on('contact_types')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['parent_id'])->references(['id'])->on('contact_us')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('tenant')->table('contact_us', function (Blueprint $table) {
            $table->dropForeign('contact_us_contact_type_id_foreign');
            $table->dropForeign('contact_us_parent_id_foreign');
        });
    }
};
