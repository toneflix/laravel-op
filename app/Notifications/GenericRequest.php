<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Jamesmills\LaravelNotificationRateLimit\RateLimitedNotification;
use Jamesmills\LaravelNotificationRateLimit\ShouldRateLimit;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;

class GenericRequest extends Notification implements ShouldQueue, ShouldRateLimit
{
    use Queueable, RateLimitedNotification;

    protected $status;

    protected $generic;

    protected $overide;

    protected $message;

    protected $map_types = [
        'book_session' => 'book a session',
    ];

    protected $rateLimitForSeconds = 15;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($generic, $status = null, $overide = null)
    {
        $this->status = $status;
        $this->generic = $generic;
        $this->overide = $overide;
        if ($overide) {
            $this->message = $overide['message'];
        } else {
            $this->message = $status
                ? __(':0 :1 your request to :2', [
                    $generic->user->fullname,
                    $status,
                    $this->map_types[$generic->meta['type'] ?? '-'] ?? $generic->meta['type'],
                ])
                : null;
        }
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
        $pref = config('settings.prefered_notification_channels', ['mail']);
        $channels = in_array('sms', $pref) && in_array('mail', $pref)
            ? ['mail', TwilioChannel::class]
            : (in_array('sms', $pref)
                ? [TwilioChannel::class]
                : ['mail']);

        if ($this->overide) {
            return ['database'];
        }

        return collect($channels)
            ->merge(['database'])
            ->all();
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
            'message_line1' => $this->message ?? __(':message. Please login to respond.', [
                'message' => $this->generic->meta['details'] ?? $this->generic->message ?? '',
            ]),
            'close_greeting' => 'Regards, <br/>'.config('settings.site_name'),
        ];

        return (new MailMessage)->view(
            ['email', 'email-plain'], $message
        )->subject(str(__($this->message ? 'New request to :0' : ':0 request :1', [
            $this->map_types[$this->generic->meta['type'] ?? '-'] ?? $this->generic->meta['type'],
            $this->status,
        ]))->ucfirst());
    }

    /**
     * Get the sms representation of the notification.
     *
     * @param  mixed  $notifiable    notifiable
     * @return \NotificationChannels\Twilio\TwilioSmsMessage
     */
    public function toTwilio($notifiable)
    {
        $message = $this->message ?? __(':message. Please login to respond.', [
            'message' => $this->generic->meta['details'] ?? $this->generic->message ?? '',
        ]);

        return (new TwilioSmsMessage())->content($message);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        if ($this->overide) {
            return $this->overide;
        }

        $notification_array = [
            'message' => $this->message ?? $this->generic->meta['details'] ?? $this->generic->message ?? '',
            'title' => str(__($this->message ? 'New request to :0' : ':0 request :1', [
                $this->map_types[$this->generic->meta['type'] ?? '-'] ?? $this->generic->meta['type'],
                $this->status,
            ]))->ucfirst(),
            'type' => 'generic_request',
            'actions' => ['accepted' => 'Accept', 'rejected' => 'Reject'],
            'icon' => 'fa-solid fa-user-group',
            'image' => $this->generic->sender->avatar,
            'request' => [
                'id' => $this->generic->id,
                'type' => get_class($this->generic),
            ],
        ];

        return $notification_array;
    }
}
