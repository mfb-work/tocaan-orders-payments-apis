<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'status',
        'total_amount',
    ];

    /**
     * الطلب ينتمي إلى مستخدم
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * الطلب يحتوي على عناصر متعددة
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * الطلب يمكن أن يكون له مدفوعات متعددة
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
