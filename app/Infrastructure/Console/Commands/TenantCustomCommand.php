<?php

namespace App\Infrastructure\Console\Commands;

use App\Main\Tenant\Domain\Models\Tenant;
use Illuminate\Console\Command;

class TenantCustomCommand extends Command
{
    //php artisan store:command PassportSeeder::class
    protected $signature = 'tenant:command {commandVar}';


    protected $description = 'Tenant Custom Command ';


    public function handle()
    {
        $command = $this->argument('commandVar');

        $tenants = Tenant::get();
        foreach ($tenants as $tenant_val) {
            try {
                $db_name = $tenant_val->database_options['db_name'];
                config(["database.connections.tenant.database" => $db_name]);
                $this->laravel['db']->purge("tenant");

                $this->info("command on database $db_name");
                $this->call($command, ['--personal']);
                $this->info("------------------------");

                //  $this->info("migration successfully for $db_name");
            } catch (\Exception $e) {
                $this->error("failed for $db_name with an exception");
                $this->error($e->getMessage());
            }
        }

        return 0;
    }
}
