<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memento Programare</title>
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
            background: linear-gradient(135deg, #FFA726 0%, #FB8C00 100%);
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
        .reminder-box {
            background-color: #fff8e1;
            border: 2px solid #FFA726;
            border-radius: 8px;
            padding: 25px;
            margin: 25px 0;
            text-align: center;
        }
        .reminder-box .big-date {
            font-size: 48px;
            font-weight: bold;
            color: #FB8C00;
            margin: 10px 0;
        }
        .reminder-box .big-time {
            font-size: 36px;
            font-weight: bold;
            color: #F57C00;
            margin: 10px 0;
        }
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #FFA726;
            padding: 20px;
            margin: 20px 0;
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
        .alert {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⏰ Memento Programare</h1>
        </div>

        <div class="content">
            <p>Bună <strong>{{ $appointment->client_name }}</strong>,</p>

            <p>Acesta este un mesaj de memento pentru programarea ta de mâine!</p>

            <div class="reminder-box">
                <p style="margin: 0; font-size: 18px; color: #666;">Programarea ta este</p>
                <div class="big-date">MÂINE</div>
                <div class="big-time">{{ $appointment->appointment_time }}</div>
            </div>

            <div class="info-box">
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
            </div>

            <div class="alert">
                <strong>⚠️ Important:</strong> Te rugăm să ajungi cu 5 minute înainte de ora programării.
            </div>

            <p><strong>În caz că ai nevoie să anulezi sau să modifici programarea:</strong></p>
            <ul>
                <li>Te rugăm să ne contactezi cât mai curând posibil</li>
                <li>Email: {{ config('mail.from.address') }}</li>
                <li>Telefon: [Numărul tău de contact]</li>
            </ul>

            <p>Așteptăm cu plăcere să te vedem!</p>

            <p>Cu stimă,<br><strong>{{ config('app.name') }}</strong></p>
        </div>

        <div class="footer">
            <p>Acest email a fost trimis automat ca memento.</p>
            <p>{{ now()->format('d.m.Y H:i') }}</p>
        </div>
    </div>
</body>
</html>
