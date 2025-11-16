@extends('layouts.doctor')

@section('title', 'Dashboard')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <div style="display:flex; align-items:center; gap:16px;">
        <div style="width:84px; height:84px; border-radius:12px; overflow:hidden; border:2px solid #e2e8f0; background:#f8fafc; display:flex; align-items:center; justify-content:center;">
            @if(!empty($doctor?->photo))
                <img src="/{{ $doctor->photo }}" alt="Foto doctor" style="width:100%; height:100%; object-fit:cover;">
            @else
                @php($initials = collect(explode(' ', Auth::user()->name))->map(fn($p) => mb_substr($p,0,1))->take(2)->implode(''))
                <div style="font-weight:700; color:#3a6ea5; font-size:28px;">{{ $initials }}</div>
            @endif
        </div>
        <div>
            <h1 style="margin: 0; font-size: 26px; color: #2d3748;">Bine ai venit, {{ Auth::user()->name }}!</h1>
            <p style="margin: 5px 0 0 0; color: #718096;">{{ now()->locale('ro')->isoFormat('dddd, D MMMM YYYY') }}</p>
        </div>
    </div>
    <a href="{{ route('doctor.profile.edit') }}" class="btn btn-primary">✏️ Editează Profil</a>
</div>

<!-- Statistici Card-uri -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 40px;">
    <div style="background: white; border: 2px solid #e2e8f0; border-radius: 15px; padding: 25px; position: relative; overflow: hidden;">
        <div style="position: absolute; top: -10px; right: -10px; width: 80px; height: 80px; background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); border-radius: 50%; opacity: 0.1;"></div>
        <div style="font-size: 14px; color: #718096; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px;">Total Programări</div>
        <div style="font-size: 42px; font-weight: 700; color: #2d3748;">{{ $totalAppointments }}</div>
        <div style="font-size: 12px; color: #a0aec0; margin-top: 5px;">De la început</div>
    </div>

    <div style="background: white; border: 2px solid #e2e8f0; border-radius: 15px; padding: 25px; position: relative; overflow: hidden;">
        <div style="position: absolute; top: -10px; right: -10px; width: 80px; height: 80px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; opacity: 0.1;"></div>
        <div style="font-size: 14px; color: #718096; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px;">Programări Viitoare</div>
        <div style="font-size: 42px; font-weight: 700; color: #2d3748;">{{ $upcomingCount }}</div>
        <div style="font-size: 12px; color: #a0aec0; margin-top: 5px;">În așteptare</div>
    </div>

    <div style="background: white; border: 2px solid #e2e8f0; border-radius: 15px; padding: 25px; position: relative; overflow: hidden;">
        <div style="position: absolute; top: -10px; right: -10px; width: 80px; height: 80px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 50%; opacity: 0.1;"></div>
        <div style="font-size: 14px; color: #718096; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px;">Astăzi</div>
        <div style="font-size: 42px; font-weight: 700; color: #2d3748;">{{ $todayCount }}</div>
        <div style="font-size: 12px; color: #a0aec0; margin-top: 5px;">{{ now()->format('d.m.Y') }}</div>
    </div>
</div>

<!-- Link-uri rapide -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 40px;">
    <a href="{{ route('doctor.appointments') }}" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 12px; text-decoration: none; text-align: center; font-weight: 600; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
        📅 Toate Programările
    </a>
    <a href="{{ route('doctor.patients') }}" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white; padding: 20px; border-radius: 12px; text-decoration: none; text-align: center; font-weight: 600; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
        👥 Pacienții Mei
    </a>
</div>

