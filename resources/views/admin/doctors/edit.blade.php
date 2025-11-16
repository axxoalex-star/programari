@extends('layouts.admin')

@section('title', 'Editează Doctor')

@section('content')
<div class="page-header">
    <h1>Editează Doctor</h1>
    <p>Actualizează informațiile doctorului</p>
</div>

<form method="POST" action="{{ route('admin.doctors.update', $doctor) }}" style="max-width: 800px;">
    @csrf
    @method('PUT')

    <h3 style="margin-bottom: 20px; color: #667eea;">Informații Generale</h3>

    <div class="form-group">
        <label for="appointment_type_id">Clinica *</label>
        <select id="appointment_type_id" name="appointment_type_id" required>
            <option value="">Selectează clinica</option>
            @foreach($appointmentTypes as $type)
            <option value="{{ $type->id }}" {{ old('appointment_type_id', $doctor->appointment_type_id) == $type->id ? 'selected' : '' }}>
                {{ $type->name }}
            </option>
            @endforeach
        </select>
        @error('appointment_type_id')
            <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label>Specialitate(e) *</label>
        @php
            $selectedIds = collect(old('department_ids', $doctor->departments->pluck('id')->toArray()));
        @endphp
        <div style="display:grid; grid-template-columns: repeat(2, minmax(200px, 1fr)); gap:8px;">
            @foreach($departments as $department)
                <label class="form-check" style="gap:8px;">
                    <input type="checkbox" name="department_ids[]" value="{{ $department->id }}" {{ $selectedIds->contains($department->id) ? 'checked' : '' }}>
                    <span>{{ $department->name }}</span>
                </label>
            @endforeach
        </div>
        @error('department_ids')
            <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
        @enderror
    </div>

    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 15px;">
        <div class="form-group">
            <label for="title">Titlu *</label>
            <input type="text" id="title" name="title" value="{{ old('title', $doctor->title) }}" required>
            @error('title')
                <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="name">Nume Complet *</label>
            <input type="text" id="name" name="name" value="{{ old('name', $doctor->name) }}" required>
            @error('name')
                <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
        <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" value="{{ old('email', $doctor->email) }}" required>
            @error('email')
                <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="phone">Telefon *</label>
            <input type="tel" id="phone" name="phone" value="{{ old('phone', $doctor->phone) }}" required>
            @error('phone')
                <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
        <div class="form-group">
            <label for="consultation_duration">Durată consultație (minute) *</label>
            <input type="number" id="consultation_duration" name="consultation_duration" value="{{ old('consultation_duration', $doctor->consultation_duration) }}" min="15" max="180" required>
            @error('consultation_duration')
                <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="consultation_price">Preț consultație (RON) *</label>
            <input type="number" id="consultation_price" name="consultation_price" value="{{ old('consultation_price', $doctor->consultation_price) }}" min="0" step="0.01" required>
            @error('consultation_price')
                <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="form-group form-check">
        <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $doctor->is_active) ? 'checked' : '' }}>
        <label for="is_active">Activ</label>
    </div>

    <h3 style="margin: 30px 0 20px; color: #667eea; border-top: 2px solid #f0f0f0; padding-top: 30px;">Program Săptămânal</h3>
    <p style="color: #666; margin-bottom: 20px; font-size: 14px;">Selectează zilele și orele în care doctorul este disponibil</p>

    @foreach(['monday'=>'Luni','tuesday'=>'Marți','wednesday'=>'Miercuri','thursday'=>'Joi','friday'=>'Vineri','saturday'=>'Sâmbătă','sunday'=>'Duminică'] as $key => $label)
    @php
        $schedule = $schedules->get($key);
    @endphp
    <div style="border: 1px solid #e0e0e0; border-radius: 8px; padding: 15px; margin-bottom: 15px; background: #f8f9fa;">
        <div style="display: grid; grid-template-columns: 150px 1fr; gap: 20px; align-items: center;">
            <div class="form-check">
                @php $isWeekday = in_array($key, ['monday','tuesday','wednesday','thursday','friday']); @endphp
                <input type="checkbox" id="schedule_{{ $key }}" name="schedules[{{ $key }}][enabled]" value="1" {{ $schedule ? 'checked' : ($isWeekday ? 'checked' : '') }}>
                <label for="schedule_{{ $key }}"><strong>{{ $label }}</strong></label>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group" style="margin: 0;">
                    <label for="schedule_{{ $key }}_start" style="font-size: 13px;">Ora început (ex: 09:00)</label>
                    <input type="text" id="schedule_{{ $key }}_start" name="schedules[{{ $key }}][start_time]" value="{{ ($schedule && !empty($schedule->start_time)) ? \Carbon\Carbon::parse($schedule->start_time)->format('H:i') : '09:00' }}" pattern="[0-2][0-9]:[0-5][0-9]" placeholder="09:00" maxlength="5">
                </div>
                <div class="form-group" style="margin: 0;">
                    <label for="schedule_{{ $key }}_end" style="font-size: 13px;">Ora sfârșit (ex: 17:00)</label>
                    <input type="text" id="schedule_{{ $key }}_end" name="schedules[{{ $key }}][end_time]" value="{{ ($schedule && !empty($schedule->end_time)) ? \Carbon\Carbon::parse($schedule->end_time)->format('H:i') : '17:00' }}" pattern="[0-2][0-9]:[0-5][0-9]" placeholder="17:00" maxlength="5">
                </div>
            </div>
        </div>
    </div>
    @endforeach

    <div style="display: flex; gap: 10px; margin-top: 30px;">
        <button type="submit" class="btn btn-primary">Actualizează Doctorul</button>
        <a href="{{ route('admin.doctors.index') }}" class="btn btn-secondary">Anulează</a>
    </div>
</form>
@endsection

