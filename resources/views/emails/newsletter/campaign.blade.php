@extends('emails.newsletter.layout')

@section('content')
    {!! $contentHtml !!}
@endsection

@section('footer_links')
    <p style="margin: 0 0 5px 0;">
        {{ __('newsletter.footer_reason') }}
    </p>
    <p style="margin: 0;">
        <a href="{{ $unsubscribeUrl }}" style="color: #6b7280; text-decoration: underline;">
            {{ __('newsletter.unsubscribe') }}
        </a>
    </p>
@endsection

@section('tracking_pixel')
    @if(!empty($trackingUrl))
        <img src="{{ $trackingUrl }}" alt="" width="1" height="1" style="display:none !important; width:1px !important; height:1px !important; border:0 !important;" />
    @endif
@endsection
