# 🏥 Aplicație Programări Medicale - Laravel 11

> Sistem complet de programări online pentru clinici medicale

## 📸 Structura Aplicației

### Frontend (Pacienți):
```
1. Alege Clinica
   ↓
2. Alege Specializarea (Departamentul)
   ↓
3. Alege Medicul
   ↓
4. Alege Data și Ora (Calendar + Sloturi orare)
   ↓
5. Completează Datele (Nume, Email, Telefon)
   ↓
6. Confirmare (Email automat)
```

### Admin Panel:
- **Dashboard** - Statistici și programări viitoare
- **Clinici** - Gestionare clinici
- **Departamente** - Gestionare departamente medicale
- **Medici/Doctori** - CRUD medici (asociați la departamente)
- **Programări** - Vizualizare și gestionare programări

## 🚀 Instalare pentru Producție

```bash
# 1. Configurează .env cu datele bazei de date
cp .env.example .env
# Editează .env cu datele corecte

# 2. Instalează dependențele
composer install --optimize-autoloader --no-dev

# 3. Generează cheia aplicației
php artisan key:generate

# 4. Rulează migrările și seeders
php artisan migrate --force
php artisan db:seed --force

# 5. Optimizează pentru producție
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**Acces:**
- Frontend: /
- Admin: /login (`admin@programari.ro` / `password`)

## 📊 Date Demo După Seed

### Clinici (3):
- Clinica Stomatologica Mihai
- Clinica Veterinara X
- Clinica Dermatologie Y

### Departamente (10):
- Alergologie și Imunologie clinică
- Cardiologie
- Chirurgie Plastica-Microchirurgie Reconstructiva
- Dermatologie
- Endocrinologie
- Gastroenterologie
- Neurologie
- Ortopedie
- Pediatrie
- și altele...

### Medici (6):
- Dr. Boghian Gabriela (Alergologie)
- Dr. Frant Loredana (Alergologie)
- Prof. Dr. Ungureanu Gabriela (Alergologie)
- Dr. Popescu Ion (Cardiologie)
- Prof. Dr. Ionescu Maria (Cardiologie)
- Dr. Marinescu Ana (Pediatrie)

## 💾 Structura Bazei de Date

```sql
appointment_types   - Clinici
departments         - Departamente medicale
doctors             - Medici (department_id)
doctor_schedules    - Programul medicilor
appointments        - Programări
medical_records     - Fișe medicale
users               - Administratori și Doctori
```

## ✅ Funcționalități Implementate

- ✅ Migrări complete pentru toate tabelele
- ✅ Modele Eloquent cu toate relațiile
- ✅ Seeders cu date demo
- ✅ Sistem autentificare multi-role (admin, doctor)
- ✅ Email templates (confirmare, memento, notificări)
- ✅ Scheduleri pentru mementouri automate
- ✅ Dashboard admin complet
- ✅ Sistem programări online pentru pacienți
- ✅ Panel medic cu programări și pacienți
- ✅ Fișe medicale pentru pacienți
- ✅ Exportare rezultate în PDF

## 🔧 Comenzi Utile

```bash
# Resetare completă bază de date
php artisan migrate:fresh --seed

# Curățare cache
php artisan config:clear && php artisan cache:clear

# Vezi toate rutele
php artisan route:list

# Trimite mementouri (test)
php artisan appointments:send-reminders
```

## 📧 Configurare Email

Configurează în `.env`:
```
MAIL_MAILER=smtp
MAIL_HOST=your-mail-server.com
MAIL_PORT=465
MAIL_USERNAME=your-email@domain.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=noreply@domain.com
MAIL_FROM_NAME="${APP_NAME}"
```

## 🎯 Conturi Demo

### Admin:
- Email: `admin@programari.ro`
- Parolă: `password`
- Acces complet la toate funcționalitățile

### Doctori:
- Email: `boghian.gabriela@clinica.ro` (sau oricare doctor din seeder)
- Parolă: `password`
- Acces la programările proprii și fișe pacienți

## 🏗️ Tehnologii

- Laravel 11
- PHP 8.2+
- MySQL
- Blade Templates
- HTML/CSS Responsive
- Email SMTP

## 📝 Licență

MIT

---

**Dezvoltat pentru clinici medicale cu suport multi-oraș și specializări** 🏥
