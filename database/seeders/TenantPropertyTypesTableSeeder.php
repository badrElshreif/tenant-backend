<?php

namespace Database\Seeders;

use App\Property\Domain\Models\PropertyType;
use Illuminate\Database\Seeder;

class TenantPropertyTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        PropertyType::insert(array(
            0 =>
                array(
                    'id' => 1,
                    'key' => 'radio',
                    'has_options' => 1,
                    'is_active' => 1,
                    'created_at' => '2022-10-04 12:05:59',
                    'updated_at' => '2022-10-04 12:05:59',
                ),
            1 =>
                array(
                    'id' => 2,
                    'key' => 'checkbox',
                    'has_options' => 1,
                    'is_active' => 1,
                    'created_at' => '2022-10-04 12:05:59',
                    'updated_at' => '2022-10-04 12:05:59',
                ),
            2 =>
                array(
                    'id' => 3,
                    'key' => 'text',
                    'has_options' => 0,
                    'is_active' => 1,
                    'created_at' => '2022-10-04 12:05:59',
                    'updated_at' => '2022-10-04 12:05:59',
                ),
            3 =>
                array(
                    'id' => 4,
                    'key' => 'select',
                    'has_options' => 1,
                    'is_active' => 1,
                    'created_at' => '2022-10-04 12:05:59',
                    'updated_at' => '2022-10-04 12:05:59',
                ),
            4 =>
                array(
                    'id' => 5,
                    'key' => 'multible_select',
                    'has_options' => 1,
                    'is_active' => 0,
                    'created_at' => '2022-10-04 12:05:59',
                    'updated_at' => '2022-10-04 12:05:59',
                ),
            5 =>
                array(
                    'id' => 6,
                    'key' => 'number',
                    'has_options' => 0,
                    'is_active' => 1,
                    'created_at' => '2022-10-04 12:05:59',
                    'updated_at' => '2022-10-04 12:05:59',
                ),
            6 =>
                array(
                    'id' => 7,
                    'key' => 'decimal',
                    'has_options' => 0,
                    'is_active' => 1,
                    'created_at' => '2022-10-04 12:05:59',
                    'updated_at' => '2022-10-04 12:05:59',
                ),
            7 =>
                array(
                    'id' => 8,
                    'key' => 'file',
                    'has_options' => 0,
                    'is_active' => 0,
                    'created_at' => '2022-10-04 12:05:59',
                    'updated_at' => '2022-10-04 12:05:59',
                ),
        ));


    }
}
