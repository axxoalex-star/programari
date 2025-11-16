<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Department;
use App\Models\AppointmentType;
use App\Models\DoctorSchedule;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function index()
    {
        $doctors = Doctor::with(['department', 'appointmentType'])->orderBy('name')->get();
        return view('admin.doctors.index', compact('doctors'));
    }

    public function create()
    {
        $appointmentTypes = AppointmentType::active()->ordered()->get();
        $departments = Department::active()->ordered()->get();
        return view('admin.doctors.create', compact('appointmentTypes', 'departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'appointment_type_id' => 'required|exists:appointment_types,id',
            'department_ids' => 'required|array|min:1',
            'department_ids.*' => 'exists:departments,id',
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:50',
            'email' => 'required|email|max:255|unique:doctors,email',
            'phone' => 'required|string|max:20',
            'consultation_duration' => 'required|integer|min:15|max:180',
            'consultation_price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        // Keep primary department_id as first selected for backward compatibility
        $validated['department_id'] = (int) collect($request->input('department_ids'))->first();

        $doctor = Doctor::create($validated);

        // Sync specialties pivot
        $doctor->departments()->sync($request->input('department_ids'));

        // Save schedules if provided
        if ($request->has('schedules')) {
            $this->saveSchedules($doctor, $request->schedules);
        }

        return redirect()->route('admin.doctors.index')
            ->with('success', 'Doctorul a fost creat cu succes!');
    }

    public function edit(Doctor $doctor)
    {
        $appointmentTypes = AppointmentType::active()->ordered()->get();
        $departments = Department::active()->ordered()->get();
        $schedules = $doctor->schedules()->get()->keyBy('day_of_week');
        return view('admin.doctors.edit', compact('doctor', 'appointmentTypes', 'departments', 'schedules'));
    }

    public function update(Request $request, Doctor $doctor)
    {
        $validated = $request->validate([
            'appointment_type_id' => 'required|exists:appointment_types,id',
            'department_ids' => 'required|array|min:1',
            'department_ids.*' => 'exists:departments,id',
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:50',
            'email' => 'required|email|max:255|unique:doctors,email,' . $doctor->id,
            'phone' => 'required|string|max:20',
            'consultation_duration' => 'required|integer|min:15|max:180',
            'consultation_price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        // Keep primary department_id as first selected for backward compatibility
        $validated['department_id'] = (int) collect($request->input('department_ids'))->first();

        $doctor->update($validated);

        // Sync specialties pivot
        $doctor->departments()->sync($request->input('department_ids'));

        // Update schedules
        if ($request->has('schedules')) {
            // Delete old schedules
            $doctor->schedules()->delete();
            // Save new schedules
            $this->saveSchedules($doctor, $request->schedules);
        }

        return redirect()->route('admin.doctors.index')
            ->with('success', 'Doctorul a fost actualizat cu succes!');
    }

    public function destroy(Doctor $doctor)
    {
        // Check if doctor has appointments
        if ($doctor->appointments()->count() > 0) {
            return redirect()->route('admin.doctors.index')
                ->with('error', 'Nu se poate șterge doctorul deoarece are programări!');
        }

        // Delete schedules
        $doctor->schedules()->delete();

        $doctor->delete();

        return redirect()->route('admin.doctors.index')
            ->with('success', 'Doctorul a fost șters cu succes!');
    }

    private function saveSchedules(Doctor $doctor, array $schedules)
    {
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        foreach ($days as $day) {
            if (isset($schedules[$day]['enabled']) && $schedules[$day]['enabled'] == '1') {
                $startTime = $schedules[$day]['start_time'] ?? null;
                $endTime = $schedules[$day]['end_time'] ?? null;

                if ($startTime && $endTime) {
                    DoctorSchedule::create([
                        'doctor_id' => $doctor->id,
                        'day_of_week' => $day,
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                        'is_active' => true,
                    ]);
                }
            }
        }
    }
}
