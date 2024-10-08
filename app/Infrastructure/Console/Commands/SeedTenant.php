<?php

namespace App\Infrastructure\Console\Commands;

use Illuminate\Console\Command;

class SeedTenant extends Command
{

    //php artisan seed:tenant store_1
    protected $signature = 'seed:tenant {db_name}';


    protected $description = 'Seed Tenant';


    public function handle(): int
    {
        try {
            $db_name = $this->argument('db_name');
            config(["database.connections.tenant.database" => $db_name]);

            //php artisan db:seed --path=database/migrations/store -database=mysql_tenant
            $this->call('db:seed', [
//                '--path' => 'database/migrations/tenant',
                '--database' => 'tenant',
                // '--force' => true,
            ]);
            // $this->info("migration successfully for $db_name");
        } catch (\Exception $e) {
            $this->error("seed failed for $db_name with an exception");
            $this->error($e->getMessage());
            return 2;
        }

        return 0;
    }
}
