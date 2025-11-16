@extends('layouts.admin')

@section('title', 'Doctori')

@section('content')
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1>Doctori</h1>
            <p>Gestionează medicii din clinică</p>
        </div>
        <a href="{{ route('admin.doctors.create') }}" class="btn btn-primary">+ Adaugă Doctor</a>
    </div>
</div>

@if($doctors->count() > 0)
    <table>
        <thead>
            <tr>
                <th>Nume</th>
                <th>Specialitate</th>
                <th>Clinica</th>
                <th>Email</th>
                <th>Telefon</th>
                <th>Durată consultație</th>
                <th>Preț</th>
                <th>Status</th>
                <th>Acțiuni</th>
            </tr>
        </thead>
        <tbody>
            @foreach($doctors as $doctor)
            <tr>
                <td><strong>{{ $doctor->title }} {{ $doctor->name }}</strong></td>
                <td>{{ $doctor->department->name }}</td>
                <td>{{ optional($doctor->appointmentType)->name }}</td>
                <td>{{ $doctor->email }}</td>
                <td>{{ $doctor->phone }}</td>
                <td>{{ $doctor->consultation_duration }} min</td>
                <td>{{ number_format($doctor->consultation_price, 0) }} RON</td>
                <td>
                    @if($doctor->is_active)
                        <span style="color: #28a745; font-weight: 600;">● Activ</span>
                    @else
                        <span style="color: #dc3545; font-weight: 600;">● Inactiv</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('admin.doctors.edit', $doctor) }}" class="btn btn-sm btn-primary">Editează</a>
                    <form method="POST" action="{{ route('admin.doctors.destroy', $doctor) }}" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Sigur vrei să ștergi acest doctor?')">Șterge</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
@else
    <div style="text-align: center; padding: 60px 20px; color: #999;">
        <p>Nu există doctori creați încă.</p>
        <a href="{{ route('admin.doctors.create') }}" class="btn btn-primary" style="margin-top: 20px;">Adaugă primul doctor</a>
    </div>
@endif
@endsection

