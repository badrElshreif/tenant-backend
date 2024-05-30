<?php
namespace App\Infrastructure\Traits;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\File;

class MyFatoorah
{
    protected $apiURL;
    protected $apiKey;

    public function __construct()
    {

        if (env('APP_ENV') == 'local')
        {
            $this->apiURL = config('attah.payment_test_url');
            $this->apiKey = config('attah.payment_test_token');
        }
        elseif (env('APP_ENV') == 'live')
        {
//Live

            $this->apiURL = config('attah.payment_live_url');
            $this->apiKey = config('attah.payment_live_token');
        }

    }

    public function getMethods()
    {

/* ------------------------ Call InitiatePayment Endpoint ------------------- */
//Fill POST fields array
        $ipPostFields = ['InvoiceAmount' => 100, 'CurrencyIso' => 'SAR'];

//Call endpoint
        $paymentMethods = $this->initiatePayment($this->apiURL, $this->apiKey, $ipPostFields);

        return $paymentMethods;

//You can save $paymentMethods information in database to be used later

//        foreach ($paymentMethods as $pm) {

//            if ($pm->PaymentMethodEn == 'VISA/MASTER') {

//                $paymentMethodId = $pm->PaymentMethodId;

//                break;

//            }
//        }
    }

    public function doPayment($data, $session_id = null, $brand_id = null, $type = null, $cvv = null)
    {
        $total = $data->remain == 0.00 ? $data->total : $data->remain;
        /* ------------------------ Call ExecutePayment Endpoint -------------------- */

        $postFields = [

//Fill required data
            //'PaymentMethodId' => intval($brand_id),
            'SessionId'          => $session_id,
            'InvoiceValue'       => $total,
            'CallBackUrl'        => route('check-payment', ['lang' => app()->getLocale()]),
            'ErrorUrl'           => route('check-payment', ['lang' => app()->getLocale()]), //or 'https://example.com/error.php'
            'CustomerName'      => $data->user->name,
            'DisplayCurrencyIso' => 'SAR',
            'MobileCountryCode'  => '+966',
            'CustomerMobile'     => $data->user->phone,
            'CustomerEmail'      => $data->user->email,
            'Language'           => app()->getLocale(), //or 'ar'
            'CustomerReference' => $data->id,
            'UserDefinedField'   => "CK-" . $data->user->id
//'This could be string, number, or array',

//'ExpiryDate'         => '', //The Invoice expires after 3 days by default. Use 'Y-m-d\TH:i:s' format in the 'Asia/Kuwait' time zone.

//'SourceInfo'         => 'Pure PHP', //For example: (Laravel/Yii API Ver2.0 integration)

//'CustomerAddress'    => $customerAddress,
            //'InvoiceItems'       => $invoiceItems,
        ];

        //Call endpoint

        $data = $this->executePayment($this->apiURL, $this->apiKey, $postFields);

        //You can save payment data in database as per your needs
        $invoiceId  = $data->InvoiceId;
        $paymentURL = $data->PaymentURL;

// 2129918

//if($data->IsDirectPayment == true){

/* ------------------------ Call DirectPayment Endpoint --------------------- */

//Fill POST fields array

// $cardInfo = [

//     'PaymentType' => 'card',

//     'Bypass3DS'   => false,

//     'Card'        => [

//         'Number'         => $card->number,

//         'ExpiryMonth'    => $card->expiry_month,

//         'ExpiryYear'     => $card->expiry_year,

//         'SecurityCode'   => $cvv,

//         'CardHolderName' => $card->holder_name

//     ]

// ];

//Call endpoint

//     $data =$this->directPayment($paymentURL, $this->apiKey, $cardInfo);
        // }

        return $data;
    }

    public function checkPayment($id)
    {
        $data = array(
            'KeyType' => 'paymentId',
            'Key'     => "$id" //the callback paymentID
        );

        $json = $this->callAPI("$this->apiURL/v2/getPaymentStatus", $this->apiKey, $data);
        // dd( $json );
        return $json;
    }

/* ------------------------ Functions --------------------------------------- */
    /*
     * Initiate Payment Endpoint Function
     */

