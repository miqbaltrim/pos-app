<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code', 'name', 'phone', 'email',
        'address', 'total_purchases', 'loyalty_points', 'is_active'
    ];

    public function saleHeads()
    {
        return $this->hasMany(SaleHead::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($customer) {
            if (empty($customer->code)) {
                $last = self::withTrashed()->max('id') ?? 0;
                $customer->code = 'CUST-' . str_pad($last + 1, 5, '0', STR_PAD_LEFT);
            }
        });
    }
}