<?php

namespace App\User\Domain\Services\UserAddress;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use Symfony\Component\HttpFoundation\Response;

class CreateUserAddressService extends Service
{
    public function handle($data = [])
    {
        $user = auth()->user();
        if(isset($data['is_primary']) && $data['is_primary'] == 1){
        	$address = $user->addresses->where('is_primary', 1)->first();
        	if($address)
        		$address->update([
        			'is_primary' => 0
        		]);
        }
        $address = $user->addresses()->create($data);
        return new GenericPayload($address, Response::HTTP_CREATED);
    }
}