    function initiatePayment($apiURL, $apiKey, $postFields)
    {

        $json = $this->callAPI("$apiURL/v2/InitiatePayment", $apiKey, $postFields);

        return $json->Data->PaymentMethods;
    }

    /*
     * Initiate Payment Endpoint Function
     */

    function initiateSession($customerIdentifier)
    {
        $postFields = array(
            'CustomerIdentifier' => $customerIdentifier
        );
        $json = $this->callAPI("$this->apiURL/v2/InitiateSession", $this->apiKey, $postFields);
        // dd($json->Data);
        return $json->Data;
    }

//------------------------------------------------------------------------------
    /*
     * Execute Payment Endpoint Function
     */

    function executePayment($apiURL, $apiKey, $postFields)
    {

// Log::info('data payment: ');
        // Log::info($postFields);

        $json = $this->callAPI("$apiURL/v2/ExecutePayment", $apiKey, $postFields);

        return $json->Data;
    }

//------------------------------------------------------------------------------
    /*
     * Execute Payment Endpoint Function
     */

    function directPayment($paymentURL, $apiKey, $postFields)
    {

        $json = $this->callAPI($paymentURL, $apiKey, $postFields);
        return $json->Data;
    }

//------------------------------------------------------------------------------
    /*
     * It is used to cancel the payment and return the funds to the customer.
     */

    public function MakeRefund($id, $amount)
    {
        $data = array(
            'KeyType' => 'paymentId',
            'Key'     => "$id" , //the callback paymentID
            "RefundChargeOnCustomer" => true,
            "ServiceChargeOnCustomer"=> true,
            "Amount"=> $amount,
            "Comment"=> "Make a Refund",
            "AmountDeductedFromSupplier"=> 0,
            "CurrencyIso"=> "SAR"
        );

        $json = $this->callAPI("$this->apiURL/v2/MakeRefund", $this->apiKey, $data);
        // dd( $json );
        return $json;
    }
//------------------------------------------------------------------------------
    /*
     * suppliers Functions
     */

    function createSupplier($store)
    {

        $postFields = array(
            'SupplierName'          => $store->name == null ? $store->translate('ar')->name : $store->name,
            'Mobile'                => '966' . $store->phone,
            "Email"                 => $store->email,
            "CommissionValue"       => 0,
            "CommissionPercentage"  => 0,
            // "DepositTerms"=> "OnDemand",
            "BankId"                => $store->mf_bnk_id ?? 0,
            "BankAccountHolderName" => $store->mf_bnk_acnt_hldr_name ?? "",
            "BankAccount"           => $store->mf_bnk_acnt_no ?? "",
            "Iban"                  => $store->mf_iban_no ?? "",
            "IsActive"              => true
        );
        $json = $this->callAPI("$this->apiURL/v2/CreateSupplier", $this->apiKey, $postFields);

        return $json;
    }

    function updateSupplier($store)
    {
        $postFields = array(
            'SupplierCode'          => intval($store->myfatoorah_code),
            'SupplierName'          => $store->name,
            'Mobile'                => '966' . $store->phone,
            "Email"                 => $store->email,
            "CommissionValue"       => 0,
            "CommissionPercentage"  => 0,
            // "DepositTerms"=> "OnDemand",
            "BankId"                => $store->mf_bnk_id ?? 0,
            "BankAccountHolderName" => $store->mf_bnk_acnt_hldr_name ?? "",
            "BankAccount"           => $store->mf_bnk_acnt_no ?? "",
            "Iban"                  => $store->mf_iban_no ?? "",
            "IsActive"              => true
        );
        $json = $this->callAPI("$this->apiURL/v2/EditSupplier", $this->apiKey, $postFields);

        return $json;
    }

    function UploadSupplierDocument($file_data)
    {
        $postFields = array(
            'SupplierCode' => intval($file_data['SupplierCode']),
            'FileUpload'   => $file_data['FileUpload'],
            'FileType'     => $file_data['FileType']
            //"ExpireDate"=> '',
        );
        //return (json_encode($postFields));
        $printData = [
            'url'     => "$this->apiURL/v2/UploadSupplierDocument", $this->apiKey,
            'request' => $postFields,
            'auth'    => "Authorization: Bearer $this->apiKey"
        ];

        // var_dump($printData);

        $json = $this->callAPI("$this->apiURL/v2/UploadSupplierDocument", $this->apiKey, $postFields, 'PUT');
        // dd($json,$postFields);
        return $json;
    }

