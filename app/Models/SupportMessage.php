<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_support_id',
        'sender_type',
        'sender_id',
        'message',
        'attachment',
    ];

    protected $casts = [
        'attachment' => 'array', // Automatically converts JSON <-> PHP array
    ];
    
    // 🔗 Each message belongs to one support ticket
    public function support()
    {
        return $this->belongsTo(SellerSupport::class, 'seller_support_id');
    }

    // 🧍 Seller relation
    public function seller()
    {
        return $this->belongsTo(Sellers::class, 'sender_id');
    }

    // 🧑‍💼 Admin relation
    public function admin()
    {
        return $this->belongsTo(AdminUser::class, 'sender_id');
    }

    // 🔍 Accessor for sender (returns correct model automatically)
    public function getSenderAttribute()
    {
        if ($this->sender_type === 'admin') {
            return $this->admin;
        }
        return $this->seller;
    }
}
