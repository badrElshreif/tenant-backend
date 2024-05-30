<?php

namespace App\Infrastructure\Domain\Payloads;

abstract class Payload
{
    protected $data = [];

    protected $status = 200;

    public function __construct($data = null, $status = null)
    {
        $this->data = $data;

        if (isset($status)) {
            $this->status = $status;
        }
    }

    public function getData()
    {
        return $this->data;
    }

    public function getStatus()
    {
        return $this->status;
    }
}
