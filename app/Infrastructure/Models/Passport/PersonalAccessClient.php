<?php
namespace App\Infrastructure\Models\Passport;
use Laravel\Passport\PersonalAccessClient as Pac;

class PersonalAccessClient extends Pac
{
    protected $connection = "tenant";
    protected $table = 'oauth_personal_access_clients';
}
