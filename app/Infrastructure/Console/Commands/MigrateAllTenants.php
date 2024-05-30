<?php

namespace App\Infrastructure\Console\Commands;


use App\Main\Tenant\Domain\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class MigrateAllTenants extends Command
{
    //php artisan migrate:all-stores
    protected $signature = 'migrate:all-tenants';


    protected $description = 'Migrate All Tenants';


    public function handle(): int
    {

        $stores = Tenant::get();
        foreach ($stores as $store_val) {
            try {
                $db_name = $store_val->database_options['db_name'];
                config(["database.connections.tenant.database" => $db_name]);
                $this->laravel['db']->purge("tenant");

                $this->info("migration database $db_name");
                $this->call('migrate', [
                    '--path' => 'database/migrations/tenant',
                    '--database' => 'tenant',
                    // '--force' => true,
                ]);
                $this->info("------------------------");

                //  $this->info("migration successfully for $db_name");
            } catch (\Exception $e) {
                $this->error("migration failed for $db_name with an exception");
                $this->error($e->getMessage());
            }
        }

        return 0;
    }
}
