@extends('layouts.admin')

@section('title', 'Conturi Recepție')

@section('content')
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1>Conturi Recepție</h1>
            <p>Gestionează conturile pentru recepție</p>
        </div>
        <a href="{{ route('admin.receptie.create') }}" class="btn btn-primary">+ Adaugă Cont Recepție</a>
    </div>
</div>

@if($users->count() > 0)
    <table>
        <thead>
            <tr>
                <th>Nume</th>
                <th>Email</th>
                <th>Clinica</th>
                <th>Status</th>
                <th>Acțiuni</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td><strong>{{ $user->name }}</strong></td>
                <td>{{ $user->email }}</td>
                <td>
                    @if($user->appointmentType)
                        <span style="color: #667eea;">{{ $user->appointmentType->name }}</span>
                    @else
                        <span style="color: #999;">Nicio clinică</span>
                    @endif
                </td>
                <td>
                    @if($user->is_active)
                        <span style="color: #28a745; font-weight: 600;">● Activ</span>
                    @else
                        <span style="color: #dc3545; font-weight: 600;">● Inactiv</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('admin.receptie.edit', $user) }}" class="btn btn-sm btn-primary">Editează</a>
                    <form method="POST" action="{{ route('admin.receptie.destroy', $user) }}" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Sigur vrei să ștergi acest cont?')">Șterge</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
@else
    <div style="text-align: center; padding: 60px 20px; color: #999;">
        <p>Nu există conturi de recepție create încă.</p>
        <a href="{{ route('admin.receptie.create') }}" class="btn btn-primary" style="margin-top: 20px;">Creează primul cont de recepție</a>
    </div>
@endif
@endsection
