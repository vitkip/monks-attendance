<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Monk extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'surname', 'photo', 'type', 'pansa', 'temple', 'status'];

    public function absences()
    {
        return $this->hasMany(Absence::class);
    }

    public function dutySchedules()
    {
        return $this->hasMany(DutySchedule::class);
    }

    public function hasDutyOn(string $date): bool
    {
        return $this->dutySchedules()->whereDate('duty_date', $date)->exists();
    }

    public function getTotalFineAttribute(): float
    {
        return (float) $this->absences()->sum('fine_amount');
    }

    public function getUnpaidFineAttribute(): float
    {
        return (float) $this->absences()->where('is_paid', 0)->sum('fine_amount');
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->name . ' ' . $this->surname);
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->type === 'monk' ? 'ພຣະສົງ' : 'ສາມະເນນ';
    }

    public function getPhotoUrlAttribute(): string
    {
        return $this->photo
            ? asset('storage/' . $this->photo)
            : asset('images/default-monk.png');
    }
}
