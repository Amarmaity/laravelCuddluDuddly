<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectionType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    // A section type can belong to many master categories
    public function masterCategories()
    {
        return $this->belongsToMany(
            MasterCategory::class,
            'master_category_sections',
            'section_type_id',
            'master_category_id'
        );
    }

    // A section type can belong to many categories
    public function categories()
    {
        return $this->belongsToMany(
            Category::class,
            'master_category_sections',
            'section_type_id',
            'category_id'
        );
    }
}