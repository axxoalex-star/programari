<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_type_id',
        'name',
        'description',
        'color',
        'icon',
        'is_active',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get the clinic (appointment type) that owns the department.
     */
    public function appointmentType(): BelongsTo
    {
        return $this->belongsTo(AppointmentType::class);
    }

    /**
     * Get the doctors for the department.
     */
    public function doctors(): HasMany
    {
        return $this->hasMany(Doctor::class);
    }

    /**
     * Get the appointments for the department.
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Get only active doctors.
     */
    public function activeDoctors(): HasMany
    {
        return $this->doctors()->where('is_active', true);
    }

    /**
     * Scope to get only active departments.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order departments.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
