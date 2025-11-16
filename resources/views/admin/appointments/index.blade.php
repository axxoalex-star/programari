@extends('layouts.admin')

@section('title', 'Programări')

@section('content')
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1>Programări</h1>
            <p>Gestionează programările clienților</p>
        </div>
        <a href="{{ route('admin.appointments.create') }}" class="btn btn-primary">+ Adaugă Programare</a>
    </div>
</div>

<!-- Filtre -->
<div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 25px;">
    <form method="GET" action="{{ route('admin.appointments.index') }}">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; align-items: end;">
            <div class="form-group" style="margin: 0;">
                <label for="department_id" style="font-size: 13px;">Departament</label>
                <select id="department_id" name="department_id">
                    <option value="">Toate</option>
                    @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                        {{ $dept->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="margin: 0;">
                <label for="doctor_id" style="font-size: 13px;">Doctor</label>
                <select id="doctor_id" name="doctor_id">
                    <option value="">Toți</option>
                    @foreach($doctors as $doctor)
                    <option value="{{ $doctor->id }}" {{ request('doctor_id') == $doctor->id ? 'selected' : '' }}>
                        {{ $doctor->full_name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="margin: 0;">
                <label for="status" style="font-size: 13px;">Status</label>
                <select id="status" name="status">
                    <option value="">Toate</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmat</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Anulat</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Finalizat</option>
                </select>
            </div>

            <div class="form-group" style="margin: 0;">
                <label for="date" style="font-size: 13px;">Data</label>
                <input type="date" id="date" name="date" value="{{ request('date') }}">
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary">Filtrează</button>
                <a href="{{ route('admin.appointments.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </div>
    </form>
</div>

@if($appointments->count() > 0)
    <table>
        <thead>
            <tr>
                <th>Client</th>
                <th>Departament</th>
                <th>Doctor</th>
                <th>Tip</th>
                <th>Data</th>
                <th>Ora</th>
                <th>Status</th>
                <th>Acțiuni</th>
            </tr>
        </thead>
        <tbody>
            @foreach($appointments as $appointment)
            <tr>
                <td>
                    <strong>{{ $appointment->client_name }}</strong><br>
                    <small style="color: #666;">{{ $appointment->client_email }}</small><br>
                    <small style="color: #666;">{{ $appointment->client_phone }}</small>
                </td>
                <td>{{ $appointment->department->name }}</td>
                <td>{{ $appointment->doctor->full_name }}</td>
                <td>{{ $appointment->appointmentType->name }}</td>
                <td>{{ $appointment->appointment_date->format('d.m.Y') }}</td>
                <td><strong>{{ $appointment->appointment_time }}</strong></td>
                <td>
                    @if($appointment->status === 'confirmed')
                        <span style="background: #28a745; color: #fff; padding: 4px 10px; border-radius: 20px; font-size: 12px;">Confirmat</span>
                    @elseif($appointment->status === 'cancelled')
                        <span style="background: #dc3545; color: #fff; padding: 4px 10px; border-radius: 20px; font-size: 12px;">Anulat</span>
                    @else
                        <span style="background: #6c757d; color: #fff; padding: 4px 10px; border-radius: 20px; font-size: 12px;">Finalizat</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('admin.appointments.edit', $appointment) }}" class="btn btn-sm btn-primary">Editează</a>
                    <form method="POST" action="{{ route('admin.appointments.destroy', $appointment) }}" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Sigur vrei să ștergi această programare?')">Șterge</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        {{ $appointments->links() }}
    </div>
@else
    <div style="text-align: center; padding: 60px 20px; color: #999;">
        <p>Nu există programări {{ request()->hasAny(['department_id', 'doctor_id', 'status', 'date']) ? 'pentru filtrele selectate' : 'create încă' }}.</p>
    </div>
@endif
@endsection
