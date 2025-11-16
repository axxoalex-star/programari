<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppointmentType;
use Illuminate\Http\Request;

class AppointmentTypeController extends Controller
{
    public function index()
    {
        $types = AppointmentType::orderBy('order')->get();
        return view('admin.appointment-types.index', compact('types'));
    }

    public function create()
    {
        return view('admin.appointment-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'order' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        AppointmentType::create($validated);

        return redirect()->route('admin.appointment-types.index')
            ->with('success', 'Clinica a fost creată cu succes!');
    }

    public function edit(AppointmentType $appointmentType)
    {
        return view('admin.appointment-types.edit', compact('appointmentType'));
    }

    public function update(Request $request, AppointmentType $appointmentType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'order' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $appointmentType->update($validated);

        return redirect()->route('admin.appointment-types.index')
            ->with('success', 'Clinica a fost actualizată cu succes!');
    }

    public function destroy(AppointmentType $appointmentType)
    {
        // Check if type has appointments
        if ($appointmentType->appointments()->count() > 0) {
            return redirect()->route('admin.appointment-types.index')
                ->with('error', 'Nu se poate șterge clinica deoarece are programări asociate!');
        }

        $appointmentType->delete();

        return redirect()->route('admin.appointment-types.index')
            ->with('success', 'Clinica a fost ștearsă cu succes!');
    }
}
