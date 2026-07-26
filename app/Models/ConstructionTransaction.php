<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConstructionTransaction extends Model
{
    protected $fillable = [
        'construction_project_id', 'type', 'amount', 'transaction_date', 'description', 'image', 'user_id',
    ];

    protected function casts(): array
    {
        return [
            'transaction_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function project()
    {
        return $this->belongsTo(ConstructionProject::class, 'construction_project_id');
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }
}
