@extends('admin.layouts.admin')

@section('title', 'General Settings')

@section('content')
<div class="card shadow-sm border-0 rounded-4 p-3">
    <h4 class="mb-4"><i class="bi bi-gear-fill me-2"></i> General Settings</h4>

    {{-- Tabs Navigation --}}
    <ul class="nav nav-tabs mb-3" id="generalSettingsTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="platform-tab" data-bs-toggle="tab" data-bs-target="#platform"
                type="button" role="tab">Platform / System</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="branding-tab" data-bs-toggle="tab" data-bs-target="#branding" type="button"
                role="tab">Branding / Media</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="communication-tab" data-bs-toggle="tab" data-bs-target="#communication"
                type="button" role="tab">Communication / Contact Info</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="policies-tab" data-bs-toggle="tab" data-bs-target="#policies" type="button"
                role="tab">Policies</button>
        </li>
    </ul>

    {{-- Tabs Content --}}
    <form action="{{ route('admin.settings.general.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="tab-content" id="generalSettingsTabsContent">

            {{-- Platform / System --}}
            <div class="tab-pane fade show active" id="platform" role="tabpanel">
                <div class="row g-3">

                    {{-- Site Name --}}
                    <div class="col-md-6">
                        <label>Site Name</label>
                        <input type="text" name="site_name" class="form-control"
                            value="{{ old('site_name', $setting->site_name ?? config('app.name')) }}">
                    </div>

                    {{-- Default Timezone --}}
                    <div class="col-md-6">
                        <label>Default Timezone</label>
                        <select name="default_timezone" id="timezoneSelect" class="form-control">
                            @foreach(timezone_identifiers_list() as $tz)
                                <option value="{{ $tz }}" {{ old('default_timezone', $setting->default_timezone ?? 'Asia/Kolkata') == $tz ? 'selected' : '' }}>
                                    {{ $tz }}
                                </option>
                            @endforeach
                        </select>
                        <small id="timezoneCurrentTime" class="text-muted d-block mt-1"></small>
                    </div>

                    {{-- Default Currency --}}
                    @php
                        function getCurrencyFromTimezone($timezone) {
                            try {
                                $tz = new DateTimeZone($timezone);
                                $location = timezone_location_get($tz);
                                $country = $location['country_code'] ?? null;

                                if ($country) {
                                    $formatter = new NumberFormatter($country, NumberFormatter::CURRENCY);
                                    $code = $formatter->getTextAttribute(NumberFormatter::CURRENCY_CODE);
                                    return $code ?: 'USD';
                                }
                            } catch (\Exception $e) {}
                            return 'USD';
                        }

                        $selectedTimezone = old('default_timezone', $setting->default_timezone ?? 'Asia/Kolkata');
                        $currency = $setting->default_currency ?? getCurrencyFromTimezone($selectedTimezone);
                    @endphp
                    <div class="col-md-6">
                        <label>Default Currency</label>
                        <input type="text" name="default_currency" id="currencyInput" class="form-control"
                            value="{{ old('default_currency', $currency) }}">
                    </div>

                    {{-- Date Format --}}
                    <div class="col-md-6">
                        <label>Date Format</label>
                        <select name="date_format" id="dateFormatSelect" class="form-control">
                            @php
                                $dateFormats = [
                                    'Y-m-d' => '2025-10-17',
                                    'd-m-Y' => '17-10-2025',
                                    'm/d/Y' => '10/17/2025',
                                    'd M, Y' => '17 Oct, 2025'
                                ];
                            @endphp
                            @foreach($dateFormats as $format => $example)
                                <option value="{{ $format }}" {{ old('date_format', $setting->date_format ?? '') == $format ? 'selected' : '' }}>
                                    {{ $example }} ({{ $format }})
                                </option>
                            @endforeach
                        </select>
                        <small id="currentDateDisplay" class="text-muted d-block mt-1"></small>
                    </div>

                    {{-- Time Format --}}
                    <div class="col-md-6">
                        <label>Time Format</label>
                        <select name="time_format" id="timeFormatSelect" class="form-control">
                            @php
                                $timeFormats = [
                                    'H:i' => '14:30',
                                    'h:i A' => '02:30 PM',
                                    'H:i:s' => '14:30:00'
                                ];
                            @endphp
                            @foreach($timeFormats as $format => $example)
                                <option value="{{ $format }}" {{ old('time_format', $setting->time_format ?? '') == $format ? 'selected' : '' }}>
                                    {{ $example }} ({{ $format }})
                                </option>
                            @endforeach
                        </select>
                        <small id="currentTimeDisplay" class="text-muted d-block mt-1"></small>
                    </div>

                </div>
            </div>

            {{-- Branding / Media --}}
            <div class="tab-pane fade" id="branding" role="tabpanel">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label>Site Logo</label>
                        <input type="file" name="logo" class="form-control">
                        @if(!empty($setting->logo))
                            <img src="{{ asset('storage/' . $setting->logo) }}" class="img-thumbnail mt-2" style="max-height:80px;">
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label>Favicon</label>
                        <input type="file" name="favicon" class="form-control">
                        @if(!empty($setting->favicon))
                            <img src="{{ asset('storage/' . $setting->favicon) }}" class="img-thumbnail mt-2" style="max-height:50px;">
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label>Banners</label>
                        <input type="file" name="banners" class="form-control" accept="image/*,video/*,animation/*">
                    </div>
                </div>
            </div>

            {{-- Communication / Contact Info --}}
            <div class="tab-pane fade" id="communication" role="tabpanel">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label>Contact Email</label>
                        <input type="email" name="contact_email" class="form-control"
                            value="{{ old('contact_email', $setting->contact_email ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label>Contact Phone</label>
                        <input type="text" name="contact_phone" class="form-control"
                            value="{{ old('contact_phone', $setting->contact_phone ?? '') }}">
                    </div>
                    <div class="col-md-12">
                        <label>Social Links</label>
                        <input type="url" name="facebook_url" class="form-control mb-1" placeholder="Facebook"
                            value="{{ old('facebook_url', $setting->facebook_url ?? '') }}">
                        <input type="url" name="linkedin_url" class="form-control mb-1" placeholder="LinkedIn"
                            value="{{ old('linkedin_url', $setting->linkedin_url ?? '') }}">
                        <input type="url" name="instagram_url" class="form-control" placeholder="Instagram"
                            value="{{ old('instagram_url', $setting->instagram_url ?? '') }}">
                    </div>
                    <div class="col-md-12">
                        <label>Contact Address</label>
                        <textarea name="contact_address" class="form-control">{{ old('contact_address', $setting->contact_address ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Policies --}}
            <div class="tab-pane fade" id="policies" role="tabpanel">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label>Terms & Conditions</label>
                        <input id="terms_input" type="hidden" name="terms_conditions"
                            value="{{ old('terms_conditions', $setting->terms_conditions ?? '') }}">
                        <trix-editor input="terms_input" class="form-control"></trix-editor>
                    </div>
                    <div class="col-md-6">
                        <label>Privacy Policy</label>
                        <input id="privacy_input" type="hidden" name="privacy_policy"
                            value="{{ old('privacy_policy', $setting->privacy_policy ?? '') }}">
                        <trix-editor input="privacy_input" class="form-control"></trix-editor>
                    </div>
                </div>
            </div>

        </div>

        <div class="text-end mt-4">
            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Save Changes</button>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const timeDisplay = document.getElementById('currentTimeDisplay');
    const formatSelect = document.getElementById('timeFormatSelect');
    const dateDisplay = document.getElementById('currentDateDisplay');
    const dateSelect = document.getElementById('dateFormatSelect');
    const tzSelect = document.getElementById('timezoneSelect');
    const tzDisplay = document.getElementById('timezoneCurrentTime');
    const currencyInput = document.getElementById('currencyInput');

    // Time display
    const timeFormats = {
        'H:i': { hour12: false, showSeconds: false },
        'h:i A': { hour12: true, showSeconds: false },
        'H:i:s': { hour12: false, showSeconds: true },
    };
    function updateTime() {
        const fmt = timeFormats[formatSelect.value] || { hour12: false, showSeconds: false };
        const now = new Date();
        const options = { hour: '2-digit', minute: '2-digit', ...(fmt.showSeconds ? { second: '2-digit' } : {}), hour12: fmt.hour12 };
        timeDisplay.innerText = "Current Time: " + now.toLocaleTimeString([], options);
    }

    formatSelect.addEventListener('change', updateTime);
    setInterval(updateTime, 1000);
    updateTime();

    // Date display
    function formatDate(date, fmt) {
        const map = {
            Y: date.getFullYear(),
            m: ('0' + (date.getMonth() + 1)).slice(-2),
            d: ('0' + date.getDate()).slice(-2),
            M: date.toLocaleString('default', { month: 'short' }),
        };
        return fmt.replace('Y', map.Y).replace('m', map.m).replace('d', map.d).replace('M', map.M);
    }
    function updateDate() {
        dateDisplay.innerText = "Current Date: " + formatDate(new Date(), dateSelect.value);
    }
    dateSelect.addEventListener('change', updateDate);
    updateDate();

    // Timezone display
    function updateTimezone() {
        try {
            const selectedTz = tzSelect.value;
            const now = new Date().toLocaleString('en-US', { timeZone: selectedTz });
            tzDisplay.innerText = `Current Time in ${selectedTz}: ${now}`;

            // Dynamically update currency using Intl API
            const region = selectedTz.split('/')[0] === 'Asia' && selectedTz.includes('Kolkata') ? 'IN' : 'US';
            currencyInput.value = new Intl.NumberFormat(region, { style: 'currency', currency: region === 'IN' ? 'INR' : 'USD' }).resolvedOptions().currency;

        } catch (e) {
            tzDisplay.innerText = 'Invalid timezone';
        }
    }
    tzSelect.addEventListener('change', updateTimezone);
    updateTimezone();
});
</script>
@endpush
