<?php

namespace App\Livewire;

use App\Models\FinancialReminder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Carbon\Carbon;

class FinancialCalendar extends Component
{
    public int $currentYear;
    public int $currentMonth;
    public bool $showForm = false;
    public ?int $reminderIdToDelete = null;

    public int $formDay = 1;
    public string $formCategory = 'Tagihan';
    public string $formDescription = '';
    public string $formAmount = '';
    public int $formRemindBefore = 0;

    public function mount(): void
    {
        $now = Carbon::now();
        $this->currentYear = $now->year;
        $this->currentMonth = $now->month;
        $this->formDay = $now->day;
    }

    public function previousMonth(): void
    {
        $this->currentMonth--;
        if ($this->currentMonth < 1) {
            $this->currentMonth = 12;
            $this->currentYear--;
        }
    }

    public function nextMonth(): void
    {
        $this->currentMonth++;
        if ($this->currentMonth > 12) {
            $this->currentMonth = 1;
            $this->currentYear++;
        }
    }

    #[Computed]
    public function holidaysByDate(): array
    {
        return Cache::remember('holidays_all', 86400, function () {
            try {
                $response = Http::timeout(5)->get('https://raw.githubusercontent.com/guangrei/APIHariLibur_V2/main/calendar.min.json');
                if ($response->successful()) {
                    return $response->json() ?? [];
                }
            } catch (\Exception $e) {
                // Return empty if API fails
            }
            return [];
        });
    }

    #[Computed]
    public function remindersByDay()
    {
        if (!Auth::check()) {
            return collect();
        }

        return FinancialReminder::where('user_id', Auth::id())
            ->forMonth($this->currentYear, $this->currentMonth)
            ->get()
            ->groupBy('day');
    }

    #[Computed]
    public function calendarDays(): array
    {
        $firstDayOfMonth = Carbon::createFromDate($this->currentYear, $this->currentMonth, 1)->dayOfWeek;
        $daysInMonth = Carbon::createFromDate($this->currentYear, $this->currentMonth, 1)->daysInMonth;

        $days = [];

        // Padding awal
        for ($i = 0; $i < $firstDayOfMonth; $i++) {
            $days[] = null;
        }

        // Tanggal dalam bulan
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $days[] = $i;
        }

        // Padding akhir agar total slot jadi kelipatan 7 (max 42)
        $totalSlots = count($days) > 35 ? 42 : 35;
        while (count($days) < $totalSlots) {
            $days[] = null;
        }

        return $days;
    }

    public function saveReminder(): void
    {
        $daysInMonth = Carbon::createFromDate($this->currentYear, $this->currentMonth, 1)->daysInMonth;

        $validated = $this->validate([
            'formDay' => "required|integer|min:1|max:{$daysInMonth}",
            'formCategory' => 'required|in:Investasi,Tabungan,Tagihan,Pemasukan',
            'formDescription' => 'required|string|max:255',
            'formAmount' => 'required|numeric|min:1',
            'formRemindBefore' => 'required|in:0,1,3,7',
        ]);

        FinancialReminder::create([
            'user_id' => Auth::id(),
            'day' => (int) $validated['formDay'],
            'month' => $this->currentMonth,
            'year' => $this->currentYear,
            'category' => $validated['formCategory'],
            'description' => strip_tags($validated['formDescription']),
            'amount' => (int) abs($validated['formAmount']),
            'remind_before' => (int) $validated['formRemindBefore'],
        ]);

        $this->reset(['formDescription', 'formAmount']);
        $this->showForm = false;

        $this->dispatch('notify',
            type: 'success',
            title: 'Berhasil!',
            message: 'Reminder keuangan berhasil ditambahkan.'
        );
    }

    public function confirmDelete(int $id): void
    {
        $this->reminderIdToDelete = $id;
    }

    public function cancelDelete(): void
    {
        $this->reminderIdToDelete = null;
    }

    public function executeDelete(): void
    {
        if (!$this->reminderIdToDelete) return;

        $reminder = FinancialReminder::where('user_id', Auth::id())->findOrFail($this->reminderIdToDelete);
        $reminder->delete();

        $this->reminderIdToDelete = null;

        $this->dispatch('notify',
            type: 'success',
            title: 'Dihapus',
            message: 'Reminder berhasil dihapus.'
        );
    }

    public function render()
    {
        return view('livewire.financial-calendar');
    }
}
