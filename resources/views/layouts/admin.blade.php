<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - {{ config('app.name') }} Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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

        .admin-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .admin-title {
            font-size: 24px;
            font-weight: 600;
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

        .admin-container {
            max-width: 1400px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .sidebar {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }

        .sidebar-nav {
            list-style: none;
        }

        .sidebar-nav li {
            margin-bottom: 5px;
        }

        .sidebar-nav a {
            display: block;
            padding: 12px 15px;
            color: #333;
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.3s;
        }

        .sidebar-nav a:hover,
        .sidebar-nav a.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
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
            border-color: #667eea;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-check {
            display: flex;
            align-items: center;
        }

        .form-check input {
            width: auto;
            margin-right: 8px;
        }

        /* Pagination tweaks */
        .content-area nav[role="navigation"] {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 8px 0;
        }
        .content-area nav[role="navigation"] svg {
            width: 16px;
            height: 16px;
            vertical-align: middle;
        }
        .content-area nav[role="navigation"] a,
        .content-area nav[role="navigation"] span {
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 15px;
            }

            .admin-container {
                padding: 0 15px;
            }

            .content-area {
                padding: 20px 15px;
            }
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <div class="header-content">
            <div class="admin-title">📋 Admin Panel</div>
            <nav class="header-nav">
                @php($user = auth()->user())
                @if($user && ($user->role === 'receptie' || (method_exists($user, 'isReceptie') && $user->isReceptie())))
                    <a href="{{ route('receptie.dashboard') }}">Dashboard</a>
                    <a href="{{ route('receptie.appointments') }}">Programări</a>
                    <a href="{{ route('receptie.patients.index') }}">Pacienți</a>
                    <a href="{{ route('receptie.departments.index') }}">Specializari</a>
                    <a href="{{ route('receptie.doctors.index') }}">Doctori</a>
                    <a href="{{ route('home') }}" target="_blank">Vezi Site</a>
                @else
                    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                    <a href="{{ route('admin.appointments.index') }}">Programări</a>
                    <a href="{{ route('admin.departments.index') }}">Specializari</a>
                    <a href="{{ route('admin.doctors.index') }}">Doctori</a>
                    <a href="{{ route('admin.appointment-types.index') }}">Clinici</a>
                    <a href="{{ route('admin.receptie.index') }}">Recepție</a>
                    <a href="{{ route('home') }}" target="_blank">Vezi Site</a>
                @endif
                <form method="POST" action="{{ route('logout') }}" class="logout-form">
                    @csrf
                    <button type="submit" class="logout-btn">Deconectare</button>
                </form>
            </nav>
        </div>
    </header>

    <div class="admin-container">
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
</body>
</html>
