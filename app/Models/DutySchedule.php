<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DutySchedule extends Model
{
    protected $fillable = [
        'monk_id', 'schedule_type', 'duty_name',
        'duty_date', 'day_of_week', 'description',
    ];

    protected $casts = [
        'duty_date'   => 'date',
        'day_of_week' => 'integer',
    ];

    public static array $dayNames = [
        1 => 'ວັນຈັນ',
        2 => 'ວັນອັງຄານ',
        3 => 'ວັນພຸດ',
        4 => 'ວັນພະຫັດ',
        5 => 'ວັນສຸກ',
        6 => 'ວັນເສົາ',
        7 => 'ວັນທິດ',
    ];

    public function monk()
    {
        return $this->belongsTo(Monk::class);
    }

    public function isWeekly(): bool
    {
        return $this->schedule_type === 'weekly';
    }

    public function getDayNameAttribute(): string
    {
        return self::$dayNames[$this->day_of_week] ?? '—';
    }

    public static function monkHasDutyOn(int $monkId, string $date): ?self
    {
        $carbon = \Carbon\Carbon::parse($date);

        return static::where('monk_id', $monkId)
            ->where(function ($q) use ($date, $carbon) {
                $q->where(function ($q1) use ($date) {
                    $q1->where('schedule_type', 'once')
                       ->whereDate('duty_date', $date);
                })->orWhere(function ($q2) use ($carbon) {
                    $q2->where('schedule_type', 'weekly')
                       ->where('day_of_week', $carbon->isoWeekday());
                });
            })
            ->first();
    }
}
