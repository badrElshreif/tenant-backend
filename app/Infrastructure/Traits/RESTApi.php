<?php

namespace App\Infrastructure\Traits;

use Symfony\Component\HttpFoundation\Response;

trait RESTApi
{

    /**
     * Return response with json object
     * @param $responseObject , $responseKey, $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendJson($responseObject, $statusCode = Response::HTTP_OK, $responseKey = 'response')
    {
        return response($responseObject, $statusCode);
    }


    /**
     * Return response with error object
     * @param $errorObject , $errorKey, $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendError($errorObject, $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY, $errorKey = 'error')
    {
        $errorResponse[$errorKey] = $errorObject;
        return response($errorResponse, $statusCode);
    }

    public function sendRedirectError($errorObject, $statusCode = Response::HTTP_PERMANENTLY_REDIRECT, $errorKey = 'error')
    {
        return response($$errorObject, $statusCode);
    }

    public function sendMessage($responseObject, $statusCode = Response::HTTP_ACCEPTED, $responseKey = 'response')
    {
        return response($responseObject, $statusCode);
    }

}


