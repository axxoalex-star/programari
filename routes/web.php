<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Admin\AppointmentTypeController;
use App\Http\Controllers\Admin\ReceptieController;
use App\Http\Controllers\Admin\AppointmentController as AdminAppointmentController;
use App\Http\Controllers\Doctor\DashboardController as DoctorDashboardController;
use App\Http\Controllers\Doctor\MedicalRecordController as DoctorMedicalRecordController;
use App\Http\Controllers\Doctor\PrintController as DoctorPrintController;
use App\Http\Controllers\Receptie\DashboardController as ReceptieDashboardController;
use App\Http\Controllers\Receptie\DoctorController as ReceptieDoctorController;
use App\Http\Controllers\Receptie\DepartmentController as ReceptieDepartmentController;

// ====================================
// RUTE PUBLICE (Frontend - Programări)
// ====================================
Route::get('/', [BookingController::class, 'index'])->name('home');

// AJAX routes pentru formularul de programare
Route::get('/booking/departments', [BookingController::class, 'getDepartments'])->name('booking.departments');
Route::get('/booking/doctors', [BookingController::class, 'getDoctors'])->name('booking.doctors');
Route::get('/booking/search-slots', [BookingController::class, 'searchAvailableSlots'])->name('booking.search-slots');
Route::post('/booking/confirm', [BookingController::class, 'confirmAppointment'])->name('booking.confirm');
Route::get('/booking/success', [BookingController::class, 'success'])->name('booking.success');

// ====================================
// RUTE AUTENTIFICARE
// ====================================
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ====================================
// RUTE ADMIN (protejate prin middleware auth + admin role)
// ====================================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // CRUD Departamente
    Route::resource('departments', DepartmentController::class);

    // CRUD Doctori
    Route::resource('doctors', DoctorController::class);

    // CRUD Tipuri Programări
    Route::resource('appointment-types', AppointmentTypeController::class);

    // CRUD Conturi Recepție
    Route::resource('receptie', ReceptieController::class);

    // CRUD Programări
    Route::resource('appointments', AdminAppointmentController::class);

    // Reorder Specializari (Departments)
    Route::post('/departments/{department}/move-up', [DepartmentController::class, 'moveUp'])->name('departments.move-up');
    Route::post('/departments/{department}/move-down', [DepartmentController::class, 'moveDown'])->name('departments.move-down');
});

// ====================================
// RUTE DOCTOR (protejate prin middleware auth + doctor role)
// ====================================
Route::middleware(['auth'])->prefix('doctor')->name('doctor.')->group(function () {
    Route::get('/dashboard', [DoctorDashboardController::class, 'index'])->name('dashboard');
    Route::get('/appointments', [DoctorDashboardController::class, 'appointments'])->name('appointments');
    Route::get('/appointments/{appointment}/edit', [DoctorDashboardController::class, 'editAppointment'])->name('appointments.edit');
    Route::put('/appointments/{appointment}', [DoctorDashboardController::class, 'updateAppointment'])->name('appointments.update');
    Route::patch('/appointments/{appointment}/status', [DoctorDashboardController::class, 'updateStatus'])->name('appointments.update-status');
    Route::patch('/appointments/{appointment}/notes', [DoctorDashboardController::class, 'updateNotes'])->name('appointments.update-notes');
    Route::get('/patients', [DoctorDashboardController::class, 'patients'])->name('patients');
    Route::get('/patients/{email}', [DoctorDashboardController::class, 'patientHistory'])->name('patients.history');
    Route::get('/profile/edit', [DoctorDashboardController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profile', [DoctorDashboardController::class, 'updateProfile'])->name('profile.update');
    Route::post('/notifications/send-next-day-email', [DoctorDashboardController::class, 'sendNextDayEmail'])->name('notifications.send-next-day-email');

    // Medical Records (Istoric medical)
    Route::get('/patients/{email}/records', [DoctorMedicalRecordController::class, 'index'])->name('patients.records.index');
    Route::get('/patients/{email}/records/create', [DoctorMedicalRecordController::class, 'create'])->name('patients.records.create');
    Route::post('/patients/{email}/records', [DoctorMedicalRecordController::class, 'store'])->name('patients.records.store');
    Route::get('/records/{record}/edit', [DoctorMedicalRecordController::class, 'edit'])->name('records.edit');
    Route::put('/records/{record}', [DoctorMedicalRecordController::class, 'update'])->name('records.update');
    Route::delete('/records/{record}', [DoctorMedicalRecordController::class, 'destroy'])->name('records.destroy');
    Route::get('/records/{record}/download', [DoctorMedicalRecordController::class, 'downloadAttachment'])->name('records.download');

    // Print routes (HTML now, PDF can be added later)
    Route::get('/patients/{email}/print/result', [DoctorPrintController::class, 'result'])->name('patients.print.result');
    Route::get('/patients/{email}/print/form/{template}', [DoctorPrintController::class, 'form'])->name('patients.print.form');
});

// ====================================
// RUTE ASISTENTĂ (protejate prin middleware auth + assistant role)
// ====================================
Route::middleware(['auth'])->prefix('assistant')->name('assistant.')->group(function () {
    // TODO: Adăugat mai târziu
    // Route::get('/dashboard', [AssistantDashboardController::class, 'index'])->name('dashboard');
    // Route::get('/appointments', [AssistantAppointmentController::class, 'index'])->name('appointments');
});

// ====================================
// RUTE RECEPȚIE (protejate prin middleware auth + receptie role)
// ====================================
Route::middleware(['auth'])->prefix('receptie')->name('receptie.')->group(function () {
    Route::get('/dashboard', [ReceptieDashboardController::class, 'index'])->name('dashboard');
    Route::get('/appointments', [ReceptieDashboardController::class, 'appointments'])->name('appointments');
    Route::get('/appointments/{appointment}/edit', [ReceptieDashboardController::class, 'editAppointment'])->name('appointments.edit');
    Route::put('/appointments/{appointment}', [ReceptieDashboardController::class, 'updateAppointment'])->name('appointments.update');
    Route::delete('/appointments/{appointment}', [ReceptieDashboardController::class, 'destroyAppointment'])->name('appointments.destroy');

    // Clinic-scoped Doctors management for Reception
    Route::resource('doctors', ReceptieDoctorController::class);

    // Clinic-scoped Specializari (Departments) management for Reception
    Route::resource('departments', ReceptieDepartmentController::class)->parameters([
        'departments' => 'department'
    ]);
    Route::post('/departments/{department}/move-up', [ReceptieDepartmentController::class, 'moveUp'])->name('departments.move-up');
    Route::post('/departments/{department}/move-down', [ReceptieDepartmentController::class, 'moveDown'])->name('departments.move-down');

    // Patients management for Reception
    Route::get('/patients', [\App\Http\Controllers\Receptie\PatientController::class, 'index'])->name('patients.index');
    Route::get('/patients/{email}', [\App\Http\Controllers\Receptie\PatientController::class, 'show'])->name('patients.show');
});
