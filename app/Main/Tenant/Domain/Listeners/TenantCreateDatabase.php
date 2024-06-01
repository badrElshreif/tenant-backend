<?php

namespace App\Main\Tenant\Domain\Listeners;

use App\Main\Tenant\Domain\Events\TenantCreated;
use App\Tenant\Admin\Domain\Models\Admin;
use Database\Seeders\TenantDatabaseSeeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TenantCreateDatabase
{

    public function __construct()
    {

    }

    public function handle(TenantCreated $event)
    {
        $tenant = $event->tenant;
        $user = $event->user;
        $db_name = "tenant_" . $tenant->id;
//        $tenant->database_options = [
//            'db_name' => $db_name,
//        ];
        $tenant->api_key = Str::random(50);
        $tenant->save();

        //create database
        $charset = config("database.connections.tenant.charset", 'utf8mb4');
        $collation = config("database.connections.tenant.collation", 'utf8mb4_unicode_ci');
        DB::statement("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET $charset COLLATE $collation;");

        //change db connection with new db
        config(["database.connections.tenant.database" => $db_name]);

        //run migration files
        //php artisan migrate --path=database/migrations/store -database=tenant
        Artisan::call('migrate', [
            '--path' => 'database/migrations/tenant',
            '--database' => 'tenant',
            '--force' => true,
        ]);

        if ($tenant) {
            Artisan::call('tenant:install-passport', [
                'tenant_id' => $tenant->id,
            ]);
        }


        $tenantDatabaseSeeder = new TenantDatabaseSeeder();
        $tenantDatabaseSeeder->run();
        /* Settings::create([
             'default_locale' => 'ar',
             'locales' => ['ar', 'en'],
             'upload_dir' => Str::slug($tenant->name),
         ]); */

        Admin::create([
            'name' => $user->name,
            'email' => $user->email,
            'username' => $tenant->name,
            'default_lang' => 'ar',
            'password' => $user->password,
            'is_super_admin' => 1,
            'is_active' => 1,
        ]);

        /*  $languages = Language::whereStatus('active')->get();
          foreach ($languages as $lang) {
              StoreLanguage::create([
                  'name' => $lang->name,
                  'code' => $lang->code,
                  'direction' => $lang->direction,
              ]);
          } */

    }
}
