@component('mail::message')
# One-Time Password (OTP) Verification

Your One-Time Password (OTP) for verification is: **{{ $data['otp'] }}**

This OTP is valid for a limited time. Please use it to complete your verification process.

Thank you,<br>
{{ config('app.name') }}
@endcomponent
