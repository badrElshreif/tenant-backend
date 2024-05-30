<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $db_name = config('database.connections.tenant.database');
        DB::connection('tenant')->statement("CREATE VIEW `products_view` AS select `$db_name`.`products`.`id` AS `id`,`$db_name`.`products`.`quantity` AS `quantity`,`$db_name`.`products`.`price` AS `price`,`$db_name`.`products`.`approved` AS `approved`,`$db_name`.`products`.`category_id` AS `category_id`,`$db_name`.`products`.`brand_id` AS `brand_id`,`$db_name`.`products`.`store_id` AS `store_id`,`$db_name`.`products`.`image` AS `image`,`$db_name`.`products`.`barcode` AS `barcode`,`$db_name`.`products`.`made_in` AS `made_in`,`$db_name`.`products`.`preview_fees` AS `preview_fees`,`$db_name`.`products`.`max_purchase_quantity` AS `max_purchase_quantity`,`$db_name`.`products`.`is_active` AS `is_active`,`$db_name`.`products`.`deactivation_start_date` AS `deactivation_start_date`,`$db_name`.`products`.`deactivation_end_date` AS `deactivation_end_date`,`$db_name`.`products`.`created_by` AS `created_by`,`$db_name`.`products`.`created_at` AS `created_at`,`$db_name`.`products`.`updated_at` AS `updated_at`,`$db_name`.`products`.`catalog` AS `catalog`,`$db_name`.`settings`.`body` AS `added_tax`,(`$db_name`.`products`.`price` * (1 + (`$db_name`.`settings`.`body` / 100))) AS `price_including_tax` from (`$db_name`.`products` left join `$db_name`.`settings` on((`$db_name`.`settings`.`key` = 'added_tax')))");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::connection('tenant')->statement("DROP VIEW IF EXISTS `products_view`");
    }
};
