@extends('layouts.admin')

@section('title', 'Istoric Pacient - Recepție')

@section('content')
<div class="page-header">
    <h1>Istoric: {{ $patient['name'] }}</h1>
    <p>{{ $patient['email'] }} • {{ $patient['phone'] }}</p>
</div>

<div style="display:flex; gap:10px; margin-bottom:16px;">
    <a class="btn btn-secondary" href="{{ route('receptie.patients.index') }}">Înapoi la Pacienți</a>
</div>

@if($appointments->count() > 0)
<table>
    <thead>
        <tr>
            <th>Data</th>
            <th>Ora</th>
            <th>Doctor</th>
            <th>Specialitate</th>
            <th>Status</th>
            <th>Notițe</th>
        </tr>
    </thead>
    <tbody>
    @foreach($appointments as $a)
        <tr>
            <td>{{ $a->appointment_date->format('d.m.Y') }}</td>
            <td>{{ $a->appointment_time }}</td>
            <td>{{ $a->doctor->title }} {{ $a->doctor->name }}</td>
            <td>{{ $a->department->name }}</td>
            <td>
                @if($a->status === 'confirmed')
                    <span style="color: #28a745; font-weight: 600;">● Confirmat</span>
                @elseif($a->status === 'pending')
                    <span style="color: #ffc107; font-weight: 600;">● În așteptare</span>
                @elseif($a->status === 'completed')
                    <span style="color: #6c757d; font-weight: 600;">● Finalizat</span>
                @elseif($a->status === 'cancelled')
                    <span style="color: #dc3545; font-weight: 600;">● Anulat</span>
                @endif
            </td>
            <td>{{ $a->notes ?: '-' }}</td>
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
    <a href="{{ route('receptie.patients.index') }}" class="btn btn-secondary">Înapoi la pacienți</a>
</div>
@endsection
