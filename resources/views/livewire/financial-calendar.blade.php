<div x-data="{ 
        init() {
            window.financialReminders = @js($this->remindersByDay->flatten(1));
        },
        async requestNotificationPermission() {
            try {
                if (!('Notification' in window)) {
                    window.dispatchEvent(new CustomEvent('notify', { detail: { type: 'danger', title: 'Gagal', message: 'Browser tidak mendukung notifikasi.' } }));
                    return;
                }
                const permission = await Notification.requestPermission();
                if (permission === 'granted') {
                    this.scheduleReminders();
                    window.dispatchEvent(new CustomEvent('notify', { detail: { type: 'success', title: 'Berhasil', message: 'Notifikasi berhasil diaktifkan!' } }));
                } else if (permission === 'denied') {
                    window.dispatchEvent(new CustomEvent('notify', { detail: { type: 'danger', title: 'Ditolak', message: 'Izin notifikasi diblokir oleh browser Anda.' } }));
                } else {
                    window.dispatchEvent(new CustomEvent('notify', { detail: { type: 'amber', title: 'Batal', message: 'Izin notifikasi belum diberikan.' } }));
                }
            } catch (error) {
                console.error('Notification Error:', error);
                window.dispatchEvent(new CustomEvent('notify', { detail: { type: 'danger', title: 'Error', message: 'Terjadi kesalahan sistem saat meminta izin.' } }));
            }
        },
        scheduleReminders() {
            const reminders = window.financialReminders;
            const now = new Date();

            reminders.forEach(reminder => {
                const targetDate = new Date(reminder.year, reminder.month - 1, reminder.day - reminder.remind_before);
                const notifDate = new Date(targetDate.getFullYear(), targetDate.getMonth(), targetDate.getDate(), 8, 0, 0);
                const delay = notifDate - now;

                if (delay > 0) {
                    setTimeout(() => {
                        new Notification('💰 Reminder Keuangan', {
                            body: `${reminder.description} — Rp ${reminder.amount_formatted}`,
                        });
                    }, delay);
                }
            });
        }
    }" 
    x-init="init()"
    @financial-reminders-updated.window="init(); scheduleReminders();">
    
    <!-- Slide-over panel -->
    <div x-show="showCalendar" class="relative z-[100]" aria-labelledby="slide-over-title" role="dialog" aria-modal="true" style="display: none;">
        <!-- Background backdrop -->
        <div x-show="showCalendar" 
             x-transition:enter="ease-in-out duration-500" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in-out duration-500" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm"></div>

        <div class="fixed inset-0 overflow-hidden">
            <div class="absolute inset-0 overflow-hidden">
                <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-0 sm:pl-16">
                    <div x-show="showCalendar" 
                         x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700" 
                         x-transition:enter-start="translate-x-full" 
                         x-transition:enter-end="translate-x-0" 
                         x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700" 
                         x-transition:leave-start="translate-x-0" 
                         x-transition:leave-end="translate-x-full" 
                         @click.outside="if (!$wire.reminderIdToDelete) showCalendar = false"
                         class="pointer-events-auto w-screen max-w-full sm:max-w-2xl">
                        
                        <div class="flex h-full flex-col overflow-y-scroll bg-gray-50 shadow-2xl">
                            <!-- Header -->
                            <div class="bg-white border-b border-gray-200 px-4 py-5 sm:px-6 relative flex-shrink-0">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 bg-gray-100 rounded-xl">
                                            <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                        <h2 class="text-xl font-bold leading-6 text-gray-900" id="slide-over-title">Financial Calendar</h2>
                                    </div>
                                    <div class="ml-3 flex h-7 items-center gap-3">
                                        <button type="button" @click="requestNotificationPermission()" class="text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-full transition-colors shadow-sm">
                                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                            <span class="hidden sm:inline">Aktifkan Notif</span>
                                            <span class="sm:hidden">Notif</span>
                                        </button>
                                        <button type="button" @click="showCalendar = false" class="relative rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-1 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-300">
                                            <span class="absolute -inset-2.5"></span>
                                            <span class="sr-only">Close panel</span>
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Main Content -->
                            <div class="relative flex-1 px-4 sm:px-6 py-6 pb-20">
                                
                                <!-- Calendar Header Navigation -->
                                <div class="flex items-center justify-between mb-4 bg-white p-2.5 sm:p-3 rounded-2xl shadow-sm border border-gray-200">
                                    <button wire:click="previousMonth" class="p-2 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-xl transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                    </button>
                                    <h3 class="text-base sm:text-lg font-bold text-gray-900">
                                        {{ \Carbon\Carbon::createFromDate($currentYear, $currentMonth, 1)->translatedFormat('F Y') }}
                                    </h3>
                                    <button wire:click="nextMonth" class="p-2 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-xl transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </button>
                                </div>

                                <!-- Calendar Grid -->
                                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-6">
                                    <div class="grid grid-cols-7 border-b border-gray-200 bg-gray-50">
                                        @foreach(['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'] as $index => $day)
                                            <div class="py-2.5 text-center text-[10px] sm:text-xs font-bold uppercase tracking-wider {{ $index === 0 ? 'text-red-500' : 'text-gray-500' }}">
                                                {{ $day }}
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="grid grid-cols-7 auto-rows-fr">
                                        @foreach($this->calendarDays as $index => $day)
                                            @php
                                                $isSunday = $index % 7 === 0;
                                                $dateString = $day ? sprintf('%04d-%02d-%02d', $currentYear, $currentMonth, $day) : null;
                                                $holiday = $dateString ? ($this->holidaysByDate[$dateString] ?? null) : null;
                                                $isHoliday = $holiday && isset($holiday['holiday']) && $holiday['holiday'];
                                                $isToday = $day == now()->day && $currentMonth == now()->month && $currentYear == now()->year;
                                                $dayReminders = $day ? ($this->remindersByDay->get($day) ?? collect()) : collect();
                                            @endphp
                                            <div class="min-h-[70px] sm:min-h-[90px] p-1 sm:p-2 border-b border-r border-gray-100 relative {{ !$day ? 'bg-gray-50/50' : '' }} {{ $isToday ? 'bg-gray-50' : 'hover:bg-gray-50/80' }} transition-colors">
                                                @if($day)
                                                    <div class="flex justify-center sm:justify-start items-start">
                                                        <span class="flex items-center justify-center w-6 h-6 sm:w-7 sm:h-7 text-xs sm:text-sm font-semibold rounded-full 
                                                            {{ $isToday ? 'bg-gray-900 text-white shadow-md shadow-gray-300' : ($isSunday || $isHoliday ? 'text-red-600' : 'text-gray-700') }}">
                                                            {{ $day }}
                                                        </span>
                                                    </div>
                                                    @if($isHoliday)
                                                        <div class="text-[9px] sm:text-[10px] text-red-500 font-medium leading-tight mt-0.5 sm:mt-1 text-center sm:text-left truncate" title="{{ $holiday['summary'][0] ?? '' }}">
                                                            {{ $holiday['summary'][0] ?? '' }}
                                                        </div>
                                                    @endif
                                                    
                                                    <div class="mt-1 sm:mt-1.5 flex flex-col gap-1 items-center sm:items-stretch">
                                                        @foreach($dayReminders->take(2) as $reminder)
                                                            @php
                                                                $colors = match($reminder->category) {
                                                                    'Investasi' => 'bg-[#E0F2FE] text-[#0369A1]',
                                                                    'Tabungan' => 'bg-[#E1F5EE] text-[#085041]',
                                                                    'Tagihan' => 'bg-[#FAECE7] text-[#712B13]',
                                                                    'Pemasukan' => 'bg-[#EAF3DE] text-[#27500A]',
                                                                    default => 'bg-gray-100 text-gray-600'
                                                                };
                                                                $dotColor = match($reminder->category) {
                                                                    'Investasi' => 'bg-[#0369A1]',
                                                                    'Tabungan' => 'bg-[#085041]',
                                                                    'Tagihan' => 'bg-[#712B13]',
                                                                    'Pemasukan' => 'bg-[#27500A]',
                                                                    default => 'bg-gray-600'
                                                                };
                                                            @endphp
                                                            <!-- Desktop View (Text Pill) -->
                                                            <div class="hidden sm:block text-[10px] px-1.5 py-0.5 rounded {{ $colors }} truncate font-medium cursor-default" title="{{ e($reminder->description) }} (Rp{{ $reminder->amount_formatted }})">
                                                                {{ e($reminder->description) }}
                                                            </div>
                                                            <!-- Mobile View (Dot Indicator) -->
                                                            <div class="sm:hidden w-2 h-2 rounded-full {{ $dotColor }}" title="{{ e($reminder->description) }}"></div>
                                                        @endforeach
                                                        @if($dayReminders->count() > 2)
                                                            <div class="hidden sm:block text-[10px] text-gray-400 font-medium px-1">
                                                                +{{ $dayReminders->count() - 2 }} lagi
                                                            </div>
                                                            <div class="sm:hidden text-[9px] text-gray-400 font-medium">
                                                                +{{ $dayReminders->count() - 2 }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="mb-4 sm:mb-6 flex justify-between items-center">
                                    <h3 class="text-base sm:text-lg font-bold text-gray-900">Daftar Reminder</h3>
                                    <button wire:click="$toggle('showForm')" class="inline-flex items-center gap-1.5 px-3 py-1.5 sm:px-4 sm:py-2 bg-gray-900 text-white text-xs sm:text-sm font-medium rounded-xl hover:bg-gray-800 transition-all shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        Tambah
                                    </button>
                                </div>

                                <!-- Add Form -->
                                <div x-show="$wire.showForm" x-collapse>
                                    <div class="bg-white p-4 sm:p-5 rounded-2xl shadow-sm border border-gray-200 mb-6">
                                        <h4 class="text-sm font-bold text-gray-900 mb-4">Tambah Reminder Baru</h4>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal</label>
                                                <select wire:model="formDay" class="w-full border-gray-300 rounded-xl focus:border-gray-500 focus:ring-gray-500 text-sm">
                                                    @for($i = 1; $i <= \Carbon\Carbon::createFromDate($currentYear, $currentMonth, 1)->daysInMonth; $i++)
                                                        <option value="{{ $i }}">{{ $i }}</option>
                                                    @endfor
                                                </select>
                                                @error('formDay') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                            </div>
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kategori</label>
                                                <select wire:model="formCategory" class="w-full border-gray-300 rounded-xl focus:border-gray-500 focus:ring-gray-500 text-sm">
                                                    <option value="Investasi">Investasi</option>
                                                    <option value="Tabungan">Tabungan</option>
                                                    <option value="Tagihan">Tagihan</option>
                                                    <option value="Pemasukan">Pemasukan</option>
                                                </select>
                                                @error('formCategory') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Keterangan</label>
                                                <input type="text" wire:model="formDescription" placeholder="Contoh: Cicilan KPR, Gaji Bulanan..." class="w-full border-gray-300 rounded-xl focus:border-gray-500 focus:ring-gray-500 text-sm">
                                                @error('formDescription') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                            </div>
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nominal (Rp)</label>
                                                <input type="text"
                                                       x-data="{
                                                           format(val) {
                                                               if (!val) return '';
                                                               let clean = val.toString().replace(/\D/g, '');
                                                               return clean ? new Intl.NumberFormat('id-ID').format(clean) : '';
                                                           }
                                                       }"
                                                       x-init="
                                                           $el.value = format($wire.formAmount || '');
                                                           $watch('$wire.formAmount', value => {
                                                               $el.value = format(value || '');
                                                           });
                                                       "
                                                       @input="$wire.formAmount = $event.target.value.replace(/\D/g, '')"
                                                       placeholder="0"
                                                       class="w-full border-gray-300 rounded-xl focus:border-gray-500 focus:ring-gray-500 text-sm">
                                                @error('formAmount') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                            </div>
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Ingatkan</label>
                                                <select wire:model="formRemindBefore" class="w-full border-gray-300 rounded-xl focus:border-gray-500 focus:ring-gray-500 text-sm">
                                                    <option value="0">Pada Hari H</option>
                                                    <option value="1">H-1</option>
                                                    <option value="3">H-3</option>
                                                    <option value="7">H-7</option>
                                                </select>
                                                @error('formRemindBefore') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                            </div>
                                        </div>
                                        <div class="flex justify-end gap-3 pt-3 border-t border-gray-100">
                                            <button wire:click="$set('showForm', false)" class="px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 hover:bg-gray-50 rounded-xl transition-colors shadow-sm">Batal</button>
                                            <button wire:click="saveReminder" class="px-4 py-2 text-sm font-medium text-white bg-gray-900 hover:bg-gray-800 rounded-xl transition-colors shadow-sm">Simpan</button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Reminder List -->
                                <div class="space-y-3">
                                    @php
                                        $allReminders = $this->remindersByDay->flatten(1)->sortBy('day');
                                    @endphp
                                    
                                    @forelse($allReminders as $reminder)
                                        @php
                                            $colors = match($reminder->category) {
                                                'Investasi' => 'bg-[#E0F2FE] text-[#0369A1]',
                                                'Tabungan' => 'bg-[#E1F5EE] text-[#085041]',
                                                'Tagihan' => 'bg-[#FAECE7] text-[#712B13]',
                                                'Pemasukan' => 'bg-[#EAF3DE] text-[#27500A]',
                                                default => 'bg-gray-100 text-gray-600'
                                            };
                                            $sidebarColor = match($reminder->category) {
                                                'Investasi' => 'bg-[#0369A1]',
                                                'Tabungan' => 'bg-[#085041]',
                                                'Tagihan' => 'bg-[#712B13]',
                                                'Pemasukan' => 'bg-[#27500A]',
                                                default => 'bg-gray-600'
                                            };
                                        @endphp
                                        <div class="bg-white p-3 sm:p-4 rounded-2xl shadow-sm border border-gray-200 flex items-start gap-3 sm:gap-4 hover:border-gray-300 transition-colors group relative overflow-hidden">
                                            <!-- decorative side bar -->
                                            <div class="absolute left-0 top-0 bottom-0 w-1 {{ $sidebarColor }}"></div>
                                            
                                            <div class="flex-1 ml-2">
                                                <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 mb-1.5 sm:mb-2">
                                                    <span class="text-[9px] sm:text-[10px] px-2 py-0.5 rounded-full font-bold uppercase tracking-wider {{ $colors }}">{{ $reminder->category }}</span>
                                                    <span class="text-[11px] sm:text-xs font-medium text-gray-500 flex items-center gap-1">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                        Tgl {{ $reminder->day }} {{ \Carbon\Carbon::createFromDate($currentYear, $currentMonth, 1)->translatedFormat('M') }}
                                                        @if($reminder->remind_before > 0)
                                                            <span class="text-amber-700 bg-amber-50 px-1.5 py-0.5 rounded text-[10px] ml-1 border border-amber-100">H-{{ $reminder->remind_before }}</span>
                                                        @endif
                                                    </span>
                                                </div>
                                                <div class="font-bold text-gray-900 text-sm sm:text-base mb-0.5">{{ e($reminder->description) }}</div>
                                                <div class="text-sm font-semibold text-gray-700">Rp {{ $reminder->amount_formatted }}</div>
                                            </div>
                                            <button wire:click="confirmDelete({{ $reminder->id }})" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all sm:opacity-0 group-hover:opacity-100 focus:opacity-100">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    @empty
                                        <div class="text-center py-10 sm:py-12 bg-white rounded-2xl border border-dashed border-gray-300">
                                            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                                <svg class="w-6 h-6 sm:w-8 sm:h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            </div>
                                            <h4 class="text-sm font-bold text-gray-900">Belum ada reminder</h4>
                                            <p class="text-xs text-gray-500 mt-1">Tambahkan reminder keuangan untuk bulan ini.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Delete Confirmation Modal -->
    @if($reminderIdToDelete)
        <div class="fixed inset-0 z-[200] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm" wire:click="cancelDelete"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border border-gray-100">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                                    Hapus Reminder
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Apakah Anda yakin ingin menghapus reminder ini? Aksi ini tidak dapat dibatalkan.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="button" wire:click="executeDelete" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            Ya, Hapus
                        </button>
                        <button type="button" wire:click="cancelDelete" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 sm:mt-0 sm:w-auto sm:text-sm transition-colors">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
