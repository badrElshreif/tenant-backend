<?php

namespace App\Infrastructure\Responders;

abstract class Responder
{
    protected $response;

    protected $data;

    abstract public function respond();

    public function withResponse($response)
    {
        $this->response = $response;

        return $this;
    }

    public function withData($data = [])
    {
        $this->data = $data;

        return $this;
    }
}
