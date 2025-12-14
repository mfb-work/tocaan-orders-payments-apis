<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'method',
        'status',
        'amount',
        'transaction_id',
        'raw_response',
    ];

    protected $casts = [
        'raw_response' => 'array',
    ];

    /**
     * الدفع ينتمي إلى طلب واحد
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
