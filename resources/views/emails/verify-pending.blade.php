@component('mail::message')
# Verify Your Email Address

Hello {{ $name }},

Thank you for registering. Please click the button below to verify your email address and complete your registration.

@component('mail::button', ['url' => $verificationUrl])
Verify Email
@endcomponent

If you did not create an account, no further action is required.

Thanks,<br>
{{ config('app.name') }}
@endcomponent