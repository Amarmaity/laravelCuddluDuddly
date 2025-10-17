<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\GeneralSetting;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class SettingsController extends Controller
{
    public function indexGeneral()
    {
        $setting = GeneralSetting::first();

        return view("admin.settings.index", compact('setting'));
    }


    // public function updateGeneral(Request $request)
    // {
    //     $validated = $request->validate([
    //         // Platform / System
    //         'site_name'        => 'required|string|max:191',
    //         'default_currency' => 'nullable|string|max:10',
    //         'default_timezone' => 'nullable|string|max:50',
    //         'date_format'      => 'nullable|string|max:20',
    //         'time_format'      => 'nullable|string|max:20',

    //         // Files
    //         'banners'          => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,mp4,webm,ogg|max:51200',
    //         'logo'             => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:10240',
    //         'favicon'          => 'nullable|image|mimes:ico,png|max:2048',

    //         // Contact info
    //         'contact_email'    => 'nullable|email|max:191',
    //         'contact_phone'    => 'nullable|string|max:20',
    //         'contact_address'  => 'nullable|string|max:255',

    //         // Social links
    //         'facebook_url'     => 'nullable|url|max:255',
    //         'linkedin_url'     => 'nullable|url|max:255',
    //         'instagram_url'    => 'nullable|url|max:255',

    //         // Content
    //         'terms_conditions' => 'nullable|string',
    //         'privacy_policy'   => 'nullable|string',
    //     ], [
    //         'contact_email.email' => 'Please provide a valid email address.',
    //         'facebook_url.url'    => 'Please enter a valid Facebook URL.',
    //         'logo.mimes'          => 'Logo must be an image (JPG, PNG, or WEBP).',
    //         'banners.mimes'       => 'Banner must be a valid image or video file.',
    //     ]);

    //     // Fetch existing record or create new
    //     $setting = GeneralSetting::first() ?? new GeneralSetting();

    //     // Handle file uploads
    //     foreach (['banners', 'logo', 'favicon'] as $fileField) {
    //         if ($request->hasFile($fileField)) {
    //             // Delete old file if exists
    //             if (!empty($setting->$fileField) && Storage::disk('public')->exists($setting->$fileField)) {
    //                 Storage::disk('public')->delete($setting->$fileField);
    //             }
    //             // Store new file
    //             $setting->$fileField = $request->file($fileField)->store('settings', 'public');
    //         }
    //     }

    //     // Fill the rest of the validated data
    //     $setting->fill($validated);
    //     $setting->save();

    //     return redirect()->back()->with('success', 'General settings updated successfully.');
    // }


    private function validatePlatform(Request $request)
    {
        return $request->validate([
            'site_name'        => 'required|string|max:191',
            'default_currency' => 'nullable|string|max:10',
            'default_timezone' => 'nullable|string|max:50',
            'date_format'      => 'nullable|string|max:20',
            'time_format'      => 'nullable|string|max:20',
        ]);
    }

    private function validateFiles(Request $request)
    {
        return $request->validate([
            'banners' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,mp4,webm,ogg|max:51200',
            'logo'    => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:10240',
            'favicon' => 'nullable|image|mimes:ico,png|max:2048',
        ]);
    }

    private function validateContact(Request $request)
    {
        return $request->validate([
            'contact_email'   => 'nullable|email|max:191',
            'contact_phone'   => 'nullable|string|max:20',
            'contact_address' => 'nullable|string|max:255',
            'facebook_url'    => 'nullable|url|max:255',
            'linkedin_url'    => 'nullable|url|max:255',
            'instagram_url'   => 'nullable|url|max:255',
        ]);
    }

    private function validatePolicies(Request $request)
    {
        return $request->validate([
            'terms_conditions' => 'nullable|string',
            'privacy_policy'   => 'nullable|string',
        ]);
    }



    public function updateGeneral(Request $request)
{
    $setting = GeneralSetting::first() ?? new GeneralSetting();

    // Validate sections
    $platformData = $this->validatePlatform($request);
    $fileData     = $this->validateFiles($request);
    $contactData  = $this->validateContact($request);
    $policyData   = $this->validatePolicies($request);

    // Handle files separately
    $this->handleFiles($request, $setting);

    // Merge all validated data
    $setting->fill(array_merge($platformData, $contactData, $policyData));
    $setting->save();

    return redirect()->back()->with('success', 'General settings updated successfully.');
}




    private function handleFiles(Request $request, GeneralSetting $setting)
{
    foreach (['banners', 'logo', 'favicon'] as $fileField) {
        if ($request->hasFile($fileField)) {
            if (!empty($setting->$fileField) && Storage::disk('public')->exists($setting->$fileField)) {
                Storage::disk('public')->delete($setting->$fileField);
            }
            $setting->$fileField = $request->file($fileField)->store('settings', 'public');
        }
    }
}








    public function boot()
    {
        $setting = GeneralSetting::first();

        if ($setting && $setting->default_timezone) {
            date_default_timezone_set($setting->default_timezone);
            Config::set('app.timezone', $setting->default_timezone);
            Carbon::setLocale(app()->getLocale());
        }
    }
}
