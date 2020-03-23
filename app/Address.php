<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'full_address'
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'id', 'updated_at'
    ];

    /**
     * Get the full address attribute.
     *
     * @return string
     */
    public function getFullAddressAttribute()
    {
        return preg_replace('/\s+/', ' ', trim("{$this->street_address}, {$this->postal_code} {$this->city}", " ,\t\n\r\0\x0B"));
    }

    /**
     * Get the events for the address.
     */
    public function events()
    {
        return $this->hasMany(Event::class);
    }
}
