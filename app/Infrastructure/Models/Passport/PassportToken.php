<?php
namespace App\Infrastructure\Models\Passport;
use Laravel\Passport\Token;

class PassportToken extends Token
{
    protected $connection = "tenant";
    protected $table = 'oauth_access_tokens';
}
