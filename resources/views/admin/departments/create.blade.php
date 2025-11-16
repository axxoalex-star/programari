@extends('layouts.admin')

@section('title', 'Adaugă Specialitate')

@section('content')
<div class="page-header">
    <h1>Adaugă Specialitate</h1>
    <p>Completează formularul pentru a adăuga o specialitate în clinică</p>
</div>

<form method="POST" action="{{ route('admin.departments.store') }}" style="max-width: 700px;">
    @csrf

    <div class="form-group">
        <label for="name">Nume Specialitate *</label>
        <input type="text" id="name" name="name" value="{{ old('name') }}" required>
    </div>

    <div class="form-group">
        <label for="color">Culoare (opțional)</label>
        <input type="color" id="color" name="color" value="{{ old('color', '#667eea') }}" style="height: 40px;">
        @error('color')
            <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
        @enderror
    </div>

    @include('admin.departments._icon-picker', ['selectedIcon' => old('icon', 'fa-stethoscope')])

    <div class="form-group form-check">
        <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
        <label for="is_active">Activ</label>
    </div>

    <div style="display:flex; gap:10px; margin-top:20px;">
        <button type="submit" class="btn btn-primary">Salvează Specialitatea</button>
        <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary">Anulează</a>
    </div>
</form>
@endsection
