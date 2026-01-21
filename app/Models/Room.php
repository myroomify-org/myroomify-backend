<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use SoftDeletes;
    
    public function bookings() {
        return $this->hasMany(Booking::class);
    }

    public function images() {
        return $this->hasMany(RoomImage::class);
    }

    public function primaryImage() {
        return $this->hasOne(RoomImage::class)->where('is_primary', true);
    }
}
