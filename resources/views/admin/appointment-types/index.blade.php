@extends('layouts.admin')

@section('title', 'Clinici')

@section('content')
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1>Clinici</h1>
            <p>Gestionează clinicile disponibile</p>
        </div>
        <a href="{{ route('admin.appointment-types.create') }}" class="btn btn-primary">+ Adaugă Clinică</a>
    </div>
</div>

@if($types->count() > 0)
    <table>
        <thead>
            <tr>
                <th>Nume</th>
                <th>Ordine</th>
                <th>Status</th>
                <th>Acțiuni</th>
            </tr>
        </thead>
        <tbody>
            @foreach($types as $type)
            <tr>
                <td><strong>{{ $type->name }}</strong></td>
                <td>{{ $type->order }}</td>
                <td>
                    @if($type->is_active)
                        <span style="color: #28a745; font-weight: 600;">● Activ</span>
                    @else
                        <span style="color: #dc3545; font-weight: 600;">● Inactiv</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('admin.appointment-types.edit', $type) }}" class="btn btn-sm btn-primary">Editează</a>
                    <form method="POST" action="{{ route('admin.appointment-types.destroy', $type) }}" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Sigur vrei să ștergi această clinică?')">Șterge</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
@else
    <div style="text-align: center; padding: 60px 20px; color: #999;">
        <p>Nu există clinici create încă.</p>
        <a href="{{ route('admin.appointment-types.create') }}" class="btn btn-primary" style="margin-top: 20px;">Creează prima clinică</a>
    </div>
@endif
@endsection
