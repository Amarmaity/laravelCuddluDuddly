<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
        'description',
        'price',
        'stock',
        'status',
    ];

    // 🔗 A product can have many reviews
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
