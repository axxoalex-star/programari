<?php

namespace App\Http\Controllers\Receptie;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Department;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display reception dashboard with stats
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Verify user is reception
        if (!$user->isReceptie() || !$user->appointment_type_id) {
            abort(403, 'Acces interzis');
        }

        // Get doctors from this clinic
        $doctorIds = Doctor::where('appointment_type_id', $user->appointment_type_id)
            ->pluck('id');

        $today = Carbon::today();

        // Statistics for this clinic's doctors
        $stats = [
            'today_appointments' => Appointment::whereIn('doctor_id', $doctorIds)
                ->where('appointment_date', $today)
                ->where('status', 'confirmed')
                ->count(),

            'upcoming_appointments' => Appointment::whereIn('doctor_id', $doctorIds)
                ->where('appointment_date', '>=', $today)
                ->where('status', 'confirmed')
                ->count(),

            'total_doctors' => Doctor::where('appointment_type_id', $user->appointment_type_id)
                ->where('is_active', true)
                ->count(),
        ];

        // Today's appointments
        $todayAppointments = Appointment::with(['doctor', 'department'])
            ->whereIn('doctor_id', $doctorIds)
            ->where('appointment_date', $today)
            ->orderBy('appointment_time')
            ->limit(10)
            ->get();

        // Upcoming appointments (next 7 days) with optional doctor filter
        $upcomingQuery = Appointment::with(['doctor', 'department'])
            ->whereIn('doctor_id', $doctorIds)
            ->where('appointment_date', '>', $today)
            ->where('appointment_date', '<=', $today->copy()->addDays(7));

        // Filter by doctor if specified
        if ($request->filled('doctor_id')) {
            $upcomingQuery->where('doctor_id', $request->doctor_id);
        }

        $upcomingAppointments = $upcomingQuery
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->limit(50)
            ->get();

        // Get list of doctors for the filter dropdown
        $doctors = Doctor::where('appointment_type_id', $user->appointment_type_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $selectedDoctorId = $request->input('doctor_id');

        return view('receptie.dashboard', compact('stats', 'todayAppointments', 'upcomingAppointments', 'doctors', 'selectedDoctorId'));
    }

    /**
     * Display all appointments for this clinic
     */
    public function appointments(Request $request)
    {
        $user = auth()->user();

        // Verify user is reception
        if (!$user->isReceptie() || !$user->appointment_type_id) {
            abort(403, 'Acces interzis');
        }

        // Get doctors from this clinic
        $doctorIds = Doctor::where('appointment_type_id', $user->appointment_type_id)
            ->pluck('id');

        $query = Appointment::with(['department', 'doctor', 'appointmentType'])
            ->whereIn('doctor_id', $doctorIds);

        // Filter by department
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Filter by doctor
        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        // Filter by date
        if ($request->filled('date')) {
            $query->where('appointment_date', $request->date);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Sort by date and time
        $appointments = $query->orderBy('appointment_date', 'desc')
                              ->orderBy('appointment_time', 'desc')
                              ->paginate(20);

        // Get departments and doctors for this clinic only
        $departments = Department::active()
            ->where('appointment_type_id', $user->appointment_type_id)
            ->ordered()
            ->get();

        $doctors = Doctor::active()
            ->where('appointment_type_id', $user->appointment_type_id)
            ->orderBy('name')
            ->get();

        return view('receptie.appointments.index', compact('appointments', 'departments', 'doctors'));
    }

    /**
     * Show form to edit an appointment
     */
    public function editAppointment(Appointment $appointment)
    {
        $user = auth()->user();

        // Verify this appointment belongs to a doctor from this clinic
        $doctor = $appointment->doctor;
        if (!$doctor || $doctor->appointment_type_id != $user->appointment_type_id) {
            abort(403, 'Acces interzis');
        }

        $departments = Department::active()
            ->where('appointment_type_id', $user->appointment_type_id)
            ->ordered()
            ->get();

        $doctors = Doctor::active()
            ->where('appointment_type_id', $user->appointment_type_id)
            ->orderBy('name')
            ->get();

        return view('receptie.appointments.edit', compact('appointment', 'departments', 'doctors'));
    }

    /**
     * Update an appointment
     */
    public function updateAppointment(Request $request, Appointment $appointment)
    {
        $user = auth()->user();

        // Verify this appointment belongs to a doctor from this clinic
        $doctor = $appointment->doctor;
        if (!$doctor || $doctor->appointment_type_id != $user->appointment_type_id) {
            abort(403, 'Acces interzis');
        }

        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'doctor_id' => 'required|exists:doctors,id',
            'client_name' => 'required|string|max:255',
            'client_email' => 'required|email|max:255',
            'client_phone' => 'required|string|max:20',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
            'status' => 'required|in:confirmed,cancelled,completed',
            'notes' => 'nullable|string',
        ]);

        // Verify the new doctor also belongs to this clinic
        $newDoctor = Doctor::find($validated['doctor_id']);
        if (!$newDoctor || $newDoctor->appointment_type_id != $user->appointment_type_id) {
            return back()->withErrors(['doctor_id' => 'Doctorul selectat nu aparține acestei clinici.'])->withInput();
        }

        // Check if slot changed
        $slotChanged = $appointment->doctor_id != $request->doctor_id ||
                      $appointment->appointment_date != $request->appointment_date ||
                      $appointment->appointment_time != $request->appointment_time;

        if ($slotChanged && $request->status !== 'cancelled') {
            // Check if new slot is available
            $isBooked = Appointment::where('doctor_id', $request->doctor_id)
                ->where('appointment_date', $request->appointment_date)
                ->where('appointment_time', $request->appointment_time)
                ->where('id', '!=', $appointment->id)
                ->where('status', 'confirmed')
                ->exists();

            if ($isBooked) {
                return back()->withErrors(['appointment_time' => 'Acest slot este deja rezervat.'])->withInput();
            }
        }

        // Keep the same appointment_type_id (clinic)
        $validated['appointment_type_id'] = $user->appointment_type_id;

        $appointment->update($validated);

        return redirect()->route('receptie.appointments')
                         ->with('success', 'Programarea a fost actualizată cu succes!');
    }

    /**
     * Delete an appointment
     */
    public function destroyAppointment(Appointment $appointment)
    {
        $user = auth()->user();

        // Verify this appointment belongs to a doctor from this clinic
        $doctor = $appointment->doctor;
        if (!$doctor || $doctor->appointment_type_id != $user->appointment_type_id) {
            abort(403, 'Acces interzis');
        }

        $appointment->delete();

        return redirect()->route('receptie.appointments')
                         ->with('success', 'Programarea a fost ștearsă cu succes!');
    }
}
