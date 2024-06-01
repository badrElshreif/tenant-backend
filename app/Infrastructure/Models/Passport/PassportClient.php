<?php
namespace App\Infrastructure\Models\Passport;
use App\Infrastructure\Traits\TenantSetConnection;
use Laravel\Passport\Client;

class PassportClient extends Client
{
    protected $connection = "tenant";

    protected $table = 'oauth_clients';

}
