<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterCategorySection extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'master_category_id',
        'section_type_id',
        'category_id',
    ];

    public function masterCategory()
    {
        return $this->belongsTo(MasterCategory::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function sectionType()
    {
        return $this->belongsTo(SectionType::class);
    }
}