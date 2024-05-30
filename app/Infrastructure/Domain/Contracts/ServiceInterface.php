<?php
namespace App\Infrastructure\Domain\Contracts;
interface ServiceInterface 
{
    public function handle($data = []);
}