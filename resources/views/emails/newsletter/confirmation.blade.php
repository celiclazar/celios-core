@extends('emails.newsletter.layout')

@section('content')
    <h2 style="margin-top: 0; color: #111827; font-size: 20px; font-weight: 700;">
        {{ __('newsletter.confirm_title') }}
    </h2>

    <p>{{ __('newsletter.confirm_greeting', ['name' => $subscriber->first_name ?: $subscriber->email]) }}</p>

    <p>{{ __('newsletter.confirm_instruction') }}</p>

    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ $verificationUrl }}" class="btn">
            {{ __('newsletter.confirm_button') }}
        </a>
    </div>

    <p style="font-size: 13px; color: #6b7280; word-break: break-all;">
        {{ __('newsletter.confirm_trouble') }}<br>
        <a href="{{ $verificationUrl }}" style="color: #10b981;">{{ $verificationUrl }}</a>
    </p>

    <p style="font-size: 12px; color: #9ca3af; margin-top: 25px;">
        {{ __('newsletter.confirm_ignore') }}
    </p>
@endsection

@section('footer_links')
    <p style="margin: 0; font-size: 11px; color: #9ca3af;">
        {{ __('newsletter.sent_to', ['email' => $subscriber->email]) }}
    </p>
@endsection
