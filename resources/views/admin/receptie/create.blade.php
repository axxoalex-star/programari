@extends('layouts.admin')

@section('title', 'Adaugă Cont Recepție')

@section('content')
<div class="page-header">
    <h1>Adaugă Cont Recepție Nou</h1>
    <p>Completează formularul pentru a adăuga un cont de recepție</p>
</div>

<form method="POST" action="{{ route('admin.receptie.store') }}" style="max-width: 600px;">
    @csrf

    <div class="form-group">
        <label for="name">Nume Complet *</label>
        <input type="text" id="name" name="name" value="{{ old('name') }}" required>
        @error('name')
            <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="email">Email *</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" required>
        @error('email')
            <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
        @enderror
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

    <div class="form-group">
        <label for="password">Parolă *</label>
        <input type="password" id="password" name="password" required>
        @error('password')
            <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="password_confirmation">Confirmă Parola *</label>
        <input type="password" id="password_confirmation" name="password_confirmation" required>
    </div>

    <div class="form-group form-check">
        <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
        <label for="is_active">Activ</label>
    </div>

    <div style="display: flex; gap: 10px; margin-top: 30px;">
        <button type="submit" class="btn btn-primary">Salvează Contul</button>
        <a href="{{ route('admin.receptie.index') }}" class="btn btn-secondary">Anulează</a>
    </div>
</form>
@endsection
