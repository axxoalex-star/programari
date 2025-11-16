<?php

namespace App\Http\Controllers\Receptie;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Doctor;
use App\Models\Department;
use App\Models\DoctorSchedule;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DoctorController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user || !$user->isReceptie() || !$user->appointment_type_id) {
            abort(403, 'Acces interzis');
        }

        $doctors = Doctor::where('appointment_type_id', $user->appointment_type_id)
            ->with(['departments'])
            ->orderBy('name')
            ->paginate(20);

        return view('receptie.doctors.index', compact('doctors'));
    }

    public function create()
    {
        $user = Auth::user();
        if (!$user || !$user->isReceptie() || !$user->appointment_type_id) {
            abort(403, 'Acces interzis');
        }

        $departments = Department::active()
            ->where('appointment_type_id', $user->appointment_type_id)
            ->ordered()
            ->get();

        return view('receptie.doctors.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->isReceptie() || !$user->appointment_type_id) {
            abort(403, 'Acces interzis');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:50',
            'email' => 'required|email|max:255|unique:doctors,email',
            'phone' => 'required|string|max:20',
            'consultation_duration' => 'required|integer|min:15|max:180',
            'consultation_price' => 'required|numeric|min:0',
            'department_ids' => 'required|array|min:1',
            'department_ids.*' => 'exists:departments,id',
            'is_active' => 'boolean',
        ]);

        $validated['appointment_type_id'] = $user->appointment_type_id;
        $validated['is_active'] = $request->has('is_active');
        // primary department for compatibility
        $validated['department_id'] = (int) collect($request->input('department_ids'))->first();

        $doctor = Doctor::create($validated);
        $doctor->departments()->sync($request->input('department_ids'));

        // Save schedules if provided
        if ($request->has('schedules')) {
            $this->saveSchedules($doctor, $request->schedules);
        }

        return redirect()->route('receptie.doctors.index')
            ->with('success', 'Doctorul a fost creat cu succes!');
    }

    public function edit(Doctor $doctor)
    {
        $user = Auth::user();
        if (!$user || !$user->isReceptie() || !$user->appointment_type_id) {
            abort(403, 'Acces interzis');
        }
        if ($doctor->appointment_type_id !== $user->appointment_type_id) {
            abort(403, 'Acces interzis');
        }

        $departments = Department::active()
            ->where('appointment_type_id', $user->appointment_type_id)
            ->ordered()
            ->get();

        // Load holidays relationship
        $doctor->load('holidays');

        // Prepare existing holidays for the view
        $existingHolidays = $doctor->holidays->pluck('holiday_date')
            ->map(fn($date) => \Carbon\Carbon::parse($date)->format('Y-m-d'))
            ->toArray();

        return view('receptie.doctors.edit', compact('doctor', 'departments', 'existingHolidays'));
    }

    public function update(Request $request, Doctor $doctor)
    {
        $user = Auth::user();
        if (!$user || !$user->isReceptie() || !$user->appointment_type_id) {
            abort(403, 'Acces interzis');
        }
        if ($doctor->appointment_type_id !== $user->appointment_type_id) {
            abort(403, 'Acces interzis');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:50',
            'email' => 'required|email|max:255|unique:doctors,email,' . $doctor->id,
            'phone' => 'required|string|max:20',
            'consultation_duration' => 'required|integer|min:15|max:180',
            'consultation_price' => 'required|numeric|min:0',
            'department_ids' => 'required|array|min:1',
            'department_ids.*' => 'exists:departments,id',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['department_id'] = (int) collect($request->input('department_ids'))->first();

        $doctor->update($validated);
        $doctor->departments()->sync($request->input('department_ids'));

        // Update schedules
        if ($request->has('schedules')) {
            $doctor->schedules()->delete();
            $this->saveSchedules($doctor, $request->schedules);
        }

        // Handle legal holidays - save to doctor_holidays table
        // Clear existing holidays first
        $doctor->holidays()->delete();

        if ($request->filled('holiday_dates')) {
            $raw = $request->input('holiday_dates');
            // Split by comma (format from calendar picker)
            $tokens = explode(',', $raw);
            $validDates = [];

            foreach ($tokens as $t) {
                $t = trim($t);
                if ($t === '') continue;

                // Validate date format
                try {
                    $d = Carbon::parse($t)->toDateString();
                    $validDates[] = $d;
                } catch (\Throwable $e) {
                    // ignore invalid date
                }
            }

            // Add new holidays
            foreach (array_unique($validDates) as $date) {
                $doctor->holidays()->create([
                    'holiday_date' => $date,
                    'description' => 'Sărbătoare legală / Concediu',
                ]);
            }
        }

        return redirect()->route('receptie.doctors.index')
            ->with('success', 'Doctorul a fost actualizat cu succes!');
    }

    public function destroy(Doctor $doctor)
    {
        $user = Auth::user();
        if (!$user || !$user->isReceptie() || !$user->appointment_type_id) {
            abort(403, 'Acces interzis');
        }
        if ($doctor->appointment_type_id !== $user->appointment_type_id) {
            abort(403, 'Acces interzis');
        }

        if ($doctor->appointments()->count() > 0) {
            return redirect()->route('receptie.doctors.index')
                ->with('error', 'Nu se poate șterge doctorul deoarece are programări!');
        }

        $doctor->schedules()->delete();
        $doctor->departments()->detach();
        $doctor->delete();

        return redirect()->route('receptie.doctors.index')
            ->with('success', 'Doctorul a fost șters cu succes!');
    }

    private function saveSchedules(Doctor $doctor, array $schedules): void
    {
        $days = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
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
