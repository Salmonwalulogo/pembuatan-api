<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    protected $fillable = [
        'transaction_number',
        'user_id',
        'customer_id',
        'customer_name',
        'subtotal',
        'discount',
        'tax',
        'other_fee',
        'grand_total',
        'paid_amount',
        'change_amount',
        'payment_method',
        'status',
    ];

    protected $casts = [
        'subtotal' => 'integer',
        'discount' => 'integer',
        'tax' => 'integer',
        'other_fee' => 'integer',
        'grand_total' => 'integer',
        'paid_amount' => 'integer',
        'change_amount' => 'integer',
    ];

    public function details(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
