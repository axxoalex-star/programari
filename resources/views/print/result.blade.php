<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8" />
    <title>Rezultat Consultație</title>
    <style>
        body { font-family: Arial, sans-serif; color:#111; }
        .page { max-width: 800px; margin: 20px auto; padding: 24px; border:1px solid #e5e7eb; border-radius:8px; }
        h1 { margin:0 0 6px 0; font-size:22px; }
        .meta { color:#555; font-size:13px; margin-bottom:16px; }
        .row { display:flex; gap:16px; }
        .col { flex:1; }
        table { width:100%; border-collapse:collapse; margin-top:12px; }
        td, th { border:1px solid #e5e7eb; padding:8px; text-align:left; }
        .actions { margin-top:16px; }
        @media print { .actions { display:none; } }
    </style>
</head>
<body>
<div class="page">
    <h1>Rezultat Consultație</h1>
    <div class="meta">Data imprimare: {{ now()->format('d.m.Y H:i') }}</div>

    <div class="row">
        <div class="col">
            <h3>Pacient</h3>
            <div>{{ $appointment->client_name }}</div>
            <div>{{ $appointment->client_email }}</div>
            <div>{{ $appointment->client_phone }}</div>
        </div>
        <div class="col">
            <h3>Doctor</h3>
            <div>{{ $doctor->full_name }}</div>
            <div>Departament: {{ $appointment->department->name }}</div>
        </div>
    </div>

    <table>
        <tr>
            <th>Data</th>
            <td>{{ $appointment->appointment_date->format('d.m.Y') }}</td>
        </tr>
        <tr>
            <th>Ora</th>
            <td>{{ $appointment->appointment_time }}</td>
        </tr>
        <tr>
            <th>Clinica</th>
            <td>{{ $appointment->appointmentType->name }}</td>
        </tr>
        <tr>
            <th>Note</th>
            <td style="white-space:pre-wrap;">{{ $appointment->notes ?: '-' }}</td>
        </tr>
    </table>

    <div class="actions">
        <button onclick="window.print()">Printează</button>
    </div>
</div>
</body>
</html>
