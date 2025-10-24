<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterCategory extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'name',
        'slug',
        'image_url',
        'status',
    ];

    // A master category can have many categories
    public function categories()
    {
        return $this->belongsToMany(
            Category::class,
            'master_category_sections',
            'master_category_id',
            'category_id'
        );
    }

    // A master category can have many section types
    public function sectionTypes()
    {
        return $this->belongsToMany(
            SectionType::class,
            'master_category_sections',
            'master_category_id',
            'section_type_id'
        )->distinct(); // 👈 this will ensure unique section types
    }
}