<?php


namespace App\Infrastructure\Domain\Contracts;


interface Payment
{
    public function getToken($data=[]);
    public function response($data=[]);

}
