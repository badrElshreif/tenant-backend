<?php

namespace App\Tenant\Store\Domain\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StoreAcceptedEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $store_name, $email, $password;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($store_name, $email, $password)
    {
        $this->store_name = $store_name;
        $this->email = $email;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.storeAccepted');
    }
}
