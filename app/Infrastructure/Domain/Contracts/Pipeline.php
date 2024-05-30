<?php


namespace App\Infrastructure\Domain\Contracts;


abstract class Pipeline
{
    abstract public function handle();
}
