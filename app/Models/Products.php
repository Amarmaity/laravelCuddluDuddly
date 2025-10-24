<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Products extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'is_approved',
        'approved_by',
        'approved_at',
        'featured',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name) . '-' . uniqid();
            }
        });
    }

    // 🔗 Relationships

    // Product belongs to a seller
    public function seller()
    {
        return $this->belongsTo(Sellers::class);
    }
    

    // Product can have many images
    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id');
    }

    public function primaryImage()
{
    return $this->hasOne(ProductImage::class, 'product_id')->where('is_primary', 1);
}

    // Product can have many reviews
    public function reviews()
    {
        return $this->hasMany(Review::class, 'product_id');
    }

    // Product approved by admin
    public function approvedBy()
    {
        return $this->belongsTo(AdminUser::class, 'approved_by');
    }

    // 🔍 Scopes
    public function scopeApproved($query)
    {
        return $query->where('is_approved', 1);
    }

    public function categorySections()
    {
        return $this->hasMany(ProductCategorySection::class, 'product_id');
    }
}
