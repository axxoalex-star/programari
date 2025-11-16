<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MedicalRecord;
use App\Models\Appointment;

class MedicalRecordController extends Controller
{
    public function index(string $email)
    {
        $user = Auth::user();
        if ($user->role !== 'doctor' || !$user->doctor_id) {
            abort(403, 'Acces interzis');
        }

        $records = MedicalRecord::where('doctor_id', $user->doctor_id)
            ->where('client_email', $email)
            ->latest()
            ->paginate(15);

        return view('doctor.records.index', [
            'email' => $email,
            'records' => $records,
        ]);
    }

    public function create(string $email)
    {
        $user = Auth::user();
        if ($user->role !== 'doctor' || !$user->doctor_id) {
            abort(403, 'Acces interzis');
        }

        return view('doctor.records.create', [
            'email' => $email,
        ]);
    }

    public function store(Request $request, string $email)
    {
        $user = Auth::user();
        if ($user->role !== 'doctor' || !$user->doctor_id) {
            abort(403, 'Acces interzis');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'appointment_id' => 'nullable|exists:appointments,id',
            'client_name' => 'nullable|string|max:255',
            'client_phone' => 'nullable|string|max:50',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $path = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = 'record_' . $user->doctor_id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/records'), $filename);
            $path = 'uploads/records/' . $filename;
        }

        MedicalRecord::create([
            'doctor_id' => $user->doctor_id,
            'appointment_id' => $validated['appointment_id'] ?? null,
            'client_email' => $email,
            'client_name' => $validated['client_name'] ?? $request->input('client_name'),
            'client_phone' => $validated['client_phone'] ?? $request->input('client_phone'),
            'title' => $validated['title'],
            'notes' => $validated['notes'] ?? null,
            'attachment_path' => $path,
        ]);

        return redirect()->route('patients.records.index', ['email' => $email])
            ->with('success', 'Nota medicală a fost creată.');
    }

    public function edit(MedicalRecord $record)
    {
        $user = Auth::user();
        if ($user->role !== 'doctor' || !$user->doctor_id || $record->doctor_id !== $user->doctor_id) {
            abort(403, 'Acces interzis');
        }
        return view('doctor.records.edit', compact('record'));
    }

    public function update(Request $request, MedicalRecord $record)
    {
        $user = Auth::user();
        if ($user->role !== 'doctor' || !$user->doctor_id || $record->doctor_id !== $user->doctor_id) {
            abort(403, 'Acces interzis');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('attachment')) {
            if ($record->attachment_path && file_exists(public_path($record->attachment_path))) {
                @unlink(public_path($record->attachment_path));
            }
            $file = $request->file('attachment');
            $filename = 'record_' . $user->doctor_id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/records'), $filename);
            $record->attachment_path = 'uploads/records/' . $filename;
        }

        $record->title = $validated['title'];
        $record->notes = $validated['notes'] ?? null;
        $record->save();

        return redirect()->route('patients.records.index', ['email' => $record->client_email])
            ->with('success', 'Nota medicală a fost actualizată.');
    }

    public function destroy(MedicalRecord $record)
    {
        $user = Auth::user();
        if ($user->role !== 'doctor' || !$user->doctor_id || $record->doctor_id !== $user->doctor_id) {
            abort(403, 'Acces interzis');
        }

        if ($record->attachment_path && file_exists(public_path($record->attachment_path))) {
            @unlink(public_path($record->attachment_path));
        }
        $email = $record->client_email;
        $record->delete();

        return redirect()->route('patients.records.index', ['email' => $email])
            ->with('success', 'Nota medicală a fost ștearsă.');
    }

    public function downloadAttachment(MedicalRecord $record)
    {
        $user = Auth::user();
        if ($user->role !== 'doctor' || !$user->doctor_id || $record->doctor_id !== $user->doctor_id) {
            abort(403, 'Acces interzis');
        }
        if (!$record->attachment_path || !file_exists(public_path($record->attachment_path))) {
            abort(404, 'Atașamentul nu a fost găsit');
        }
        return response()->download(public_path($record->attachment_path));
    }
}
