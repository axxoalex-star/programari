@extends('layouts.doctor')

@section('title', 'Editează Profil')

@section('content')
<div class="page-header">
    <h1>Editează Profil</h1>
    <p>Actualizează informațiile contului tău</p>
</div>

<form method="POST" action="{{ route('doctor.profile.update') }}" enctype="multipart/form-data" style="max-width: 800px;">
    @csrf
    @method('PUT')

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
        <div class="form-group">
            <label for="title">Titlu *</label>
            <input type="text" id="title" name="title" value="{{ old('title', $doctor->title) }}" required>
            @error('title')<span style="color:#dc3545; font-size:13px;">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
            <label for="name">Nume *</label>
            <input type="text" id="name" name="name" value="{{ old('name', $doctor->name) }}" required>
            @error('name')<span style="color:#dc3545; font-size:13px;">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" value="{{ old('email', $doctor->email) }}" required>
            @error('email')<span style="color:#dc3545; font-size:13px;">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
            <label for="phone">Telefon *</label>
            <input type="text" id="phone" name="phone" value="{{ old('phone', $doctor->phone) }}" required>
            @error('phone')<span style="color:#dc3545; font-size:13px;">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label for="consultation_duration">Durată consultație (minute) *</label>
            <input type="number" id="consultation_duration" name="consultation_duration" min="15" max="180" value="{{ old('consultation_duration', $doctor->consultation_duration) }}" required>
            @error('consultation_duration')<span style="color:#dc3545; font-size:13px;">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
            <label for="consultation_price">Preț consultație *</label>
            <input type="number" step="0.01" id="consultation_price" name="consultation_price" value="{{ old('consultation_price', $doctor->consultation_price) }}" required>
            @error('consultation_price')<span style="color:#dc3545; font-size:13px;">{{ $message }}</span>@enderror
        </div>

        <div class="form-group" style="grid-column: span 2;">
            <label for="photo">Fotografie</label>
            <input type="file" id="photo" name="photo" accept="image/*">
            @if($doctor->photo)
                <div style="margin-top:10px;">
                    <img src="/{{ $doctor->photo }}" alt="Foto" style="height:80px;border-radius:8px;border:1px solid #e5e7eb;">
                </div>
            @endif
            @error('photo')<span style="color:#dc3545; font-size:13px;">{{ $message }}</span>@enderror
        </div>
    </div>

    <div class="page-header" style="margin-top:30px;">
        <h1 style="font-size:20px;">Preferințe notificări</h1>
        <p>Setări pentru emailul zilnic cu programările de a doua zi</p>
    </div>

    <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px;">
        <div class="form-group">
            <label for="receive_next_day_email">Primește email zilnic</label>
            <select id="receive_next_day_email" name="receive_next_day_email">
                <option value="0" {{ old('receive_next_day_email', $doctor->receive_next_day_email ?? false) ? '' : 'selected' }}>Nu</option>
                <option value="1" {{ old('receive_next_day_email', $doctor->receive_next_day_email ?? false) ? 'selected' : '' }}>Da</option>
            </select>
        </div>
        <div class="form-group">
            <label for="next_day_email_hour">Ora trimitere (0-23)</label>
            <input type="number" min="0" max="23" id="next_day_email_hour" name="next_day_email_hour" value="{{ old('next_day_email_hour', $doctor->next_day_email_hour ?? 7) }}">
        </div>
        <div class="form-group">
            <label for="notification_email">Email notificări (opțional)</label>
            <input type="email" id="notification_email" name="notification_email" value="{{ old('notification_email', $doctor->notification_email ?? $doctor->email) }}">
        </div>
    </div>

    <div class="page-header" style="margin-top:30px;">
        <h1 style="font-size:20px;">Zile de sărbătoare legală / Concedii</h1>
        <p>Selectează una sau mai multe zile în care NU lucrați. Aceste zile vor fi excluse din programări.</p>
    </div>

    @php
        // Get holidays from database as array of date strings
        $existingHolidays = $doctor->holidays->pluck('holiday_date')
            ->map(fn($date) => \Carbon\Carbon::parse($date)->format('Y-m-d'))
            ->toArray();
    @endphp

    <input type="hidden" id="holiday_dates" name="holiday_dates" value="{{ old('holiday_dates', implode(',', $existingHolidays)) }}">

    <div style="background: #f8f9fa; border-radius: 10px; padding: 20px; margin-bottom: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h3 style="margin: 0; font-size: 16px; color: #333;">📅 Selectează zi liberă</h3>
            <div style="display: flex; gap: 10px;">
                <button type="button" id="prevMonth" class="btn btn-sm btn-secondary">‹</button>
                <button type="button" id="nextMonth" class="btn btn-sm btn-secondary">›</button>
            </div>
        </div>

        <!-- Single Calendar -->
        <div id="holidayCalendar" style="background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); max-width: 560px; margin: 0 auto;"></div>

        <div style="text-align: center; margin-top: 15px;">
            <button type="button" id="addSelectedDates" class="btn btn-primary" disabled>+ Adaugă <span id="selectedCount"></span></button>
            <button type="button" id="clearSelection" class="btn btn-secondary" style="display: none;">Anulează selecția</button>
        </div>
    </div>

    <!-- Zile libere list -->
    <div style="margin-top: 20px;">
        <h3 style="font-size: 16px; color: #333; margin-bottom: 10px;">Zile libere configurate:</h3>
        <div id="holidaysList" style="background: white; border-radius: 10px; padding: 20px; border: 1px solid #e5e7eb; min-height: 100px;">
            @if($doctor->holidays->count() > 0)
                <!-- Will be populated by JavaScript -->
            @else
                <p style="color: #999; text-align: center; margin: 20px 0;">Nu există zile libere configurate încă.</p>
            @endif
        </div>
    </div>

    @error('holiday_dates')<span style="color:#dc3545; font-size:13px;">{{ $message }}</span>@enderror

