@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="page-header">
    <h1>Dashboard</h1>
    <p>Prezentare generală a programărilor</p>
</div>

<!-- Coduri de Integrare -->
<div style="background: #f8f9fa; border-radius: 10px; padding: 25px; margin-bottom: 30px; border-left: 4px solid #667eea;">
    <h2 style="margin-top: 0; color: #333; font-size: 20px; display: flex; align-items: center; gap: 10px;">
        <span style="font-size: 24px;">🔗</span> Coduri de Integrare (Shortcodes)
    </h2>
    <p style="color: #666; margin-bottom: 20px;">Copiază și lipește aceste coduri pe website-ul tău pentru a integra calendarul de programări sau pagina de login.</p>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <!-- Calendar Programări -->
        <div style="background: white; border-radius: 8px; padding: 20px; border: 1px solid #e0e0e0;">
            <h3 style="margin-top: 0; color: #667eea; font-size: 16px; margin-bottom: 10px;">📅 Calendar Programări</h3>
            <p style="font-size: 13px; color: #666; margin-bottom: 15px;">Integrează calendarul pe orice pagină HTML</p>

            <div style="position: relative;">
                <textarea id="bookingCode" readonly style="width: 100%; height: 120px; padding: 12px; font-family: 'Courier New', monospace; font-size: 12px; border: 1px solid #ddd; border-radius: 5px; background: #f5f5f5; resize: vertical;"><iframe src="{{ url('/') }}" width="100%" height="800" frameborder="0" scrolling="yes" style="border: none; border-radius: 10px;"></iframe></textarea>
                <button onclick="copyToClipboard('bookingCode')" style="position: absolute; top: 10px; right: 10px; background: #667eea; color: white; border: none; padding: 6px 12px; border-radius: 5px; cursor: pointer; font-size: 12px;">📋 Copiază</button>
            </div>
        </div>

        <!-- Pagina de Login -->
        <div style="background: white; border-radius: 8px; padding: 20px; border: 1px solid #e0e0e0;">
            <h3 style="margin-top: 0; color: #667eea; font-size: 16px; margin-bottom: 10px;">🔐 Pagina de Login</h3>
            <p style="font-size: 13px; color: #666; margin-bottom: 15px;">Integrează pagina de login pentru staff</p>

            <div style="position: relative;">
                <textarea id="loginCode" readonly style="width: 100%; height: 120px; padding: 12px; font-family: 'Courier New', monospace; font-size: 12px; border: 1px solid #ddd; border-radius: 5px; background: #f5f5f5; resize: vertical;"><iframe src="{{ url('/login') }}" width="100%" height="600" frameborder="0" scrolling="no" style="border: none; border-radius: 10px;"></iframe></textarea>
                <button onclick="copyToClipboard('loginCode')" style="position: absolute; top: 10px; right: 10px; background: #667eea; color: white; border: none; padding: 6px 12px; border-radius: 5px; cursor: pointer; font-size: 12px;">📋 Copiază</button>
            </div>
        </div>
    </div>

    <div style="margin-top: 20px; padding: 15px; background: #fff3cd; border-radius: 8px; border-left: 4px solid #ffc107;">
        <strong style="color: #856404;">💡 Instrucțiuni de folosire:</strong>
        <ul style="margin: 10px 0 0 20px; color: #856404; font-size: 13px;">
            <li>Click pe butonul <strong>"📋 Copiază"</strong> pentru a copia codul</li>
            <li>Lipește codul în pagina HTML unde vrei să apară calendarul sau login-ul</li>
            <li>Poți ajusta <code>width</code> și <code>height</code> după necesități</li>
            <li>Link direct calendar: <a href="{{ url('/') }}" target="_blank" style="color: #667eea;">{{ url('/') }}</a></li>
            <li>Link direct login: <a href="{{ url('/login') }}" target="_blank" style="color: #667eea;">{{ url('/login') }}</a></li>
        </ul>
    </div>
</div>

<script>
function copyToClipboard(elementId) {
    const textarea = document.getElementById(elementId);
    textarea.select();
    textarea.setSelectionRange(0, 99999); // For mobile

    try {
        document.execCommand('copy');

        // Visual feedback
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = '✅ Copiat!';
        button.style.background = '#28a745';

        setTimeout(() => {
            button.innerHTML = originalText;
            button.style.background = '#667eea';
        }, 2000);
    } catch (err) {
        alert('Eroare la copiere. Te rog copiază manual.');
    }
}
</script>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 40px;">
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 25px; border-radius: 10px; color: white;">
        <div style="font-size: 14px; opacity: 0.9; margin-bottom: 5px;">Total Programări</div>
        <div style="font-size: 36px; font-weight: bold;">{{ $totalAppointments }}</div>
    </div>

    <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); padding: 25px; border-radius: 10px; color: white;">
        <div style="font-size: 14px; opacity: 0.9; margin-bottom: 5px;">Programări Viitoare</div>
        <div style="font-size: 36px; font-weight: bold;">{{ $upcomingCount }}</div>
    </div>

    <div style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); padding: 25px; border-radius: 10px; color: white;">
        <div style="font-size: 14px; opacity: 0.9; margin-bottom: 5px;">Departamente Active</div>
        <div style="font-size: 36px; font-weight: bold;">{{ $totalDepartments }}</div>
    </div>

    <div style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); padding: 25px; border-radius: 10px; color: white;">
        <div style="font-size: 14px; opacity: 0.9; margin-bottom: 5px;">Doctori Activi</div>
        <div style="font-size: 36px; font-weight: bold;">{{ $totalDoctors }}</div>
    </div>
</div>

<div class="page-header">
    <h2>Programări Viitoare</h2>
</div>

@if($upcomingAppointments->count() > 0)
    <table>
        <thead>
            <tr>
                <th>Client</th>
                <th>Departament / Doctor</th>
                <th>Data</th>
                <th>Ora</th>
                <th>Status</th>
                <th>Acțiuni</th>
            </tr>
        </thead>
        <tbody>
            @foreach($upcomingAppointments as $appointment)
            <tr>
                <td><strong>{{ $appointment->client_name }}</strong><br>
                    <small>{{ $appointment->client_email }}</small>
                </td>
                <td>
                    {{ $appointment->department->name }}<br>
                    <small>{{ $appointment->doctor->full_name }}</small>
                </td>
                <td>{{ $appointment->appointment_date->format('d.m.Y') }}</td>
                <td><strong>{{ $appointment->appointment_time }}</strong></td>
                <td>
                    @if($appointment->status === 'confirmed')
                        <span style="background: #28a745; color: #fff; padding: 4px 10px; border-radius: 20px; font-size: 12px;">Confirmat</span>
                    @elseif($appointment->status === 'cancelled')
                        <span style="background: #dc3545; color: #fff; padding: 4px 10px; border-radius: 20px; font-size: 12px;">Anulat</span>
                    @else
                        <span style="background: #6c757d; color: #fff; padding: 4px 10px; border-radius: 20px; font-size: 12px;">{{ ucfirst($appointment->status) }}</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('admin.appointments.edit', $appointment) }}" class="btn btn-primary btn-sm">Editează</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
@else
    <p style="text-align: center; padding: 40px; color: #666;">Nu există programări viitoare.</p>
@endif

<div style="margin-top: 30px; text-align: center;">
    <a href="{{ route('admin.appointments.index') }}" class="btn btn-primary">Vezi Toate Programările</a>
</div>
@endsection
