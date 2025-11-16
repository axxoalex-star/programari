<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Programare Nouă</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .info-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 20px;
            margin: 20px 0;
        }
        .info-box h2 {
            margin-top: 0;
            color: #856404;
            font-size: 18px;
        }
        .detail-row {
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: bold;
            color: #6c757d;
            display: inline-block;
            width: 150px;
        }
        .detail-value {
            color: #212529;
        }
        .client-info {
            background-color: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 20px;
            margin: 20px 0;
        }
        .client-info h3 {
            margin-top: 0;
            color: #1976D2;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #f5576c;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔔 Programare Nouă Înregistrată</h1>
        </div>

        <div class="content">
            <p><strong>Atenție!</strong> O nouă programare a fost înregistrată în sistem.</p>

            <div class="info-box">
                <h2>Detalii Programare</h2>

                <div class="detail-row">
                    <span class="detail-label">Clinica:</span>
                    <span class="detail-value"><strong>{{ $appointment->appointmentType->name }}</strong></span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Departament:</span>
                    <span class="detail-value">{{ $appointment->department->name }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Doctor:</span>
                    <span class="detail-value">{{ $appointment->doctor->full_name }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Data:</span>
                    <span class="detail-value">{{ $appointment->appointment_date->format('d.m.Y') }} ({{ $appointment->appointment_date->locale('ro')->isoFormat('dddd') }})</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Ora:</span>
                    <span class="detail-value">{{ $appointment->appointment_time }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Durata:</span>
                    <span class="detail-value">{{ $appointment->doctor->consultation_duration }} minute</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value">{{ ucfirst($appointment->status) }}</span>
                </div>
            </div>

            <div class="client-info">
                <h3>Date Client</h3>

                <div class="detail-row">
                    <span class="detail-label">Nume:</span>
                    <span class="detail-value">{{ $appointment->client_name }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Email:</span>
                    <span class="detail-value"><a href="mailto:{{ $appointment->client_email }}">{{ $appointment->client_email }}</a></span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Telefon:</span>
                    <span class="detail-value"><a href="tel:{{ $appointment->client_phone }}">{{ $appointment->client_phone }}</a></span>
                </div>
            </div>

            @if($appointment->notes)
            <div class="detail-row">
                <span class="detail-label">Notițe:</span>
                <span class="detail-value">{{ $appointment->notes }}</span>
            </div>
            @endif

            <p style="margin-top: 30px;">
                <a href="{{ url('/admin/appointments/' . $appointment->id) }}" class="button">Vezi în Panou Admin</a>
            </p>
        </div>

        <div class="footer">
            <p>Notificare automată - {{ now()->format('d.m.Y H:i') }}</p>
        </div>
    </div>
</body>
</html>
