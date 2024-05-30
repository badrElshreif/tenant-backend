<?php

namespace Database\Seeders;

use App\Property\Domain\Models\PropertyTypeTranslation;
use Illuminate\Database\Seeder;

class TenantPropertyTypeTranslationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        PropertyTypeTranslation::insert(array(
            0 =>
                array(
                    'id' => 1,
                    'name' => 'راديو',
                    'locale' => 'ar',
                    'property_type_id' => 1,
                ),
            1 =>
                array(
                    'id' => 2,
                    'name' => 'radio',
                    'locale' => 'en',
                    'property_type_id' => 1,
                ),
            2 =>
                array(
                    'id' => 3,
                    'name' => 'تحديد',
                    'locale' => 'ar',
                    'property_type_id' => 2,
                ),
            3 =>
                array(
                    'id' => 4,
                    'name' => 'checkbox',
                    'locale' => 'en',
                    'property_type_id' => 2,
                ),
            4 =>
                array(
                    'id' => 5,
                    'name' => 'نص',
                    'locale' => 'ar',
                    'property_type_id' => 3,
                ),
            5 =>
                array(
                    'id' => 6,
                    'name' => 'text',
                    'locale' => 'en',
                    'property_type_id' => 3,
                ),
            6 =>
                array(
                    'id' => 7,
                    'name' => 'اختيار',
                    'locale' => 'ar',
                    'property_type_id' => 4,
                ),
            7 =>
                array(
                    'id' => 8,
                    'name' => 'select',
                    'locale' => 'en',
                    'property_type_id' => 4,
                ),
            8 =>
                array(
                    'id' => 9,
                    'name' => 'اختيار متعدد',
                    'locale' => 'ar',
                    'property_type_id' => 5,
                ),
            9 =>
                array(
                    'id' => 10,
                    'name' => 'multiple_select',
                    'locale' => 'en',
                    'property_type_id' => 5,
                ),
            10 =>
                array(
                    'id' => 11,
                    'name' => 'رقم صحيح',
                    'locale' => 'ar',
                    'property_type_id' => 6,
                ),
            11 =>
                array(
                    'id' => 12,
                    'name' => 'Integer Number',
                    'locale' => 'en',
                    'property_type_id' => 6,
                ),
            12 =>
                array(
                    'id' => 13,
                    'name' => 'رقم عشرى',
                    'locale' => 'ar',
                    'property_type_id' => 7,
                ),
            13 =>
                array(
                    'id' => 14,
                    'name' => 'Decimal',
                    'locale' => 'en',
                    'property_type_id' => 7,
                ),
            14 =>
                array(
                    'id' => 15,
                    'name' => 'ملف',
                    'locale' => 'ar',
                    'property_type_id' => 8,
                ),
            15 =>
                array(
                    'id' => 16,
                    'name' => 'file',
                    'locale' => 'en',
                    'property_type_id' => 8,
                ),
        ));


    }
}
