<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 0 0 5px 5px;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Email Test</h1>
    </div>
    <div class="content">
        <h2>Configurare Email Reușită!</h2>
        <p>Acest email confirmă că configurarea SMTP a fost realizată cu succes.</p>
        <p><strong>Detalii configurare:</strong></p>
        <ul>
            <li>Server SMTP: mail.testaxxo.site</li>
            <li>Port: 465 (SSL)</li>
            <li>Username: admin@testaxxo.site</li>
        </ul>
        <p>Sistemul dvs. de email funcționează corect și este gata de utilizare!</p>
    </div>
    <div class="footer">
        <p>Trimis de la {{ config('app.name') }}</p>
        <p>{{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
