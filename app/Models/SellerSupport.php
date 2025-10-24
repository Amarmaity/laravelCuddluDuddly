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
        'status'
    ];

    public function seller()
    {

        return $this->belongsTo(Sellers::class);
    }

    public function admin()
    {
        return $this->belongsTo(AdminUser::class);
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
}
