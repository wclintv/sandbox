<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Email;
use App\Models\Invitation;

class Invite extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $email;
    public $invitation;
    public $callback_url;

    public function __construct(Email $email,Invitation $invitation, $callback_url)
    {
        $this->email = $email;
        $this->invitation = $invitation;
        $this->callback_url = $callback_url;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("Invitation to snapdsk.com")->view('emails.invitation');
    }
}
