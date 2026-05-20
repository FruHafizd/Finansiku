<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialReminder extends Model
{
    protected $fillable = [
        'user_id',
        'day',
        'month',
        'year',
        'category',
        'description',
        'amount',
        'remind_before',
    ];

    protected $casts = [
        'amount' => 'integer',
    ];

    protected $appends = [
        'amount_formatted'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForMonth($query, $year, $month)
    {
        return $query->where('year', $year)->where('month', $month);
    }

    public function getAmountFormattedAttribute(): string
    {
        return number_format($this->amount, 0, ',', '.');
    }
}
