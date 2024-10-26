<?php

namespace App\Infrastructure\Enums;


enum ResponseType: string
{
    case CollectionWithPaginated = 'collection_with_paginated';
    case CollectionList = 'collection_list';
    case SingleResource = 'single_resource';
    case NoContent = 'no_content';
    case ResetContent = 'reset_content'; //205 return data without modification
    case UnprocessableEntity = 'unprocessable_entity'; //422
    case ServerError = 'server_error'; // 500
    case Error = 'error';
    case Success = 'success';
    case View = 'view';
    case Custom = 'custom';

}
