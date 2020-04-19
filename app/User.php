<?php

namespace App;

use App\Notifications\PasswordResetNotification;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['password'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Set password hashed attribute.
     *
     * @param  string  $password
     * @return void
     */
    public function setPasswordAttribute(string $password)
    {
        $this->attributes['password'] = Hash::make($password);
    }

    /**
     * Scope a query to only include users are not admin.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotAdmin($query)
    {
        return $query->select('id', 'first_name', 'last_name', 'role')
            ->withCount(['messages' => function ($query) {
                $query->whereNull('read_at');
            }])
            ->orderBy('messages_count', 'desc');
    }

    /**
     * Check the users has unread messages.
     *
     * @return bool
     */
    public function hasUnreadMessages()
    {
        $unread = $this->withCount(['messages' => function ($query) {
            $query->whereNull('read_at');
        }])
        ->orWhere('id', $this->id)
        ->pluck('messages_count');

        return ($unread[0] > 0) ? true : false;
    }

    /**
     * Update the users read_at of his messages.
     *
     * @return collection
     */
    public function readAllMessages()
    {
        return $this->messages
            ->whereNull('read_at')->each(function ($message) {
                $message->read();
            });
    }

    /**
     * Check the users is admin.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new PasswordResetNotification($token));
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailNotification());
    }

    /**
     * Get the events for the user.
     */
    public function events()
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Get the messages for the user.
     */
    public function messages()
    {
        return $this->hasMany(Message::class, 'from_id');
    }
}

