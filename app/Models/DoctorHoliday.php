<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorHoliday extends Model
{
    protected $fillable = [
        'doctor_id',
        'holiday_date',
        'description',
    ];

    protected $casts = [
        'holiday_date' => 'date',
    ];

    /**
     * Get the doctor that owns the holiday.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }
}
