<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
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
        'event_program' => 'array',
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

    public function getFormattedStartDateAttribute()
    {
        return $this->start_date->format('d/m/Y');
    }

    public function getFormattedEndDateAttribute()
    {
        return $this->end_date->format('d/m/Y');
    }

    public function getFormattedStartTimeAttribute()
    {
        return date('H:i', strtotime($this->start_time));
    }

    public function getFormattedPublishAtAttribute()
    {
        return $this->publish_at->format('d/m/Y');
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
}


