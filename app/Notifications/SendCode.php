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

    protected $type;

    protected $token;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($token = null, $type = 'reset')
    {
        $this->type = $type;
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
        $this->token ??= $notifiable->code;

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
        $notifiable = $notifiable->user ?? $notifiable;

        /** @var \Carbon\Carbon */
        $datetime = $notifiable->last_attempt;

        $dateAdd = $datetime?->addSeconds(config('settings.token_lifespan', 30));

        $message = [
            'reset' => [
                'name' => $notifiable->firstname,
                'cta' => ['code' => $this->token],
                'message_line1' => 'You are receiving this email because we received a password reset request for your account.',
                'message_line2' => __('This password reset code will expire in :0.', [
                    $dateAdd->longAbsoluteDiffForHumans(),
                ]),
                'message_line3' => 'If you did not request a password reset, no further action is required.',
                'close_greeting' => __('Regards, <br/>:0', [
                    config('settings.site_name'),
                ]),
                'message_help' => 'Please use the code above to recover your account ',
            ],
            'verify' => [
                'name' => $notifiable->firstname,
                'cta' => ['code' => $this->token],
                'message_line1' => __('You are receiving this email because you created an account on <b>:0</b> and we need to verify that you own this email addrress. <br /> use the code below to verify your email address.', [
                    config('settings.site_name'),
                ]),
                'message_line2' => __('This verification code will expire in :0.', [
                    $dateAdd->longAbsoluteDiffForHumans(),
                ]),
                'message_line3' => 'If you do not recognize this activity, no further action is required as the associated account will be deleted in few days if left unverified.',
                'close_greeting' => __('Regards, <br/>', [config('settings.site_name')]),
                'message_help' => 'Please use the code above to verify your account ',
            ],
        ];

        if (isset($message[$this->type])) {
            return (new MailMessage())->view(['email', 'email-plain'], $message[$this->type])
                ->subject(__($this->type === 'reset'
                    ? 'Reset your :0 password.'
                    : 'Verify your account at :0', [config('settings.site_name')]));
        }
    }

    /**
     * Get the sms representation of the notification.
     *
     * @param  mixed  $n  notifiable
     */
    public function toSms($n)
    {
        $n = $n->user ?? $n;

        /** @var \Carbon\Carbon */
        $datetime = $n->last_attempt;
        $dateAdd = $datetime?->addSeconds(config('settings.token_lifespan', 30));

        $message = [
            'reset' => __('Use this code :0 to reset your :1 password, It expires in :2.', [
                $this->token,
                config('settings.site_name'),
                $dateAdd->longAbsoluteDiffForHumans(),
            ]),
            'verify-phone' => __('use this code :0 to verify your :1 phone number, It expires in :2.', [
                $this->token,
                config('settings.site_name'),
                $dateAdd->longAbsoluteDiffForHumans(),
            ]),
        ];

        if (isset($message[$this->type])) {
            $message = __('Hi :0, ', [$n->firstname]).$message[$this->type];

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
