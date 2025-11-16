<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Doctor;
use Illuminate\Support\Collection;

class DoctorNextDaySchedule extends Mailable
{
    use Queueable, SerializesModels;

    public Doctor $doctor;
    public Collection $appointments;
    public string $date;

    public function __construct(Doctor $doctor, Collection $appointments, string $date)
    {
        $this->doctor = $doctor;
        $this->appointments = $appointments;
        $this->date = $date;
    }

    public function build()
    {
        return $this->subject('Programări pentru ' . date('d.m.Y', strtotime($this->date)))
            ->view('emails.doctor-next-day-schedule');
    }
}
