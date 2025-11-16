<?php

namespace App\Http\Controllers\Receptie;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Models\Department;
use App\Models\Doctor;

class DepartmentController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user || !$user->isReceptie() || !$user->appointment_type_id) {
            abort(403, 'Acces interzis');
        }

        // Ensure unique sequential order within this clinic
        $this->normalizeOrders($user->appointment_type_id);

        $departments = Department::where('appointment_type_id', $user->appointment_type_id)
            ->orderBy('order')
            ->get();

        return view('receptie.departments.index', compact('departments'));
    }

    public function create()
    {
        $user = Auth::user();
        if (!$user || !$user->isReceptie() || !$user->appointment_type_id) {
            abort(403, 'Acces interzis');
        }

        // Same icon options used in admin create
        $iconOptions = [
            ['emoji' => '❤️', 'label' => 'Cardiologie'],
            ['emoji' => '🫁', 'label' => 'Pneumologie'],
            ['emoji' => '🦴', 'label' => 'Ortopedie'],
            ['emoji' => '🧠', 'label' => 'Neurologie'],
            ['emoji' => '👁️', 'label' => 'Oftalmologie'],
            ['emoji' => '👂', 'label' => 'ORL'],
            ['emoji' => '🧪', 'label' => 'Laborator'],
            ['emoji' => '👶', 'label' => 'Pediatrie'],
            ['emoji' => '🤰', 'label' => 'Ginecologie'],
            ['emoji' => '⚕️', 'label' => 'Medicină Internă'],
        ];

        return view('receptie.departments.create', compact('iconOptions'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->isReceptie() || !$user->appointment_type_id) {
            abort(403, 'Acces interzis');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['appointment_type_id'] = $user->appointment_type_id;
        // Auto-assign order to last within clinic
        $maxOrder = Department::where('appointment_type_id', $user->appointment_type_id)->max('order');
        $validated['order'] = ($maxOrder ?? 0) + 1;

        Department::create($validated);

        // Re-sequence after insert
        $this->normalizeOrders($user->appointment_type_id);

        return redirect()->route('receptie.departments.index')
            ->with('success', 'Specialitatea a fost creată cu succes!');
    }

    public function edit(Department $department)
    {
        $user = Auth::user();
        if (!$user || !$user->isReceptie() || !$user->appointment_type_id) {
            abort(403, 'Acces interzis');
        }
        if ($department->appointment_type_id !== $user->appointment_type_id) {
            abort(403, 'Acces interzis');
        }

        $iconOptions = [
            ['emoji' => '❤️', 'label' => 'Cardiologie'],
            ['emoji' => '🫁', 'label' => 'Pneumologie'],
            ['emoji' => '🦴', 'label' => 'Ortopedie'],
            ['emoji' => '🧠', 'label' => 'Neurologie'],
            ['emoji' => '👁️', 'label' => 'Oftalmologie'],
            ['emoji' => '👂', 'label' => 'ORL'],
            ['emoji' => '🧪', 'label' => 'Laborator'],
            ['emoji' => '👶', 'label' => 'Pediatrie'],
            ['emoji' => '🤰', 'label' => 'Ginecologie'],
            ['emoji' => '⚕️', 'label' => 'Medicină Internă'],
        ];

        return view('receptie.departments.edit', compact('department', 'iconOptions'));
    }

    public function update(Request $request, Department $department)
    {
        $user = Auth::user();
        if (!$user || !$user->isReceptie() || !$user->appointment_type_id) {
            abort(403, 'Acces interzis');
        }
        if ($department->appointment_type_id !== $user->appointment_type_id) {
            abort(403, 'Acces interzis');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        // Do not change order here (reordered via moveUp/moveDown)
        unset($validated['order']);

        $department->update($validated);

        return redirect()->route('receptie.departments.index')
            ->with('success', 'Specialitatea a fost actualizată cu succes!');
    }

    public function destroy(Department $department)
    {
        $user = Auth::user();
        if (!$user || !$user->isReceptie() || !$user->appointment_type_id) {
            abort(403, 'Acces interzis');
        }
        if ($department->appointment_type_id !== $user->appointment_type_id) {
            abort(403, 'Acces interzis');
        }

        // Prevent delete if has doctors in this clinic
        $hasDoctorsQuery = Doctor::where('appointment_type_id', $user->appointment_type_id)
            ->where(function ($q) use ($department) {
                $q->where('department_id', $department->id);
            });

        // Only check pivot if table exists (guards before migration run)
        if (Schema::hasTable('doctor_department')) {
            $hasDoctorsQuery->orWhereHas('departments', function ($qq) use ($department) {
                $qq->where('departments.id', $department->id);
            });
        }

        $hasDoctors = $hasDoctorsQuery->exists();
        if ($hasDoctors) {
            return redirect()->route('receptie.departments.index')
                ->with('error', 'Nu se poate șterge specialitatea deoarece are doctori asociați în această clinică!');
        }

        $department->delete();

        return redirect()->route('receptie.departments.index')
            ->with('success', 'Specialitatea a fost ștearsă cu succes!');
    }

    public function moveUp(Department $department)
    {
        $user = Auth::user();
        if (!$user || !$user->isReceptie() || !$user->appointment_type_id || $department->appointment_type_id !== $user->appointment_type_id) {
            abort(403, 'Acces interzis');
        }

        $prev = Department::where('appointment_type_id', $user->appointment_type_id)
            ->where('order', '<', $department->order)
            ->orderBy('order', 'desc')
            ->first();
        if ($prev) {
            $tmp = $department->order;
            $department->order = $prev->order;
            $prev->order = $tmp;
            $department->save();
            $prev->save();
        }
        // Fix any duplicates/gaps
        $this->normalizeOrders($user->appointment_type_id);
        return redirect()->route('receptie.departments.index');
    }

    public function moveDown(Department $department)
    {
        $user = Auth::user();
        if (!$user || !$user->isReceptie() || !$user->appointment_type_id || $department->appointment_type_id !== $user->appointment_type_id) {
            abort(403, 'Acces interzis');
        }

        $next = Department::where('appointment_type_id', $user->appointment_type_id)
            ->where('order', '>', $department->order)
            ->orderBy('order', 'asc')
            ->first();
        if ($next) {
            $tmp = $department->order;
            $department->order = $next->order;
            $next->order = $tmp;
            $department->save();
            $next->save();
        }
        // Fix any duplicates/gaps
        $this->normalizeOrders($user->appointment_type_id);
        return redirect()->route('receptie.departments.index');
    }

    private function normalizeOrders(int $appointmentTypeId): void
    {
        $items = Department::where('appointment_type_id', $appointmentTypeId)
            ->orderBy('order')
            ->orderBy('id')
            ->get();
        $order = 1;
        foreach ($items as $item) {
            if ($item->order !== $order) {
                $item->order = $order;
                $item->save();
            }
            $order++;
        }
    }
}
