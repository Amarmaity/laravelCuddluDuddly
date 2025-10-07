<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    protected $fillable = [
        'seller_id',
        'initiated_by',
        'amount',
        'currency',
        'status',
        'method',
        'provider',
        'provider_payout_id',
        'beneficiary_snapshot',
        'provider_response',
        'fee',
        'idempotency_key',
        'remarks',
        'requested_at',
        'processed_at'
    ];

    protected $casts = [
        'beneficiary_snapshot' => 'array',
        'provider_response' => 'array',
        'requested_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    public function seller()
    {
        return $this->belongsTo(Sellers::class);
    }
    public function admin()
    {
        return $this->belongsTo(AdminUser::class, 'initiated_by');
    }
}
