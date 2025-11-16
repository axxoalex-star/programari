@extends('layouts.admin')

@section('title', 'Pacienți - Recepție')

@section('content')
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1>Pacienți</h1>
            <p>Listă cu toți pacienții din clinică</p>
        </div>
    </div>
</div>

<div style="background:#f8f9fa;padding:16px;border-radius:8px;margin-bottom:16px;">
    <form method="GET" action="{{ route('receptie.patients.index') }}" style="display:flex; gap:10px; align-items:end;">
        <div class="form-group" style="margin:0; flex:1;">
            <label for="q" style="font-size:13px;">Caută pacient (nume, email sau telefon)</label>
            <input type="text" id="q" name="q" value="{{ request('q') }}" placeholder="Ex: Popescu, 07..., mail@...">
        </div>
        <div style="display:flex; gap:8px;">
            <button class="btn btn-primary" type="submit">Caută</button>
            <a class="btn btn-secondary" href="{{ route('receptie.patients.index') }}">Reset</a>
        </div>
    </form>
</div>

@if($patients->count() > 0)
<div style="color:#6c757d; font-size:13px; margin-bottom:10px;">Rezultate: {{ $patients->total() }}</div>
<table>
    <thead>
        <tr>
            <th>Pacient</th>
            <th>Email</th>
            <th>Telefon</th>
            <th>Total programări</th>
            <th>Ultima programare</th>
            <th>Acțiuni</th>
        </tr>
    </thead>
    <tbody>
        @foreach($patients as $p)
        <tr>
            <td><strong>{{ $p->client_name }}</strong></td>
            <td>{{ $p->client_email }}</td>
            <td>{{ $p->client_phone }}</td>
            <td>{{ $p->total_appointments }}</td>
            <td>{{ \Carbon\Carbon::parse($p->last_appointment)->format('d.m.Y') }}</td>
            <td>
                <a href="{{ route('receptie.patients.show', $p->client_email) }}" class="btn btn-sm btn-primary">Vezi Istoric</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
<div style="margin-top: 20px;">
    {{ $patients->withQueryString()->links() }}
</div>
@else
    <div style="text-align: center; padding: 60px 20px; color: #999;">
        <p>{{ request('q') ? 'Nu s-au găsit pacienți care să corespundă căutării.' : 'Nu există pacienți înregistrați încă.' }}</p>
    </div>
@endif
@endsection
