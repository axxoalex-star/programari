@extends('layouts.admin')

@section('title', 'Adaugă Programare')

@section('content')
<div class="page-header">
    <h1>Adaugă Programare Nouă</h1>
    <p>Creează o programare manuală</p>
</div>

<form method="POST" action="{{ route('admin.appointments.store') }}" style="max-width: 800px;">
    @csrf

    <h3 style="margin-bottom: 20px; color: #667eea;">Informații Programare</h3>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
        <div class="form-group">
            <label for="department_id">Departament *</label>
            <select id="department_id" name="department_id" required>
                <option value="">Selectează departament</option>
                @foreach($departments as $dept)
                <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                    {{ $dept->name }}
                </option>
                @endforeach
            </select>
            @error('department_id')
                <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="doctor_id">Doctor *</label>
            <select id="doctor_id" name="doctor_id" required>
                <option value="">Selectează doctor</option>
            </select>
            @error('doctor_id')
                <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="form-group">
        <label for="appointment_type_id">Clinica *</label>
        <select id="appointment_type_id" name="appointment_type_id" required>
            <option value="">Selectează clinica</option>
            @foreach($appointmentTypes as $type)
            <option value="{{ $type->id }}" {{ old('appointment_type_id') == $type->id ? 'selected' : '' }}>
                {{ $type->name }}
            </option>
            @endforeach
        </select>
        @error('appointment_type_id')
            <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
        @enderror
    </div>

    <h3 style="margin: 30px 0 20px; color: #667eea; border-top: 2px solid #f0f0f0; padding-top: 30px;">Informații Client</h3>

    <div class="form-group">
        <label for="client_name">Nume Complet *</label>
        <input type="text" id="client_name" name="client_name" value="{{ old('client_name') }}" required>
        @error('client_name')
            <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
        @enderror
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
        <div class="form-group">
            <label for="client_email">Email *</label>
            <input type="email" id="client_email" name="client_email" value="{{ old('client_email') }}" required>
            @error('client_email')
                <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="client_phone">Telefon *</label>
            <input type="tel" id="client_phone" name="client_phone" value="{{ old('client_phone') }}" required>
            @error('client_phone')
                <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <h3 style="margin: 30px 0 20px; color: #667eea; border-top: 2px solid #f0f0f0; padding-top: 30px;">Data și Ora</h3>

    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
        <div class="form-group">
            <label for="appointment_date">Data *</label>
            <input type="date" id="appointment_date" name="appointment_date" value="{{ old('appointment_date') }}" required>
            @error('appointment_date')
                <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="appointment_time">Ora *</label>
            <input type="time" id="appointment_time" name="appointment_time" value="{{ old('appointment_time', '09:00') }}" required>
            @error('appointment_time')
                <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="status">Status *</label>
            <select id="status" name="status" required>
                <option value="confirmed" {{ old('status', 'confirmed') == 'confirmed' ? 'selected' : '' }}>Confirmat</option>
                <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Anulat</option>
                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Finalizat</option>
            </select>
            @error('status')
                <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="form-group">
        <label for="notes">Notițe</label>
        <textarea id="notes" name="notes">{{ old('notes') }}</textarea>
        @error('notes')
            <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
        @enderror
    </div>

    <div style="display: flex; gap: 10px; margin-top: 30px;">
        <button type="submit" class="btn btn-primary">Salvează Programarea</button>
        <a href="{{ route('admin.appointments.index') }}" class="btn btn-secondary">Anulează</a>
    </div>
</form>

<script>
// Load doctors when department changes
document.getElementById('department_id').addEventListener('change', async function() {
    const departmentId = this.value;
    const doctorSelect = document.getElementById('doctor_id');

    doctorSelect.innerHTML = '<option value="">Încărcare...</option>';

    if (!departmentId) {
        doctorSelect.innerHTML = '<option value="">Selectează doctor</option>';
        return;
    }

    try {
        const response = await fetch(`/booking/doctors?department_id=${departmentId}`);
        const doctors = await response.json();

        doctorSelect.innerHTML = '<option value="">Selectează doctor</option>';
        doctors.forEach(doctor => {
            const option = document.createElement('option');
            option.value = doctor.id;
            option.textContent = `${doctor.title} ${doctor.name}`;
            doctorSelect.appendChild(option);
        });
    } catch (error) {
        console.error('Error loading doctors:', error);
        doctorSelect.innerHTML = '<option value="">Eroare la încărcare</option>';
    }
});
</script>
@endsection
