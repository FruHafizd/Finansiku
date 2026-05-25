<?php

namespace App\Livewire\Home;

use App\Livewire\Concerns\RefreshesOnTransactionChange;
use App\Services\FinancialScoreService;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class FinancialScore extends Component
{
    use RefreshesOnTransactionChange;

    public $scoreData;

    public function mount(FinancialScoreService $service)
    {
        $this->refreshScore($service);
    }

    public function refreshScore(FinancialScoreService $service)
    {
        $this->scoreData = $service->calculateCurrentMonthScore(Auth::id());
    }

    public function render()
    {
        return view('livewire.home.financial-score');
    }
}
