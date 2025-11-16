@extends('layouts.doctor')

@section('title', 'Editează Programare')

@section('content')
<div class="page-header">
    <h1>Editează Programare</h1>
    <p>Actualizează detaliile programării</p>
</div>

<form method="POST" action="{{ route('doctor.appointments.update', $appointment) }}" style="max-width: 700px;">
    @csrf
    @method('PUT')

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
        <div class="form-group">
            <label>Data *</label>
            <input type="date" name="appointment_date" value="{{ old('appointment_date', optional($appointment->appointment_date)->format('Y-m-d')) }}" required>
            @error('appointment_date')<span style="color:#dc3545; font-size:13px;">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
            <label>Ora *</label>
            <input type="time" name="appointment_time" value="{{ old('appointment_time', $appointment->appointment_time) }}" required>
            @error('appointment_time')<span style="color:#dc3545; font-size:13px;">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label>Status *</label>
            <select name="status" required>
                @php($statuses = ['pending' => 'În așteptare', 'confirmed' => 'Confirmat', 'cancelled' => 'Anulat', 'completed' => 'Finalizat'])
                @foreach($statuses as $val=>$label)
                    <option value="{{ $val }}" {{ old('status', $appointment->status) === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            @error('status')<span style="color:#dc3545; font-size:13px;">{{ $message }}</span>@enderror
        </div>
        <div class="form-group" style="grid-column: span 2;">
            <label>Notițe</label>
            <textarea name="notes" rows="4">{{ old('notes', $appointment->notes) }}</textarea>
            @error('notes')<span style="color:#dc3545; font-size:13px;">{{ $message }}</span>@enderror
        </div>
    </div>

    <div style="display:flex; gap:10px; margin-top:20px;">
        <button class="btn btn-primary" type="submit">Salvează</button>
        <a href="{{ route('doctor.appointments') }}" class="btn btn-secondary">Înapoi</a>
    </div>
</form>
@endsection
