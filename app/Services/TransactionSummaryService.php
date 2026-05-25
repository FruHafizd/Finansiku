<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class TransactionSummaryService
{
    /**
     * Ringkasan pemasukan, pengeluaran, saldo bulan ini vs bulan lalu.
     */
    public function getMonthlySummary(): array
    {
        $currentIncome  = (float) Transaction::currentMonth()->income()->sum('amount');
        $currentExpense = (float) Transaction::currentMonth()->expense()->sum('amount');
        $prevIncome     = (float) Transaction::previousMonth()->income()->sum('amount');
        $prevExpense    = (float) Transaction::previousMonth()->expense()->sum('amount');
        $hasPrev        = Transaction::previousMonth()->exists();

        return [
            'income'  => [
                'current' => $currentIncome,
                'change'  => $hasPrev ? $this->pct($currentIncome, $prevIncome) : null,
                'hasPrev' => $hasPrev,
            ],
            'expense' => [
                'current' => $currentExpense,
                'change'  => $hasPrev ? $this->pct($currentExpense, $prevExpense) : null,
                'hasPrev' => $hasPrev,
            ],
            'balance' => [
                'current'    => $currentIncome - $currentExpense,
                'prevAmount' => $prevIncome - $prevExpense,
                'hasPrev'    => $hasPrev,
            ],
        ];
    }

    /**
     * Data chart pengeluaran per kategori bulan ini.
     */
    public function getExpenseByCategory(): array
    {
        $results = Transaction::currentMonth()
            ->expense()
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->select(
                'categories.name as category',
                'categories.color',
                DB::raw('SUM(transactions.amount) as total')
            )
            ->groupBy('categories.id', 'categories.name', 'categories.color')
            ->orderByDesc('total')
            ->get();

        return [
            'labels' => $results->pluck('category')->toArray(),
            'colors' => $results->pluck('color')->map(fn($c) => $c ?? '#6366f1')->toArray(),
            'data'   => $results->pluck('total')->map(fn($v) => (float) $v)->toArray(),
        ];
    }

    /**
     * Hitung persentase perubahan.
     */
    private function pct(float $current, float $prev): ?float
    {
        if ($prev == 0) return null;
        return round((($current - $prev) / $prev) * 100, 1);
    }
}
