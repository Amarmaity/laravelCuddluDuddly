<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_name',
        'default_currency',
        'default_timezone',
        'date_format',
        'time_format',
        'banners',
        'logo',
        'favicon',
        'contact_email',
        'contact_phone',
        'contact_address',
        'facebook_url',
        'linkedin_url',
        'instagram_url',
        'terms_conditions',
        'privacy_policy',
    ];
}
    