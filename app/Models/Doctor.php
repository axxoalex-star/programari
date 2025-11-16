<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_type_id',
        'department_id',
        'name',
        'title',
        'email',
        'phone',
        'bio',
        'photo',
        'consultation_duration',
        'consultation_price',
        'is_active',
        'receive_next_day_email',
        'next_day_email_hour',
        'notification_email',
        'holiday_dates',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'consultation_price' => 'decimal:2',
        'consultation_duration' => 'integer',
        'receive_next_day_email' => 'boolean',
        'next_day_email_hour' => 'integer',
        'holiday_dates' => 'array',
    ];

    /**
     * Get the clinic (appointment type) that the doctor belongs to.
     */
    public function appointmentType(): BelongsTo
    {
        return $this->belongsTo(AppointmentType::class);
    }

    /**
     * Get the department that the doctor belongs to.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get all specialties (departments) the doctor belongs to via pivot.
     */
    public function departments()
    {
        return $this->belongsToMany(Department::class, 'doctor_department');
    }

    /**
     * Get the schedules for the doctor.
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(DoctorSchedule::class);
    }

    /**
     * Get only active schedules.
     */
    public function activeSchedules(): HasMany
    {
        return $this->schedules()->where('is_active', true);
    }

    /**
     * Get the appointments for the doctor.
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Get the holidays for the doctor.
     */
    public function holidays(): HasMany
    {
        return $this->hasMany(DoctorHoliday::class);
    }

    /**
     * Get the user account for the doctor.
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'doctor_id');
    }

    /**
     * Scope to get only active doctors.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by department.
     */
    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where(function ($q) use ($departmentId) {
            $q->where('department_id', $departmentId)
              ->orWhereHas('departments', function ($qq) use ($departmentId) {
                  $qq->where('departments.id', $departmentId);
              });
        });
    }

    /**
     * Get the doctor's full name with title.
     */
    public function getFullNameAttribute(): string
    {
        return $this->title ? $this->title . ' ' . $this->name : $this->name;
    }

    /**
     * Check if doctor works on a specific day.
     */
    public function worksOnDay(string $day): bool
    {
        return $this->activeSchedules()
            ->where('day_of_week', strtolower($day))
            ->exists();
    }

    /**
     * Get doctor's schedule for a specific day.
     */
    public function getScheduleForDay(string $day)
    {
        return $this->activeSchedules()
            ->where('day_of_week', strtolower($day))
            ->first();
    }
}
