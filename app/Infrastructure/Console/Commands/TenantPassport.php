<?php

namespace App\Infrastructure\Console\Commands;

use App\Infrastructure\Models\Passport\PassportClient;
use App\Infrastructure\Models\Passport\PersonalAccessClient;
use App\Main\Tenant\Domain\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

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
                    $db_name = "tenant_" . $tenant_val->id;
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
                $db_name = "tenant_" . $tenant->id;
                config(["database.connections.tenant.database" => $db_name]);
                config(["passport.default" => 'tenant']);
                config(["passport.connection" => 'tenant']);
                $this->laravel['db']->purge("tenant");

                $this->info("install passport on database $db_name");

                $client = PassportClient::create([
                    'name' => $tenant->name,
                    'secret' => Str::random(32),
                    'redirect' => "http://localhost",
                    'personal_access_client' => 1,
                    'password_client' => '0',
                    'revoked' => '0',
                ]);
                PersonalAccessClient::create([
                    'client_id' => $client->id,
                ]);

                /*
                 *  $client =  $this->laravel['db']->table('oauth_clients')->insert([
                    'name' => $tenant->name,
                    'secret' => env('PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET'),
                    'redirect' => "http://localhost",
                    'personal_access_client' => "1",
                    'password_client' => '0',
                    'revoked' => '0',
                ]);
                $this->laravel['db']->table('oauth_personal_access_clients')->insert([
                    'client_id' => $client->id,
                ]);
                 */

//                $this->call("passport:client", [
//                    '--personal',
//                    '--user_id' => $tenant->id,
//                    '--name' => $tenant->name,
//                    '--redirect_uri' => "http://store1.localhost/auth/callback",
//                ]);
//                $client = PassportClient::first();
//                $client->update([
//                    'personal_access_client' => 1,
//                ]);
//                PersonalAccessClient::create([
//                    'client_id' => $client->id,
//                ]);

//                $oauth_clients = $this->laravel['db']->table('oauth_clients')->orderBy('id', 'ASC')->limit(1)
//                    ->update([
//                        'personal_access_client' => 1,
//                    ]);

                $this->info("------------------------");

            } catch (\Exception $e) {
                dd($e);
                $this->error($e->getMessage());
                return 2;
            }
        }

        return 0;
    }
}
