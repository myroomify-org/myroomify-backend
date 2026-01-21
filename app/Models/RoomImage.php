<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoomImage extends Model
{
    use SoftDeletes;

    public function room() {
        return $this->belongsTo(Room::class);
    }

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    protected static function booted()
    {
        static::saving(function ($image) {
            if ($image->is_primary) {
                self::where('room_id', $image->room_id)
                    ->where('id', '!=', $image->id)
                    ->update(['is_primary' => false]);
            }
        });
    }
}
