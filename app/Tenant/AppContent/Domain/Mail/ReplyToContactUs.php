<?php

namespace App\Tenant\AppContent\Domain\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Tenant\AppContent\Domain\Models\Setting;
class ReplyToContactUs extends Mailable
{
    use Queueable, SerializesModels;
    public $message, $subject;
     /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($message, $subject)
    {
        $this->message = $message;
        $this->subject = $subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("Replay To ".$this->subject)->markdown('emails.ReplyToContactUs', [
                    'message' => $this->message,
                    'subject' => $this->subject,
                ]);

    }
}
