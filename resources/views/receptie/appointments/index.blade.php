@extends('layouts.admin')

@section('title', 'Programări - Recepție')

@section('content')
<div class="page-header">
    <h1>Programări</h1>
    <p>Lista tuturor programărilor pentru clinica ta</p>
</div>

<form method="GET" action="{{ route('receptie.appointments') }}" style="display:grid; grid-template-columns:1fr 1fr 1fr 1fr; gap:12px; margin-bottom:16px;">
    <div class="form-group">
        <label for="department_id">Specialitate</label>
        <select id="department_id" name="department_id">
            <option value="">Toate</option>
            @foreach($departments as $dep)
                <option value="{{ $dep->id }}" {{ request('department_id') == $dep->id ? 'selected' : '' }}>{{ $dep->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="doctor_id">Doctor</label>
        <select id="doctor_id" name="doctor_id">
            <option value="">Toți</option>
            @foreach($doctors as $doc)
                <option value="{{ $doc->id }}" {{ request('doctor_id') == $doc->id ? 'selected' : '' }}>{{ ($doc->title ? $doc->title.' ' : '') . $doc->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="date">Data</label>
        <input type="date" id="date" name="date" value="{{ request('date') }}">
    </div>
    <div class="form-group">
        <label for="status">Status</label>
        <select id="status" name="status">
            <option value="">Toate</option>
            <option value="confirmed" {{ request('status')=='confirmed'?'selected':'' }}>Confirmată</option>
            <option value="cancelled" {{ request('status')=='cancelled'?'selected':'' }}>Anulată</option>
            <option value="completed" {{ request('status')=='completed'?'selected':'' }}>Finalizată</option>
        </select>
    </div>
    <div style="grid-column: span 4; display:flex; gap:8px;">
        <button class="btn btn-primary" type="submit">Filtrează</button>
        <a class="btn btn-secondary" href="{{ route('receptie.appointments') }}">Resetează</a>
    </div>
</form>

@if($appointments->count() > 0)
<table>
    <thead>
        <tr>
            <th>Data</th>
            <th>Ora</th>
            <th>Pacient</th>
            <th>Telefon</th>
            <th>Specialitate</th>
            <th>Doctor</th>
            <th>Status</th>
            <th>Acțiuni</th>
        </tr>
    </thead>
    <tbody>
        @foreach($appointments as $a)
        @php
            $doc = $a->doctor;
            $docName = $doc ? trim(($doc->title ? $doc->title.' ' : '').($doc->name ?? '')) : '';
        @endphp
        <tr>
            <td>{{ \Carbon\Carbon::parse($a->appointment_date)->format('Y-m-d') }}</td>
            <td>{{ \Carbon\Carbon::parse($a->appointment_time)->format('H:i') }}</td>
            <td>{{ $a->client_name }}</td>
            <td>{{ $a->client_phone }}</td>
            <td>{{ optional($a->department)->name }}</td>
            <td>{{ $docName }}</td>
            <td>{{ ucfirst($a->status) }}</td>
            <td>
                <a href="{{ route('receptie.appointments.edit', $a) }}" class="btn btn-sm btn-primary">Editează</a>
                <form action="{{ route('receptie.appointments.destroy', $a) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger" onclick="return confirm('Ștergi această programare?')">Șterge</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div style="margin-top:12px;">
    {{ $appointments->links() }}
</div>
@else
    <div style="text-align:center; padding:40px; color:#6b7280;">Nu s-au găsit programări.</div>
@endif
@endsection
