<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Panel Doctor</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
        }

        .doctor-header {
            background: #2f3e46; /* toned-down, neutral */
            color: white;
            padding: 15px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.12);
        }

        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .doctor-title {
            font-size: 24px;
            font-weight: 600;
        }

        .doctor-name {
            font-size: 14px;
            opacity: 0.9;
            margin-top: 3px;
        }

        .header-nav {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .header-nav a {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .header-nav a:hover {
            background: rgba(255,255,255,0.2);
        }

        .logout-form {
            display: inline;
        }

        .logout-btn {
            background: rgba(255,255,255,0.2);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .logout-btn:hover {
            background: rgba(255,255,255,0.3);
        }

        .doctor-container {
            max-width: 1400px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .content-area {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .page-header {
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .page-header h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 5px;
        }

        .page-header p {
            color: #666;
            font-size: 14px;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .alert-success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        .alert-error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-primary {
            background: #3a6ea5;
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(58, 110, 165, 0.35);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-success {
            background: #28a745;
            color: white;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-warning {
            background: #ffc107;
            color: #000;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th,
        table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }

        table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }

        table tr:hover {
            background-color: #f8f9fa;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #3a6ea5;
        }

        /* Notification bell */
        .notif {
            position: relative;
        }
        .notif-btn {
            position: relative;
            background: rgba(255,255,255,0.14);
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
        }
        .notif-badge {
            position: absolute;
            top: -6px;
            right: -6px;
            background: #e63946;
            color: white;
            font-size: 11px;
            padding: 2px 6px;
            border-radius: 999px;
        }
        .notif-panel {
            position: absolute;
            right: 0;
            top: 42px;
            width: 360px;
            max-height: 420px;
            overflow: auto;
            background: white;
            color: #2d3748;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            display: none;
            z-index: 1000;
        }
        .notif-header {
            padding: 12px 16px;
            border-bottom: 1px solid #edf2f7;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .notif-item { padding: 12px 16px; border-bottom: 1px solid #f1f5f9; }
        .notif-item:last-child { border-bottom: none; }
        .notif-time { font-weight: 600; color: #3a6ea5; }
        .notif-empty { padding: 24px; text-align: center; color: #94a3b8; }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 15px;
            }

            .doctor-container {
                padding: 0 15px;
            }

            .content-area {
                padding: 20px 15px;
            }
        }
    </style>
</head>
<body>
    <header class="doctor-header">
        <div class="header-content">
            <div>
                <div class="doctor-title">Panel Doctor</div>
                <div class="doctor-name">{{ Auth::user()->name }}</div>
            </div>
            <nav class="header-nav">
                <a href="{{ route('doctor.dashboard') }}">Dashboard</a>
                <a href="{{ route('doctor.appointments') }}">Programări</a>
                <a href="{{ route('doctor.patients') }}">Pacienți</a>
                <a href="{{ route('home') }}" target="_blank">Vezi Site</a>
                <div class="notif" id="notif">
                    <?php
                        $doctorUser = Auth::user();
                        $todayList = collect();
                        $todayCount = 0;
                        if ($doctorUser && $doctorUser->role === 'doctor' && $doctorUser->doctor_id) {
                            $todayList = \App\Models\Appointment::where('doctor_id', $doctorUser->doctor_id)
                                ->whereDate('appointment_date', today())
                                ->orderBy('appointment_time')
                                ->get();
                            $todayCount = $todayList->count();
                        }
                    ?>
                    <button class="notif-btn" onclick="toggleNotif()">🔔
                        @if($todayCount > 0)
                            <span class="notif-badge">{{ $todayCount }}</span>
                        @endif
                    </button>
                    <div class="notif-panel" id="notifPanel">
                        <div class="notif-header">
                            <span>Programări azi</span>
                            <form method="POST" action="{{ route('doctor.notifications.send-next-day-email') }}">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-primary">Trimite pe email lista de mâine</button>
                            </form>
                        </div>
                        @if($todayCount === 0)
                            <div class="notif-empty">Nu ai programări astăzi.</div>
                        @else
                            @foreach($todayList as $a)
                                <div class="notif-item">
                                    <div class="notif-time">{{ $a->appointment_time }} • {{ $a->appointmentType->name ?? '—' }}</div>
                                    <div style="color:#475569; font-weight:600;">{{ $a->client_name }}</div>
                                    <div style="color:#64748b; font-size:12px;">{{ $a->client_phone }} • {{ $a->client_email }}</div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="logout-form">
                    @csrf
                    <button type="submit" class="logout-btn">Deconectare</button>
                </form>
            </nav>
        </div>
    </header>

    <div class="doctor-container">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        <div class="content-area">
            @yield('content')
        </div>
    </div>
    <script>
        function toggleNotif() {
            var p = document.getElementById('notifPanel');
            if (!p) return;
            p.style.display = (p.style.display === 'block') ? 'none' : 'block';
        }
        document.addEventListener('click', function(e){
            var n = document.getElementById('notif');
            var p = document.getElementById('notifPanel');
            if(!n || !p) return;
            if(!n.contains(e.target)) { p.style.display = 'none'; }
        });
    </script>
</body>
</html>
