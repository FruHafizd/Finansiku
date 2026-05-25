<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\Transaction;
use Carbon\Carbon;

class FinancialScoreService
{
    public function calculateCurrentMonthScore($userId)
    {
        $now = Carbon::now();

        // 1. Data Pemasukan & Pengeluaran
        $income  = (float) Transaction::currentMonth()->income()->sum('amount');
        $expense = (float) Transaction::currentMonth()->expense()->sum('amount');

        // 2. Data Budget
        $totalBudget = Budget::where('user_id', $userId)
            ->where('month', $now->month)
            ->where('year', $now->year)
            ->sum('limit_amount');

        // 3. Consistency (Transaction Count)
        $transactionCount = Transaction::currentMonth()->count();

        // --- Perhitungan Skor ---

        // A. Savings Rate (Bobot 45%)
        $savingsScore = 0;
        if ($income > 0) {
            $savingsRate = ($income - $expense) / $income;
            if ($savingsRate >= 0.20) {
                $savingsScore = 45;
            } elseif ($savingsRate > 0) {
                $savingsScore = ($savingsRate / 0.20) * 45;
            }
        }

        // B. Budget Adherence (Bobot 40%)
        $budgetScore = 0;
        if ($totalBudget > 0) {
            $budgetRate = $expense / $totalBudget;
            if ($budgetRate <= 0.85) {
                $budgetScore = 40;
            } elseif ($budgetRate < 1) {
                // Linear decrease from 0.85 to 1.0
                $budgetScore = 40 * (1 - (($budgetRate - 0.85) / 0.15));
            }
        } else {
            // Fallback: Jika tidak ada budget, bandingkan dengan rata-rata pengeluaran 3 bulan terakhir
            $avgExpense = Transaction::expense()
                ->whereBetween('date', [$now->copy()->subMonths(3)->startOfMonth(), $now->copy()->subMonth()->endOfMonth()])
                ->sum('amount') / 3;

            if ($avgExpense > 0) {
                if ($expense <= $avgExpense) {
                    $budgetScore = 40;
                } else {
                    $budgetScore = max(0, 40 * (1 - (($expense - $avgExpense) / ($avgExpense * 0.5))));
                }
            } else {
                $budgetScore = 20; // Default jika data historis tidak ada
            }
        }

        // C. Consistency (Bobot 15%)
        $consistencyScore = min(15, ($transactionCount / 10) * 15);

        $totalScore = round($savingsScore + $budgetScore + $consistencyScore);

        return [
            'total' => $totalScore,
            'breakdown' => [
                'savings' => round($savingsScore, 1),
                'budget' => round($budgetScore, 1),
                'consistency' => round($consistencyScore, 1),
            ],
            'status' => $this->getStatus($totalScore),
            'color' => $this->getColor($totalScore),
            'income' => $income,
            'expense' => $expense,
            'total_budget' => $totalBudget,
            'has_transactions' => $transactionCount > 0
        ];
    }

    private function getStatus($score)
    {
        if ($score <= 50) return 'Kritis, Perlu Dievaluasi!';
        if ($score <= 75) return 'Cukup, Tapi Bisa Lebih Baik';
        return 'Hebat, Keuanganmu Sehat!';
    }

    private function getColor($score)
    {
        if ($score <= 50) return '#ef4444'; // Red
        if ($score <= 75) return '#f59e0b'; // Amber
        return '#10b981'; // Emerald
    }
}
