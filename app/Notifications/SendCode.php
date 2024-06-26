<?php

namespace App\Notifications;

use App\Enums\SmsProvider;
use App\Helpers\Providers;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendCode extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $type;

    protected ?string $code;

    protected ?string $token;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(?string $code = null, string $type = 'reset', ?string $token = null)
    {
        $this->type = $type;
        $this->code = $code;
        $this->token = $token;
        $this->afterCommit();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array
     */
    public function via($notifiable)
    {
        $channels = str($this->type)->after('verify-')->is('phone')
            ? [SmsProvider::getChannel()]
            : (
                str($this->type)->is('verify')
                ? ['mail']
                : Providers::config('prefered_notification_channels', ['mail', 'sms'])
            );


        return collect($channels)->map(fn ($ch) => $ch == 'sms' ? SmsProvider::getChannel() : $ch)->toArray();
    }

    /**
     * Get the mail representation of the notification.
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $this->code ??= $notifiable->code;
        $this->token ??= $notifiable->token;
        $notifiable = $notifiable->user ?? $notifiable;

        /** @var \Carbon\Carbon */
        $datetime = $notifiable->last_attempt;

        $dateAdd = $datetime?->addSeconds(Providers::config('token_lifespan', 30));

        $message = Providers::messageParser(
            "send_code::$this->type",
            $notifiable,
            [
                'type' => $this->type,
                'code' => $this->code,
                'token' => $this->token,
                'duration' => $dateAdd->longAbsoluteDiffForHumans(),
            ]
        );

        return (new MailMessage())
            ->subject($message->subject)
            ->view(['email', 'email-plain'], [
                'subject' => $message->subject,
                'lines' => $message->lines
            ]);
    }

    /**
     * Get the sms representation of the notification.
     *
     * @param  mixed  $n  notifiable
     */
    public function toSms($n)
    {
        $this->code ??= $n->code;
        $this->token ??= $n->token;
        $n ??= $n->user ?? $n;

        /** @var \Carbon\Carbon */
        $datetime = $n->last_attempt;
        $dateAdd = $datetime?->addSeconds(Providers::config('token_lifespan', 30));

        $message = [
            'reset' => __('Use this code :0 to reset your :1 password, It expires in :2.', [
                $this->code,
                Providers::config('site_name'),
                $dateAdd->longAbsoluteDiffForHumans(),
            ]),
            'verify-phone' => __('use this code :0 to verify your :1 phone number, It expires in :2.', [
                $this->code,
                Providers::config('site_name'),
                $dateAdd->longAbsoluteDiffForHumans(),
            ]),
        ];

        if (isset($message[$this->type])) {
            $message = __('Hi :0, ', [$n->firstname]) . $message[$this->type];

            return SmsProvider::getMessage($message);
        }
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
