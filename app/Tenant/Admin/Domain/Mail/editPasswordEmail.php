<?php

namespace App\Tenant\Admin\Domain\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class editPasswordEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $admin_name, $email, $password;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($admin_name, $email, $password)
    {
        $this->admin_name = $admin_name;
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
        return $this->markdown('emails.editPasswordEmail',[ $this->admin_name, $this->email, $this->password]);
    }
}
