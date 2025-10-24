<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cancellation extends Model
{
    use HasFactory;

    protected $table = 'cancellations';

    protected $fillable = [
        'order_id',
        'user_id',
        'reason',
        'status',
        'approved_by',
        'approved_at',
    ];

    // protected $dates = ['approved_at', 'created_at', 'updated_at'];
    protected $casts = [
        'approved_at' => 'datetime',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(AdminUser::class, 'approved_by');
    }
}
