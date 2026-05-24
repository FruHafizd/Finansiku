<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta name="google-site-verification" content="XDUltp0hE8n1iQSBjhJ339PM7d_XqldKzRBP33wC-m4" />
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased text-[#0F172A]" x-data="{ sidebarOpen: false, showCalendar: false }">
        <div class="min-h-screen bg-[#F8FAFC] flex">
            <!-- Sidebar Navigation -->
            <livewire:layout.navigation />

            <!-- Main Content Area -->
            <div class="flex-1 lg:ml-64 md:ml-20 flex flex-col min-h-screen">
                <!-- Mobile top bar (hamburger + logo) -->
                <header class="md:hidden sticky top-0 z-40 bg-white border-b border-[#E2E8F0] px-4 h-14 flex items-center">
                    <button @click="sidebarOpen = true" class="text-[#64748B] hover:text-[#0F172A] focus:outline-none p-1 -ml-1 rounded-md">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <div class="ml-3 flex items-center">
                        <x-application-logo class="block h-8 w-auto fill-current text-gray-800" />
                    </div>
                </header>

                @if (isset($header))
                    <header class="bg-white shadow-sm border-b border-[#E2E8F0]">
                        <div class="py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endif

                <main class="flex-1 p-4 sm:p-6 lg:p-8">
                    {{ $slot }}
                </main>

                <x-footer />
            </div>
        </div>

        {{-- Budget Alert Toast --}}
        <!-- Global Notifications (Toast) -->
        <div
            x-data="{
                toasts: [],
                add(data) {
                    const toast = Array.isArray(data) ? data[0] : data;
                    const id = Date.now();
                    this.toasts.push({ ...toast, id, show: true });
                    setTimeout(() => this.remove(id), 5000);
                },
                remove(id) {
                    const toast = this.toasts.find(t => t.id === id);
                    if (toast) toast.show = false;
                    setTimeout(() => {
                        this.toasts = this.toasts.filter(t => t.id !== id);
                    }, 400);
                }
            }"
            x-on:budget-alert.window="add($event.detail)"
            x-on:notify.window="add($event.detail)"
            class="fixed top-5 right-5 z-[100] flex flex-col gap-4 w-full max-w-sm px-4 sm:px-0"
        >
            <template x-for="toast in toasts" :key="toast.id">
                <div
                    x-show="toast.show"
                    x-transition:enter="transition ease-out duration-500"
                    x-transition:enter-start="opacity-0 translate-x-12 scale-95"
                    x-transition:enter-end="opacity-100 translate-x-0 scale-100"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100 translate-x-0"
                    x-transition:leave-end="opacity-0 translate-x-12"
                    class="relative group"
                >
                    <div
                        :class="{
                            'bg-white/90 border-emerald-500/30 shadow-emerald-500/10': toast.type === 'success',
                            'bg-white/90 border-red-500/30 shadow-red-500/10': toast.type === 'danger',
                            'bg-white/90 border-amber-500/30 shadow-amber-500/10': toast.type !== 'success' && toast.type !== 'danger'
                        }"
                        class="backdrop-blur-xl border-2 rounded-2xl p-4 shadow-2xl overflow-hidden transition-all duration-300 hover:scale-[1.02]"
                    >
                        <div class="flex items-start gap-4">
                            <!-- Icon with soft glow background -->
                            <div
                                :class="{
                                    'bg-emerald-100 text-emerald-600 ring-emerald-50': toast.type === 'success',
                                    'bg-red-100 text-red-600 ring-red-50': toast.type === 'danger',
                                    'bg-amber-100 text-amber-600 ring-amber-50': toast.type !== 'success' && toast.type !== 'danger'
                                }"
                                class="flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center ring-4"
                            >
                                <!-- Success Icon -->
                                <template x-if="toast.type === 'success'">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                    </svg>
                                </template>
                                <!-- Danger Icon -->
                                <template x-if="toast.type === 'danger'">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </template>
                                <!-- Warning/Default Icon -->
                                <template x-if="toast.type !== 'success' && toast.type !== 'danger'">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </template>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 pt-0.5">
                                <h3
                                    :class="{
                                        'text-emerald-900': toast.type === 'success',
                                        'text-red-900': toast.type === 'danger',
                                        'text-amber-900': toast.type !== 'success' && toast.type !== 'danger'
                                    }"
                                    class="text-sm font-bold tracking-tight"
                                    x-text="toast.title"
                                ></h3>
                                <p
                                    class="text-gray-600 text-xs mt-1 leading-relaxed font-medium"
                                    x-text="toast.message"
                                ></p>
                            </div>

                            <!-- Close Button -->
                            <button
                                @click="remove(toast.id)"
                                class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition-colors bg-gray-50 hover:bg-gray-100 p-1.5 rounded-lg"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Modern Progress Bar -->
                        <div class="absolute bottom-0 left-0 right-0 h-1.5 bg-gray-100/50">
                            <div
                                :class="{
                                    'bg-emerald-500': toast.type === 'success',
                                    'bg-red-500': toast.type === 'danger',
                                    'bg-amber-500': toast.type !== 'success' && toast.type !== 'danger'
                                }"
                                class="h-full transition-all duration-100"
                                :style="'animation: shrink 5s linear forwards'"
                            ></div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <style>
            @keyframes shrink {
                from { width: 100%; }
                to   { width: 0%; }
            }
        </style>

        {{-- Financial Calendar Slide-over --}}
        <livewire:financial-calendar />

        @livewireScripts
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        @stack('scripts')
    </body>
</html>