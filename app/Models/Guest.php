<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guest extends Model
{
    use SoftDeletes;
    
    public function booking() {
        return $this->belongsTo(Booking::class);
    }

    public function address() {
        return $this->belongsTo(Address::class);
    }
}
