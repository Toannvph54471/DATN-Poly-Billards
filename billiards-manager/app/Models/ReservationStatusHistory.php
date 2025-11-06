<?php

namespace App\Models;

class ReservationStatusHistory extends BaseModel
{
    protected $fillable = [
        'reservation_id',
        'old_status',
        'new_status',
        'changed_by',
        'note'
    ];

    protected $casts = [
        'old_status' => 'string',
        'new_status' => 'string'
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}