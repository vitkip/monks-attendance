<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FundTransaction extends Model
{
    protected $fillable = [
        'type', 'amount', 'transaction_date', 'monk_id', 'party_name', 'description', 'image', 'user_id',
    ];

    protected function casts(): array
    {
        return [
            'transaction_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function monk()
    {
        return $this->belongsTo(Monk::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    public function getPartyLabelAttribute(): string
    {
        return $this->monk?->full_name ?? $this->party_name ?? 'ບໍ່ລະບຸ';
    }

    public static function types(): array
    {
        return [
            'income' => 'ລາຍຮັບ',
            'expense' => 'ລາຍຈ່າຍ',
        ];
    }
}
