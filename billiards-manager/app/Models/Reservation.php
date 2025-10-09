<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'table_id',
        'customer_id',
        'customer_name',
        'customer_phone',
        'reservation_time',
        'duration',
        'guest_count',
        'status',
        'note',
        'created_by'
    ];

    protected $casts = [
        'reservation_time' => 'datetime'
    ];

    // Relationships
    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'Confirmed');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('reservation_time', today());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('reservation_time', '>=', now())
            ->whereIn('status', ['Pending', 'Confirmed']);
    }

    // Methods
    public function confirm()
    {
        $this->update(['status' => 'Confirmed']);
    }

    public function checkIn()
    {
        $this->update(['status' => 'CheckedIn']);

        // Create bill from reservation
        $bill = Bill::create([
            'bill_number' => Bill::generateBillNumber(),
            'table_id' => $this->table_id,
            'customer_id' => $this->customer_id,
            'staff_id' => $this->created_by,
            'start_time' => now(),
            'status' => 'Open'
        ]);

        return $bill;
    }

    public function cancel()
    {
        $this->update(['status' => 'Cancelled']);
        $this->table->markAsAvailable();
    }

    public function isExpired()
    {
        return $this->reservation_time->addMinutes($this->duration)->lt(now()) && 
               $this->status === 'Pending';
    }

    public function getEndTimeAttribute()
    {
        return $this->reservation_time->addMinutes($this->duration);
    }
}