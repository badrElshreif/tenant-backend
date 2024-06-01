<?php
namespace App\Infrastructure\Models\Passport;
use Laravel\Passport\Token;

class PassportRefreshToken extends \Laravel\Passport\RefreshToken
{
    protected $connection = "tenant";
}
