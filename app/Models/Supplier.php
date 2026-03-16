<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code', 'name', 'phone', 'email',
        'address', 'contact_person', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function purchaseHeads()
    {
        return $this->hasMany(PurchaseHead::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($supplier) {
            if (empty($supplier->code)) {
                $supplier->code = self::generateCode();
            }
        });
    }

    /**
     * Generate kode unik dengan retry untuk handle race condition
     */
    public static function generateCode(): string
    {
        return DB::transaction(function () {
            // Lock table untuk prevent duplicate
            $last = self::withTrashed()
                ->lockForUpdate()
                ->orderByDesc('id')
                ->value('id') ?? 0;

            $attempts = 0;
            do {
                $code = 'SUP-' . str_pad($last + 1 + $attempts, 5, '0', STR_PAD_LEFT);
                $exists = self::withTrashed()->where('code', $code)->exists();
                $attempts++;
            } while ($exists && $attempts < 10);

            if ($exists) {
                // Fallback: pakai timestamp
                $code = 'SUP-' . now()->format('ymdHis');
            }

            return $code;
        });
    }
}