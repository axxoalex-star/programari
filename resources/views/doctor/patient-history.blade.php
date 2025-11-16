@extends('layouts.doctor')

@section('title', 'Istoric pacient')

@section('content')
<div class="page-header">
    <h1>Istoric: {{ $patient['name'] }}</h1>
    <p>{{ $patient['email'] }} • {{ $patient['phone'] }}</p>
</div>

<div style="display:flex; gap:10px; margin-bottom:16px;">
    <a class="btn btn-secondary" href="{{ route('doctor.patients') }}">Înapoi la Pacienți</a>
</div>

@if($appointments->count() > 0)
<table>
    <thead>
        <tr>
            <th>Data</th>
            <th>Ora</th>
            <th>Tip</th>
            <th>Status</th>
            <th style="width: 40%;">Notițe</th>
            <th>Acțiuni</th>
        </tr>
    </thead>
    <tbody>
    @foreach($appointments as $a)
        <tr>
            <td>{{ $a->appointment_date->format('d.m.Y') }}</td>
            <td>{{ $a->appointment_time }}</td>
            <td>{{ $a->appointmentType->name }}</td>
            <td>{{ ucfirst($a->status) }}</td>
            <td>
                <form method="POST" action="{{ route('doctor.appointments.update-notes', $a) }}">
                    @csrf
                    @method('PATCH')
                    <textarea name="notes" rows="2" style="width:100%; resize: vertical;">{{ old('notes', $a->notes) }}</textarea>
                    @error('notes')<div style="color:#dc3545;font-size:12px;">{{ $message }}</div>@enderror
                    <div style="margin-top:6px;">
                        <button type="submit" class="btn btn-sm btn-primary">Salvează</button>
                    </div>
                </form>
            </td>
            <td>
                <a href="{{ route('doctor.appointments.edit', $a) }}" class="btn btn-sm btn-secondary">Editează</a>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
@else
    <div style="text-align: center; padding: 60px 20px; color: #999;">
        <p>Nu există programări anterioare.</p>
    </div>
@endif

<div style="margin-top: 20px;">
    <a href="{{ route('doctor.patients') }}" class="btn btn-secondary">Înapoi la pacienți</a>
</div>
@endsection
