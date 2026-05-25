<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class Transaction extends Model
{
    use HasFactory;
    protected $table = 'transactions';

    protected $casts = [
        'date' => 'date'
    ];

    protected $fillable = [
        'user_id',
        'name',
        'amount',
        'type',
        'date',
        'recurring_transactions_id',
        'category_id',
        'account_id',
        'to_account_id',
    ];
    
    protected static function booted()
    {
        static::addGlobalScope('user', function (Builder $builder) {
            if (Auth::check()) {
                $builder->where('transactions.user_id', Auth::id());
            }
        });  
    }

    public function user()  {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function account() 
    {
        return $this->belongsTo(Account::class);    
    }

    public function toAccount()
    {
        return $this->belongsTo(Account::class, 'to_account_id');
    }

    /* ------------------------------------------------------------------ */
    /*  Query Scopes                                                      */
    /* ------------------------------------------------------------------ */

    /**
     * Filter transaksi bulan ini.
     */
    public function scopeCurrentMonth(Builder $query): Builder
    {
        return $query->whereBetween('date', [
            now()->startOfMonth(),
            now()->endOfMonth(),
        ]);
    }

    /**
     * Filter transaksi bulan lalu.
     */
    public function scopePreviousMonth(Builder $query): Builder
    {
        return $query->whereBetween('date', [
            now()->subMonth()->startOfMonth(),
            now()->subMonth()->endOfMonth(),
        ]);
    }

    /**
     * Filter transaksi berdasarkan bulan & tahun tertentu.
     */
    public function scopeForMonth(Builder $query, int $month, int $year): Builder
    {
        return $query->whereMonth('date', $month)
                     ->whereYear('date', $year);
    }

    /**
     * Filter berdasarkan tipe (income / expense).
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeIncome(Builder $query): Builder
    {
        return $query->ofType('income');
    }

    public function scopeExpense(Builder $query): Builder
    {
        return $query->ofType('expense');
    }
}
