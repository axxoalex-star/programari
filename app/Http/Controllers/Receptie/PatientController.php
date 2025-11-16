<?php

namespace App\Http\Controllers\Receptie;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Verifică că utilizatorul este recepție
        if ($user->role !== 'receptie') {
            abort(403, 'Acces interzis');
        }

        // Obține toți pacienții din clinica acestui user
        $query = Appointment::where('appointment_type_id', $user->appointment_type_id)
            ->select('client_name', 'client_email', 'client_phone')
            ->selectRaw('COUNT(*) as total_appointments')
            ->selectRaw('MAX(appointment_date) as last_appointment')
            ->groupBy('client_email', 'client_name', 'client_phone');

        // Căutare după nume, email, telefon
        $search = $request->input('q');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('client_name', 'like', "%{$search}%")
                  ->orWhere('client_email', 'like', "%{$search}%")
                  ->orWhere('client_phone', 'like', "%{$search}%");
            });
        }

        $patients = $query->orderBy('last_appointment', 'desc')
            ->paginate(20)
            ->appends(['q' => $search]);

        return view('receptie.patients.index', compact('patients'));
    }

    public function show($email)
    {
        $user = Auth::user();

        if ($user->role !== 'receptie') {
            abort(403, 'Acces interzis');
        }

        // Obține istoricul pacientului din această clinică
        $appointments = Appointment::with(['department', 'appointmentType', 'doctor'])
            ->where('appointment_type_id', $user->appointment_type_id)
            ->where('client_email', $email)
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->get();

        if ($appointments->isEmpty()) {
            abort(404, 'Pacientul nu a fost găsit');
        }

        $patient = [
            'name' => $appointments->first()->client_name,
            'email' => $appointments->first()->client_email,
            'phone' => $appointments->first()->client_phone,
        ];

        return view('receptie.patients.show', compact('appointments', 'patient'));
    }
}
