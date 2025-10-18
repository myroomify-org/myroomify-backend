<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use SoftDeletes;
    
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function room() {
        return $this->belongsTo(Room::class);
    }

    public function guests() {
        return $this->hasMany(Guest::class);
    }

    public function bookingBillingDetail() {
        return $this->hasOne(BookingBillingDetail::class);
    }
}
