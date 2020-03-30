<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'create_at',
        'read_at',
    ];

    /**
     * Scope a query to get conversation.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $from
     * @param  int  $to
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetConversation($query, int $from, int $to)
    {
        return $query->whereRaw("((from_id = $from AND to_id = $to) OR (from_id = $to AND to_id = $from))")
            ->orderBy('create_at', 'DESC');
    }

    /**
     * Update the messages read_at.
     *
     * @return App\Message
     */
    public function read()
    {
        $this->read_at = Carbon::now();

        return $this->save();
    }

    /**
     * Get the user that owns the message.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'from_id');
    }
}
