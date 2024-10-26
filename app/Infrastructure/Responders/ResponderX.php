<?php

namespace App\Infrastructure\Responders;

use App\Infrastructure\Domain\Resources\GenericNameResource;
use App\Infrastructure\Enums\ResponseType;
use App\Infrastructure\Traits\ApiPaginator;
use App\Infrastructure\Traits\RESTApi;
use Symfony\Component\HttpFoundation\Response;

class ResponderX extends Responder
{

    use ApiPaginator;

    public function respond()
    {

        return $this->sendJson(
            GenericNameResource::collection($this->response->getData()),
            $this->response->getStatus()
        );
    }


    public function sendJson($responseObject, $statusCode = Response::HTTP_OK, $message = 'success')
    {
        $responseArr['message'] = $message;
        $responseArr['status'] = true;
        $responseArr['result'] = $responseObject;
        return response()->json($responseArr, $statusCode);
    }


    public function sendError($errorObject, $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY, $errorKey = 'errors')
    {
        $errorResponse['status'] = false;
        if (is_object($errorObject)) {
            $errorResponse['message'] = is_array($errorObject->messages()) ? $errorObject->first() : $errorObject;
        }

        $errorResponse[$errorKey] = $errorObject;
        return response()->json($errorResponse, $statusCode);
    }

    public function sendRedirectError($errorObject, $statusCode = Response::HTTP_PERMANENTLY_REDIRECT, $errorKey = 'error')
    {
        return response()->json($errorObject, $statusCode);
    }

    public function sendMessage($responseObject, $statusCode = Response::HTTP_ACCEPTED, $responseKey = 'response')
    {
        $responseArr['status'] = false;
        $responseArr['data'] = $responseObject;
        return response()->json($responseArr, $statusCode);
    }

    public function getApiResponse($type, $data = [], $status = Response::HTTP_OK, $resource = null)
    {
        if ($type == ResponseType::CollectionWithPaginated) {
            return $this->sendJson($this->getPaginatedResponse(
                $this->response->getData(),
                $resource ? $resource::collection($this->response->getData()) : []
            ), $this->response->getStatus());
        } else if ($type == ResponseType::CollectionList) {
            return $this->sendJson(
                $resource::collection($this->response->getData()),
                $this->response->getStatus()
            );
        } else if ($type == ResponseType::SingleResource) {
            return $this->sendJson(
                new $resource($data),
                $status
            );
        } else if ($type == ResponseType::Error) {
            return $this->sendError($data, $status);
        }

        return $this->sendJson($this->response->getData(), $status);
    }

    public function getViewResponse($viewPath, $data = [], $resource = null)
    {
        return view($viewPath, $data);
    }
}
