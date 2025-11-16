<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Programări pentru {{ date('d.m.Y', strtotime($date)) }}</title>
</head>
<body style="font-family: Arial, sans-serif; color: #222;">
    <h2 style="margin-bottom:4px;">Programări pentru {{ date('d.m.Y', strtotime($date)) }}</h2>
    <p style="margin-top:0;">{{ $doctor->full_name ?? ($doctor->title ? $doctor->title.' '.$doctor->name : $doctor->name) }}</p>

    @if($appointments->count() === 0)
        <p>Nu aveți programări pentru această zi.</p>
    @else
        <table width="100%" cellspacing="0" cellpadding="8" style="border-collapse: collapse; margin-top: 10px;">
            <thead>
                <tr style="background:#f3f4f6;">
                    <th align="left">Ora</th>
                    <th align="left">Pacient</th>
                    <th align="left">Tip</th>
                    <th align="left">Contact</th>
                </tr>
            </thead>
            <tbody>
                @foreach($appointments as $a)
                <tr style="border-bottom:1px solid #e5e7eb;">
                    <td><strong>{{ $a->appointment_time }}</strong></td>
                    <td>{{ $a->client_name }}</td>
                    <td>{{ $a->appointmentType->name ?? '-' }}</td>
                    <td>{{ $a->client_phone }} / {{ $a->client_email }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <p style="color:#6b7280; font-size:12px; margin-top:24px;">Acest email a fost generat automat.</p>
</body>
</html>
