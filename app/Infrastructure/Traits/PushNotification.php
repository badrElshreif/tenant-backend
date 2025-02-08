<?php

namespace App\Infrastructure\Traits;

trait PushNotification
{

    public function sendNotification($user, $notification_data, $is_admin = false)
    {
        return $this->sendFcmNotification($user, $notification_data, $is_admin);
    }

    public function sendFcmNotification($user, $notification_data, $is_admin = false)
    {
        $firebaseTokens = [];
        if ($is_admin) {
            $firebaseTokens = \App\User\Domain\Models\DeviceToken::where('tokenable_type', 'App\Admin\Domain\Models\Admin')->pluck('device_token')->all();
        } else {
            if ($user) {
                $firebaseTokens = $user->deviceTokens->pluck('device_token')->all();
            } else {
                $firebaseTokens = \App\User\Domain\Models\DeviceToken::where('tokenable_type', 'App\User\Domain\Models\Customer')->pluck('device_token')->all();
            }
        }

        $notif_arr = array(
            'model_id' => $notification_data['model_id'] ?? null,
            'channelKey' => 'basic_channel',
            'channelId' => 'Atah_834485892691',
            'title' => $notification_data['title'],
            'body' => $notification_data['body'],
            'type' => $notification_data['type'] ?? null,
            'store_id' => $notification_data['store_id'] ?? null,
            'order_status' => $notification_data['order_status'] ?? null,
            'transaction_status' => $notification_data['transaction_status'] ?? null,
        );

        $data_arr = array(
            'model_id' => $notification_data['model_id'] ?? null,
            'channelKey' => 'basic_channel',
            'channelId' => 'Atah_834485892691',
            'title' => $notification_data['title'],
            'body' => $notification_data['body'],
            'store_id' => $notification_data['store_id'] ?? null,
            'type' => $notification_data['type'] ?? null,
            'order_status' => $notification_data['order_status'] ?? null,
            'transaction_status' => $notification_data['transaction_status'] ?? null,
        );

        $data = [
            "registration_ids" => $firebaseTokens,
            "mutable_content" => true,
            "content_available" => true,
            "priority" => "high",
            "data" => $data_arr,
            "notification" => $notif_arr
        ];

        $dataString = json_encode($data);

        $headers = [
            'Authorization: key=' . config('app.fcm_server_key'),
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

        $response = curl_exec($ch);
        curl_close($ch);
        if ($response === false) {
            $result = 0;
        } else {

            $result = 1;
        }

        return $result;
    }

}


