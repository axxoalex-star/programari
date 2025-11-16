@extends('layouts.admin')

@section('title', 'Editează Clinica')

@section('content')
<div class="page-header">
    <h1>Editează Clinica</h1>
    <p>Actualizează informațiile clinicii</p>
</div>

<form method="POST" action="{{ route('admin.appointment-types.update', $appointmentType) }}" style="max-width: 600px;">
    @csrf
    @method('PUT')

    <div class="form-group">
        <label for="name">Nume Clinică *</label>
        <input type="text" id="name" name="name" value="{{ old('name', $appointmentType->name) }}" required>
        @error('name')
            <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="order">Ordine de afișare *</label>
        <input type="number" id="order" name="order" value="{{ old('order', $appointmentType->order) }}" min="1" required>
        @error('order')
            <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
        @enderror
        <small style="color: #666; font-size: 13px;">Ordinea în care va fi afișat în listă (1 = primul)</small>
    </div>

    <div class="form-group form-check">
        <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $appointmentType->is_active) ? 'checked' : '' }}>
        <label for="is_active">Activ</label>
    </div>

    <div style="display: flex; gap: 10px; margin-top: 30px;">
        <button type="submit" class="btn btn-primary">Actualizează Clinica</button>
        <a href="{{ route('admin.appointment-types.index') }}" class="btn btn-secondary">Anulează</a>
    </div>
</form>
@endsection
