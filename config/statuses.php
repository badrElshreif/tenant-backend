<?php

//** No Need for this file : check this file vendor/symfony/http-foundation/Response */

use Symfony\Component\HttpFoundation\Response;

return [
    'SUCCESS' => [
        Response::HTTP_OK, // 200 list ( collection ) HTTP_OK
        Response::HTTP_ACCEPTED, // 202 paginated ( collection with paginated ) HTTP_ACCEPTED
        Response::HTTP_CREATED,  // 201 created | updated ( single resource ) HTTP_CREATED
        Response::HTTP_NO_CONTENT,  // 204 deleted with no content HTTP_NO_CONTENT
        Response::HTTP_RESET_CONTENT, //205 return data without modification
    ],
    'ERROR' => [
        Response::HTTP_UNPROCESSABLE_ENTITY, // 422 HTTP_UNPROCESSABLE_ENTITY
        Response::HTTP_INTERNAL_SERVER_ERROR // 500 HTTP_INTERNAL_SERVER_ERROR
    ]
];
