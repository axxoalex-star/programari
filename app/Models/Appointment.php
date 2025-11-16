<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_type_id',
        'department_id',
        'doctor_id',
        'client_name',
        'client_email',
        'client_phone',
        'appointment_date',
        'appointment_time',
        'status',
        'notes',
    ];

    protected $casts = [
        'appointment_date' => 'date',
    ];

    /**
     * Get the appointment type.
     */
    public function appointmentType(): BelongsTo
    {
        return $this->belongsTo(AppointmentType::class);
    }

    /**
     * Get the department.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the doctor.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Scope for upcoming appointments.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('appointment_date', '>=', Carbon::today())
                     ->orderBy('appointment_date')
                     ->orderBy('appointment_time');
    }

    /**
     * Scope for past appointments.
     */
    public function scopePast($query)
    {
        return $query->where('appointment_date', '<', Carbon::today())
                     ->orderBy('appointment_date', 'desc')
                     ->orderBy('appointment_time', 'desc');
    }

    /**
     * Scope for filtering by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering by doctor.
     */
    public function scopeByDoctor($query, $doctorId)
    {
        return $query->where('doctor_id', $doctorId);
    }

    /**
     * Scope for filtering by department.
     */
    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Scope for today's appointments.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('appointment_date', Carbon::today());
    }

    /**
     * Check if appointment needs a reminder (1 day before).
     */
    public function needsReminder(): bool
    {
        $appointmentDateTime = Carbon::parse($this->appointment_date . ' ' . $this->appointment_time);
        $tomorrow = Carbon::tomorrow();

        return $appointmentDateTime->isSameDay($tomorrow) && $this->status === 'confirmed';
    }

    /**
     * Get the full appointment datetime.
     */
    public function getFullDateTimeAttribute(): Carbon
    {
        return Carbon::parse($this->appointment_date . ' ' . $this->appointment_time);
    }

    /**
     * Get formatted date.
     */
    public function getFormattedDateAttribute(): string
    {
        return Carbon::parse($this->appointment_date)->format('d.m.Y');
    }

    /**
     * Get formatted time.
     */
    public function getFormattedTimeAttribute(): string
    {
        return Carbon::parse($this->appointment_time)->format('H:i');
    }

    /**
     * Get day name in Romanian.
     */
    public function getDayNameAttribute(): string
    {
        $days = [
            'Monday' => 'Luni',
            'Tuesday' => 'Marți',
            'Wednesday' => 'Miercuri',
            'Thursday' => 'Joi',
            'Friday' => 'Vineri',
            'Saturday' => 'Sâmbătă',
            'Sunday' => 'Duminică',
        ];

        return $days[Carbon::parse($this->appointment_date)->format('l')] ?? '';
    }

    /**
     * Check if appointment is confirmed.
     */
    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    /**
     * Check if appointment is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if appointment is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }
}
