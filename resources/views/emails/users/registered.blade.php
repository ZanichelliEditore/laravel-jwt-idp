@component('mail::message')
# Verify your e-mail

{{ $user->name }}, thanks for sign up on Laravel IDP.

@component('mail::button', ['url' => route('verification-account', ['code' => $verificationCode])])
Verify e-mail
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
