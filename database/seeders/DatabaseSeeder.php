<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. CLINICI (trebuie create mai întâi pentru foreign key)
        $types = [
            ['id' => 1, 'name' => 'Clinica Stomatologica Mihai', 'order' => 1],
            ['id' => 2, 'name' => 'Clinica Veterinara X', 'order' => 2],
            ['id' => 3, 'name' => 'Clinica Dermatologie Y', 'order' => 3],
        ];

        foreach ($types as $type) {
            DB::table('appointment_types')->insert(array_merge($type, [
                'created_at' => now(),
                'updated_at' => now(),
                'is_active' => true,
            ]));
        }

        // 2. DEPARTAMENTE (specifice fiecărei clinici)
        $departments = [
            // Clinica Stomatologică Mihai
            ['appointment_type_id' => 1, 'name' => 'Ortodonție', 'color' => '#4ECDC4', 'order' => 1],
            ['appointment_type_id' => 1, 'name' => 'Implantologie', 'color' => '#95E1D3', 'order' => 2],
            ['appointment_type_id' => 1, 'name' => 'Endodonție', 'color' => '#F38181', 'order' => 3],
            ['appointment_type_id' => 1, 'name' => 'Parodontologie', 'color' => '#FFE66D', 'order' => 4],
            ['appointment_type_id' => 1, 'name' => 'Protetică', 'color' => '#B4E7CE', 'order' => 5],

            // Clinica Veterinară X
            ['appointment_type_id' => 2, 'name' => 'Vaccinuri', 'color' => '#FF6B6B', 'order' => 1],
            ['appointment_type_id' => 2, 'name' => 'Examene paraclinice', 'color' => '#4ECDC4', 'order' => 2],
            ['appointment_type_id' => 2, 'name' => 'Stomatologie', 'color' => '#95E1D3', 'order' => 3],
            ['appointment_type_id' => 2, 'name' => 'Chirurgie', 'color' => '#F38181', 'order' => 4],
            ['appointment_type_id' => 2, 'name' => 'Imagistică', 'color' => '#FFE66D', 'order' => 5],

            // Clinica Dermatologie Y
            ['appointment_type_id' => 3, 'name' => 'Dermatologie Estetică', 'color' => '#E0BBE4', 'order' => 1],
            ['appointment_type_id' => 3, 'name' => 'Dermatologie Medicală', 'color' => '#957DAD', 'order' => 2],
            ['appointment_type_id' => 3, 'name' => 'Tratamente Laser', 'color' => '#FEC8D8', 'order' => 3],
            ['appointment_type_id' => 3, 'name' => 'Cosmetologie', 'color' => '#FFDFD3', 'order' => 4],
            ['appointment_type_id' => 3, 'name' => 'Tratamente Acnee', 'color' => '#B4E7CE', 'order' => 5],
        ];

        foreach ($departments as $dept) {
            DB::table('departments')->insert(array_merge($dept, [
                'created_at' => now(),
                'updated_at' => now(),
                'is_active' => true,
            ]));
        }

        // 3. DOCTORI - actualizăm appointment_type_id bazat pe department_id
        DB::table('doctors')->update(['appointment_type_id' => DB::raw('(SELECT appointment_type_id FROM departments WHERE departments.id = doctors.department_id)')]);

        // Dacă nu există doctori, îi creăm
        $doctors = [
            // Alergologie
            [
                'id' => 1,
                'appointment_type_id' => 1,
                'department_id' => 1,
                'name' => 'Boghian Gabriela',
                'title' => 'Dr.',
                'email' => 'boghian.gabriela@clinica.ro',
                'phone' => '0740111222',
                'consultation_duration' => 30,
                'consultation_price' => 200,
            ],
            [
                'id' => 2,
                'appointment_type_id' => 1,
                'department_id' => 1,
                'name' => 'Frant Loredana',
                'title' => 'Dr.',
                'email' => 'frant.loredana@clinica.ro',
                'phone' => '0740111223',
                'consultation_duration' => 45,
                'consultation_price' => 250,
            ],
            [
                'id' => 3,
                'appointment_type_id' => 1,
                'department_id' => 1,
                'name' => 'Ungureanu Gabriela',
                'title' => 'Prof. Dr.',
                'email' => 'ungureanu.gabriela@clinica.ro',
                'phone' => '0740111224',
                'consultation_duration' => 60,
                'consultation_price' => 300,
            ],
            // Cardiologie
            [
                'id' => 4,
                'appointment_type_id' => 1,
                'department_id' => 2,
                'name' => 'Popescu Ion',
                'title' => 'Dr.',
                'email' => 'popescu.ion@clinica.ro',
                'phone' => '0740111225',
                'consultation_duration' => 45,
                'consultation_price' => 280,
            ],
            [
                'id' => 5,
                'appointment_type_id' => 1,
                'department_id' => 2,
                'name' => 'Ionescu Maria',
                'title' => 'Prof. Dr.',
                'email' => 'ionescu.maria@clinica.ro',
                'phone' => '0740111226',
                'consultation_duration' => 60,
                'consultation_price' => 350,
            ],
            // Pediatrie
            [
                'id' => 6,
                'appointment_type_id' => 2,
                'department_id' => 10,
                'name' => 'Marinescu Ana',
                'title' => 'Dr.',
                'email' => 'marinescu.ana@clinica.ro',
                'phone' => '0740111227',
                'consultation_duration' => 30,
                'consultation_price' => 180,
            ],
        ];

        foreach ($doctors as $doctor) {
            DB::table('doctors')->insert(array_merge($doctor, [
                'created_at' => now(),
                'updated_at' => now(),
                'is_active' => true,
            ]));
        }

        // 4. PROGRAM DOCTORI - Toți doctorii trebuie să aibă program!
        $schedules = [
            // Dr. Boghian Gabriela (id=1) - Luni-Vineri 08:00-16:00
            ['doctor_id' => 1, 'day_of_week' => 'monday', 'start_time' => '08:00', 'end_time' => '16:00'],
            ['doctor_id' => 1, 'day_of_week' => 'tuesday', 'start_time' => '08:00', 'end_time' => '16:00'],
            ['doctor_id' => 1, 'day_of_week' => 'wednesday', 'start_time' => '08:00', 'end_time' => '16:00'],
            ['doctor_id' => 1, 'day_of_week' => 'thursday', 'start_time' => '08:00', 'end_time' => '16:00'],
            ['doctor_id' => 1, 'day_of_week' => 'friday', 'start_time' => '08:00', 'end_time' => '16:00'],

            // Dr. Frant Loredana (id=2) - Luni-Sâmbătă 09:00-17:00
            ['doctor_id' => 2, 'day_of_week' => 'monday', 'start_time' => '09:00', 'end_time' => '17:00'],
            ['doctor_id' => 2, 'day_of_week' => 'tuesday', 'start_time' => '09:00', 'end_time' => '17:00'],
            ['doctor_id' => 2, 'day_of_week' => 'wednesday', 'start_time' => '09:00', 'end_time' => '17:00'],
            ['doctor_id' => 2, 'day_of_week' => 'thursday', 'start_time' => '09:00', 'end_time' => '17:00'],
            ['doctor_id' => 2, 'day_of_week' => 'friday', 'start_time' => '09:00', 'end_time' => '17:00'],
            ['doctor_id' => 2, 'day_of_week' => 'saturday', 'start_time' => '09:00', 'end_time' => '13:00'],

            // Prof. Dr. Ungureanu Gabriela (id=3) - Marți-Joi 10:00-18:00
            ['doctor_id' => 3, 'day_of_week' => 'tuesday', 'start_time' => '10:00', 'end_time' => '18:00'],
            ['doctor_id' => 3, 'day_of_week' => 'wednesday', 'start_time' => '10:00', 'end_time' => '18:00'],
            ['doctor_id' => 3, 'day_of_week' => 'thursday', 'start_time' => '10:00', 'end_time' => '18:00'],

            // Dr. Popescu Ion (id=4) - Luni-Vineri 08:00-16:00
            ['doctor_id' => 4, 'day_of_week' => 'monday', 'start_time' => '08:00', 'end_time' => '16:00'],
            ['doctor_id' => 4, 'day_of_week' => 'tuesday', 'start_time' => '08:00', 'end_time' => '16:00'],
            ['doctor_id' => 4, 'day_of_week' => 'wednesday', 'start_time' => '08:00', 'end_time' => '16:00'],
            ['doctor_id' => 4, 'day_of_week' => 'thursday', 'start_time' => '08:00', 'end_time' => '16:00'],
            ['doctor_id' => 4, 'day_of_week' => 'friday', 'start_time' => '08:00', 'end_time' => '16:00'],

            // Prof. Dr. Ionescu Maria (id=5) - Luni, Miercuri, Vineri 09:00-17:00
            ['doctor_id' => 5, 'day_of_week' => 'monday', 'start_time' => '09:00', 'end_time' => '17:00'],
            ['doctor_id' => 5, 'day_of_week' => 'wednesday', 'start_time' => '09:00', 'end_time' => '17:00'],
            ['doctor_id' => 5, 'day_of_week' => 'friday', 'start_time' => '09:00', 'end_time' => '17:00'],

            // Dr. Marinescu Ana (id=6) - Luni-Joi 10:00-18:00
            ['doctor_id' => 6, 'day_of_week' => 'monday', 'start_time' => '10:00', 'end_time' => '18:00'],
            ['doctor_id' => 6, 'day_of_week' => 'tuesday', 'start_time' => '10:00', 'end_time' => '18:00'],
            ['doctor_id' => 6, 'day_of_week' => 'wednesday', 'start_time' => '10:00', 'end_time' => '18:00'],
            ['doctor_id' => 6, 'day_of_week' => 'thursday', 'start_time' => '10:00', 'end_time' => '18:00'],
        ];

        foreach ($schedules as $schedule) {
            DB::table('doctor_schedules')->insert(array_merge($schedule, [
                'created_at' => now(),
                'updated_at' => now(),
                'is_active' => true,
            ]));
        }

        // 5. UTILIZATORI
        // Admin
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@programari.ro',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Doctori - creăm conturi pentru TOȚI doctorii
        $doctorUsers = [
            ['doctor_id' => 1, 'name' => 'Dr. Boghian Gabriela', 'email' => 'boghian.gabriela@clinica.ro'],
            ['doctor_id' => 2, 'name' => 'Dr. Frant Loredana', 'email' => 'frant.loredana@clinica.ro'],
            ['doctor_id' => 3, 'name' => 'Prof. Dr. Ungureanu Gabriela', 'email' => 'ungureanu.gabriela@clinica.ro'],
            ['doctor_id' => 4, 'name' => 'Dr. Popescu Ion', 'email' => 'popescu.ion@clinica.ro'],
            ['doctor_id' => 5, 'name' => 'Prof. Dr. Ionescu Maria', 'email' => 'ionescu.maria@clinica.ro'],
            ['doctor_id' => 6, 'name' => 'Dr. Marinescu Ana', 'email' => 'marinescu.ana@clinica.ro'],
        ];

        foreach ($doctorUsers as $doctorUser) {
            User::create([
                'name' => $doctorUser['name'],
                'email' => $doctorUser['email'],
                'password' => Hash::make('password'),
                'role' => 'doctor',
                'doctor_id' => $doctorUser['doctor_id'],
                'is_active' => true,
            ]);
        }

        // Asistentă
        User::create([
            'name' => 'Asistenta Medicală',
            'email' => 'asistenta@clinica.ro',
            'password' => Hash::make('password'),
            'role' => 'assistant',
            'is_active' => true,
        ]);

        // Recepție - câte un cont pentru fiecare clinică
        $receptieUsers = [
            ['appointment_type_id' => 1, 'name' => 'Recepție Clinica Stomatologică', 'email' => 'receptie.stomato@clinica.ro'],
            ['appointment_type_id' => 2, 'name' => 'Recepție Clinica Veterinară', 'email' => 'receptie.vet@clinica.ro'],
            ['appointment_type_id' => 3, 'name' => 'Recepție Clinica Dermatologie', 'email' => 'receptie.derm@clinica.ro'],
        ];

        foreach ($receptieUsers as $receptieUser) {
            User::create([
                'name' => $receptieUser['name'],
                'email' => $receptieUser['email'],
                'password' => Hash::make('password'),
                'role' => 'receptie',
                'appointment_type_id' => $receptieUser['appointment_type_id'],
                'is_active' => true,
            ]);
        }
    }
}
