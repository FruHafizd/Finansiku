<?php

use App\Livewire\Actions\Logout;
use App\Models\Budget;
use Livewire\Volt\Component;

new class extends Component
{
    public int $exceededCount = 0;

    public function mount(): void
    {
        $this->loadExceededCount();
    }

    public function loadExceededCount(): void
    {
        $this->exceededCount = Budget::getExceededBudgets(auth()->id())->count();
    }

    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }

    protected $listeners = [
        'budget-created' => 'loadExceededCount',
        'budget-updated' => 'loadExceededCount',
        'budget-deleted' => 'loadExceededCount',
        'transaction-created' => 'loadExceededCount',
        'transaction-deleted' => 'loadExceededCount',
        'transaction-updated' => 'loadExceededCount',
    ];
}; ?>

<div>
<!-- Mobile Overlay Backdrop -->
<div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/30 z-40 md:hidden" @click="sidebarOpen = false" style="display: none;"></div>

<!-- Sidebar -->
<aside
  :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
  class="fixed inset-y-0 left-0 z-50 bg-white border-r border-[#E2E8F0]
         w-64 md:w-20 lg:w-64
         md:translate-x-0 transition-transform duration-300 ease-in-out
         flex flex-col"
>
  <!-- Logo Area -->
  <div class="h-14 flex items-center px-4 border-b border-[#E2E8F0] shrink-0">
    <a href="{{ route('home') }}" wire:navigate class="flex items-center overflow-hidden">
      <x-application-logo />
    </a>
    <!-- Mobile close button -->
    <button @click="sidebarOpen = false" class="md:hidden ml-auto text-[#64748B] hover:text-[#0F172A]">
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>
  </div>

  <!-- Navigation Menu (scrollable) -->
  <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
    <p class="px-3 mb-2 text-xs font-semibold text-[#64748B] uppercase tracking-wider lg:block md:hidden block">Menu</p>

    <x-nav-link :href="route('home')" :active="request()->routeIs('home')" wire:navigate>
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        <span class="lg:block md:hidden block truncate">{{ __('Beranda') }}</span>
    </x-nav-link>

    <x-nav-link :href="route('transaction.index')" :active="request()->routeIs('transaction.index')" wire:navigate>
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
        </svg>
        <span class="lg:block md:hidden block truncate">{{ __('Transaksi') }}</span>
    </x-nav-link>

    <x-nav-link :href="route('budget.index')" :active="request()->routeIs('budget.index')" wire:navigate>
        <div class="relative flex items-center">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            @if ($exceededCount > 0)
                <span class="absolute -top-1.5 -right-1.5 flex h-3 w-3 lg:hidden md:flex hidden">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-[#EF4444]"></span>
                </span>
            @endif
        </div>
        <div class="flex items-center justify-between w-full lg:flex md:hidden flex">
            <span class="truncate ml-3 lg:ml-0 md:ml-3 ml-0">{{ __('Budget') }}</span>
            @if ($exceededCount > 0)
                <span class="inline-flex items-center justify-center px-2 py-0.5 ml-2 text-xs font-bold text-white bg-[#EF4444] rounded-full animate-pulse">
                    {{ $exceededCount }}
                </span>
            @endif
        </div>
    </x-nav-link>

    <x-nav-link :href="route('account.index')" :active="request()->routeIs('account.index')" wire:navigate>
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
        </svg>
        <span class="lg:block md:hidden block truncate">{{ __('Rekening') }}</span>
    </x-nav-link>

    <p class="px-3 mt-6 mb-2 text-xs font-semibold text-[#64748B] uppercase tracking-wider lg:block md:hidden block">Lainnya</p>

    <button @click="showCalendar = true; sidebarOpen = false" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-[#64748B] hover:bg-[#F1F5F9] hover:text-[#334155] transition-all duration-200">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
        </svg>
        <span class="lg:block md:hidden block truncate">Kalender Keuangan</span>
    </button>
  </nav>

  <!-- User Card (sticky bottom) -->
  <div class="border-t border-[#E2E8F0] p-4 shrink-0">
    <x-dropdown align="top" width="48">
        <x-slot name="trigger">
            <button class="w-full flex items-center gap-3 focus:outline-none rounded-lg p-1 hover:bg-[#F1F5F9] transition-colors">
                <div class="w-9 h-9 shrink-0 rounded-full bg-[#0EA5E9] text-white flex items-center justify-center text-sm font-bold shadow-sm"
                     x-data="{{ json_encode(['name' => auth()->user()->name]) }}"
                     x-text="name.charAt(0).toUpperCase()"
                     x-on:profile-updated.window="name = $event.detail.name">
                </div>
                <div class="text-left flex-1 min-w-0 lg:block md:hidden block">
                    <p class="text-sm font-semibold text-[#0F172A] truncate" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></p>
                    <p class="text-xs text-[#64748B] truncate">{{ auth()->user()->email }}</p>
                </div>
            </button>
        </x-slot>

        <x-slot name="content">
            <x-dropdown-link :href="route('profile')" wire:navigate>
                {{ __('Profile') }}
            </x-dropdown-link>
            <x-dropdown-link :href="route('settings.data')" wire:navigate>
                {{ __('Data') }}
            </x-dropdown-link>
            <button wire:click="logout" class="w-full text-start">
                <x-dropdown-link>
                    {{ __('Log Out') }}
                </x-dropdown-link>
            </button>
        </x-slot>
    </x-dropdown>
  </div>
</aside>
</div>