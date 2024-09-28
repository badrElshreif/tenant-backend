<?php

namespace App\Tenant\AppContent\Domain\Services\API;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\AppContent\Domain\Mail\PromotionMail;
use Illuminate\Support\Arr;
use App\User\Domain\Models\User;
use App\Order\Domain\Models\Order;

class SendPromotionMailService extends Service
{
    public function handle($data = [])
    {
        //try {
            $data['users_emails'] = [];
            // type in: [all_clients,specific_clients,cities_clients,products_clients]

            if($data['type'] == 'all_clients')
                $data['users_emails'] = User::whereIsActive(1)->whereNotNull('email')->pluck('email')->toArray();

            if($data['type'] == 'specific_clients')
                $data['users_emails'] = User::whereIsActive(1)->whereIn('id', $data['users'])->whereNotNull('email')->pluck('email')->toArray();

            if($data['type'] == 'cities_clients'){
                $data['users_emails'] = User::whereIsActive(1)->whereNotNull('email')
                ->whereHas('addresses', function ($query) use ($data) {
                    $query->whereIn('city_id', $data['cities']);
                })
                ->pluck('email')->toArray();
            }

            if($data['type'] == 'products_clients'){
                $users = Order::whereHas('orderItems.product', function($q) use($data) {
                    $q->whereIn('id', $data['products']);
                })->distinct()->pluck('user_id')->toArray();

                $data['users_emails'] = User::whereIsActive(1)->whereNotNull('email')
                ->whereIn('id', $users)->whereNotNull('email')->pluck('email')->toArray();
            }
            $data['from_email'] = isset($data['from_email'])? $data['from_email'] : null;
            // foreach ($data['users_emails'] as $recipient) {
            //     \Mail::to($recipient)->queue(new \App\Tenant\AppContent\Domain\Mail\PromotionMail($data['message'], $data['from_email']));
            // }

            dispatch(
                new \App\Jobs\SendPromotionEmailJob(
                    Arr::only($data, ['users_emails', 'message', 'from_email'])
                )
            );
        // } catch (Exception $ex) {
        //     return new GenericPayload(
        //         ['message' => __('error.someThingWrong')], 422
        //     );
        // }
        return new GenericPayload(['message' => __('success.sentSuccessfully')]);

    }
}
