<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategorySection extends Model
{
    use HasFactory;
    protected $table = 'product_category_section';

    protected $fillable = [
        'product_id',
        'master_category_section_id',
    ];

    // 🔗 Belongs to product
    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }

    // 🔗 Belongs to master_category_sections
    public function masterCategorySection()
    {
        return $this->belongsTo(MasterCategorySection::class, 'master_category_section_id');
    }

    // 🪄 Shortcuts for category chain (for easy access in eager load)
    public function masterCategory()
    {
        return $this->hasOneThrough(
            MasterCategory::class,
            MasterCategorySection::class,
            'id', // Foreign key on MasterCategorySection
            'id', // Foreign key on MasterCategory
            'master_category_section_id', // Local key on ProductCategorySection
            'master_category_id' // Local key on MasterCategorySection
        );
    }

    public function sectionType()
    {
        return $this->hasOneThrough(
            SectionType::class,
            MasterCategorySection::class,
            'id',
            'id',
            'master_category_section_id',
            'section_type_id'
        );
    }

    public function category()
    {
        return $this->hasOneThrough(
            Category::class,
            MasterCategorySection::class,
            'id',
            'id',
            'master_category_section_id',
            'category_id'
        );
    }
}
