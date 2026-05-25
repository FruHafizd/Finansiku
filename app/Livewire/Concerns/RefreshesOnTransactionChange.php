<?php

namespace App\Livewire\Concerns;

/**
 * Trait untuk komponen yang perlu refresh otomatis
 * ketika transaksi dibuat, dihapus, atau diupdate.
 */
trait RefreshesOnTransactionChange
{
    public function getListeners(): array
    {
        return array_merge($this->listeners ?? [], [
            'transaction-created' => '$refresh',
            'transaction-deleted' => '$refresh',
            'transaction-updated' => '$refresh',
        ]);
    }
}
