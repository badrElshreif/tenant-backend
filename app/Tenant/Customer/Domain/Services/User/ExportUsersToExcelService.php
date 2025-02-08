<?php

namespace App\User\Domain\Services\User;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\User\Domain\Models\Customer;
use App\User\Domain\Filters\UserFilter;
use App\User\Domain\Exports\UsersExport;
use Excel;
use Symfony\Component\HttpFoundation\Response;

class ExportUsersToExcelService extends Service
{
    protected $user, $filter;

    public function __construct(Customer $user, UserFilter $filter)
    {
        $this->user = $user;
        $this->filter = $filter;
    }

    public function handle($data = [])
    {
        return new GenericPayload(
        	Excel::download(new UsersExport($this->user, $this->filter), 'Users.xlsx'),
            Response::HTTP_RESET_CONTENT
        );
    }
}


