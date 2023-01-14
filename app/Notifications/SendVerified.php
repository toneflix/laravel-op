<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Twilio\TwilioSmsMessage;

class SendVerified extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($type = 'mail')
    {
        $this->type = $type;
        $this->afterCommit();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [$this->type, 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $message = [
            'name' => $notifiable->firstname,
            'message_line1' => __('You are receiving this email because you just verified your at account :0 and we want to use this medium to welcome you into our community', [config('settings.site_name')]),
            'close_greeting' => __('Regards, <br/>:0', [config('settings.site_name')]),
        ];

        return (new MailMessage)->view(
            ['email', 'email-plain'], $message
        )
        ->subject(__('Welcome to the :0 community.', [config('settings.site_name')]));
    }

    /**
     * Get the sms representation of the notification.
     *
     * @param  mixed  $n    notifiable
     * @return \NotificationChannels\Twilio\TwilioSmsMessage
     */
    public function toTwilio($n)
    {
        $message = __('Your :0 account has been verified successfully, welcome to our community.', [config('settings.site_name')]);

        $message = __('Hi :0, ', [$n->firstname]).$message;

        return (new TwilioSmsMessage())
            ->content($message);

        return false;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($n)
    {
        return [
            'type' => 'verification',
            'title' => ($this->type === 'mail') ? __('Email Address Verified') : __('Phone Number Verified'),
            'message' => __('Your account has been verified successfully, welcome to our community.'),
        ];
    }
}