<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Programare Confirmată - Programare Online</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .success-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 100%;
            padding: 50px 40px;
            text-align: center;
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .success-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            animation: scaleIn 0.6s ease-out 0.3s both;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }

        .success-icon svg {
            width: 60px;
            height: 60px;
            stroke: white;
            stroke-width: 3;
            fill: none;
        }

        h1 {
            color: #2d3748;
            font-size: 32px;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .subtitle {
            color: #718096;
            font-size: 18px;
            margin-bottom: 40px;
            line-height: 1.6;
        }

        .details-box {
            background: linear-gradient(135deg, #f8f9ff 0%, #f0e6ff 100%);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            text-align: left;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid rgba(102, 126, 234, 0.2);
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: #667eea;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-value {
            color: #2d3748;
            font-weight: 700;
            font-size: 16px;
        }

        .email-notice {
            background: #fef3c7;
            border: 2px solid #fbbf24;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            display: flex;
            align-items: start;
            gap: 15px;
        }

        .email-notice svg {
            flex-shrink: 0;
            width: 24px;
            height: 24px;
            stroke: #f59e0b;
            fill: none;
        }

        .email-notice-text {
            color: #78350f;
            font-size: 15px;
            line-height: 1.6;
            text-align: left;
        }

        .countdown-box {
            background: #e0e7ff;
            border-radius: 10px;
            padding: 20px;
            margin-top: 30px;
        }

        .countdown-text {
            color: #4c1d95;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .countdown {
            color: #667eea;
            font-size: 48px;
            font-weight: 700;
            font-family: 'Courier New', monospace;
        }

        .manual-link {
            margin-top: 20px;
        }

        .manual-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s;
        }

        .manual-link a:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        @media (max-width: 640px) {
            .success-container {
                padding: 40px 25px;
            }

            h1 {
                font-size: 26px;
            }

            .subtitle {
                font-size: 16px;
            }

            .countdown {
                font-size: 36px;
            }
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">
            <svg viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
        </div>

        <h1>Programare Confirmată!</h1>
        <p class="subtitle">
            Vă mulțumim pentru alegerea noastră. Programarea dumneavoastră a fost înregistrată cu succes.
        </p>

        <div class="details-box">
            <div class="detail-row">
                <span class="detail-label">Data</span>
                <span class="detail-value">{{ \Carbon\Carbon::parse($date)->format('d.m.Y') }} ({{ \Carbon\Carbon::parse($date)->locale('ro')->isoFormat('dddd') }})</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Ora</span>
                <span class="detail-value">{{ $time }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Doctor</span>
                <span class="detail-value">{{ $doctor }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Departament</span>
                <span class="detail-value">{{ $department }}</span>
            </div>
        </div>

        <div class="email-notice">
            <svg viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            <div class="email-notice-text">
                <strong>Verificați emailul!</strong><br>
                Am trimis un email de confirmare cu toate detaliile programării. Dacă nu găsiți emailul, verificați și folderul SPAM.
            </div>
        </div>

        <div class="countdown-box">
            <div class="countdown-text">Veți fi redirecționat automat în:</div>
            <div class="countdown" id="countdown">10</div>
        </div>

        <div class="manual-link">
            <a href="{{ route('home') }}">← Înapoi la pagina principală</a>
        </div>
    </div>

    <script>
        let seconds = 10;
        const countdownElement = document.getElementById('countdown');

        const interval = setInterval(() => {
            seconds--;
            countdownElement.textContent = seconds;

            if (seconds <= 0) {
                clearInterval(interval);
                window.location.href = "{{ route('home') }}";
            }
        }, 1000);
    </script>
</body>
</html>