    function transferBalance($supplierCode, $transferAmount, $transferType = 'push')
    {
        $postFields = array(
            'SupplierCode'   => intval($supplierCode),
            'TransferAmount' => floatval($transferAmount),
            'TransferType'   => $transferType
            //"InternalNotes"=> "",
        );
        $json = $this->callAPI("$this->apiURL/v2/TransferBalance", $this->apiKey, $postFields);

        return $json;
    }

    function getBanksList()
    {

        // $json = $this->callAPI("$this->apiURL/v2/GetBanks", $this->apiKey, [], 'GET');

        $client = new Client(['headers' => [
            'Authorization'                    => "Bearer " . $this->apiKey,
            'Accept'                           => "application/json",
            'Content-Type'                     => "application/json",
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow-Credentials' => 'true'
        ]]);

        $response = $client->get($this->apiURL . '/v2/GetBanks');

        $response = json_decode($response->getBody());

        return $response;
    }

//------------------------------------------------------------------------------
    /*
     * Call API Endpoint Function
     */

    function callAPI($endpointURL, $apiKey, $postFields = [], $requestType = 'POST')
    {
        $content_type = "";

        if (! isset($postFields['FileUpload']))
        {
            $content_type = "application/json ";
        }

        $curl = curl_init($endpointURL);
        curl_setopt_array($curl, array(
            CURLOPT_CUSTOMREQUEST  => $requestType,
            CURLOPT_POSTFIELDS     => json_encode($postFields),
            CURLOPT_HTTPHEADER     => array("Authorization: Bearer $apiKey", "Content-Type: $content_type"),
            CURLOPT_RETURNTRANSFER => true
        ));
        // dd($content_type);

        $response = curl_exec($curl);

        if (isset($postFields['FileUpload']))
        {

//var_dump($response);

// return $response;
            // dd($postFields);

        }

        $curlErr = curl_error($curl);

        curl_close($curl);

        if ($curlErr)
        {
            //Curl is not working in your server
            throw new \Exception($curlErr);
//            die("Curl Error: $curlErr");
        }

        $error = $this->handleError($response);

// Log::error('response payment');

// Log::error($error);
        if ($error)
        {
//            die("Error: $error");
            throw new \Exception($error);
        }

        return json_decode($response);
    }

//------------------------------------------------------------------------------
    /*
     * Handle Endpoint Errors Function
     */

    function handleError($response)
    {

        $json = json_decode($response);

        if (isset($json->IsSuccess) && $json->IsSuccess == true)
        {
            return null;
        }

//Check for the errors
        if (isset($json->ValidationErrors) || isset($json->FieldsErrors))
        {
            $errorsObj = isset($json->ValidationErrors) ? $json->ValidationErrors : $json->FieldsErrors;
            $blogDatas = array_column($errorsObj, 'Error', 'Name');

            $error = implode(', ', array_map(function ($k, $v)
            {
                return "$k: $v";
            }, array_keys($blogDatas), array_values($blogDatas)));
        }
        else if (isset($json->Data->ErrorMessage))
        {
            $error = $json->Data->ErrorMessage;
        }

        if (empty($error))
        {
            $error = (isset($json->Message)) ? $json->Message : (! empty($response) ? $response : 'API key or API URL is not correct');
        }

        return $error;
    }

    /* -------------------------------------------------------------------------- */

    public static function pathToUploadedFile($path)
    {

        $name = File::name($path);

        $extension = File::extension($path);

        $originalName = $name . '.' . $extension;

        $mimeType = File::mimeType($path);

// $error = null;

// $test = $public;

//$uploaded_file = new UploadedFile( $path, $originalName, $mimeType, $error, $test );

//   $object = (object)[

//     "FileName"=>  $originalName ,

//     "MediaType"=> $mimeType,

//     "Buffer"=> $uploaded_file,
        //   ];

    }



}
