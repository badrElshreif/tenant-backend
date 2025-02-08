<?php

namespace App\User\Actions\User;

use App\User\Domain\Services\User\ExportUsersToExcelService;
use App\User\Responders\UserResponder;

class ExportUsersToExcelAction 
{
    public function __construct(UserResponder $responder, ExportUsersToExcelService $services) 
    {
        $this->responder = $responder;
        $this->services = $services;
    }

    public function __invoke() 
    {
        return $this->responder->withResponse(
            $this->services->handle()
        )->respond();
    }
}