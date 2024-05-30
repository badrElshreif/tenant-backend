<?php

namespace App\Infrastructure\Console\Commands;

use App\Main\Tenant\Domain\Models\Tenant;
use Illuminate\Console\Command;

class TenantPassport extends Command
{
    //php artisan tenant:install-passport
    protected $signature = 'tenant:install-passport {tenant_id?}';


    protected $description = 'Tenant install Passport';


    public function handle()
    {

        $tenant_id = $this->argument('tenant_id');
        config(['set_tenant_connection' => 1]);

        if (!$tenant_id) {
            $tenants = Tenant::get();
            foreach ($tenants as $tenant_val) {
                try {
                    $db_name = $tenant_val->database_options['db_name'];
                    config(["database.connections.tenant.database" => $db_name]);
                    // config(["passport.storage.database.connection" => 'mysql_tenant']);
                    $this->laravel['db']->purge("tenant");

                    $this->info("install passport on database $db_name");
                    $this->call("passport:install", ['--force' => true]);
                    $this->info("------------------------");

                } catch (\Exception $e) {
                    $this->error("failed for $db_name with an exception");
                    $this->error($e->getMessage());
                }
            }
        } else {
            try {
                $tenant = Tenant::find($tenant_id);
                if (!$tenant) {
                    $this->error("tenant not found !");
                    return 1;
                }
                $db_name = $tenant->database_options['db_name'];
                config(["database.connections.tenant.database" => $db_name]);
                // config(["passport.storage.database.connection" => 'tenant']);
                $this->laravel['db']->purge("tenant");

                $this->info("install passport on database $db_name");
                $this->call("passport:install", ['--force' => true]);
                $this->info("------------------------");

            } catch (\Exception $e) {
                $this->error($e->getMessage());
                return 2;
            }
        }

        return 0;
    }
}
