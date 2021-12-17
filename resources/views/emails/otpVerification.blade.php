@component('mail::message')
# {{ config('app.name') }}
<h1>Hello {{ $name }}, We send you the OTP code, don't tell anyone about this code! </h1>
<p>OTP Code :</p>
@component('mail::button', ['url' => ''])
{{ $otp_code }}
@endcomponent

Regards,<br>
{{ config('app.name') }}
@endcomponent