<style>
.holiday-calendar-header {
    text-align: center;
    font-weight: 700;
    font-size: 18px;
    margin-bottom: 15px;
    color: #333;
    padding-bottom: 10px;
    border-bottom: 2px solid #667eea;
}

.holiday-calendar-weekdays {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 8px;
    margin-bottom: 10px;
}

.holiday-weekday {
    text-align: center;
    font-size: 12px;
    font-weight: 600;
    color: #666;
    padding: 8px 0;
}

.holiday-calendar-days {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 8px;
}

.holiday-day {
    aspect-ratio: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s;
    border: 1px solid transparent;
    background: white;
}

.holiday-day:hover:not(.empty):not(.past) {
    background: #f5f5f5;
    transform: scale(1.05);
}

.holiday-day.empty {
    cursor: default;
}

.holiday-day.past {
    color: #ccc;
    cursor: not-allowed;
}

.holiday-day.weekend {
    background: #fef3c7;
}

.holiday-day.today {
    border: 2px solid #f59e0b;
    font-weight: 600;
}

.holiday-day.selected-temp {
    background: #667eea;
    color: white;
    font-weight: 600;
    border-color: #667eea;
}

.holiday-day.already-selected {
    background: #e0e0e0;
    color: #999;
    cursor: not-allowed;
    text-decoration: line-through;
}

.holiday-item {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin: 4px;
    padding: 6px 12px;
    background: #667eea;
    color: white;
    border-radius: 6px;
    font-size: 12px;
    transition: all 0.2s;
}

.holiday-item:hover {
    background: #5568d3;
}

.holiday-item-date {
    font-weight: 600;
}

.holiday-item-remove {
    background: rgba(255,255,255,0.2);
    border: none;
    color: white;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    transition: all 0.2s;
}

.holiday-item-remove:hover {
    background: rgba(255,255,255,0.3);
    transform: scale(1.1);
}
</style>

<script>
let savedHolidays = new Set(@json($existingHolidays));
let currentDate = new Date();
let selectedDatesTemp = new Set(); // Changed from single to multiple

const romanianDays = ['Lu', 'Ma', 'Mi', 'Jo', 'Vi', 'Sb', 'Du'];
const romanianMonths = [
    'Ianuarie', 'Februarie', 'Martie', 'Aprilie', 'Mai', 'Iunie',
    'Iulie', 'August', 'Septembrie', 'Octombrie', 'Noiembrie', 'Decembrie'
];

function renderCalendar() {
    const container = document.getElementById('holidayCalendar');
    container.innerHTML = '';

    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();

    // Header
    const header = document.createElement('div');
    header.className = 'holiday-calendar-header';
    header.textContent = `${romanianMonths[month]} ${year}`;
    container.appendChild(header);

    // Weekdays
    const weekdays = document.createElement('div');
    weekdays.className = 'holiday-calendar-weekdays';
    romanianDays.forEach(day => {
        const dayHeader = document.createElement('div');
        dayHeader.className = 'holiday-weekday';
        dayHeader.textContent = day;
        weekdays.appendChild(dayHeader);
    });
    container.appendChild(weekdays);

    // Days grid
    const daysGrid = document.createElement('div');
    daysGrid.className = 'holiday-calendar-days';

    // Get first day of month (0 = Sunday, adjust to Monday = 0)
    const firstDay = new Date(year, month, 1).getDay();
    const adjustedFirstDay = firstDay === 0 ? 6 : firstDay - 1;

    // Days in month
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    // Empty cells before first day
    for (let i = 0; i < adjustedFirstDay; i++) {
        const emptyDiv = document.createElement('div');
        emptyDiv.className = 'holiday-day empty';
        daysGrid.appendChild(emptyDiv);
    }

    // Days
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    for (let day = 1; day <= daysInMonth; day++) {
        const dayDiv = document.createElement('div');
        dayDiv.className = 'holiday-day';
        dayDiv.textContent = day;

        const dateObj = new Date(year, month, day);
        const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const dayOfWeek = dateObj.getDay();

        // Past days (disabled)
        if (dateObj < today) {
            dayDiv.classList.add('past');
        }

        // Weekend styling
        if (dayOfWeek === 0 || dayOfWeek === 6) {
            dayDiv.classList.add('weekend');
        }

        // Today styling
        if (year === today.getFullYear() && month === today.getMonth() && day === today.getDate()) {
            dayDiv.classList.add('today');
        }

        // Already selected (saved)
        if (savedHolidays.has(dateStr)) {
            dayDiv.classList.add('already-selected');
            dayDiv.title = 'Deja adăugată';
        }

        // Temporary selection (multiple)
        if (selectedDatesTemp.has(dateStr)) {
            dayDiv.classList.add('selected-temp');
        }

        // Click handler
        if (dateObj >= today && !savedHolidays.has(dateStr)) {
            dayDiv.onclick = () => toggleDateSelection(dateStr);
        }

        daysGrid.appendChild(dayDiv);
    }

    container.appendChild(daysGrid);

    // Update button state
    updateButtonState();
}

