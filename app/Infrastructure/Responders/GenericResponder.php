<?php

namespace App\Infrastructure\Responders;

use Illuminate\Http\Response;

class GenericResponder extends Responder implements ResponderInterface
{


    public function respond()
    {
        $this->response = (object)$this->response;
        if ($this->response->status) {
            $data = collect($this->response)->except(['status', 'message', 'code']);

            if (request()->is_paginated == 1) {
                $result = $data?? [];
            } else {
                $result = $data ?? [];
            }

            return response()->json([
                'status' => $this->response->status ?? true,
                'message' => $this->response->message ?? "success",
                //'data' => $data,
                'result' => $result,
            ], $this->response->code ?? Response::HTTP_OK);
        }

        return response()->json($this->response, $this->response->code ?? Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function view($path)
    {
        $this->response = (object)$this->response;

        if ($this->response->data instanceof \Illuminate\Http\Resources\Json\AnonymousResourceCollection) {
            $dataArray = $this->response->data->toArray(request());
            $dataObject = json_decode(json_encode($dataArray));
            return response()->view($path, ['data' => $dataObject]);
        }

        return response()->view($path, $this->response->data ?? []);
    }
}
