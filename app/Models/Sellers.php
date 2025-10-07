<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sellers extends Model
{
    protected $table = 'sellers';

    protected $fillable = [
        'user_id',
        'name',
        'contact_person',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'gst_number',
        'pan_number',
        'bank_account_number',
        'bank_name',
        'ifsc_code',
        'upi_id',
        'compliance_status',
        'bank_verified',
        'logo',
        'documents',
        'commission_rate',
        'is_active',
    ];

    protected $casts = [
        'documents' => 'array',
        'is_active' => 'boolean',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(Products::class);
    }
}