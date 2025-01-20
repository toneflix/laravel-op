<?php

namespace App\Models;

use App\Helpers\Providers;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * @property int $id;
 * @property string $key The message template key;
 * @property string $subject The message subject;
 * @property string $plain The message plain content;
 * @property string $html The message html content;
 * @property array<string, string> $args;
 * @property bool $active If the template is active;
 * @property \Carbon\Carbon $created_at;
 * @property \Carbon\Carbon $updated_at;
 */
class NotificationTemplate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'key',
        'subject',
        'plain',
        'html',
        'args',
        'active',
        'allowed',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array<string, string>
     */
    protected $attributes = [
        'args' => '[]',
        'allowed' => '[]',
        'active' => true,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts()
    {
        return [
            'args' => \Illuminate\Database\Eloquent\Casts\AsCollection::class,
            'allowed' => \Illuminate\Database\Eloquent\Casts\AsCollection::class,
            'active' => 'boolean',
        ];
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param  string|null  $field
     * @return self|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        try {
            return $this->where('id', $value)
                ->orWhere('key', $value)
                ->firstOrFail();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return self::buildDefault($value, true);
        }
    }

    /**
     * Get defaults from settings
     *
     * @return Collection<int, static>
     */
    public static function loadDefaults(): Collection
    {
        return new Collection(collect(config('messages'))->map(
            fn ($_, $key) => self::buildDefault($key)
        )->filter(fn ($_, $key) => $key !== 'signature')->values());
    }

    /**
     * Get defaults from settings
     *
     * @return Collection<int, static>
     */
    public static function buildDefault(string $key, bool $strict = false): self
    {
        $parsed = Providers::messageParser($key);
        $allowed = config("messages.$key.allowed", ['html', 'plain']);

        if ($parsed->notFound && $strict) {
            throw (new ModelNotFoundException('Error Processing Request', 1))->setModel(new static());
        }

        preg_match_all('/:(\w+)/', $parsed->plainBody, $args);

        $html = (new MailMessage())
            ->view(['email', 'email-plain'], [
                'lines' => $parsed->lines,
                'subject' => $parsed->subject,
            ])->render();

        $template = new static([
            'id' => -1,
            'key' => $key,
            'subject' => $parsed->subject,
            'plain' => $parsed->plainBody,
            'html' => $html,
            'args' => collect([
                'firstname',
                'lastname',
                'fullname',
                'email',
                'phone',
                'app_name',
                'app_url',
            ])->merge($args[1])->unique(),
            'active' => true,
            'allowed' => $allowed,
        ]);

        $template->id = -1;

        return $template;
    }
}
