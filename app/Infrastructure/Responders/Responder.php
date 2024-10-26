<?php

namespace App\Infrastructure\Responders;

abstract class Responder
{
    protected $response;

    protected $data;

    protected $type;
    protected $resource;

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

    public function withType($type)
    {
        $this->type = $type;

        return $this;
    }

    public function withResource($resource)
    {
        $this->resource = $resource;

        return $this;
    }

    public function getResponse()
    {
        return $this->response;
    }



    public function getView($view)
    {
        return $this->response->view($view, $this->data);
    }

}
