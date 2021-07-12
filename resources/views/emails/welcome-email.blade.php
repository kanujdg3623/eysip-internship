@component('mail::message')
# Registered

Your profile is approved. Go to this link to reset password.

@component('mail::button', ['url' => $link])
Password Reset
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
