<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Monk extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'surname', 'photo', 'type', 'pansa', 'temple', 'status',
        'birth_date', 'ordination_date',
    ];

    protected $casts = [
        'birth_date'      => 'date',
        'ordination_date' => 'date',
    ];

    public static function statuses(): array
    {
        return [
            'active' => 'ຍັງບວດ',
            'disrobed' => 'ສິກແລ້ວ',
            'transferred' => 'ຍ້າຍວັດ',
        ];
    }

    /**
     * Number of vassa (rains retreats) completed since ordination.
     * Approximated as full calendar years elapsed since ordination_date.
     */
    public static function calculatePansa($ordinationDate): int
    {
        if (! $ordinationDate) {
            return 0;
        }

        $ordination = $ordinationDate instanceof Carbon ? $ordinationDate : Carbon::parse($ordinationDate);

        if ($ordination->isFuture()) {
            return 0;
        }

        return (int) $ordination->diffInYears(Carbon::now(), false);
    }

    public function getAgeAttribute(): ?int
    {
        return $this->birth_date ? (int) $this->birth_date->diffInYears(Carbon::now(), false) : null;
    }

    public function absences()
    {
        return $this->hasMany(Absence::class);
    }

    public function dutySchedules()
    {
        return $this->hasMany(DutySchedule::class);
    }

    public function fundTransactions()
    {
        return $this->hasMany(FundTransaction::class);
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
        return match ($this->type) {
            'monk'   => 'ພຣະສົງ',
            'novice' => 'ສາມະເນນ',
            'nun'    => 'ແມ່ຂາວ',
            default  => $this->type,
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statuses()[$this->status] ?? $this->status;
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function getPhotoUrlAttribute(): string
    {
        return $this->photo
            ? asset('storage/' . $this->photo)
            : asset('images/default-monk.svg');
    }
}
