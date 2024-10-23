<?php

namespace App\Infrastructure\Domain\Payloads;

abstract class Payload
{
    protected $data = [];

    protected $status = 200;
    protected $type;

    public function __construct($data = null, $status = null,$type=null)
    {
        $this->data = $data;
        $this->type = $type;

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

    public function getType()
    {
        return $this->type;
    }
}
