<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'doctor_id',
        'appointment_type_id',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the doctor associated with the user (if user is a doctor).
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Get the clinic associated with the user (if user is receptie).
     */
    public function appointmentType(): BelongsTo
    {
        return $this->belongsTo(AppointmentType::class);
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is a doctor.
     */
    public function isDoctor(): bool
    {
        return $this->role === 'doctor';
    }

    /**
     * Check if user is an assistant.
     */
    public function isAssistant(): bool
    {
        return $this->role === 'assistant';
    }

    /**
     * Check if user is receptie.
     */
    public function isReceptie(): bool
    {
        return $this->role === 'receptie';
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if user has any of the given roles.
     */
    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles);
    }

    /**
     * Scope to filter users by role.
     */
    public function scopeByRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope to get only active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get only admins.
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    /**
     * Scope to get only doctors.
     */
    public function scopeDoctors($query)
    {
        return $query->where('role', 'doctor');
    }

    /**
     * Scope to get only assistants.
     */
    public function scopeAssistants($query)
    {
        return $query->where('role', 'assistant');
    }

    /**
     * Scope to get only receptie users.
     */
    public function scopeReceptie($query)
    {
        return $query->where('role', 'receptie');
    }

    /**
     * Get the role label in Romanian.
     */
    public function getRoleLabelAttribute(): string
    {
        return match($this->role) {
            'admin' => 'Administrator',
            'doctor' => 'Doctor',
            'assistant' => 'Asistentă',
            'receptie' => 'Recepție',
            default => 'Necunoscut',
        };
    }
}
