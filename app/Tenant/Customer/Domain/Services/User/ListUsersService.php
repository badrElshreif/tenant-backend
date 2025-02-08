<?php

namespace App\User\Domain\Services\User;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\User\Domain\Models\Customer;
use App\User\Domain\Filters\UserFilter;
use Symfony\Component\HttpFoundation\Response;

class ListUsersService extends Service
{
    protected $user, $filter;

    public function __construct(Customer $user, UserFilter $filter)
    {
        $this->user = $user;
        $this->filter = $filter;
    }

    public function handle($data = [])
    {
        $order = request()->get('orderBy', 'id');
        $order_type = request()->get('orderType', 'DESC');
        $users = $this->user->filter($this->filter);
        if(isset(request()->is_paginated) && (request()->is_paginated == 'false' || request()->is_paginated == 0)){
            $users = $users->get();
            return new GenericPayload($users, Response::HTTP_OK);
        }else{
            $users = $users->orderBy($order, $order_type)->paginate(config('app.pagination_limit'));
            return new GenericPayload($users, Response::HTTP_ACCEPTED);
        }



    }
}
