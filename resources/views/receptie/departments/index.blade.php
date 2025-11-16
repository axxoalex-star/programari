@extends('layouts.admin')

@section('title', 'Specializari - Recepție')

@section('content')
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1>Specializari</h1>
            <p>Gestionează specializările clinicii tale</p>
        </div>
        <a href="{{ route('receptie.departments.create') }}" class="btn btn-primary">+ Adaugă Specialitate</a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-error">{{ session('error') }}</div>
@endif

@if($departments->count() > 0)
<table>
    <thead>
        <tr>
            <th>Nume</th>
            <th>Icon/Culoare</th>
            <th>Status</th>
            <th>Acțiuni</th>
        </tr>
    </thead>
    <tbody>
        @foreach($departments as $department)
        <tr>
            <td><strong>{{ $department->name }}</strong></td>
            <td>
                @if($department->icon)
                    <i class="fa {{ $department->icon }}" style="font-size: 24px; color: {{ $department->color ?? '#667eea' }};"></i>
                @else
                    <span style="display: inline-block; width: 30px; height: 20px; background: {{ $department->color }}; border-radius: 4px; border: 1px solid #ddd;"></span>
                    <small style="color:#888;">{{ $department->color }}</small>
                @endif
            </td>
            <td>{!! $department->is_active ? '<span style="color:#28a745;font-weight:600;">● Activ</span>' : '<span style="color:#dc3545;font-weight:600;">● Inactiv</span>' !!}</td>
            <td>
                <form method="POST" action="{{ route('receptie.departments.move-up', $department) }}" style="display:inline;">
                    @csrf
                    <button class="btn btn-sm btn-secondary" title="Mută sus">▲</button>
                </form>
                <form method="POST" action="{{ route('receptie.departments.move-down', $department) }}" style="display:inline;">
                    @csrf
                    <button class="btn btn-sm btn-secondary" title="Mută jos">▼</button>
                </form>
                <a href="{{ route('receptie.departments.edit', $department) }}" class="btn btn-sm btn-primary">Editează</a>
                <form method="POST" action="{{ route('receptie.departments.destroy', $department) }}" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Sigur vrei să ștergi această specialitate?')">Șterge</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
    <div style="text-align: center; padding: 60px 20px; color: #999;">
        <p>Nu există specialități create încă.</p>
        <a href="{{ route('receptie.departments.create') }}" class="btn btn-primary" style="margin-top: 20px;">Creează prima specialitate</a>
    </div>
@endif
@endsection
