@component('mail::custom_layout')
@slot('header')
@component('mail::header', ['url' => ''])
@endcomponent
@endslot

@slot('slot')
<h1 style="
background-color: #2196f3;
margin-bottom: 0px;
color: white;
padding: 24px;
">
{{ $title }}
</h1>
<div style="padding: 32px">
	<p>Kami dari tim {{config('app.name')}}, melalui email ini kami menginformasikan password baru Anda adalah <strong>{{$generatepass}}</strong></p>
	<p>Terima kasih</p>
    <div style="text-align: center; margin-top: 24px">
        <a href="{{ url('/') }}"
            style="display: inline-block; text-decoration: none; color: #2196f3">{{ config('app.name') }}</a>
        <p style="margin: 0px"></p>
        Developed by
        <a href="https://rectmedia.com" style="display: inline-block; color: #2196f3">RECTmedia</a>
    </div>
</div>
@endslot

@slot('footer')
@component('mail::footer')
@endcomponent
@endslot
@endcomponent
