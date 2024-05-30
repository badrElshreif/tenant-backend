<?php

namespace Database\Seeders;

use App\AppContent\Domain\Models\Setting;
use Illuminate\Database\Seeder;

class TenantSettingsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        Setting::insert(array(
            0 =>
                array(
                    'body' => '5',
                    'created_at' => '2022-10-04 12:05:59',
                    'id' => 1,
                    'is_active' => 1,
                    'key' => 'application_dues',
                    'property_type_id' => 7,
                    'target' => 'superAdmin',
                    'updated_at' => '2022-10-04 12:05:59',
                ),
            1 =>
                array(
                    'body' => '5',
                    'created_at' => '2022-10-04 12:05:59',
                    'id' => 2,
                    'is_active' => 0,
                    'key' => 'added_tax',
                    'property_type_id' => 7,
                    'target' => 'superAdmin',
                    'updated_at' => '2022-10-04 12:05:59',
                ),
            2 =>
                array(
                    'body' => '0',
                    'created_at' => '2022-10-04 12:05:59',
                    'id' => 3,
                    'is_active' => 1,
                    'key' => 'order_min_cost',
                    'property_type_id' => 7,
                    'target' => 'superAdmin',
                    'updated_at' => '2023-02-12 18:33:48',
                ),
            3 =>
                array(
                    'body' => '14',
                    'created_at' => '2022-10-04 12:05:59',
                    'id' => 4,
                    'is_active' => 1,
                    'key' => 'refund_period',
                    'property_type_id' => 6,
                    'target' => 'superAdmin',
                    'updated_at' => '2022-10-04 12:05:59',
                ),
            4 =>
                array(
                    'body' => '10',
                    'created_at' => '2022-10-04 12:05:59',
                    'id' => 5,
                    'is_active' => 1,
                    'key' => 'delivery_charge',
                    'property_type_id' => 7,
                    'target' => 'superAdmin',
                    'updated_at' => '2023-04-17 11:54:32',
                ),
            5 =>
                array(
                    'body' => '15',
                    'created_at' => '2022-10-04 12:05:59',
                    'id' => 6,
                    'is_active' => 1,
                    'key' => 'order_added_tax',
                    'property_type_id' => 7,
                    'target' => 'superAdmin',
                    'updated_at' => '2023-04-27 08:33:02',
                ),
            6 =>
                array(
                    'body' => '1',
                    'created_at' => '2022-10-04 12:05:59',
                    'id' => 7,
                    'is_active' => 1,
                    'key' => 'can_rate',
                    'property_type_id' => 1,
                    'target' => 'superAdmin',
                    'updated_at' => '2023-04-27 08:33:02',
                ),
            7 =>
                array(
                    'body' => '0096612345678',
                    'created_at' => '2022-10-04 12:05:59',
                    'id' => 8,
                    'is_active' => 1,
                    'key' => 'whatsapp',
                    'property_type_id' => 3,
                    'target' => 'superAdmin',
                    'updated_at' => '2022-10-04 12:05:59',
                ),
            8 =>
                array(
                    'body' => 'https://twitter.com/therealbinbaz/status/439297999739437056?lang=ar',
                    'created_at' => '2022-10-04 12:05:59',
                    'id' => 9,
                    'is_active' => 1,
                    'key' => 'twitter',
                    'property_type_id' => 3,
                    'target' => 'superAdmin',
                    'updated_at' => '2022-10-05 12:09:04',
                ),
            9 =>
                array(
                    'body' => 'https://www.instagram.com/',
                    'created_at' => '2022-10-04 12:05:59',
                    'id' => 10,
                    'is_active' => 1,
                    'key' => 'instagram',
                    'property_type_id' => 3,
                    'target' => 'superAdmin',
                    'updated_at' => '2022-10-05 12:09:04',
                ),
            10 =>
                array(
                    'body' => 'https://www.facebook.com/',
                    'created_at' => '2022-10-04 12:05:59',
                    'id' => 11,
                    'is_active' => 1,
                    'key' => 'facebook',
                    'property_type_id' => 3,
                    'target' => 'superAdmin',
                    'updated_at' => '2022-10-05 12:09:04',
                ),
            11 =>
                array(
                    'body' => '966512365478',
                    'created_at' => '2022-10-04 12:05:59',
                    'id' => 12,
                    'is_active' => 1,
                    'key' => 'phone',
                    'property_type_id' => 3,
                    'target' => 'superAdmin',
                    'updated_at' => '2022-10-04 12:05:59',
                ),
            12 =>
                array(
                    'body' => 'info@RK.smok',
                    'created_at' => '2022-10-04 12:05:59',
                    'id' => 13,
                    'is_active' => 1,
                    'key' => 'email',
                    'property_type_id' => 3,
                    'target' => 'superAdmin',
                    'updated_at' => '2022-10-05 12:09:04',
                ),
            13 =>
                array(
                    'body' => 'https://play.google.com/store/games?hl=en&gl=US',
                    'created_at' => '2022-10-04 12:05:59',
                    'id' => 14,
                    'is_active' => 1,
                    'key' => 'google_store',
                    'property_type_id' => 3,
                    'target' => 'superAdmin',
                    'updated_at' => '2022-10-05 12:09:04',
                ),
            14 =>
                array(
                    'body' => 'https://www.apple.com/eg/app-store/',
                    'created_at' => '2022-10-04 12:05:59',
                    'id' => 15,
                    'is_active' => 1,
                    'key' => 'apple_store',
                    'property_type_id' => 3,
                    'target' => 'superAdmin',
                    'updated_at' => '2022-10-05 12:09:04',
                ),
            15 =>
                array(
                    'body' => 'https://www.youtube.com/',
                    'created_at' => '2022-10-04 12:05:59',
                    'id' => 16,
                    'is_active' => 1,
                    'key' => 'youtube',
                    'property_type_id' => 3,
                    'target' => 'superAdmin',
                    'updated_at' => '2022-10-05 12:09:04',
                ),
            16 =>
                array(
                    'body' => 'يجب ان يكون طول الصورة 150 والعرض 200',
                    'created_at' => '2022-10-04 12:05:59',
                    'id' => 17,
                    'is_active' => 1,
                    'key' => 'product_photos_notes_ar',
                    'property_type_id' => 3,
                    'target' => 'superAdmin',
                    'updated_at' => '2022-10-05 12:09:04',
                ),
            17 =>
                array(
                    'body' => 'photo width should be 200',
                    'created_at' => '2022-10-04 12:05:59',
                    'id' => 18,
                    'is_active' => 1,
                    'key' => 'product_photos_notes_en',
                    'property_type_id' => 3,
                    'target' => 'superAdmin',
                    'updated_at' => '2022-10-04 12:05:59',
                ),
            18 =>
                array(
                    'body' => 'اقر علي الموافقة بالتسوق',
                    'created_at' => '2022-10-04 12:05:59',
                    'id' => 19,
                    'is_active' => 1,
                    'key' => 'shopping_confirmation_ar',
                    'property_type_id' => 3,
                    'target' => 'superAdmin',
                    'updated_at' => '2022-10-04 12:05:59',
                ),
            19 =>
                array(
                    'body' => 'Accept Shopping',
                    'created_at' => '2022-10-04 12:05:59',
                    'id' => 20,
                    'is_active' => 1,
                    'key' => 'shopping_confirmation_en',
                    'property_type_id' => 3,
                    'target' => 'superAdmin',
                    'updated_at' => '2022-10-04 12:05:59',
                ),
        ));


    }
}
