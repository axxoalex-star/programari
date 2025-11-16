<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AppointmentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ReceptieController extends Controller
{
    /**
     * Display a listing of reception accounts.
     */
    public function index()
    {
        $users = User::with('appointmentType')
            ->where('role', 'receptie')
            ->orderBy('name')
            ->get();

        return view('admin.receptie.index', compact('users'));
    }

    /**
     * Show the form for creating a new reception account.
     */
    public function create()
    {
        $appointmentTypes = AppointmentType::active()->ordered()->get();
        return view('admin.receptie.create', compact('appointmentTypes'));
    }

    /**
     * Store a newly created reception account.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'appointment_type_id' => 'required|exists:appointment_types,id',
            'is_active' => 'boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['role'] = 'receptie';
        $validated['is_active'] = $request->has('is_active');

        User::create($validated);

        return redirect()->route('admin.receptie.index')
            ->with('success', 'Contul de recepție a fost creat cu succes!');
    }

    /**
     * Show the form for editing a reception account.
     */
    public function edit(User $receptie)
    {
        // Verify this is actually a reception user
        if ($receptie->role !== 'receptie') {
            return redirect()->route('admin.receptie.index')
                ->with('error', 'Contul specificat nu este un cont de recepție!');
        }

        $appointmentTypes = AppointmentType::active()->ordered()->get();
        return view('admin.receptie.edit', compact('receptie', 'appointmentTypes'));
    }

    /**
     * Update a reception account.
     */
    public function update(Request $request, User $receptie)
    {
        // Verify this is actually a reception user
        if ($receptie->role !== 'receptie') {
            return redirect()->route('admin.receptie.index')
                ->with('error', 'Contul specificat nu este un cont de recepție!');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $receptie->id,
            'password' => 'nullable|string|min:6|confirmed',
            'appointment_type_id' => 'required|exists:appointment_types,id',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        // Only update password if provided
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $receptie->update($validated);

        return redirect()->route('admin.receptie.index')
            ->with('success', 'Contul de recepție a fost actualizat cu succes!');
    }

    /**
     * Delete a reception account.
     */
    public function destroy(User $receptie)
    {
        // Verify this is actually a reception user
        if ($receptie->role !== 'receptie') {
            return redirect()->route('admin.receptie.index')
                ->with('error', 'Contul specificat nu este un cont de recepție!');
        }

        $receptie->delete();

        return redirect()->route('admin.receptie.index')
            ->with('success', 'Contul de recepție a fost șters cu succes!');
    }
}
