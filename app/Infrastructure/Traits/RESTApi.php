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
        $responseArr['status'] = true;
        $responseArr['data'] = $responseObject;
        return response()->json($responseArr, $statusCode);
    }


    /**
     * Return response with error object
     * @param $errorObject , $errorKey, $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
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

}


