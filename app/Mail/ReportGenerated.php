<?php

namespace App\Mail;

use App\Helpers\Provider;
use App\Helpers\Url;
use Carbon\CarbonImmutable;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReportGenerated extends Mailable
{
    use Queueable, SerializesModels;

    public \App\Services\MessageParser $message;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        public Model $form,
        public $batch = null,
        public $title = null
    ) {
        $timestamp = CarbonImmutable::now()->timestamp;

        $sid = 'dataset';
        // $sid = $form instanceof Form ? $form->id : 'dataset';
        $encoded = "/$timestamp/{$sid}";
        $params = Url::base64urlEncode(str(get_class($form))->replace('\\', '.')->append($encoded));

        $this->message = Provider::messageParser(
            'send_report',
            [
                'form_name' => $this->title ?? $this->form->name,
                'period' => 'daily',
                'link' => route('download.formdata', [$timestamp, $params, $this->batch]),
                'ttl' => '10 hours',
                'app_name' => dbconfig('app_name'),
            ]
        );
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->message->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            html: 'email',
            text: 'email-plain',
            with: [
                'subject' => $this->message->subject,
                'lines' => $this->message->lines,
            ],
        );
    }
}
