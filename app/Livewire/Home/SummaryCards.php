<?php

namespace App\Livewire\Home;

use App\Livewire\Concerns\RefreshesOnTransactionChange;
use App\Services\TransactionSummaryService;
use Livewire\Component;

class SummaryCards extends Component
{
    use RefreshesOnTransactionChange;

    public function render(TransactionSummaryService $service)
    {
        return view('livewire.home.summary-cards', [
            'summary' => $service->getMonthlySummary(),
        ]);
    }
}