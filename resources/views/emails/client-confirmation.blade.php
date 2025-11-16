<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmare Programare</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 20px 0;
        }
        .info-box h2 {
            margin-top: 0;
            color: #667eea;
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
            background-color: #667eea;
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
            <h1>✓ Programare Confirmată</h1>
        </div>

        <div class="content">
            <p>Bună <strong>{{ $appointment->client_name }}</strong>,</p>

            <p>Îți mulțumim că ai ales serviciile noastre! Programarea ta a fost înregistrată cu succes.</p>

            <div class="info-box">
                <h2>Detalii Programare</h2>

                <div class="detail-row">
                    <span class="detail-label">Clinica:</span>
                    <span class="detail-value">{{ $appointment->appointmentType->name }}</span>
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
                    <span class="detail-label">Durata estimată:</span>
                    <span class="detail-value">{{ $appointment->doctor->consultation_duration }} minute</span>
                </div>

                @if($appointment->doctor->consultation_price > 0)
                <div class="detail-row">
                    <span class="detail-label">Tarif consultație:</span>
                    <span class="detail-value">{{ number_format($appointment->doctor->consultation_price, 2) }} RON</span>
                </div>
                @endif
            </div>

            <p><strong>Ce trebuie să știi:</strong></p>
            <ul>
                <li>Te rugăm să ajungi cu 5 minute înainte de programare</li>
                <li>Vei primi un memento cu o zi înainte de programare</li>
                <li>Dacă ai nevoie să anulezi sau să modifici programarea, te rugăm să ne contactezi</li>
            </ul>

            <p>Dacă ai întrebări, nu ezita să ne contactezi.</p>

            <p>Cu stimă,<br><strong>{{ config('app.name') }}</strong></p>
        </div>

        <div class="footer">
            <p>Acest email a fost trimis automat. Te rugăm să nu răspunzi la acest mesaj.</p>
            <p>{{ now()->format('d.m.Y H:i') }}</p>
        </div>
    </div>
</body>
</html>
