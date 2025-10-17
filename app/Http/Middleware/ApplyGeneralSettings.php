<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use App\Models\GeneralSetting;
use Carbon\Carbon;

class ApplyGeneralSettings
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $setting = GeneralSetting::first();

        if ($setting) {
            // Set timezone
            if ($setting->default_timezone) {
                date_default_timezone_set($setting->default_timezone);
                Config::set('app.timezone', $setting->default_timezone);
                Carbon::setLocale(app()->getLocale());
            }

            // Set application language/locale
            if ($setting->language) {
                App::setLocale($setting->language);
            }
        }

        return $next($request);
    }
}
