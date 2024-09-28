<?php

namespace App\Tenant\AppContent\Domain\Services\Setting;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\AppContent\Domain\Models\Setting;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class GetSettingsService extends Service
{
    public function handle($data = [])
    {
        try {
            $target = 'superAdmin';
            if(isset($data['target']) && !auth()->check()) {
                $target = $data['target'];
            } else {
                if(optional(auth()->user())->store_id != null){
                    if(optional(auth()->user()->store)->type == 'centers')
                        $target = 'centers';
                    else
                        $target = 'stores';
                }
            }

        	$settings = Setting::whereTarget($target)->whereIsActive(1)->get();
            return new GenericPayload($settings, Response::HTTP_OK);
        } catch (Exception $ex) {
            return new GenericPayload(
                ['message' => __('error.someThingWrong')], 422
            );
        }

    }
}
