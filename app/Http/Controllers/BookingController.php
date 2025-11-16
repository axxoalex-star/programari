<?php

namespace App\Http\Controllers;

use App\Models\AppointmentType;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Appointment;
use App\Mail\ClientConfirmation;
use App\Mail\StaffNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Afișează pagina de programare.
     */
    public function index()
    {
        $appointmentTypes = AppointmentType::active()->ordered()->get();
        $departments = []; // Se vor încărca prin AJAX după selectarea clinicii

        return view('booking.index', compact('appointmentTypes', 'departments'));
    }

    /**
     * Obține departamentele filtrate după clinică (AJAX).
     */
    public function getDepartments(Request $request)
    {
        $appointmentTypeId = $request->input('appointment_type_id');

        $departments = Department::active()
            ->where('appointment_type_id', $appointmentTypeId)
            ->orderBy('order')
            ->get(['id', 'name', 'color']);

        return response()->json($departments);
    }

    /**
     * Obține doctorii filtrat după departament (AJAX).
     */
    public function getDoctors(Request $request)
    {
        $departmentId = $request->input('department_id');

        $doctors = Doctor::active()
            ->byDepartment($departmentId)
            ->orderBy('name')
            ->get(['id', 'name', 'title', 'consultation_duration'])
            ->map(function ($doctor) {
                return [
                    'id' => $doctor->id,
                    'name' => $doctor->name,
                    'title' => $doctor->title,
                    'full_name' => $doctor->full_name,
                    'duration' => $doctor->consultation_duration,
                ];
            });

        return response()->json($doctors);
    }

    /**
     * Caută locuri disponibile (AJAX).
     */
    public function searchAvailableSlots(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date_from' => 'required|date|after_or_equal:today',
            'date_to' => 'required|date|after_or_equal:date_from',
            'appointment_type_id' => 'required|exists:appointment_types,id',
            'department_id' => 'required|exists:departments,id',
            'doctor_id' => 'nullable|exists:doctors,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $startDate = Carbon::parse($request->date_from);
        $endDate = Carbon::parse($request->date_to);

        // If doctor is specified, use only that doctor
        if ($request->doctor_id) {
            $doctors = Doctor::with(['activeSchedules', 'holidays'])->where('id', $request->doctor_id)->get();
        } else {
            // Get all doctors from the selected department (via pivot or primary)
            $doctors = Doctor::with(['activeSchedules', 'holidays'])
                ->byDepartment($request->department_id)
                ->where('is_active', true)
                ->get();
        }

        $allSlots = [];
        foreach ($doctors as $doctor) {
            $doctorSlots = $this->generateAvailableSlots($doctor, $startDate, $endDate);
            $allSlots = array_merge($allSlots, $doctorSlots);
        }

        // Sort slots by date and time
        usort($allSlots, function($a, $b) {
            if ($a['date'] === $b['date']) {
                return strcmp($a['time'], $b['time']);
            }
            return strcmp($a['date'], $b['date']);
        });

        return response()->json([
            'success' => true,
            'slots' => $allSlots
        ]);
    }

    /**
     * Generează sloturi disponibile pentru doctor în perioada dată.
     */
    private function generateAvailableSlots(Doctor $doctor, Carbon $startDate, Carbon $endDate): array
    {
        $slots = [];
        $currentDate = $startDate->copy();
        $consultationDuration = $doctor->consultation_duration;

        // Zilele săptămânii în română
        $daysRo = [
            'Monday' => 'Luni',
            'Tuesday' => 'Marți',
            'Wednesday' => 'Miercuri',
            'Thursday' => 'Joi',
            'Friday' => 'Vineri',
            'Saturday' => 'Sâmbătă',
            'Sunday' => 'Duminică',
        ];

        // Mapare zile pentru DB
        $dayMap = [
            'Monday' => 'monday',
            'Tuesday' => 'tuesday',
            'Wednesday' => 'wednesday',
            'Thursday' => 'thursday',
            'Friday' => 'friday',
            'Saturday' => 'saturday',
            'Sunday' => 'sunday',
        ];

        while ($currentDate->lte($endDate)) {
            $dayOfWeek = $currentDate->format('l');
            $dayOfWeekDb = $dayMap[$dayOfWeek];

            // Verifică dacă această zi este zi liberă/concediu pentru doctor
            $isHoliday = $doctor->holidays->contains(function ($holiday) use ($currentDate) {
                return Carbon::parse($holiday->holiday_date)->isSameDay($currentDate);
            });

            // Skip this date if it's a holiday
            if ($isHoliday) {
                $currentDate->addDay();
                continue;
            }

            // Verifică dacă doctorul lucrează în acea zi
            $schedule = $doctor->activeSchedules->firstWhere('day_of_week', $dayOfWeekDb);

            if ($schedule) {
                // Extrage doar ora din timestamp
                $startTimeOnly = Carbon::parse($schedule->start_time)->format('H:i:s');
                $endTimeOnly = Carbon::parse($schedule->end_time)->format('H:i:s');

                $startTime = Carbon::parse($currentDate->format('Y-m-d') . ' ' . $startTimeOnly);
                $endTime = Carbon::parse($currentDate->format('Y-m-d') . ' ' . $endTimeOnly);

                // Generează sloturi la fiecare consultationDuration minute
                $slotTime = $startTime->copy();

                while ($slotTime->copy()->addMinutes($consultationDuration)->lte($endTime)) {
                    // Verifică dacă slotul este disponibil (nu există programare confirmată)
                    $isBooked = Appointment::where('doctor_id', $doctor->id)
                        ->where('appointment_date', $currentDate->format('Y-m-d'))
                        ->where('appointment_time', $slotTime->format('H:i:s'))
                        ->where('status', 'confirmed')
                        ->exists();

                    if (!$isBooked) {
                        $slots[] = [
                            'date' => $currentDate->format('Y-m-d'),
                            'day_of_week' => $dayOfWeekDb,
                            'time' => $slotTime->format('H:i'),
                            'department' => $doctor->department->name,
                            'department_id' => $doctor->department_id,
                            'doctor' => $doctor->full_name,
                            'doctor_id' => $doctor->id,
                        ];
                    }

                    $slotTime->addMinutes($consultationDuration);
                }
            }

            $currentDate->addDay();
        }

        return $slots;
    }

    /**
     * Confirmă programarea (AJAX).
     */
    public function confirmAppointment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'appointment_type_id' => 'required|exists:appointment_types,id',
            'department_id' => 'required|exists:departments,id',
            'doctor_id' => 'required|exists:doctors,id',
            'client_name' => 'required|string|max:255',
            'client_email' => 'required|email|max:255',
            'client_phone' => 'required|string|max:20',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
            'gdpr_consent' => 'required|accepted',
        ], [
            'gdpr_consent.accepted' => 'Trebuie să fiți de acord cu prelucrarea datelor personale.',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        // Verifică din nou disponibilitatea (pentru a evita race conditions)
        $isBooked = Appointment::where('doctor_id', $request->doctor_id)
            ->where('appointment_date', $request->appointment_date)
            ->where('appointment_time', $request->appointment_time)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($isBooked) {
            return response()->json(['error' => 'Acest slot a fost deja rezervat. Vă rugăm selectați alt slot.'], 422);
        }

        // Creează programarea (automat confirmată)
        $appointment = Appointment::create([
            'appointment_type_id' => $request->appointment_type_id,
            'department_id' => $request->department_id,
            'doctor_id' => $request->doctor_id,
            'client_name' => $request->client_name,
            'client_email' => $request->client_email,
            'client_phone' => $request->client_phone,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'status' => 'confirmed',
            'notes' => $request->notes,
        ]);

        // Încarcă relațiile pentru emailuri
        $appointment->load(['appointmentType', 'department', 'doctor']);

        // Trimite email de confirmare către client
        try {
            Mail::to($appointment->client_email)->send(new ClientConfirmation($appointment));
        } catch (\Exception $e) {
            \Log::error('Email client failed: ' . $e->getMessage());
        }

        // Trimite email de notificare către staff (doctor și admin)
        try {
            // Email către doctor
            if ($appointment->doctor->email) {
                Mail::to($appointment->doctor->email)->send(new StaffNotification($appointment));
            }

            // Email către admin (ia primul admin activ)
            $admin = \App\Models\User::where('role', 'admin')->where('is_active', true)->first();
            if ($admin) {
                Mail::to($admin->email)->send(new StaffNotification($appointment));
            }
        } catch (\Exception $e) {
            \Log::error('Email staff failed: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Programarea a fost confirmată cu succes! Veți primi un email de confirmare.',
            'appointment_id' => $appointment->id,
        ]);
    }

    /**
     * Afișează pagina de succes după programare.
     */
    public function success(Request $request)
    {
        return view('booking.success', [
            'date' => $request->query('date'),
            'time' => $request->query('time'),
            'doctor' => $request->query('doctor'),
            'department' => $request->query('department'),
        ]);
    }
}