<!-- Programări Astăzi -->
@if($todayAppointments->count() > 0)
<div style="background: white; border: 2px solid #e2e8f0; border-radius: 15px; padding: 25px; margin-bottom: 30px;">
    <h2 style="margin: 0 0 20px 0; font-size: 20px; color: #2d3748;">📍 Programări Astăzi</h2>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f7fafc; border-bottom: 2px solid #e2e8f0;">
                    <th style="padding: 12px; text-align: left; font-size: 13px; color: #4a5568; font-weight: 600;">Ora</th>
                    <th style="padding: 12px; text-align: left; font-size: 13px; color: #4a5568; font-weight: 600;">Pacient</th>
                    <th style="padding: 12px; text-align: left; font-size: 13px; color: #4a5568; font-weight: 600;">Tip</th>
                    <th style="padding: 12px; text-align: left; font-size: 13px; color: #4a5568; font-weight: 600;">Status</th>
                    <th style="padding: 12px; text-align: left; font-size: 13px; color: #4a5568; font-weight: 600;">Acțiuni</th>
                </tr>
            </thead>
            <tbody>
                @foreach($todayAppointments as $appointment)
                <tr style="border-bottom: 1px solid #e2e8f0;">
                    <td style="padding: 15px;"><strong style="font-size: 18px; color: #667eea;">{{ $appointment->appointment_time }}</strong></td>
                    <td style="padding: 15px;">
                        <div style="font-weight: 600; color: #2d3748;">{{ $appointment->client_name }}</div>
                        <div style="font-size: 13px; color: #718096;">{{ $appointment->client_phone }}</div>
                    </td>
                    <td style="padding: 15px;">{{ $appointment->appointmentType->name }}</td>
                    <td style="padding: 15px;">
                        @if($appointment->status === 'pending')
                            <span style="background: #fef3c7; color: #92400e; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">În așteptare</span>
                        @elseif($appointment->status === 'confirmed')
                            <span style="background: #d1fae5; color: #065f46; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">Confirmat</span>
                        @elseif($appointment->status === 'cancelled')
                            <span style="background: #fee2e2; color: #991b1b; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">Anulat</span>
                        @else
                            <span style="background: #e5e7eb; color: #1f2937; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">Finalizat</span>
                        @endif
                    </td>
                    <td style="padding: 15px;">
                        <div style="display: flex; gap: 8px;">
                            @if($appointment->status !== 'completed')
                            <form method="POST" action="{{ route('doctor.appointments.update-status', $appointment) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="completed">
                                <button type="submit" class="btn btn-sm btn-success">✓ Finalizat</button>
                            </form>
                            @endif
                            <a href="{{ route('doctor.appointments.edit', $appointment) }}" class="btn btn-sm btn-primary">✏️ Editează</a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<!-- Programări Viitoare -->
<div style="background: white; border: 2px solid #e2e8f0; border-radius: 15px; padding: 25px;">
    <h2 style="margin: 0 0 20px 0; font-size: 20px; color: #2d3748;">📆 Programări Viitoare (următoarele 20)</h2>

    @if($upcomingAppointments->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f7fafc; border-bottom: 2px solid #e2e8f0;">
                        <th style="padding: 12px; text-align: left; font-size: 13px; color: #4a5568; font-weight: 600;">Data</th>
                        <th style="padding: 12px; text-align: left; font-size: 13px; color: #4a5568; font-weight: 600;">Ora</th>
                        <th style="padding: 12px; text-align: left; font-size: 13px; color: #4a5568; font-weight: 600;">Pacient</th>
                        <th style="padding: 12px; text-align: left; font-size: 13px; color: #4a5568; font-weight: 600;">Tip</th>
                        <th style="padding: 12px; text-align: left; font-size: 13px; color: #4a5568; font-weight: 600;">Status</th>
                        <th style="padding: 12px; text-align: left; font-size: 13px; color: #4a5568; font-weight: 600;">Acțiuni</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($upcomingAppointments as $appointment)
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 15px;">{{ $appointment->appointment_date->format('d.m.Y') }}</td>
                        <td style="padding: 15px;"><strong style="color: #667eea;">{{ $appointment->appointment_time }}</strong></td>
                        <td style="padding: 15px;">
                            <div style="font-weight: 600; color: #2d3748;">{{ $appointment->client_name }}</div>
                            <div style="font-size: 13px; color: #718096;">{{ $appointment->client_email }}</div>
                            <div style="font-size: 13px; color: #718096;">{{ $appointment->client_phone }}</div>
                        </td>
                        <td style="padding: 15px;">{{ $appointment->appointmentType->name }}</td>
                        <td style="padding: 15px;">
                            @if($appointment->status === 'pending')
                                <span style="background: #fef3c7; color: #92400e; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">În așteptare</span>
                            @elseif($appointment->status === 'confirmed')
                                <span style="background: #d1fae5; color: #065f46; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">Confirmat</span>
                            @elseif($appointment->status === 'cancelled')
                                <span style="background: #fee2e2; color: #991b1b; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">Anulat</span>
                            @else
                                <span style="background: #e5e7eb; color: #1f2937; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">Finalizat</span>
                            @endif
                        </td>
                        <td style="padding: 15px;">
                            <div style="display: flex; gap: 8px;">
                                <a href="{{ route('doctor.appointments.edit', $appointment) }}" class="btn btn-sm btn-primary">✏️ Editează</a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p style="text-align: center; padding: 40px; color: #a0aec0;">Nu există programări viitoare.</p>
    @endif
</div>

@endsection
