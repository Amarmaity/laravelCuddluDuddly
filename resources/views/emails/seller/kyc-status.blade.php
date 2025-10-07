@component('mail::message')
    # Hello {{ $seller->contact_person }},

    Your KYC application status has been updated.

    @switch($status)
        @case('verified')
            ✅ Congratulations! Your application has been **verified** successfully.
            You may now proceed to use all seller features on our platform.
        @break

        @case('rejected')
            ❌ Unfortunately, your application has been **rejected**.
            **Reason:** {{ $reason }}
        @break
    @endswitch

    Thanks,
    {{ config('app.name') }}
@endcomponent