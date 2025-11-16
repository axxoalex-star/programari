@extends('layouts.admin')

@section('title', 'Dashboard Recepție')

@section('content')
<div class="page-header">
    <h1>Dashboard Recepție - {{ auth()->user()->appointmentType->name ?? 'Clinica' }}</h1>
    <p>Vizualizare rapidă a programărilor pentru clinica dumneavoastră</p>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
        <div style="font-size: 14px; opacity: 0.9; margin-bottom: 5px;">Programări Astăzi</div>
        <div style="font-size: 36px; font-weight: 600;">{{ $stats['today_appointments'] }}</div>
    </div>

    <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
        <div style="font-size: 14px; opacity: 0.9; margin-bottom: 5px;">Programări Viitoare</div>
        <div style="font-size: 36px; font-weight: 600;">{{ $stats['upcoming_appointments'] }}</div>
    </div>

    <div style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
        <div style="font-size: 14px; opacity: 0.9; margin-bottom: 5px;">Doctori Activi</div>
        <div style="font-size: 36px; font-weight: 600;">{{ $stats['total_doctors'] }}</div>
    </div>
</div>

@if($todayAppointments->count() > 0)
<div style="margin-bottom: 30px;">
    <h2 style="margin-bottom: 20px; color: #333; font-size: 22px;">📅 Programări Astăzi</h2>
    <table>
        <thead>
            <tr>
                <th>Oră</th>
                <th>Pacient</th>
                <th>Doctor</th>
                <th>Specialitate</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($todayAppointments as $appointment)
            <tr>
                <td><strong>{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}</strong></td>
                <td>{{ $appointment->client_name }}</td>
                <td>{{ $appointment->doctor->title }} {{ $appointment->doctor->name }}</td>
                <td>{{ $appointment->department->name }}</td>
                <td>
                    @if($appointment->status === 'confirmed')
                        <span style="color: #28a745; font-weight: 600;">✓ Confirmată</span>
                    @elseif($appointment->status === 'completed')
                        <span style="color: #007bff; font-weight: 600;">✓ Completată</span>
                    @else
                        <span style="color: #dc3545; font-weight: 600;">✗ Anulată</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
<div style="text-align: center; padding: 40px; background: #f8f9fa; border-radius: 10px; margin-bottom: 30px;">
    <p style="color: #999; font-size: 16px;">Nu există programări pentru astăzi</p>
</div>
@endif

@if($upcomingAppointments->count() > 0 || request()->filled('doctor_id'))
<div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0; color: #333; font-size: 22px;">📆 Programări Următoarele 7 Zile</h2>
        <form method="GET" action="{{ route('receptie.dashboard') }}" id="doctorFilterForm" style="margin: 0;">
            <select name="doctor_id" id="doctorFilter" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;" onchange="this.form.submit()">
                <option value="">Toți doctorii</option>
                @foreach($doctors as $doctor)
                    <option value="{{ $doctor->id }}" {{ $selectedDoctorId == $doctor->id ? 'selected' : '' }}>
                        {{ $doctor->title }} {{ $doctor->name }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    @if($upcomingAppointments->count() > 0)
    <table>
        <thead>
            <tr>
                <th>Dată</th>
                <th>Oră</th>
                <th>Pacient</th>
                <th>Doctor</th>
                <th>Specialitate</th>
                <th>Status</th>
                <th>Acțiuni</th>
            </tr>
        </thead>
        <tbody>
            @foreach($upcomingAppointments as $appointment)
            <tr>
                <td>{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d.m.Y') }}</td>
                <td><strong>{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}</strong></td>
                <td>{{ $appointment->client_name }}</td>
                <td>{{ $appointment->doctor->title }} {{ $appointment->doctor->name }}</td>
                <td>{{ $appointment->department->name }}</td>
                <td>
                    @if($appointment->status === 'confirmed')
                        <span style="color: #28a745; font-weight: 600;">✓ Confirmată</span>
                    @elseif($appointment->status === 'completed')
                        <span style="color: #007bff; font-weight: 600;">✓ Completată</span>
                    @else
                        <span style="color: #dc3545; font-weight: 600;">✗ Anulată</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('receptie.appointments.edit', $appointment) }}" class="btn btn-sm btn-primary">Editează</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div style="margin-top: 20px; text-align: center;">
        <a href="{{ route('receptie.appointments') }}" class="btn btn-primary">Vezi Toate Programările</a>
    </div>
    @else
    <div style="text-align: center; padding: 40px; background: #f8f9fa; border-radius: 10px;">
        <p style="color: #999; font-size: 16px;">
            @if($selectedDoctorId)
                Nu există programări pentru doctorul selectat în următoarele 7 zile
            @else
                Nu există programări în următoarele 7 zile
            @endif
        </p>
    </div>
    @endif
</div>
@endif
@endsection
