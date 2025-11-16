<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\AppointmentType;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Appointment::with(['department', 'doctor', 'appointmentType']);

        // Filtrare după departament
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Filtrare după doctor
        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        // Filtrare după clinică
        if ($request->filled('appointment_type_id')) {
            $query->where('appointment_type_id', $request->appointment_type_id);
        }

        // Filtrare după dată
        if ($request->filled('date')) {
            $query->where('appointment_date', $request->date);
        }

        // Filtrare după status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Sortare implicit după dată și oră
        $appointments = $query->orderBy('appointment_date', 'desc')
                              ->orderBy('appointment_time', 'desc')
                              ->paginate(20);

        $departments = Department::active()->ordered()->get();
        $doctors = Doctor::active()->orderBy('name')->get();
        $appointmentTypes = AppointmentType::active()->ordered()->get();

        return view('admin.appointments.index', compact('appointments', 'departments', 'doctors', 'appointmentTypes'));
    }

    public function create()
    {
        $departments = Department::active()->ordered()->get();
        $appointmentTypes = AppointmentType::active()->ordered()->get();
        return view('admin.appointments.create', compact('departments', 'appointmentTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_type_id' => 'required|exists:appointment_types,id',
            'client_name' => 'required|string|max:255',
            'client_email' => 'required|email|max:255',
            'client_phone' => 'required|string|max:20',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
            'status' => 'required|in:confirmed,cancelled,completed',
            'notes' => 'nullable|string',
        ]);

        Appointment::create($validated);

        return redirect()->route('admin.appointments.index')
                         ->with('success', 'Programarea a fost creată cu succes!');
    }

    public function show(Appointment $appointment)
    {
        $appointment->load(['department', 'doctor', 'appointmentType']);
        return view('admin.appointments.show', compact('appointment'));
    }

    public function edit(Appointment $appointment)
    {
        $departments = Department::active()->ordered()->get();
        $doctors = Doctor::active()->orderBy('name')->get();
        $appointmentTypes = AppointmentType::active()->ordered()->get();
        return view('admin.appointments.edit', compact('appointment', 'departments', 'doctors', 'appointmentTypes'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_type_id' => 'required|exists:appointment_types,id',
            'client_name' => 'required|string|max:255',
            'client_email' => 'required|email|max:255',
            'client_phone' => 'required|string|max:20',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
            'status' => 'required|in:confirmed,cancelled,completed',
            'notes' => 'nullable|string',
        ]);

        // Verifică disponibilitatea slotului doar dacă s-a schimbat data, ora sau doctorul
        $slotChanged = $appointment->doctor_id != $request->doctor_id ||
                      $appointment->appointment_date != $request->appointment_date ||
                      $appointment->appointment_time != $request->appointment_time;

        if ($slotChanged && $request->status !== 'cancelled') {
            // Verifică dacă noul slot este disponibil (exclude programarea curentă)
            $isBooked = Appointment::where('doctor_id', $request->doctor_id)
                ->where('appointment_date', $request->appointment_date)
                ->where('appointment_time', $request->appointment_time)
                ->where('id', '!=', $appointment->id)
                ->where('status', 'confirmed')
                ->exists();

            if ($isBooked) {
                return back()->withErrors(['appointment_time' => 'Acest slot este deja rezervat. Vă rugăm să selectați alt slot.'])
                            ->withInput();
            }
        }

        $appointment->update($validated);

        return redirect()->route('admin.appointments.index')
                         ->with('success', 'Programarea a fost actualizată cu succes!');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();

        return redirect()->route('admin.appointments.index')
                         ->with('success', 'Programarea a fost ștearsă cu succes!');
    }
}

