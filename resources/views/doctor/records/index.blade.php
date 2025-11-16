@extends('layouts.doctor')

@section('title', 'Istoric medical')

@section('content')
<div class="page-header" style="display:flex; justify-content:space-between; align-items:center;">
    <div>
        <h1>Istoric medical pacient</h1>
        <p>{{ $email }}</p>
    </div>
    <a href="{{ route('doctor.patients.records.create', $email) }}" class="btn btn-primary">+ Adaugă notă</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if($records->count() > 0)
<table>
    <thead>
        <tr>
            <th>Titlu</th>
            <th>Creat la</th>
            <th>Atașament</th>
            <th>Acțiuni</th>
        </tr>
    </thead>
    <tbody>
        @foreach($records as $r)
        <tr>
            <td><strong>{{ $r->title }}</strong></td>
            <td>{{ $r->created_at->format('d.m.Y H:i') }}</td>
            <td>
                @if($r->attachment_path)
                    <a href="{{ route('doctor.records.download', $r) }}" class="btn btn-sm btn-secondary">Descarcă</a>
                @else
                    <small style="color:#999;">-</small>
                @endif
            </td>
            <td>
                <a href="{{ route('doctor.records.edit', $r) }}" class="btn btn-sm btn-primary">Editează</a>
                <form method="POST" action="{{ route('doctor.records.destroy', $r) }}" style="display:inline" onsubmit="return confirm('Ștergi nota?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">Șterge</button>
                </form>
            </td>
        </tr>
        <tr>
            <td colspan="4"><div style="color:#555; white-space:pre-wrap;">{{ $r->notes }}</div></td>
        </tr>
        @endforeach
    </tbody>
</table>
<div style="margin-top:16px;">{{ $records->links() }}</div>
@else
    <div style="text-align:center; padding:48px; color:#777;">Nu există înregistrări.</div>
@endif

<div style="margin-top:16px;">
    <a href="{{ route('doctor.patients') }}" class="btn btn-secondary">Înapoi la pacienți</a>
</div>
@endsection
