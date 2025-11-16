<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Alege clinica - {{ config('app.name') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f6f6f6;
            min-height: 100vh;
            padding: 0;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .header {
            position: relative;
            text-align: left;
            color: #fff;
            margin-bottom: 30px;
            background-image: linear-gradient(rgba(0,0,0,0.35), rgba(0,0,0,0.35)), url('https://images.unsplash.com/photo-1580281657523-47e42b1f0a2b?q=80&w=1600&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            padding: 60px 20px;
        }

        .header h1 {
            font-size: 40px;
            margin-bottom: 8px;
        }

        .header p {
            font-size: 18px;
            opacity: 0.95;
        }

        .main-content {
            display: grid;
            grid-template-columns: 360px 1fr;
            gap: 25px;
            margin: -40px auto 30px;
            max-width: 1200px;
            padding: 0 20px;
        }

        .filters-panel {
            background: #fff;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.12);
            height: fit-content;
            position: sticky;
            top: 20px;
            border: 1px solid #eee;
        }

        .filters-panel h2 {
            color: #c00000;
            margin-bottom: 25px;
            font-size: 22px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #c00000;
            box-shadow: 0 0 0 3px rgba(192, 0, 0, 0.1);
        }


        .btn-search {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #c00000 0%, #ef233c 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }

        .btn-search:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(239, 35, 60, 0.35);
        }

        .btn-search:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .results-panel {
            background: #fff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.12);
            min-height: 500px;
            border: 1px solid #eee;
        }

        .results-panel h2 {
            color: #c00000;
            margin-bottom: 20px;
            font-size: 22px;
        }

        /* Calendar + Time Slots Layout */
        .calendar-container {
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        /* Calendar Styles */
        .calendar {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #e5e7eb;
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .calendar-header h3 {
            font-size: 16px;
            font-weight: 700;
            color: #333;
            margin: 0;
        }

        .calendar-nav {
            display: flex;
            gap: 10px;
        }

        .calendar-nav button {
            background: #f5f5f5;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .calendar-nav button:hover {
            background: #e0e0e0;
        }

        .calendar-weekdays {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 8px;
            margin-bottom: 10px;
        }

        .calendar-weekday {
            text-align: center;
            font-size: 12px;
            font-weight: 600;
            color: #666;
            padding: 8px 0;
        }

        .calendar-days {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 8px;
        }

        .calendar-day {
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid transparent;
        }

        .calendar-day:not(.disabled):not(.empty):hover {
            background: #f5f5f5;
        }

        .calendar-day.disabled {
            color: #ccc;
            cursor: not-allowed;
        }

        .calendar-day.empty {
            cursor: default;
        }

        .calendar-day.has-slots {
            background: #e8f5e9;
            color: #2e7d32;
            font-weight: 600;
        }

        .calendar-day.selected {
            background: linear-gradient(135deg, #c00000 0%, #ef233c 100%);
            color: #fff;
            font-weight: 700;
        }

        .calendar-day.today {
            border-color: #c00000;
        }

        /* Time Slots Styles */
        .time-slots {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #e5e7eb;
        }

        .time-slots-header {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .time-slots-header h3 {
            font-size: 16px;
            font-weight: 700;
            color: #333;
            margin: 0 0 5px 0;
        }

        .time-slots-header p {
            font-size: 14px;
            color: #666;
            margin: 0;
        }

        .time-slots-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }

        .time-slot-btn {
            background: #fff;
            border: 2px solid #c00000;
            color: #c00000;
            padding: 12px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s;
            text-align: center;
        }

        .time-slot-btn:hover {
            background: #c00000;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(192, 0, 0, 0.2);
        }

        .time-slot-btn.selected {
            background: linear-gradient(135deg, #c00000 0%, #ef233c 100%);
            border-color: #c00000;
            color: #fff;
        }

        @media (max-width: 768px) {
            .calendar-container {
                grid-template-columns: 1fr;
            }

            .time-slots-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        .notification {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            display: none;
        }

        .notification.info {
            background-color: #e3f2fd;
            border-left: 4px solid #2196f3;
            color: #1565c0;
        }

        .notification.success {
            background-color: #e8f5e9;
            border-left: 4px solid #4caf50;
            color: #2e7d32;
        }

        .notification.error {
            background-color: #ffebee;
            border-left: 4px solid #f44336;
            color: #c62828;
        }

        .notification.show {
            display: block;
        }

        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .results-table thead {
            background-color: #f8f9fa;
        }

        .results-table th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #495057;
            border-bottom: 2px solid #dee2e6;
        }

        .results-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #e9ecef;
        }

        .results-table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .btn-select-slot {
            background: linear-gradient(135deg, #c00000 0%, #ef233c 100%);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-select-slot:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(239, 35, 60, 0.35);
        }

        .btn-select-slot.selected {
            background: #28a745;
        }

        .time-slot-btn {
            background: #fff;
            border: 1px solid #c00000;
            color: #c00000;
            padding: 8px 10px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 12px;
            line-height: 1;
            transition: all 0.2s;
        }

        .time-slot-btn:hover {
            background: #c00000;
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(192, 0, 0, 0.2);
        }

        .time-slot-btn.selected {
            background: linear-gradient(135deg, #c00000 0%, #ef233c 100%);
            border-color: #c00000;
            color: #fff;
        }

        .btn-pagination {
            background: linear-gradient(135deg, #c00000 0%, #ef233c 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s;
        }

        .btn-pagination:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(239, 35, 60, 0.35);
        }

        .btn-pagination:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .booking-form {
            border-top: 2px solid #f0f0f0;
            padding-top: 25px;
            display: none;
        }

        .booking-form.show {
            display: block;
        }

        .booking-form h3 {
            color: #333;
            margin-bottom: 20px;
            font-size: 20px;
        }

        .booking-form .form-group {
            margin-bottom: 20px;
        }

        .checkbox-group {
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .checkbox-group input[type="checkbox"] {
            width: auto;
            margin-top: 4px;
        }

        .checkbox-group label {
            font-weight: normal;
            font-size: 13px;
            color: #666;
            line-height: 1.5;
        }

        .btn-confirm {
            background: linear-gradient(135deg, #c00000 0%, #ef233c 100%);
            color: white;
            border: none;
            padding: 14px 40px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-confirm:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(236, 72, 153, 0.4);
        }

        .btn-confirm:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .loading {
            text-align: center;
            padding: 40px;
            color: #666;
        }

        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }

        .empty-state svg {
            width: 80px;
            height: 80px;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .admin-link {
            text-align: center;
            margin-top: 30px;
        }

        .admin-link a {
            color: white;
            opacity: 0.8;
            text-decoration: none;
            font-size: 14px;
        }

        .admin-link a:hover {
            opacity: 1;
        }

        @media (max-width: 992px) {
            .main-content {
                grid-template-columns: 1fr;
            }

            .filters-panel {
                position: static;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📋 Alege clinica</h1>
            <p>Alege data și departamentul dorit pentru a găsi locuri disponibile</p>
        </div>

        <div class="main-content">
            <!-- Panel Stânga - Filtre -->
            <div class="filters-panel">
                <h2>🔍 Criterii de căutare</h2>

                <form id="search-form">
                    <!-- Hidden date fields - auto-filled by JavaScript -->
                    <input type="hidden" id="date-from" name="date_from">
                    <input type="hidden" id="date-to" name="date_to">

                    <div class="form-group">
                        <label>Clinica</label>
                        <select id="appointment-type" name="appointment_type_id" required>
                            <option value="">Selectează clinica</option>
                            @foreach($appointmentTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Departament</label>
                        <select id="department" name="department_id" required>
                            <option value="">Selectează departament</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Medic (opțional)</label>
                        <select id="doctor" name="doctor_id">
                            <option value="">Toți medicii</option>
                        </select>
                    </div>

                    <button type="submit" class="btn-search" id="btn-search">
                        🔎 CAUTĂ LOCURI DISPONIBILE
                    </button>
                </form>
            </div>

            <!-- Panel Dreapta - Rezultate -->
            <div class="results-panel">
                <h2>Rezultate căutare</h2>

                <!-- Notificări -->
                <div id="notification" class="notification"></div>

                <!-- Calendar + Time Slots -->
                <div id="results-container" style="display: none;">
                    <div class="calendar-container">
                        <!-- Calendar -->
                        <div class="calendar">
                            <div class="calendar-header">
                                <h3 id="calendar-month-year">noiembrie 2025</h3>
                                <div class="calendar-nav">
                                    <button type="button" id="prev-month">◀</button>
                                    <button type="button" id="next-month">▶</button>
                                </div>
                            </div>
                            <div class="calendar-weekdays">
                                <div class="calendar-weekday">Lu</div>
                                <div class="calendar-weekday">Ma</div>
                                <div class="calendar-weekday">Mi</div>
                                <div class="calendar-weekday">Jo</div>
                                <div class="calendar-weekday">Vi</div>
                                <div class="calendar-weekday">Sâ</div>
                                <div class="calendar-weekday">Du</div>
                            </div>
                            <div class="calendar-days" id="calendar-days">
                                <!-- Days will be generated by JavaScript -->
                            </div>
                        </div>

                        <!-- Time Slots -->
                        <div class="time-slots" id="time-slots" style="display: none;">
                            <div class="time-slots-header">
                                <h3 id="selected-date-header">Vineri, noiembrie 21</h3>
                                <p id="available-slots-count">8 locuri disponibile</p>
                            </div>
                            <div class="time-slots-grid" id="time-slots-grid">
                                <!-- Time slots will be generated by JavaScript -->
                            </div>
                        </div>

                        <!-- Empty state for time slots -->
                        <div class="empty-state" id="time-slots-empty" style="display: none;">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 60px; height: 60px; margin: 0 auto 20px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p>Selectează o zi din calendar pentru a vedea orele disponibile</p>
                        </div>
                    </div>
                </div>

                <!-- Empty state initial -->
                <div id="empty-initial" class="empty-state">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <p>Folosește filtrele din stânga pentru a căuta locuri disponibile</p>
                </div>

                <!-- Formular confirmare -->
                <div id="booking-form" class="booking-form">
                    <h3>Finalizare programare</h3>

                    <div class="form-group">
                        <label>E-mail *</label>
                        <input type="email" id="client-email" placeholder="exemplu@email.com" required>
                    </div>

                    <div class="form-group">
                        <label>Nume complet *</label>
                        <input type="text" id="client-name" placeholder="Ion Popescu" required>
                    </div>

                    <div class="form-group">
                        <label>Telefon *</label>
                        <input type="tel" id="client-phone" placeholder="0740123456" required>
                    </div>

                    <div class="form-group checkbox-group">
                        <input type="checkbox" id="gdpr-consent" required>
                        <label for="gdpr-consent">
                            Sunt de acord cu prelucrarea datelor personale conform GDPR și cu politica de confidențialitate a clinicii.
                        </label>
                    </div>

                    <button type="button" class="btn-confirm" id="btn-confirm">
                        ✓ CONFIRMĂ PROGRAMAREA
                    </button>
                </div>
            </div>
        </div>

        <div class="admin-link">
            <a href="{{ route('login') }}">🔐 Acces Admin</a>
        </div>
    </div>

    <script>
        // State
        let selectedSlot = null;

        // Setup CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // Elements
        const searchForm = document.getElementById('search-form');
        const appointmentTypeSelect = document.getElementById('appointment-type');
        const departmentSelect = document.getElementById('department');
        const doctorSelect = document.getElementById('doctor');
        const resultsContainer = document.getElementById('results-container');
        const bookingForm = document.getElementById('booking-form');
        const notification = document.getElementById('notification');
        const btnSearch = document.getElementById('btn-search');
        const btnConfirm = document.getElementById('btn-confirm');

        // Appointment Type (Clinica) change -> Load departments
        appointmentTypeSelect.addEventListener('change', async function() {
            const appointmentTypeId = this.value;
            departmentSelect.innerHTML = '<option value="">Încărcare...</option>';
            doctorSelect.innerHTML = '<option value="">Selectează medic</option>';

            if (!appointmentTypeId) {
                departmentSelect.innerHTML = '<option value="">Selectează departament</option>';
                return;
            }

            try {
                const response = await fetch(`/booking/departments?appointment_type_id=${appointmentTypeId}`, {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const departments = await response.json();

                departmentSelect.innerHTML = '<option value="">Selectează departament</option>';
                departments.forEach(dept => {
                    const option = document.createElement('option');
                    option.value = dept.id;
                    option.textContent = dept.name;
                    departmentSelect.appendChild(option);
                });
            } catch (error) {
                console.error('Error loading departments:', error);
                showNotification('Eroare la încărcarea departamentelor', 'error');
            }
        });

        // Department change -> Load doctors
        departmentSelect.addEventListener('change', async function() {
            const departmentId = this.value;
            doctorSelect.innerHTML = '<option value="">Încărcare...</option>';

            if (!departmentId) {
                doctorSelect.innerHTML = '<option value="">Selectează medic</option>';
                return;
            }

            try {
                const response = await fetch(`/booking/doctors?department_id=${departmentId}`, {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const doctors = await response.json();

                doctorSelect.innerHTML = '<option value="">Toți medicii</option>';
                doctors.forEach(doctor => {
                    const option = document.createElement('option');
                    option.value = doctor.id;
                    option.textContent = `${doctor.title} ${doctor.name}`;
                    doctorSelect.appendChild(option);
                });
            } catch (error) {
                console.error('Error loading doctors:', error);
                showNotification('Eroare la încărcarea medicilor', 'error');
            }
        });

        // Global variables for calendar
        let currentMonth = new Date().getMonth();
        let currentYear = new Date().getFullYear();
        let allAvailableSlots = [];
        let selectedDate = null;

        // Search form submit
        searchForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            // Auto-fill hidden date fields (search next 60 days)
            const today = new Date();
            const futureDate = new Date(today);
            futureDate.setDate(today.getDate() + 60);
            document.getElementById('date-from').value = today.toISOString().split('T')[0];
            document.getElementById('date-to').value = futureDate.toISOString().split('T')[0];

            const formData = new FormData(this);
            btnSearch.disabled = true;
            btnSearch.textContent = '⏳ Căutare în curs...';

            // Hide empty state, show results container
            document.getElementById('empty-initial').style.display = 'none';
            resultsContainer.style.display = 'block';

            // Hide time slots during loading
            document.getElementById('time-slots').style.display = 'none';
            document.getElementById('time-slots-empty').style.display = 'block';

            bookingForm.classList.remove('show');
            selectedSlot = null;

            try {
                const params = new URLSearchParams(formData);
                const response = await fetch(`/booking/search-slots?${params}`, {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.slots && data.slots.length > 0) {
                    allAvailableSlots = data.slots;
                    // Set calendar to first available month
                    const firstSlotDate = new Date(data.slots[0].date);
                    currentMonth = firstSlotDate.getMonth();
                    currentYear = firstSlotDate.getFullYear();
                    displayCalendar();
                    document.getElementById('time-slots-empty').style.display = 'block';
                    showNotification(`Am găsit ${data.slots.length} locuri disponibile`, 'success');
                } else {
                    resultsContainer.style.display = 'none';
                    document.getElementById('empty-initial').style.display = 'block';
                    document.getElementById('empty-initial').innerHTML = `
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p>Nu am găsit locuri disponibile pentru criteriile selectate.<br>Te rugăm să încerci alte filtre.</p>
                    `;
                    showNotification('Nu au fost găsite locuri disponibile', 'info');
                }
            } catch (error) {
                console.error('Search error:', error);
                showNotification('Eroare la căutare. Te rugăm să încerci din nou.', 'error');
                resultsContainer.style.display = 'none';
                document.getElementById('empty-initial').style.display = 'block';
                document.getElementById('empty-initial').innerHTML = `
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p>A apărut o eroare. Te rugăm să încerci din nou.</p>
                `;
            } finally {
                btnSearch.disabled = false;
                btnSearch.textContent = '🔎 CAUTĂ LOCURI DISPONIBILE';
            }
        });

        // Calendar Functions
        const months = ['ianuarie', 'februarie', 'martie', 'aprilie', 'mai', 'iunie',
                       'iulie', 'august', 'septembrie', 'octombrie', 'noiembrie', 'decembrie'];
        const monthsFull = ['Ianuarie', 'Februarie', 'Martie', 'Aprilie', 'Mai', 'Iunie',
                           'Iulie', 'August', 'Septembrie', 'Octombrie', 'Noiembrie', 'Decembrie'];
        const dayNames = ['Duminică', 'Luni', 'Marți', 'Miercuri', 'Joi', 'Vineri', 'Sâmbătă'];
        const days = {
            'monday': 'Luni',
            'tuesday': 'Marți',
            'wednesday': 'Miercuri',
            'thursday': 'Joi',
            'friday': 'Vineri',
            'saturday': 'Sâmbătă',
            'sunday': 'Duminică'
        };

        function displayCalendar() {
            const firstDay = new Date(currentYear, currentMonth, 1);
            const lastDay = new Date(currentYear, currentMonth + 1, 0);
            const prevLastDay = new Date(currentYear, currentMonth, 0);

            const firstDayIndex = firstDay.getDay() === 0 ? 6 : firstDay.getDay() - 1; // Monday = 0
            const lastDayDate = lastDay.getDate();
            const prevLastDayDate = prevLastDay.getDate();

            document.getElementById('calendar-month-year').textContent =
                `${months[currentMonth]} ${currentYear}`;

            // Build days array
            let daysHTML = '';

            // Previous month days
            for (let x = firstDayIndex; x > 0; x--) {
                daysHTML += `<div class="calendar-day empty disabled">${prevLastDayDate - x + 1}</div>`;
            }

            // Current month days
            const today = new Date();
            for (let day = 1; day <= lastDayDate; day++) {
                const dateStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                const slotsForDay = allAvailableSlots.filter(slot => slot.date === dateStr);
                const isToday = day === today.getDate() && currentMonth === today.getMonth() && currentYear === today.getFullYear();
                const isSelected = selectedDate === dateStr;
                const isPast = new Date(currentYear, currentMonth, day) < new Date(today.getFullYear(), today.getMonth(), today.getDate());

                let classes = 'calendar-day';
                if (isPast) classes += ' disabled';
                if (isToday) classes += ' today';
                if (slotsForDay.length > 0) classes += ' has-slots';
                if (isSelected) classes += ' selected';

                const onclick = (slotsForDay.length > 0 && !isPast) ? `selectDay('${dateStr}')` : '';
                daysHTML += `<div class="${classes}" onclick="${onclick}">${day}</div>`;
            }

            document.getElementById('calendar-days').innerHTML = daysHTML;
        }

        function selectDay(dateStr) {
            selectedDate = dateStr;
            displayCalendar();

            const slotsForDay = allAvailableSlots.filter(slot => slot.date === dateStr);

            if (slotsForDay.length > 0) {
                displayTimeSlots(dateStr, slotsForDay);
            }
        }

        function displayTimeSlots(dateStr, slots) {
            const date = new Date(dateStr + 'T00:00:00');
            const dayName = dayNames[date.getDay()];
            const dayNum = date.getDate();
            const monthName = monthsFull[date.getMonth()];

            document.getElementById('selected-date-header').textContent =
                `${dayName}, ${monthName} ${dayNum}`;
            document.getElementById('available-slots-count').textContent =
                `${slots.length} ${slots.length === 1 ? 'loc disponibil' : 'locuri disponibile'}`;

            let timeSlotsHTML = '';
            slots.forEach(slot => {
                timeSlotsHTML += `
                    <button type="button" class="time-slot-btn" onclick="selectTimeSlot('${slot.date}', '${slot.time}', ${slot.doctor_id}, ${slot.department_id})">
                        ${slot.time}
                    </button>
                `;
            });

            document.getElementById('time-slots-grid').innerHTML = timeSlotsHTML;
            document.getElementById('time-slots').style.display = 'block';
            document.getElementById('time-slots-empty').style.display = 'none';
        }

        // Calendar navigation
        document.getElementById('prev-month').addEventListener('click', () => {
            currentMonth--;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            displayCalendar();
        });

        document.getElementById('next-month').addEventListener('click', () => {
            currentMonth++;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            displayCalendar();
        });

        // Select time slot
        window.selectTimeSlot = function(date, time, doctorId, departmentId) {
            // Update button states
            document.querySelectorAll('.time-slot-btn').forEach(btn => {
                btn.classList.remove('selected');
            });
            event.target.classList.add('selected');

            // Set selected slot
            selectedSlot = {
                date: date,
                time: time,
                doctor_id: doctorId,
                department_id: departmentId
            };

            // Show booking form
            bookingForm.classList.add('show');
            bookingForm.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

            const dateObj = new Date(date + 'T00:00:00');
            showNotification(`Ai selectat data: ${dateObj.toLocaleDateString('ro-RO')} la ora ${time}`, 'info');
        };

        // Confirm booking
        btnConfirm.addEventListener('click', async function() {
            if (!selectedSlot) {
                showNotification('Te rugăm să selectezi un slot', 'error');
                return;
            }

            const email = document.getElementById('client-email').value.trim();
            const name = document.getElementById('client-name').value.trim();
            const phone = document.getElementById('client-phone').value.trim();
            const gdpr = document.getElementById('gdpr-consent').checked;

            if (!email || !name || !phone) {
                showNotification('Te rugăm să completezi toate câmpurile obligatorii', 'error');
                return;
            }

            if (!gdpr) {
                showNotification('Te rugăm să accepți condițiile GDPR', 'error');
                return;
            }

            btnConfirm.disabled = true;
            btnConfirm.textContent = '⏳ Se trimite...';

            try {
                const response = await fetch('/booking/confirm', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        doctor_id: selectedSlot.doctor_id,
                        department_id: selectedSlot.department_id,
                        appointment_type_id: document.getElementById('appointment-type').value,
                        appointment_date: selectedSlot.date,
                        appointment_time: selectedSlot.time,
                        client_email: email,
                        client_name: name,
                        client_phone: phone,
                        gdpr_consent: gdpr ? '1' : '0'
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    // Redirect către pagina de succes cu detaliile programării
                    const params = new URLSearchParams({
                        date: selectedSlot.date,
                        time: selectedSlot.time,
                        doctor: selectedSlot.doctor,
                        department: selectedSlot.department
                    });
                    window.location.href = `/booking/success?${params.toString()}`;
                } else {
                    showNotification(data.error || data.message || 'Eroare la salvarea programării', 'error');
                }
            } catch (error) {
                console.error('Booking error:', error);
                showNotification('Eroare la trimiterea cererii. Te rugăm să încerci din nou.', 'error');
            } finally {
                btnConfirm.disabled = false;
                btnConfirm.textContent = '✓ CONFIRMĂ PROGRAMAREA';
            }
        });

        // Show notification
        function showNotification(message, type = 'info') {
            notification.textContent = message;
            notification.className = `notification ${type} show`;

            setTimeout(() => {
                notification.classList.remove('show');
            }, 5000);
        }
    </script>
</body>
</html>
