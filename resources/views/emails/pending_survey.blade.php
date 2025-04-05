@component('mail::message')
# Pending Survey Notification

Dear {{ $user->name }},

This is to notify you that there is a pending survey for cargo details. Please complete the survey at your earliest convenience.

Thank you,
{{ config('app.name') }}
@endcomponent
