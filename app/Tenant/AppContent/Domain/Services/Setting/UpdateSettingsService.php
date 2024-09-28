<?php

namespace App\Tenant\AppContent\Domain\Services\Setting;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\AppContent\Domain\Models\Setting;
use App\Tenant\AppContent\Domain\Resources\SettingResource;
use Symfony\Component\HttpFoundation\Response;

class UpdateSettingsService extends Service
{
    public function handle($data = [])
    {
        try {
            $msg = __('success.updatedSuccessfuly');
            if(isset($data['settings'])){
                foreach ($data['settings'] as $setting) {
                    Setting::updateOrCreate(
                        [
                            'key' => $setting['key']
                        ],
                        $setting
                    );
                }
            }
            return new GenericPayload(['message' => $msg], Response::HTTP_RESET_CONTENT);
            // return new GenericPayload(['message' => $msg , 'settings' => SettingResource::collection(Setting::whereTarget($target)->get())], Response::HTTP_RESET_CONTENT);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            throw new ModelNotFoundException;
        } catch (Exception $ex) {
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        }


    }
}
