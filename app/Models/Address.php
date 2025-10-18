<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use SoftDeletes;
    
    public function city() {
        return $this->belongsTo(City::class);
    }

    public function profiles() {
        return $this->hasMany(Profile::class);
    }

    public function guests() {
        return $this->hasMany(Guest::class);
    }

    public function bookingBillingDetails() {
        return $this->hasMany(BookingBillingDetail::class);
    }
}
