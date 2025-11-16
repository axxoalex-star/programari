<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Department;
use App\Models\Doctor;

class DashboardController extends Controller
{
    public function index()
    {
        $upcomingAppointments = Appointment::with(['department', 'doctor', 'appointmentType'])
            ->upcoming()
            ->take(10)
            ->get();

        $totalAppointments = Appointment::count();
        $upcomingCount = Appointment::upcoming()->count();
        $totalDepartments = Department::active()->count();
        $totalDoctors = Doctor::active()->count();

        return view('admin.dashboard', compact(
            'upcomingAppointments',
            'totalAppointments',
            'upcomingCount',
            'totalDepartments',
            'totalDoctors'
        ));
    }
}
