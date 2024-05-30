<?php

namespace App\Infrastructure\Traits;

trait SMS
{
    public function generateCode($phone)
    {
        return cache()->remember($phone, 60, function () {
            //return rand(1000, 9999);
            return '1234';
        });
    }

    public function sendSMS($phone, $message){
        return true;
        // return $this->sendMsegatSms($phone, $message);
    }

    public function sendHismsSms($phone, $message){

        $number = $phone;
        $sender=env('sms_username');
    //    $sender='ARAS for AC';
        $url = "https://www.hisms.ws/api.php?send_sms&username=" . env('sms_user') .
            "&password=" . env('sms_password') . "&numbers=" . $number . "&sender=". $sender."&message=" . urlencode($message);
    //    dd($url);
    //    $options = array(
    //        CURLOPT_RETURNTRANSFER => true,     // return web page
    //        CURLOPT_HEADER => false,    // don't return headers
    //        CURLOPT_FOLLOWLOCATION => true,     // follow redirects
    //        CURLOPT_ENCODING => "",       // handle all encodings
    //        CURLOPT_USERAGENT => "spider", // who am i
    //        CURLOPT_AUTOREFERER => true,     // set referer on redirect
    //        CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
    //        CURLOPT_TIMEOUT => 120,      // timeout on response
    //        CURLOPT_MAXREDIRS => 10,       // stop after 10 redirects
    //        CURLOPT_SSL_VERIFYPEER => false     // Disabled SSL Cert checks
    //    );
    //    $ch = curl_init($url);
    //    curl_setopt_array($ch, $options);
    //    $content = curl_exec($ch);
    //    $err = curl_errno($ch);
    //    $errmsg = curl_error($ch);
    //    $header = curl_getinfo($ch);
    //    curl_close($ch);
        $content = (int) file_get_contents($url);
        $result = validate_response($content);
        $msg = ['response' => $content, 'result' => $result];
    //    \Illuminate\Support\Facades\Log::info('sms',$msg);
        return $msg;

    }

    function sendMsegatSms($mobile_number, $msg){
       $data = [
           "userName" => config('services.msegat.msegat_user_name'),
           "userSender" => config('services.msegat.msegat_sender_name'),
           "numbers" => $mobile_number,
           "apiKey" => config('services.msegat.msegat_key'),
           "msg" => $msg,
           "msgEncoding"=>"UTF8"
       ];

       $client = new \GuzzleHttp\Client();
       $res = $client->request('POST', config('services.msegat.msegat_base_url'), [
           'headers' => [
               'Accept' => 'application/json',
               'Content-Type' => 'application/json',
               'Accept-Language' => app()->getLocale() == 'ar' ? 'ar-Sa' : 'en-Uk'
           ],
           'body' => json_encode($data),
       ]);
       if($res){
           $data = json_decode($res->getBody()->getContents());
           dd($data);
           if($data->code){
               return true;
           }else{
               return false;
           }
       }else{
           return false;
       }
    }

}


