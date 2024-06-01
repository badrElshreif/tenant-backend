<?php
namespace App\Infrastructure\Models\Passport;
use App\Infrastructure\Traits\TenantSetConnection;
use Laravel\Passport\AuthCode;

class PassportAuthCode extends AuthCode
{
    //use TenantSetConnection;
    protected $connection = "tenant";
    protected $table = 'oauth_auth_codes';
}
