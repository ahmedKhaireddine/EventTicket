<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetNotification extends Notification
{
    use Queueable;

    /**
     *  The access token of user model.
     *
     * @var string
     */
    public $token;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        // A remplacé par la suite
        // Lien vers la page qui contient le formulaire de réinitialisation de mot de passe
        $url = "http://127.0.0.1:8000/{$this->token}";

        return (new MailMessage)->view(
            'emails.forgot-email',
            [
                'button_sentence' => trans('reset-password.button_sentence'),
                'greeting' => trans('reset-password.greeting'),
                'description_line' => trans('reset-password.description_line'),
                'additional_information' => trans('reset-password.additional_information'),
                'ending_sentence' => trans('reset-password.ending_sentence'),
                'team_sentance' => trans('reset-password.team_sentance'),
                'title' => trans('reset-password.title'),
                'url' => $url,
            ]
        );
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
