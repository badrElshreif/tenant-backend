<?php

namespace App\Tenant\Store\Domain\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StoreAdminAcceptedEmail extends Mailable
{
    use Queueable, SerializesModels;
    // public $store_name, $username, $email, $phone;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    // public function __construct($store_name, $username, $email, $phone)
    public function __construct()
    {
        // $this->store_name = $store_name;
        // $this->username = $username;
        // $this->email = $email;
        // $this->phone = $phone;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.storeAdminAccepted');
    }
}
