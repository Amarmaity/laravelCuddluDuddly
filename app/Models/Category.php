<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    public $timestamps = false;   // 👈 ADD THIS

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image_url',
    ];

    // A category can belong to many master categories through master_category_sections
    public function masterCategories()
    {
        return $this->belongsToMany(
            MasterCategory::class,
            'master_category_sections',
            'category_id',
            'master_category_id'
        );
    }

    // A category can have many section types through master_category_sections
    public function sectionTypes()
    {
        return $this->belongsToMany(
            SectionType::class,
            'master_category_sections',
            'category_id',
            'section_type_id'
        );
    }
}
