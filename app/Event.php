<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use SoftDeletes;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'end_date',
        'publish_at',
        'start_date',
    ];

    /**
     * Get the start date attribute.
     *
     * @var string
     */
    public function getFormattedStartDateAttribute()
    {
        return $this->start_date->format('d/m/Y');
    }

    /**
     * Get the end date attribute.
     *
     * @var string
     */
    public function getFormattedEndDateAttribute()
    {
        return $this->end_date->format('d/m/Y');
    }

    /**
     * Get the start time attribute.
     *
     * @var string
     */
    public function getFormattedStartTimeAttribute()
    {
        return date('H:i', strtotime($this->start_time));
    }

    /**
     * Get the publish_at attribute.
     *
     * @var string
     */
    public function getFormattedPublishAtAttribute()
    {
        return $this->publish_at->format('d/m/Y');
    }

    /**
     * Get the total tickets number attribute.
     *
     * @var int
     */
    public function getTotalTicketsNumberAttribute()
    {
        return $this->tickets->sum(function ($ticket) {
            return $ticket->tickets_number;
        });
    }

    /**
     * Get the total tickets remaining attribute.
     *
     * @var int
     */
    public function getTotalTicketsRemainingAttribute()
    {
        return $this->tickets->sum(function ($ticket) {
            return $ticket->tickets_remain;
        });
    }

    /**
     * Get the price string attribute.
     *
     * @var string
     */
    public function getFormattedPriceAttribute()
    {
        $ticketsCount = $this->tickets->count();

        $price = number_format(($this->tickets->min('price') / 100), 2, '.', '');

        if ($ticketsCount > 1) {
            return preg_replace('/\s+/', ' ', trim("A Partir de {$price} €", " ,\t\n\r\0\x0B"));
        }

        if ($ticketsCount == 1) {
            return preg_replace('/\s+/', ' ', trim("{$price} €", " ,\t\n\r\0\x0B"));
        }

        return null;
    }

    /**
     * Get the user that owns the event.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the address that owns the event.
     */
    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * Get the tickets for the event.
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Get the tickets for the event.
     */
    public function translations()
    {
        return $this->hasMany(EventTranslation::class, 'event_id');
    }
}


