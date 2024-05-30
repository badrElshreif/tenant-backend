<?php

namespace App\Infrastructure\Console\Commands;

use Illuminate\Console\Command;

class MigrateTenant extends Command
{

    //php artisan migrate:tenant store_1
    protected $signature = 'migrate:tenant {db_name}';


    protected $description = 'Migrate Tenant';


    public function handle(): int
    {
        try {
            $db_name = $this->argument('db_name');
            config(["database.connections.tenant.database" => $db_name]);

            //php artisan migrate --path=database/migrations/store -database=mysql_tenant
            $this->call('migrate', [
                '--path' => 'database/migrations/tenant',
                '--database' => 'tenant',
                // '--force' => true,
            ]);
            // $this->info("migration successfully for $db_name");
        } catch (\Exception $e) {
            $this->error("migration failed for $db_name with an exception");
            $this->error($e->getMessage());
            return 2;
        }

        return 0;
    }
}
