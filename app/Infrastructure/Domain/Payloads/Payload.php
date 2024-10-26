<?php

namespace App\Infrastructure\Domain\Payloads;


abstract class Payload
{
    protected $data = [];

    protected $status = 200;
    protected $type;
    protected $resource;

    public function __construct($data = null, $status = null, $type = null, $resource = null)
    {
        $this->data = $data;
        $this->type = $type;
        $this->resource = $resource;

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

    public function getResource()
    {
        return $this->resource;
    }


}
