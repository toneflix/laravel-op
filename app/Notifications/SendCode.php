<?php

namespace App\Notifications;

use App\Enums\SmsProvider;
use App\Helpers\Provider;
use App\Helpers\Url;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SendCode extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        public ?string $code = null,
        public string $type = 'reset',
        public ?string $token = null
    ) {
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
                : Provider::config('prefered_notification_channels', ['mail', 'sms'])
            );

        return collect($channels)->map(fn($ch) => $ch == 'sms' ? SmsProvider::getChannel() : $ch)->toArray();
    }

    /**
     * Get the mail representation of the notification.
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $this->code ??= $notifiable->code;
        $this->token ??= $notifiable->token ?? Url::base64urlEncode($this->code . '|' . md5(time()));
        $notifiable = $notifiable->user ?? $notifiable;

        /** @var \Carbon\Carbon */
        $datetime = $notifiable->last_attempt;

        $dateAdd = $datetime?->addSeconds(Provider::config('token_lifespan', 30));

        $message = Provider::messageParser(
            "send_code::$this->type",
            $notifiable,
            [
                'type' => $this->type,
                'code' => $this->code,
                'token' => $this->token,
                'label' => 'email address',
                'app_url' => config('app.frontend_url', config('app.url')),
                'app_name' => Provider::config('app_name'),
                'duration' => $dateAdd->longAbsoluteDiffForHumans(),
            ]
        );

        return $message->toMail();
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
        $dateAdd = $datetime?->addSeconds(Provider::config('token_lifespan', 30));

        $message = Provider::messageParser(
            "send_code::$this->type",
            $n,
            [
                'type' => $this->type,
                'code' => $this->code,
                'token' => $this->token,
                'label' => 'email address',
                'app_url' => config('app.frontend_url', config('app.url')),
                'app_name' => Provider::config('app_name'),
                'duration' => $dateAdd->longAbsoluteDiffForHumans(),
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
