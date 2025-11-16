<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use App\Models\Doctor;
use Carbon\Carbon;

class PrintController extends Controller
{
    // Print results summary for an appointment (HTML view; can be converted to PDF later)
    public function result(Request $request, string $email)
    {
        $user = Auth::user();
        if ($user->role !== 'doctor' || !$user->doctor_id) {
            abort(403, 'Acces interzis');
        }

        $appointmentId = $request->query('appointment_id');
        $appointment = Appointment::with(['appointmentType', 'department', 'doctor'])
            ->where('doctor_id', $user->doctor_id)
            ->when($appointmentId, fn($q) => $q->where('id', $appointmentId))
            ->where('client_email', $email)
            ->latest('appointment_date')
            ->latest('appointment_time')
            ->firstOrFail();

        $doctor = Doctor::findOrFail($user->doctor_id);

        return view('print.result', compact('appointment', 'doctor'));
    }

    // Print cabinet-specific form by template key
    public function form(Request $request, string $email, string $template)
    {
        $user = Auth::user();
        if ($user->role !== 'doctor' || !$user->doctor_id) {
            abort(403, 'Acces interzis');
        }

        $appointmentId = $request->query('appointment_id');
        $appointment = Appointment::with(['appointmentType', 'department', 'doctor'])
            ->where('doctor_id', $user->doctor_id)
            ->when($appointmentId, fn($q) => $q->where('id', $appointmentId))
            ->where('client_email', $email)
            ->latest('appointment_date')
            ->latest('appointment_time')
            ->firstOrFail();

        $doctor = Doctor::findOrFail($user->doctor_id);

        // map template to view
        $view = match ($template) {
            'consent' => 'print.forms.consent',
            'referral' => 'print.forms.referral',
            'prescription' => 'print.forms.prescription',
            default => 'print.forms.generic',
        };

        return view($view, compact('appointment', 'doctor', 'template'));
    }
}
