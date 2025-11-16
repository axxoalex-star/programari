@extends('layouts.doctor')

@section('title', 'Adaugă notă medicală')

@section('content')
<div class="page-header">
    <h1>Adaugă notă medicală</h1>
    <p>Pacient: {{ $email }}</p>
</div>

<form method="POST" action="{{ route('doctor.patients.records.store', $email) }}" enctype="multipart/form-data" style="max-width: 800px;">
    @csrf

    <div class="form-group">
        <label for="title">Titlu *</label>
        <input type="text" id="title" name="title" value="{{ old('title') }}" required>
        @error('title')<div style="color:#dc3545;font-size:12px;">{{ $message }}</div>@enderror
    </div>

    <div class="form-group">
        <label for="notes">Note</label>
        <textarea id="notes" name="notes" rows="6">{{ old('notes') }}</textarea>
        @error('notes')<div style="color:#dc3545;font-size:12px;">{{ $message }}</div>@enderror
    </div>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
        <div class="form-group">
            <label for="client_name">Nume pacient</label>
            <input type="text" id="client_name" name="client_name" value="{{ old('client_name') }}">
            @error('client_name')<div style="color:#dc3545;font-size:12px;">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label for="client_phone">Telefon</label>
            <input type="text" id="client_phone" name="client_phone" value="{{ old('client_phone') }}">
            @error('client_phone')<div style="color:#dc3545;font-size:12px;">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="form-group">
        <label for="attachment">Atașament (pdf/jpg/png, max 5MB)</label>
        <input type="file" id="attachment" name="attachment" accept=".pdf,.jpg,.jpeg,.png">
        @error('attachment')<div style="color:#dc3545;font-size:12px;">{{ $message }}</div>@enderror
    </div>

    <div style="display:flex; gap:10px;">
        <button class="btn btn-primary" type="submit">Salvează</button>
        <a href="{{ route('doctor.patients.records.index', $email) }}" class="btn btn-secondary">Anulează</a>
    </div>
</form>
@endsection
