<?php

namespace App\Tenant\Store\Domain\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StoreRejectedEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $store_name, $reason;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($store_name, $reason)
    {
        $this->store_name = $store_name;
        $this->reason = $reason;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.storeRejected');
    }
}
