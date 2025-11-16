@extends('layouts.admin')

@section('title', 'Adaugă Clinică')

@section('content')
<div class="page-header">
    <h1>Adaugă Clinică Nouă</h1>
    <p>Completează formularul pentru a adăuga o clinică</p>
</div>

<form method="POST" action="{{ route('admin.appointment-types.store') }}" style="max-width: 600px;">
    @csrf

    <div class="form-group">
        <label for="name">Nume Clinică *</label>
        <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="ex: Clinica Stomatologica Mihai">
        @error('name')
            <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="order">Ordine de afișare *</label>
        <input type="number" id="order" name="order" value="{{ old('order', 1) }}" min="1" required>
        @error('order')
            <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
        @enderror
        <small style="color: #666; font-size: 13px;">Ordinea în care va fi afișat în listă (1 = primul)</small>
    </div>

    <div class="form-group form-check">
        <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
        <label for="is_active">Activ</label>
    </div>

    <div style="display: flex; gap: 10px; margin-top: 30px;">
        <button type="submit" class="btn btn-primary">Salvează Clinica</button>
        <a href="{{ route('admin.appointment-types.index') }}" class="btn btn-secondary">Anulează</a>
    </div>
</form>
@endsection
