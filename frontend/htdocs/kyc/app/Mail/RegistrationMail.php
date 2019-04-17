<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegistrationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The User instance.
     *
     * @var User
     */
    protected $User;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($User)
    {
        $this->User = $User;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $User = $this->User;
        return $this->subject('Thank you for registering with '.env('WEBSITE_NAME').' :)')->view('email.register', compact('User'));
    }
}
