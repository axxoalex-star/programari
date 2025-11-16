<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\DoctorNextDaySchedule;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Verifică că utilizatorul este doctor și are doctor_id
        if ($user->role !== 'doctor' || !$user->doctor_id) {
            abort(403, 'Acces interzis');
        }

        $doctorId = $user->doctor_id;
        $doctor = \App\Models\Doctor::find($doctorId);

        // Programări viitoare
        $upcomingAppointments = Appointment::with(['department', 'appointmentType'])
            ->where('doctor_id', $doctorId)
            ->where('status', '!=', 'completed')
            ->upcoming()
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->take(20)
            ->get();

        // Programări astăzi
        $todayAppointments = Appointment::with(['department', 'appointmentType'])
            ->where('doctor_id', $doctorId)
            ->where('status', '!=', 'completed')
            ->whereDate('appointment_date', today())
            ->orderBy('appointment_time')
            ->get();

        // Statistici
        $totalAppointments = Appointment::where('doctor_id', $doctorId)->count();
        $upcomingCount = Appointment::where('doctor_id', $doctorId)
            ->where('status', '!=', 'completed')
            ->upcoming()
            ->count();
        $todayCount = $todayAppointments->count();

        return view('doctor.dashboard', compact(
            'upcomingAppointments',
            'todayAppointments',
            'totalAppointments',
            'upcomingCount',
            'todayCount',
            'doctor'
        ));
    }

    public function appointments(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'doctor' || !$user->doctor_id) {
            abort(403, 'Acces interzis');
        }

        $query = Appointment::with(['department', 'appointmentType'])
            ->where('doctor_id', $user->doctor_id);

        // Filtre
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('appointment_date', $request->date);
        }

        $appointments = $query->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->paginate(20);

        return view('doctor.appointments', compact('appointments'));
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        $user = Auth::user();

        // Verifică că programarea aparține acestui doctor
        if ($appointment->doctor_id !== $user->doctor_id) {
            abort(403, 'Nu poți modifica programările altor doctori');
        }

        $validated = $request->validate([
            'status' => 'required|in:confirmed,cancelled,completed',
            'notes' => 'nullable|string',
        ]);

        $appointment->update($validated);

        return redirect()->back()->with('success', 'Programarea a fost actualizată!');
    }

    public function editAppointment(Appointment $appointment)
    {
        $user = Auth::user();

        // Verifică că programarea aparține acestui doctor
        if ($appointment->doctor_id !== $user->doctor_id) {
            abort(403, 'Nu poți modifica programările altor doctori');
        }

        return view('doctor.appointments-edit', compact('appointment'));
    }

    public function updateAppointment(Request $request, Appointment $appointment)
    {
        $user = Auth::user();

        // Verifică că programarea aparține acestui doctor
        if ($appointment->doctor_id !== $user->doctor_id) {
            abort(403, 'Nu poți modifica programările altor doctori');
        }

        $validated = $request->validate([
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
            'status' => 'required|in:confirmed,cancelled,completed',
            'notes' => 'nullable|string',
        ]);

        // Verifică disponibilitatea slotului dacă s-a schimbat data/ora
        $slotChanged = $appointment->appointment_date != $request->appointment_date ||
                      $appointment->appointment_time != $request->appointment_time;

        if ($slotChanged && $request->status !== 'cancelled') {
            $isBooked = Appointment::where('doctor_id', $user->doctor_id)
                ->where('appointment_date', $request->appointment_date)
                ->where('appointment_time', $request->appointment_time)
                ->where('id', '!=', $appointment->id)
                ->where('status', 'confirmed')
                ->exists();

            if ($isBooked) {
                return back()->withErrors(['appointment_time' => 'Acest slot este deja rezervat.'])->withInput();
            }
        }

        $appointment->update($validated);

        return redirect()->route('doctor.appointments')->with('success', 'Programarea a fost actualizată cu succes!');
    }

    public function patients()
    {
        $user = Auth::user();

        if ($user->role !== 'doctor' || !$user->doctor_id) {
            abort(403, 'Acces interzis');
        }

        $query = Appointment::where('doctor_id', $user->doctor_id)
            ->select('client_name', 'client_email', 'client_phone')
            ->selectRaw('COUNT(*) as total_appointments')
            ->selectRaw('MAX(appointment_date) as last_appointment')
            ->groupBy('client_email', 'client_name', 'client_phone');

        // Căutare după nume, email, telefon
        $search = request('q');
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

        return view('doctor.patients', compact('patients'));
    }

    public function patientHistory($email)
    {
        $user = Auth::user();

        if ($user->role !== 'doctor' || !$user->doctor_id) {
            abort(403, 'Acces interzis');
        }

        // Obține istoricul pacientului
        $appointments = Appointment::with(['department', 'appointmentType'])
            ->where('doctor_id', $user->doctor_id)
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

        return view('doctor.patient-history', compact('appointments', 'patient'));
    }

    public function updateNotes(Request $request, Appointment $appointment)
    {
        $user = Auth::user();

        if ($user->role !== 'doctor' || !$user->doctor_id) {
            abort(403, 'Acces interzis');
        }

        if ($appointment->doctor_id !== $user->doctor_id) {
            abort(403, 'Nu poți modifica programările altor doctori');
        }

        $validated = $request->validate([
            'notes' => 'nullable|string',
        ]);

        $appointment->update(['notes' => $validated['notes'] ?? null]);

        // Redirect înapoi la istoricul pacientului
        return redirect()
            ->route('doctor.patients.history', $appointment->client_email)
            ->with('success', 'Notițele au fost actualizate.');
    }

    public function editProfile()
    {
        $user = Auth::user();

        if ($user->role !== 'doctor' || !$user->doctor_id) {
            abort(403, 'Acces interzis');
        }

        $doctor = \App\Models\Doctor::with('holidays')->findOrFail($user->doctor_id);

        return view('doctor.profile-edit', compact('doctor', 'user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'doctor' || !$user->doctor_id) {
            abort(403, 'Acces interzis');
        }

        $doctor = \App\Models\Doctor::findOrFail($user->doctor_id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:50',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'consultation_duration' => 'required|integer|min:15|max:180',
            'consultation_price' => 'required|numeric|min:0',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'receive_next_day_email' => 'nullable|boolean',
            'next_day_email_hour' => 'nullable|integer|min:0|max:23',
            'notification_email' => 'nullable|email|max:255',
            // Legal holidays provided as text (comma/newline separated YYYY-MM-DD)
            'holiday_dates' => 'nullable|string',
        ]);

        // Upload photo dacă există
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = 'doctor_' . $doctor->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/doctors'), $filename);
            $validated['photo'] = 'uploads/doctors/' . $filename;

            // Șterge poza veche dacă există
            if ($doctor->photo && file_exists(public_path($doctor->photo))) {
                unlink(public_path($doctor->photo));
            }
        }

        // Map preferences
        $doctor->receive_next_day_email = $request->boolean('receive_next_day_email');
        if ($request->filled('next_day_email_hour')) {
            $doctor->next_day_email_hour = (int) $request->next_day_email_hour;
        }
        if ($request->filled('notification_email')) {
            $doctor->notification_email = $request->notification_email;
        }
        // Handle legal holidays - save to doctor_holidays table
        // Clear existing holidays first
        $doctor->holidays()->delete();

        if ($request->filled('holiday_dates')) {
            $raw = $request->input('holiday_dates');
            // Split by comma (new format from calendar picker)
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

        $doctor->fill($validated);
        $doctor->save();

        // Actualizează și datele user-ului
        $user->update([
            'name' => $request->title . ' ' . $request->name,
            'email' => $request->email,
        ]);

        return redirect()->route('doctor.profile.edit')->with('success', 'Profilul a fost actualizat cu succes!');
    }

    public function sendNextDayEmail(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'doctor' || !$user->doctor_id) {
            abort(403, 'Acces interzis');
        }

        $doctor = \App\Models\Doctor::findOrFail($user->doctor_id);
        $tomorrow = Carbon::tomorrow()->toDateString();

        $appointments = Appointment::with(['appointmentType'])
            ->where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', $tomorrow)
            ->orderBy('appointment_time')
            ->get();

        $to = $doctor->notification_email ?: $doctor->email;

        try {
            Mail::to($to)->send(new DoctorNextDaySchedule($doctor, $appointments, $tomorrow));
        } catch (\Throwable $e) {
            return back()->with('error', 'Nu s-a putut trimite emailul: ' . $e->getMessage());
        }

        return back()->with('success', 'Emailul cu programările de mâine a fost trimis către ' . $to . '!');
    }
}
