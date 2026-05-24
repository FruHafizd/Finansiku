<div {{ $attributes->merge(['class' => 'flex items-center gap-2']) }}>
    {{-- Ikon dompet/keuangan tema Sky --}}
    <div class="w-8 h-8 shrink-0 bg-[#0EA5E9] rounded-lg flex items-center justify-center shadow-md shadow-[#0EA5E9]/20">
        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
        </svg>
    </div>
    {{-- Teks nama app --}}
    <span class="font-bold text-[#0F172A] text-lg tracking-tight whitespace-nowrap lg:block md:hidden block">
        Finansiku
    </span>
</div>
