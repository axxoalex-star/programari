@extends('layouts.doctor')

@section('title', 'Programările Mele')

@section('content')
<div class="page-header">
    <h1>Programările Mele</h1>
    <p>Lista completă a programărilor tale</p>
</div>

<!-- Filtre -->
<div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 25px;">
    <form method="GET" action="{{ route('doctor.appointments') }}">
        <div style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 15px; align-items: end;">
            <div class="form-group" style="margin: 0;">
                <label for="status" style="font-size: 13px;">Status</label>
                <select id="status" name="status">
                    <option value="">Toate</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmat</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Anulat</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Finalizat</option>
                </select>
            </div>

            <div class="form-group" style="margin: 0;">
                <label for="date" style="font-size: 13px;">Data</label>
                <input type="date" id="date" name="date" value="{{ request('date') }}">
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary">Filtrează</button>
                <a href="{{ route('doctor.appointments') }}" class="btn btn-secondary">Reset</a>
            </div>
        </div>
    </form>
</div>

@if($appointments->count() > 0)
    <table>
        <thead>
            <tr>
                <th>Data</th>
                <th>Ora</th>
                <th>Client</th>
                <th>Tip</th>
                <th>Status</th>
                <th>Acțiuni</th>
            </tr>
        </thead>
        <tbody>
            @foreach($appointments as $appointment)
            <tr>
                <td>{{ $appointment->appointment_date->format('d.m.Y') }}</td>
                <td><strong>{{ $appointment->appointment_time }}</strong></td>
                <td>
                    <small style="color: #666;">{{ $appointment->client_email }}</small><br>
                    <small style="color: #666;">{{ $appointment->client_phone }}</small>
                </td>
                <td>{{ $appointment->appointmentType->name }}</td>
                <td>
                    @if($appointment->status === 'confirmed')
                        <span style="background: #28a745; color: #fff; padding: 4px 10px; border-radius: 20px; font-size: 12px;">Confirmat</span>
                    @elseif($appointment->status === 'cancelled')
                        <span style="background: #dc3545; color: #fff; padding: 4px 10px; border-radius: 20px; font-size: 12px;">Anulat</span>
                    @else
                        <span style="background: #6c757d; color: #fff; padding: 4px 10px; border-radius: 20px; font-size: 12px;">Finalizat</span>
                    @endif
                </td>
                <td>
                    <div style="display:flex; flex-direction:row; gap:8px; align-items:center;">
                        @if($appointment->status !== 'completed')
                        <a href="{{ route('doctor.appointments.edit', $appointment) }}" class="btn btn-sm btn-primary" title="Editează" aria-label="Editează" style="display:inline-flex; align-items:center; justify-content:center; width:36px; height:36px; padding:0;">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:18px; height:18px;">
                                <path d="M12 20h9"/>
                                <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/>
                            </svg>
                        </a>
                        @endif

                        @if($appointment->status === 'confirmed')
                        <form method="POST" action="{{ route('doctor.appointments.update-status', $appointment) }}" onsubmit="return confirm('Sigur vrei să anulezi această programare?');" style="display:inline;">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="cancelled">
                            <button type="submit" class="btn btn-sm btn-danger" title="Anulează" aria-label="Anulează" style="display:inline-flex; align-items:center; justify-content:center; width:36px; height:36px; padding:0;">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:18px; height:18px;">
                                    <polyline points="3 6 5 6 21 6"/>
                                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                    <path d="M10 11v6"/>
                                    <path d="M14 11v6"/>
                                    <path d="M9 6V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/>
                                </svg>
                            </button>
                        </form>
                        @elseif($appointment->status === 'confirmed' && $appointment->appointment_date->isPast())
                        <form method="POST" action="{{ route('doctor.appointments.update-status', $appointment) }}" style="display:inline;">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="completed">
                            <button type="submit" class="btn btn-sm btn-success" title="Marchează finalizat" aria-label="Marchează finalizat" style="display:inline-flex; align-items:center; justify-content:center; width:36px; height:36px; padding:0;">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:18px; height:18px;">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        {{ $appointments->links() }}
    </div>
@else
    <div style="text-align: center; padding: 60px 20px; color: #999;">
        <p>Nu există programări {{ request()->hasAny(['status', 'date']) ? 'pentru filtrele selectate' : '' }}.</p>
    </div>
@endif
@endsection
