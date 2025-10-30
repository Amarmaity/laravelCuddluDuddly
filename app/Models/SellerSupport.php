<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerSupport extends Model
{
    protected $fillable = [
        'seller_id',
        'admin_id',
        'product_id',
        'subject',
        'message',
        'status',
        'closed_by',
        'reopened_by',
    ];

    public function seller()
    {

        return $this->belongsTo(Sellers::class);
    }

    public function admin()
    {
        return $this->belongsTo(AdminUser::class, 'admin_id');
    }

    public function product()
    {
        return $this->belongsTo(Products::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'product_id');
    }

    // Primary product image
    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class, 'product_id', 'product_id')
            ->where('is_primary', 1);
    }
    public function messages()
    {
        return $this->hasMany(SupportMessage::class);
    }

    public function closedBy()
    {
        return $this->belongsTo(AdminUser::class, 'closed_by');
    }

    public function reopenedBy()
    {
        return $this->belongsTo(AdminUser::class, 'reopened_by');
    }
}