function toggleDateSelection(dateStr) {
    if (selectedDatesTemp.has(dateStr)) {
        selectedDatesTemp.delete(dateStr);
    } else {
        selectedDatesTemp.add(dateStr);
    }
    renderCalendar();
}

function updateButtonState() {
    const count = selectedDatesTemp.size;
    const addBtn = document.getElementById('addSelectedDates');
    const clearBtn = document.getElementById('clearSelection');
    const countSpan = document.getElementById('selectedCount');

    if (count === 0) {
        addBtn.disabled = true;
        clearBtn.style.display = 'none';
        countSpan.textContent = '';
    } else {
        addBtn.disabled = false;
        clearBtn.style.display = 'inline-block';
        if (count === 1) {
            countSpan.textContent = '1 zi liberă';
        } else {
            countSpan.textContent = count + ' zile libere';
        }
    }
}

function clearSelection() {
    selectedDatesTemp.clear();
    renderCalendar();
}

function addHolidays() {
    if (selectedDatesTemp.size === 0) return;

    // Add all selected dates to saved holidays
    selectedDatesTemp.forEach(dateStr => {
        savedHolidays.add(dateStr);
    });

    // Clear temporary selection
    selectedDatesTemp.clear();

    // Update hidden input
    updateHiddenInput();

    // Re-render
    renderCalendar();
    renderHolidaysList();
}

function removeHoliday(dateStr) {
    savedHolidays.delete(dateStr);
    updateHiddenInput();
    renderCalendar();
    renderHolidaysList();
}

function updateHiddenInput() {
    document.getElementById('holiday_dates').value = Array.from(savedHolidays).sort().join(',');
}

function renderHolidaysList() {
    const container = document.getElementById('holidaysList');

    if (savedHolidays.size === 0) {
        container.innerHTML = '<p style="color: #999; text-align: center; margin: 20px 0;">Nu există zile libere configurate încă.</p>';
        return;
    }

    // Filter out past dates and sort
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    const futureDates = Array.from(savedHolidays).filter(dateStr => {
        const date = new Date(dateStr + 'T00:00:00');
        return date >= today;
    }).sort();

    // Update savedHolidays to only include future dates
    savedHolidays = new Set(futureDates);
    updateHiddenInput();

    if (futureDates.length === 0) {
        container.innerHTML = '<p style="color: #999; text-align: center; margin: 20px 0;">Nu există zile libere configurate încă.</p>';
        return;
    }

    container.innerHTML = futureDates.map(dateStr => {
        const date = new Date(dateStr + 'T00:00:00');
        const day = date.getDate();
        const month = romanianMonths[date.getMonth()];
        const year = date.getFullYear();
        const dayOfWeek = romanianDays[date.getDay() === 0 ? 6 : date.getDay() - 1];

        return `
            <div class="holiday-item">
                <span class="holiday-item-date">${day} ${month} ${year}</span>
                <button type="button" class="holiday-item-remove" onclick="removeHoliday('${dateStr}')" title="Șterge">×</button>
            </div>
        `;
    }).join('');
}

document.getElementById('prevMonth').addEventListener('click', () => {
    currentDate.setMonth(currentDate.getMonth() - 1);
    renderCalendar();
});

document.getElementById('nextMonth').addEventListener('click', () => {
    currentDate.setMonth(currentDate.getMonth() + 1);
    renderCalendar();
});

document.getElementById('addSelectedDates').addEventListener('click', addHolidays);
document.getElementById('clearSelection').addEventListener('click', clearSelection);

// Initial render
renderCalendar();
renderHolidaysList();
</script>

    <div style="display:flex; gap:10px; margin-top:20px;">
        <button class="btn btn-primary" type="submit">Salvează</button>
        <a href="{{ route('doctor.dashboard') }}" class="btn btn-secondary">Anulează</a>
    </div>
</form>
@endsection
