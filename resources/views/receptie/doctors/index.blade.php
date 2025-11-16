@extends('layouts.admin')

@section('title', 'Doctori - Recepție')

@section('content')
<div class="page-header">
    <div style="display:flex; justify-content:space-between; align-items:center;">
        <div>
            <h1>Doctori</h1>
            <p>Gestionează doctorii clinicii tale</p>
        </div>
        <a href="{{ route('receptie.doctors.create') }}" class="btn btn-primary">+ Adaugă Doctor</a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-error">{{ session('error') }}</div>
@endif

@if($doctors->count() > 0)
<table>
    <thead>
        <tr>
            <th>Nume</th>
            <th>Specialități</th>
            <th>Email</th>
            <th>Telefon</th>
            <th>Status</th>
            <th>Acțiuni</th>
        </tr>
    </thead>
    <tbody>
        @foreach($doctors as $doctor)
        <tr>
            <td><strong>{{ $doctor->title }} {{ $doctor->name }}</strong></td>
            <td>
                @if($doctor->departments && $doctor->departments->count())
                    {{ $doctor->departments->pluck('name')->implode(', ') }}
                @else
                    {{ optional($doctor->department)->name }}
                @endif
            </td>
            <td>{{ $doctor->email }}</td>
            <td>{{ $doctor->phone }}</td>
            <td>{!! $doctor->is_active ? '<span style="color:#28a745;font-weight:600;">● Activ</span>' : '<span style="color:#dc3545;font-weight:600;">● Inactiv</span>' !!}</td>
            <td>
                <a class="btn btn-sm btn-primary" href="{{ route('receptie.doctors.edit', $doctor) }}">Editează</a>
                <form action="{{ route('receptie.doctors.destroy', $doctor) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger" onclick="return confirm('Ștergi acest doctor?')">Șterge</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
<div style="margin-top:12px;">{{ $doctors->links() }}</div>
@else
    <div style="text-align:center; padding:40px; color:#6b7280;">Nu există doctori.</div>
@endif
@endsection
