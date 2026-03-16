<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseHead extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'purchase_number', 'purchase_date', 'supplier_id', 'user_id',
        'subtotal', 'discount_amount', 'tax_amount',
        'grand_total', 'status', 'notes'
    ];

    protected $casts = [
        'purchase_date' => 'date',
    ];

    public function details()
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($purchase) {
            if (empty($purchase->purchase_number)) {
                $today = now()->format('Ymd');
                $lastToday = self::whereDate('created_at', today())
                    ->withTrashed()
                    ->count();
                $purchase->purchase_number = 'PO-' . $today . '-' . str_pad($lastToday + 1, 4, '0', STR_PAD_LEFT);
            }
        });
    }
}