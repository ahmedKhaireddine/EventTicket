<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeMailable extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The name of user model.
     *
     * @var string
     */
    public $name;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(trans('Welcome to Events'))
            ->view('emails.welcome')
            ->with([
                'greeting' => trans('welcome-email.greeting') . $this->name . trans('welcome-email.text'),
                'description' => trans('welcome-email.description'),
                'website' => trans('welcome-email.website'),
                'admin' => trans('welcome-email.admin'),
                'news' => trans('welcome-email.news'),
            ]);
    }
}
