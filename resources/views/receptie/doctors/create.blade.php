@extends('layouts.admin')

@section('title', 'Adaugă Doctor - Recepție')

@section('content')
<div class="page-header">
    <h1>Adaugă Doctor</h1>
    <p>Completează datele doctorului din clinica ta</p>
</div>

<form method="POST" action="{{ route('receptie.doctors.store') }}" style="max-width: 800px;">
    @csrf

    <div class="form-group">
        <label for="name">Nume Complet *</label>
        <input type="text" id="name" name="name" value="{{ old('name') }}" required>
        @error('name')<span style="color:#dc3545; font-size:13px;">{{ $message }}</span>@enderror
    </div>

    <div class="form-group">
        <label for="title">Titlu *</label>
        <input type="text" id="title" name="title" value="{{ old('title', 'Dr.') }}" required>
        @error('title')<span style="color:#dc3545; font-size:13px;">{{ $message }}</span>@enderror
    </div>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
        <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required>
            @error('email')<span style="color:#dc3545; font-size:13px;">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
            <label for="phone">Telefon *</label>
            <input type="text" id="phone" name="phone" value="{{ old('phone') }}" required>
            @error('phone')<span style="color:#dc3545; font-size:13px;">{{ $message }}</span>@enderror
        </div>
    </div>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
        <div class="form-group">
            <label for="consultation_duration">Durată consultație (minute) *</label>
            <input type="number" id="consultation_duration" min="15" max="180" name="consultation_duration" value="{{ old('consultation_duration', 30) }}" required>
            @error('consultation_duration')<span style="color:#dc3545; font-size:13px;">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
            <label for="consultation_price">Preț consultație (RON) *</label>
            <input type="number" id="consultation_price" min="0" step="0.01" name="consultation_price" value="{{ old('consultation_price', 200) }}" required>
            @error('consultation_price')<span style="color:#dc3545; font-size:13px;">{{ $message }}</span>@enderror
        </div>
    </div>

    <div class="form-group">
        <label>Specialitate(e) *</label>
        <div style="display:grid; grid-template-columns: repeat(2, minmax(200px, 1fr)); gap:8px;">
            @foreach($departments as $dep)
                <label class="form-check" style="gap:8px;">
                    <input type="checkbox" name="department_ids[]" value="{{ $dep->id }}" {{ (collect(old('department_ids', []))->contains($dep->id)) ? 'checked' : '' }}>
                    <span>{{ $dep->name }}</span>
                </label>
            @endforeach
        </div>
        @error('department_ids')<span style="color:#dc3545; font-size:13px;">{{ $message }}</span>@enderror
    </div>

    <div class="form-group form-check">
        <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
        <label for="is_active">Activ</label>
    </div>

    <h3 style="margin: 30px 0 20px; color: #667eea; border-top: 2px solid #f0f0f0; padding-top: 30px;">Program Săptămânal</h3>
    @php($days = ['monday'=>'Luni','tuesday'=>'Marți','wednesday'=>'Miercuri','thursday'=>'Joi','friday'=>'Vineri','saturday'=>'Sâmbătă','sunday'=>'Duminică'])
    @foreach($days as $key=>$label)
        <div style="border: 1px solid #e0e0e0; border-radius: 8px; padding: 15px; margin-bottom: 15px; background: #f8f9fa;">
            <div style="display: grid; grid-template-columns: 150px 1fr; gap: 20px; align-items: center;">
                <div class="form-check">
                    <input type="checkbox" id="schedule_{{ $key }}" name="schedules[{{ $key }}][enabled]" value="1" {{ in_array($key,['monday','tuesday','wednesday','thursday','friday']) ? 'checked' : '' }}>
                    <label for="schedule_{{ $key }}"><strong>{{ $label }}</strong></label>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group" style="margin: 0;">
                        <label for="schedule_{{ $key }}_start" style="font-size: 13px;">Ora început (ex: 09:00)</label>
                        <input type="text" id="schedule_{{ $key }}_start" name="schedules[{{ $key }}][start_time]" value="09:00" pattern="[0-2][0-9]:[0-5][0-9]" placeholder="09:00" maxlength="5">
                    </div>
                    <div class="form-group" style="margin: 0;">
                        <label for="schedule_{{ $key }}_end" style="font-size: 13px;">Ora sfârșit (ex: 17:00)</label>
                        <input type="text" id="schedule_{{ $key }}_end" name="schedules[{{ $key }}][end_time]" value="17:00" pattern="[0-2][0-9]:[0-5][0-9]" placeholder="17:00" maxlength="5">
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <div style="display:flex; gap:10px; margin-top:20px;">
        <button class="btn btn-primary" type="submit">Salvează</button>
        <a class="btn btn-secondary" href="{{ route('receptie.doctors.index') }}">Anulează</a>
    </div>
</form>
@endsection
