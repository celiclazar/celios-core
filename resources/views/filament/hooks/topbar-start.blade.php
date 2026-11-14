@php
    $brand = current_brand();
    $brandName = $brand?->name ?: 'Celios';
@endphp
<div class="hidden lg:flex items-center gap-2 text-xs sm:text-sm font-medium text-[#424751] dark:text-gray-400 me-3 shrink-0 select-none">
    <span class="font-bold text-[#111c2e] dark:text-gray-100 tracking-tight">{{ $brandName }}</span>
    <span class="text-[#c3c6d3] dark:text-gray-600">/</span>
    <span class="text-[#424751] dark:text-gray-400 font-medium">Workspace</span>
    <span class="text-[#c3c6d3] dark:text-gray-600 ms-1">|</span>
</div>
