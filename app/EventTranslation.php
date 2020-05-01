<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventTranslation extends Model
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
        'event_program' => 'array',
    ];

    /**
     * Get the event that owns the translation.
     */
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
