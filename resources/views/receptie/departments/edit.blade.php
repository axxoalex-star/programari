@extends('layouts.admin')

@section('title', 'Editează Specialitate - Recepție')

@section('content')
<div class="page-header">
    <h1>Editează Specialitate</h1>
    <p>Modifică detaliile specialității</p>
</div>

<form method="POST" action="{{ route('receptie.departments.update', $department) }}" style="max-width: 700px;">
    @csrf
    @method('PUT')

    <div class="form-group">
        <label for="name">Nume Specialitate *</label>
        <input type="text" id="name" name="name" value="{{ old('name', $department->name) }}" required>
        @error('name')<span style="color:#dc3545; font-size:13px;">{{ $message }}</span>@enderror
    </div>

    <div class="form-group">
        <label for="color">Culoare (opțional)</label>
        <input type="color" id="color" name="color" value="{{ old('color', $department->color) }}" style="height: 40px;">
        @error('color')<span style="color:#dc3545; font-size:13px;">{{ $message }}</span>@enderror
    </div>

    @include('receptie.departments._icon-picker', ['selectedIcon' => old('icon', $department->icon ?? 'fa-stethoscope')])

    <div class="form-group form-check">
        <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $department->is_active) ? 'checked' : '' }}>
        <label for="is_active">Activ</label>
    </div>

    <div style="display:flex; gap:10px; margin-top:20px;">
        <button type="submit" class="btn btn-primary">Salvează</button>
        <a href="{{ route('receptie.departments.index') }}" class="btn btn-secondary">Anulează</a>
    </div>
</form>
@endsection
