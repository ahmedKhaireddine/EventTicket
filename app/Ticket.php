<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class Ticket extends Model
{
    use SoftDeletes;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Get the translations attribute.
     *
     * @var string
     */
    public function getTranslationsNeededAttribute()
    {
        $user = Auth::guard('api')->user();

        if (isset($user)) {
            return $this->translations;
        } else {
            $locale = App::getLocale();

            return collect([
                $this->translations
                    ->where('locale', $locale)
                    ->first()
            ]);
        }
    }

    /**
     * Get the event that owns the ticket.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the translations for the ticket.
     */
    public function translations()
    {
        return $this->hasMany(TicketTranslation::class);
    }
}
