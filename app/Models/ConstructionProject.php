<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConstructionProject extends Model
{
    protected $fillable = [
        'name', 'description', 'start_date', 'target_amount', 'image', 'status', 'show_transactions', 'user_id',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'target_amount' => 'decimal:2',
            'show_transactions' => 'boolean',
        ];
    }

    public function transactions()
    {
        return $this->hasMany(ConstructionTransaction::class);
    }

    public function incomeTransactions()
    {
        return $this->hasMany(ConstructionTransaction::class)->where('type', 'income');
    }

    public function expenseTransactions()
    {
        return $this->hasMany(ConstructionTransaction::class)->where('type', 'expense');
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getTotalIncomeAttribute(): float
    {
        return (float) $this->transactions()->where('type', 'income')->sum('amount');
    }

    public function getTotalExpenseAttribute(): float
    {
        return (float) $this->transactions()->where('type', 'expense')->sum('amount');
    }

    public function getBalanceAttribute(): float
    {
        return $this->total_income - $this->total_expense;
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    public function getProgressPercentAttribute(): ?float
    {
        if (! $this->target_amount || (float) $this->target_amount <= 0) {
            return null;
        }

        return min(100, round($this->total_income / (float) $this->target_amount * 100, 1));
    }

    public static function statuses(): array
    {
        return [
            'ongoing'   => 'ກຳລັງດຳເນີນການ',
            'paused'    => 'ຢຸດຊົ່ວຄາວ',
            'completed' => 'ສຳເລັດແລ້ວ',
        ];
    }
}
