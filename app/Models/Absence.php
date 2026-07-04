<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    protected $fillable = [
        'monk_id', 'fine_rate_id', 'absent_date',
        'reason', 'fine_amount', 'is_paid', 'note',
    ];

    protected $casts = [
        'absent_date' => 'date',
        'fine_amount' => 'decimal:2',
    ];

    public function monk()
    {
        return $this->belongsTo(Monk::class);
    }

    public function fineRate()
    {
        return $this->belongsTo(FineRate::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->is_paid ? 'ຈ່າຍແລ້ວ' : 'ຍັງບໍ່ຈ່າຍ';
    }
}
