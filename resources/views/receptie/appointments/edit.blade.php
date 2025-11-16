@extends('layouts.admin')

@section('title', 'Editează Programare - Recepție')

@section('content')
<div class="page-header">
    <h1>Editează Programare</h1>
    <p>Modifică detaliile programării selectate</p>
</div>

<form method="POST" action="{{ route('receptie.appointments.update', $appointment) }}" style="max-width:900px;">
    @csrf
    @method('PUT')

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
        <div class="form-group">
            <label for="department_id">Specialitate *</label>
            <select id="department_id" name="department_id" required>
                @foreach($departments as $dep)
                    <option value="{{ $dep->id }}" {{ old('department_id', $appointment->department_id) == $dep->id ? 'selected' : '' }}>{{ $dep->name }}</option>
                @endforeach
            </select>
            @error('department_id')<span style="color:#dc3545; font-size:13px;">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
            <label for="doctor_id">Doctor *</label>
            <select id="doctor_id" name="doctor_id" required>
                @foreach($doctors as $doc)
                    @php $docName = trim(($doc->title? $doc->title.' ' : '').$doc->name); @endphp
                    <option value="{{ $doc->id }}" {{ old('doctor_id', $appointment->doctor_id) == $doc->id ? 'selected' : '' }}>{{ $docName }}</option>
                @endforeach
            </select>
            @error('doctor_id')<span style="color:#dc3545; font-size:13px;">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label for="client_name">Nume pacient *</label>
            <input type="text" id="client_name" name="client_name" value="{{ old('client_name', $appointment->client_name) }}" required>
            @error('client_name')<span style="color:#dc3545; font-size:13px;">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
            <label for="client_email">Email pacient *</label>
            <input type="email" id="client_email" name="client_email" value="{{ old('client_email', $appointment->client_email) }}" required>
            @error('client_email')<span style="color:#dc3545; font-size:13px;">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label for="client_phone">Telefon pacient *</label>
            <input type="text" id="client_phone" name="client_phone" value="{{ old('client_phone', $appointment->client_phone) }}" required>
            @error('client_phone')<span style="color:#dc3545; font-size:13px;">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
            <label for="appointment_date">Data *</label>
            <input type="date" id="appointment_date" name="appointment_date" value="{{ old('appointment_date', \Carbon\Carbon::parse($appointment->appointment_date)->format('Y-m-d')) }}" required>
            @error('appointment_date')<span style="color:#dc3545; font-size:13px;">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label for="appointment_time">Ora *</label>
            <input type="time" id="appointment_time" name="appointment_time" value="{{ old('appointment_time', \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i')) }}" required>
            @error('appointment_time')<span style="color:#dc3545; font-size:13px;">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
            <label for="status">Status *</label>
            <select id="status" name="status" required>
                <option value="confirmed" {{ old('status', $appointment->status) == 'confirmed' ? 'selected' : '' }}>Confirmată</option>
                <option value="cancelled" {{ old('status', $appointment->status) == 'cancelled' ? 'selected' : '' }}>Anulată</option>
                <option value="completed" {{ old('status', $appointment->status) == 'completed' ? 'selected' : '' }}>Finalizată</option>
            </select>
            @error('status')<span style="color:#dc3545; font-size:13px;">{{ $message }}</span>@enderror
        </div>

        <div class="form-group" style="grid-column: span 2;">
            <label for="notes">Note</label>
            <textarea id="notes" name="notes" rows="4">{{ old('notes', $appointment->notes) }}</textarea>
            @error('notes')<span style="color:#dc3545; font-size:13px;">{{ $message }}</span>@enderror
        </div>
    </div>

    <div style="display:flex; gap:10px; margin-top:20px;">
        <button type="submit" class="btn btn-primary">Salvează</button>
        <a href="{{ route('receptie.appointments') }}" class="btn btn-secondary">Anulează</a>
    </div>
</form>
@endsection
