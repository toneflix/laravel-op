<?php

namespace App\Notifications;

use App\Helpers\Provider;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountVerified extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public string $type = 'email'
    ) {
        $this->afterCommit();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [...Provider::config('prefered_notification_channels', ['mail', 'sms']), 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $message = Provider::messageParser(
            'send_verified',
            $notifiable,
            [
                'type' => $this->type,
                'label' => 'email address',
                'app_url' => config('app.frontend_url', config('app.url')),
                'app_name' => Provider::config('app_name'),
            ]
        );

        return $message->toMail();
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $message = Provider::messageParser(
            'send_verified:sms',
            $notifiable,
            [
                'type' => $this->type,
                'label' => 'phone number',
                'app_url' => config('app.frontend_url', config('app.url')),
                'app_name' => Provider::config('app_name'),
            ]
        );

        return [
            'title' => $message->subject,
            'message' => $message->toPlain(),
            'important' => false,
        ];
    }

    /**
     * Get the sms representation of the notification.
     *
     * @param  mixed  $n  notifiable
     */
    public function toSms($n)
    {
        $n ??= $n->user ?? $n;

        $message = Provider::messageParser(
            'send_verified:sms',
            $n,
            [
                'type' => $this->type,
                'label' => 'phone number',
                'app_url' => config('app.frontend_url', config('app.url')),
                'app_name' => Provider::config('app_name'),
            ]
        );

        return SmsProvider::getMessage($message->toPlain());
    }

    public function toTwilio($n): \NotificationChannels\Twilio\TwilioSmsMessage
    {
        return $this->toSms($n);
    }

    public function toKudiSms($n): \ToneflixCode\KudiSmsNotification\KudiSmsMessage
    {
        return $this->toSms($n);
    }
}
