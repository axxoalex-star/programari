<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'day_of_week',
        'start_time',
        'end_time',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    /**
     * Get the doctor that owns the schedule.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Scope to get only active schedules.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by day of week.
     */
    public function scopeForDay($query, string $day)
    {
        return $query->where('day_of_week', strtolower($day));
    }

    /**
     * Get the available days of the week.
     */
    public static function getDaysOfWeek(): array
    {
        return [
            'monday' => 'Luni',
            'tuesday' => 'Marți',
            'wednesday' => 'Miercuri',
            'thursday' => 'Joi',
            'friday' => 'Vineri',
            'saturday' => 'Sâmbătă',
            'sunday' => 'Duminică',
        ];
    }
}
