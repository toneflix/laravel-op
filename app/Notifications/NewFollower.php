<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;

class NewFollower extends Notification
{
    use Queueable;

    protected $user;

    protected $action;

    protected $map_actions = [
        'follow' => 'is now following',
        'unfollow' => 'unfollowed',
    ];

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user, $action = 'follow')
    {
        $this->user = $user;
        $this->action = $action;
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
            'message_line1' => __(':user :action you.', [
                'user' => $this->user->fullname,
                'action' => $this->map_actions[$this->action],
            ]),
            'close_greeting' => 'Regards, <br/>'.config('settings.site_name'),
        ];

        return (new MailMessage)->view(
            ['email', 'email-plain'], $message
        )->subject(__(':user :actioned you', [
            'user' => $this->user->fullname,
            'action' => $this->action,
        ]));
    }

    /**
     * Get the sms representation of the notification.
     *
     * @param  mixed  $notifiable    notifiable
     * @return \NotificationChannels\Twilio\TwilioSmsMessage
     */
    public function toTwilio($notifiable)
    {
        $message = __(':user :action you.', [
            'user' => $this->user->fullname,
            'action' => $this->map_actions[$this->action],
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
        return [
            'message' => __(':user :action you.', [
                'user' => $this->user->fullname,
                'action' => $this->map_actions[$this->action],
            ]),
            'title' => 'Follower Notification',
            'type' => 'relationship',
            'actions' => [
                [
                    'label' => 'View Profile',
                    'action' => route('user.profile', ['user' => $this->user->username]),
                ],
            ],
            'icon' => 'groups',
            'image' => $this->user->avatar,
        ];
    }
}
