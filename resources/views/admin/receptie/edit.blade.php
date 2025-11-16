@extends('layouts.admin')

@section('title', 'Editează Cont Recepție')

@section('content')
<div class="page-header">
    <h1>Editează Cont Recepție</h1>
    <p>Actualizează informațiile contului de recepție</p>
</div>

<form method="POST" action="{{ route('admin.receptie.update', $receptie) }}" style="max-width: 600px;">
    @csrf
    @method('PUT')

    <div class="form-group">
        <label for="name">Nume Complet *</label>
        <input type="text" id="name" name="name" value="{{ old('name', $receptie->name) }}" required>
        @error('name')
            <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="email">Email *</label>
        <input type="email" id="email" name="email" value="{{ old('email', $receptie->email) }}" required>
        @error('email')
            <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="appointment_type_id">Clinica *</label>
        <select id="appointment_type_id" name="appointment_type_id" required>
            <option value="">Selectează clinica</option>
            @foreach($appointmentTypes as $type)
            <option value="{{ $type->id }}" {{ old('appointment_type_id', $receptie->appointment_type_id) == $type->id ? 'selected' : '' }}>
                {{ $type->name }}
            </option>
            @endforeach
        </select>
        @error('appointment_type_id')
            <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="password">Parolă Nouă (lasă gol pentru a păstra parola actuală)</label>
        <input type="password" id="password" name="password">
        @error('password')
            <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="password_confirmation">Confirmă Parola Nouă</label>
        <input type="password" id="password_confirmation" name="password_confirmation">
    </div>

    <div class="form-group form-check">
        <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $receptie->is_active) ? 'checked' : '' }}>
        <label for="is_active">Activ</label>
    </div>

    <div style="display: flex; gap: 10px; margin-top: 30px;">
        <button type="submit" class="btn btn-primary">Actualizează Contul</button>
        <a href="{{ route('admin.receptie.index') }}" class="btn btn-secondary">Anulează</a>
    </div>
</form>
@endsection
