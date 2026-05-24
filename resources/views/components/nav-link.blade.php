@props(['active', 'icon' => null])

@php
$classes = ($active ?? false)
    ? 'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold bg-[#E0F2FE] text-[#0EA5E9] transition-all duration-200'
    : 'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-[#64748B] hover:bg-[#F1F5F9] hover:text-[#334155] transition-all duration-200';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
