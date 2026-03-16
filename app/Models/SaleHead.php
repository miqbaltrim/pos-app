<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleHead extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_number', 'transaction_date', 'customer_id', 'user_id',
        'subtotal', 'discount_percent', 'discount_amount',
        'tax_percent', 'tax_amount', 'grand_total',
        'paid_amount', 'change_amount',
        'payment_method', 'payment_reference',
        'status', 'notes'
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'change_amount' => 'decimal:2',
    ];

    // ===== RELATIONSHIPS =====
    public function details()
    {
        return $this->hasMany(SaleDetail::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ===== AUTO GENERATE INVOICE =====
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($sale) {
            if (empty($sale->invoice_number)) {
                $today = now()->format('Ymd');
                $lastToday = self::whereDate('created_at', today())
                    ->withTrashed()
                    ->count();
                $sale->invoice_number = 'INV-' . $today . '-' . str_pad($lastToday + 1, 4, '0', STR_PAD_LEFT);
            }
            if (empty($sale->transaction_date)) {
                $sale->transaction_date = today();
            }
        });
    }

    // ===== SCOPES =====
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('transaction_date', today());
    }

    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('transaction_date', [$from, $to]);
    }
}