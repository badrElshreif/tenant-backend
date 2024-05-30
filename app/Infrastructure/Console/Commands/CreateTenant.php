<?php

namespace App\Infrastructure\Console\Commands;

use App\Main\Tenant\Domain\Services\CreateTenantService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class CreateTenant extends Command
{
    //php artisan create:tenant
    protected $signature = 'create:tenant';


    protected $description = 'Create Tenant';


    public function handle(CreateTenantService $createTenant)
    {
        $tenant_name = $this->ask('What is your Tenant name?');
        $this->info("Tenant name is, $tenant_name");

        $email = $this->ask('What is your Email?');
        $this->info("Email is, $email");

        $password = $this->secret('What is your password?');
        $this->info("This is really secure. Your password is $password");


        $data = [
            'name' => $tenant_name,
            'password' => $password,
            'email' => $email,
        ];

        $this->output->progressStart(10);
        $tenant = $createTenant->handle($data);
        if ($tenant) {
           // Artisan::call("tenant:install-passport " . $tenant->id);
        }

        for ($i = 0; $i < 10; $i++) {
            sleep(1);
            $this->output->progressAdvance();
        }
        $this->output->progressFinish();
        return 0;
    }
}
