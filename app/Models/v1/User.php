<?php

namespace App\Models\v1;

use App\Notifications\SendCode;
use App\Traits\Extendable;
use App\Traits\Permissions;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Scout\Searchable as Scoutable;
use Overtrue\LaravelFollow\Traits\Followable;
use Overtrue\LaravelFollow\Traits\Follower;
use Propaganistas\LaravelPhone\Exceptions\CountryCodeException;
use Propaganistas\LaravelPhone\Exceptions\NumberFormatException;
use Propaganistas\LaravelPhone\Exceptions\NumberParseException;
use Propaganistas\LaravelPhone\PhoneNumber;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;
use ToneflixCode\LaravelFileable\Traits\Fileable;

class User extends Authenticatable implements MustVerifyEmail, Searchable
{
    use HasApiTokens, HasFactory, Notifiable, Extendable, Permissions, Fileable, Scoutable, Follower, Followable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        // 'privileges',
        'firstname',
        'lastname',
        'address',
        'country',
        'state',
        'city',
        'email',
        'phone',
        'username',
        'password',
        'type',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verify_code',
        'phone_verify_code',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'last_attempt' => 'datetime',
        'access_data' => 'array',
        'privileges' => 'array',
        'verified' => 'boolean',
        'settings' => 'array',
        'dob' => 'datetime',
        'last_seen' => 'datetime',
        'hidden' => 'boolean',
        'verification_level' => 'integer',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'about',
        'avatar',
        'fullname',
        'role_name',
        'wallet_bal',
        'role_route',
        'permissions',
        'basic_stats',
        'onlinestatus',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'privileges' => '[]',
        'settings' => '{"newsletter":false,"updates":false, "noifications": false}',
    ];

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('id', $value)
            ->orWhere('username', $value)
            ->firstOrFail();
    }

    public function registerFileable()
    {
        $this->fileableLoader([
            'image' => 'avatar',
        ]);
    }

    public static function registerEvents()
    {
        static::creating(function ($user) {
            $eser = Str::of($user->email)->explode('@');
            $user->username = $user->username ?? $eser->first(fn ($k) => (User::where('username', $k)
                ->doesntExist()), $eser->first().rand(100, 999));
        });
    }

    /**
     * Get the name of the index associated with the model.
     *
     * @return string
     */
    public function searchableAs()
    {
        return 'users_index';
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    #[SearchUsingPrefix(['id', 'email', 'username'])]
    public function toSearchableArray()
    {
        return [
            'id' => (int) $this->id,
            'email' => $this->email,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'username' => $this->username,
            'country' => $this->country,
            'state' => $this->state,
            'city' => $this->city,
        ];
    }

    public function getSearchResult(): SearchResult
    {
        return new \Spatie\Searchable\SearchResult(
            $this,
            $this->fullname,
        );
    }

    /**
     * Add a cool default for empty user about.
     *
     * @return string
     */
    protected function about(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ?? config('default_user_about', 'Only business minded!'),
        );
    }

    /**
     * Get the URL to the fruit bay category's photo.
     *
     * @return string
     */
    protected function avatar(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->images['image'] ?? $this->default_image,
        );
    }

    public function basicStats(): Attribute
    {
        $friends = $this->friends;

        return new Attribute(
            get: fn () => [
                'followers' => 0,
                'following' => 0,
            ],
        );
    }

    public function fullname(): Attribute
    {
        return new Attribute(
            get: fn () => collect([
                $this->firstname,
                $this->lastname,
            ])->filter()->implode(' '),
        );
    }

    /**
     * Get name to use. Should be overridden in model to reflect your project
     *
     * @return string $name
     */
    public function getNameAttribute()
    {
        if ($this->firstname && $this->lastname) {
            return $this->fullname;
        }

        if ($this->firstname) {
            return $this->firstname;
        }

        if ($this->username) {
            return $this->username;
        }

        // if none is found, just return the email
        return $this->email;
    }

    public function hasVerifiedPhone()
    {
        return $this->phone_verified_at !== null;
    }

    /**
     * Get all of the user's reviews.
     */
    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    /**
     * Get all of the reviews made by this user.
     */
    public function reviewed()
    {
        return $this->hasMany(Review::class);
    }

    public function scopeIsOnline($query, $is_online = true)
    {
        if ($is_online) {
            // Check if the user's last last_seen was less than 5 minutes ago
            $query->where('last_seen', '>=', now()->subMinutes(5));
        } else {
            // Check if the user's last last_seen was more than 5 minutes ago
            $query->where('last_seen', '<', now()->subMinutes(5));
        }
    }

    public function scopeIsOnlineWithPrivilege($query, $privilege = 'admin', $is_online = true, $exclude = [])
    {
        $query->whereJsonContains('privileges', $privilege);
        $query->whereNotIn('id', $exclude);

        if ($is_online) {
            // Scope to only online users
            $query->isOnline();
        }

        $query->inRandomOrder();
    }

    public function markEmailAsVerified()
    {
        $this->last_attempt = null;
        $this->email_verify_code = null;
        $this->email_verified_at = now();
        $this->save();

        if ($this->wasChanged('email_verified_at')) {
            return true;
        }

        return false;
    }

    public function markPhoneAsVerified()
    {
        $this->last_attempt = null;
        $this->phone_verify_code = null;
        $this->phone_verified_at = now();
        $this->save();

        if ($this->wasChanged('phone_verified_at')) {
            return true;
        }

        return false;
    }

    /**
     * Return the user's online status
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function onlinestatus(): Attribute
    {
        return new Attribute(
            get: fn () => ($this->last_seen ?? now()->subMinutes(6))->gt(now()->subMinutes(5)) ? 'online' : 'offline',
        );
    }

    /**
     * Interact with the user's permissions.
     *
     * @return  \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function permissions(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getPermissions($this),
        );
    }

    /**
     * Interact with the user's phone.
     *
     * @return  \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function phone(): Attribute
    {
        return Attribute::make(
            set: function ($value) {
                try {
                    return ['phone' => $value ? (string) PhoneNumber::make($value, $this->ipInfo('country'))->formatE164() : $value];
                } catch (NumberParseException | NumberFormatException | CountryCodeException $th) {
                    return ['phone' => $value];
                }
            }
        );
    }

    /**
     * Interact with the user's role.
     *
     * @return  \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function roleName(): Attribute
    {
        return Attribute::make(
            get: fn () => ($this->role === 'user'
                ? 'User'
                : ($this->role === 'admin'
                    ? 'Admin'
                    : 'Unknown'
                )
            ),
        );
    }

    /**
     * Interact with the user's role.
     *
     * @return  \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function roleRoute(): Attribute
    {
        return Attribute::make(
            get: fn () => ($this->role === 'user'
                ? 'feeds'
                : ($this->role === 'admin'
                    ? 'feeds'
                    : 'feeds'
                )
            ),
        );
    }

    /**
     * Route notifications for the mail channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return array|string
     */
    public function routeNotificationForMail()
    {
        // Return email address and name...
        return [$this->email => $this->firstname];
    }

    /**
     * Route notifications for the twillio channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return array|string
     */
    public function routeNotificationForTwilio()
    {
        return $this->phone;
    }

    public function sendEmailVerificationNotification()
    {
        $this->last_attempt = now();
        $this->email_verify_code = mt_rand(100000, 999999);
        $this->save();

        $this->notify(new SendCode($this->email_verify_code, 'verify'));
    }

    public function sendPhoneVerificationNotification()
    {
        $this->last_attempt = now();
        $this->phone_verify_code = mt_rand(100000, 999999);
        $this->save();

        $this->notify(new SendCode($this->phone_verify_code, 'verify-phone'));
    }

    /**
     * Get the subscription associated with the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class)->where('status', 'active');
    }

    /**
     * Get all of the transactions for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get all of the wallet transactions for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function wallet_transactions(): HasMany
    {
        return $this->hasMany(Wallet::class)->statusIs('failed', false);
    }

    public function walletBal(): Attribute
    {
        $credit = $this->wallet_transactions()->credit();
        $debit = $this->wallet_transactions()->debit();

        return new Attribute(
            get: fn () => $credit->sum('amount') - $debit->sum('amount'),
        );
    }
}
