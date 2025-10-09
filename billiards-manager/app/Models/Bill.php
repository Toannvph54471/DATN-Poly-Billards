<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_number',
        'table_id',
        'customer_id',
        'staff_id',
        'start_time',
        'end_time',
        'total_amount',
        'discount_amount',
        'final_amount',
        'payment_method',
        'payment_status',
        'status',
        'note'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_amount' => 'decimal:2'
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

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function details()
    {
        return $this->hasMany(BillDetail::class);
    }

    public function timeUsage()
    {
        return $this->hasMany(BillTimeUsage::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Scopes
    public function scopeOpen($query)
    {
        return $query->where('status', 'Open');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'Closed');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'Paid');
    }

    // Methods
    public function calculateTotal()
    {
        $productsTotal = $this->details()->where('is_combo_component', false)->sum('total_price');
        $timeTotal = $this->timeUsage()->sum('total_price');
        
        $this->total_amount = $productsTotal + $timeTotal;
        $this->final_amount = $this->total_amount - $this->discount_amount;
        
        return $this->save();
    }

    public function getPlayDuration()
    {
        return $this->timeUsage()->sum('duration_minutes');
    }

    public function closeBill()
    {
        $this->update([
            'status' => 'Closed',
            'end_time' => now()
        ]);

        // Mark table as available
        if ($this->table) {
            $this->table->markAsAvailable();
        }

        // Update customer stats
        if ($this->customer) {
            $this->customer->incrementVisits();
            $this->customer->addToTotalSpent($this->final_amount);
            $this->customer->promoteToVip();
        }
    }

    public function isOpen()
    {
        return $this->status === 'Open';
    }

    public static function generateBillNumber()
    {
        $latest = static::latest()->first();
        $number = $latest ? (int) str_replace('HD', '', $latest->bill_number) + 1 : 1;
        return 'HD' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }
}