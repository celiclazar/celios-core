@php
    $brand = current_brand();
@endphp
@if($brand?->favicon_url)
    <link rel="icon" href="{{ $brand->favicon_url }}" />
@endif
