<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'appointment_id',
        'client_email',
        'client_name',
        'client_phone',
        'title',
        'notes',
        'attachment_path',
        'tags',
    ];

    protected $casts = [
        'tags' => 'array',
    ];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }
}
