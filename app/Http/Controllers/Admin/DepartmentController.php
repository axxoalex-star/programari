<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::orderBy('order')->get();
        return view('admin.departments.index', compact('departments'));
    }

    public function create()
    {
        return view('admin.departments.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:7',
            'icon' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        // Auto-assign order as last within the same clinic if appointment_type_id present
        if ($request->filled('appointment_type_id')) {
            $validated['appointment_type_id'] = (int) $request->appointment_type_id;
            $maxOrder = Department::where('appointment_type_id', $validated['appointment_type_id'])->max('order');
            $validated['order'] = ($maxOrder ?? 0) + 1;
        } else {
            $maxOrder = Department::max('order');
            $validated['order'] = ($maxOrder ?? 0) + 1;
        }

        Department::create($validated);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Specialitatea a fost creată cu succes!');
    }

    public function edit(Department $department)
    {
        return view('admin.departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:7',
            'icon' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        // Do not modify order here; use moveUp/moveDown actions
        unset($validated['order']);

        $department->update($validated);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Specialitatea a fost actualizată cu succes!');
    }

    public function destroy(Department $department)
    {
        // Check if department has doctors
        if ($department->doctors()->count() > 0) {
            return redirect()->route('admin.departments.index')
                ->with('error', 'Nu se poate șterge specialitatea deoarece are doctori asociați!');
        }

        $department->delete();

        return redirect()->route('admin.departments.index')
            ->with('success', 'Specialitatea a fost ștearsă cu succes!');
    }

    public function moveUp(Department $department)
    {
        $prev = Department::where('appointment_type_id', $department->appointment_type_id)
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
        $this->normalizeOrders($department->appointment_type_id);
        return redirect()->route('admin.departments.index');
    }

    public function moveDown(Department $department)
    {
        $next = Department::where('appointment_type_id', $department->appointment_type_id)
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
        $this->normalizeOrders($department->appointment_type_id);
        return redirect()->route('admin.departments.index');
    }

    private function normalizeOrders(?int $appointmentTypeId = null): void
    {
        $query = Department::query();
        if ($appointmentTypeId) {
            $query->where('appointment_type_id', $appointmentTypeId);
        }
        $items = $query->orderBy('appointment_type_id')->orderBy('order')->orderBy('id')->get();
        $currentType = null;
        $order = 1;
        foreach ($items as $item) {
            if ($currentType !== $item->appointment_type_id) {
                $currentType = $item->appointment_type_id;
                $order = 1;
            }
            if ($item->order !== $order) {
                $item->order = $order;
                $item->save();
            }
            $order++;
        }
    }
}
