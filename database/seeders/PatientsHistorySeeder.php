<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PatientsHistorySeeder extends Seeder
{
    public function run(): void
    {
        // Lista cu 30 pacienți
        $patients = [
            // 4 pacienți cu istoric (vor avea mai multe programări)
            ['name' => 'Popescu Maria', 'email' => 'popescu.maria@email.ro', 'phone' => '0740111001', 'has_history' => true],
            ['name' => 'Ionescu Ion', 'email' => 'ionescu.ion@email.ro', 'phone' => '0740111002', 'has_history' => true],
            ['name' => 'Georgescu Ana', 'email' => 'georgescu.ana@email.ro', 'phone' => '0740111003', 'has_history' => true],
            ['name' => 'Dumitrescu Vasile', 'email' => 'dumitrescu.vasile@email.ro', 'phone' => '0740111004', 'has_history' => true],

            // Restul de 26 pacienți
            ['name' => 'Popa Elena', 'email' => 'popa.elena@email.ro', 'phone' => '0740111005', 'has_history' => false],
            ['name' => 'Radu George', 'email' => 'radu.george@email.ro', 'phone' => '0740111006', 'has_history' => false],
            ['name' => 'Constantin Mihai', 'email' => 'constantin.mihai@email.ro', 'phone' => '0740111007', 'has_history' => false],
            ['name' => 'Stan Andreea', 'email' => 'stan.andreea@email.ro', 'phone' => '0740111008', 'has_history' => false],
            ['name' => 'Marin Cristina', 'email' => 'marin.cristina@email.ro', 'phone' => '0740111009', 'has_history' => false],
            ['name' => 'Stoica Alexandru', 'email' => 'stoica.alexandru@email.ro', 'phone' => '0740111010', 'has_history' => false],
            ['name' => 'Dinu Ioana', 'email' => 'dinu.ioana@email.ro', 'phone' => '0740111011', 'has_history' => false],
            ['name' => 'Năstase Daniel', 'email' => 'nastase.daniel@email.ro', 'phone' => '0740111012', 'has_history' => false],
            ['name' => 'Ilie Gabriela', 'email' => 'ilie.gabriela@email.ro', 'phone' => '0740111013', 'has_history' => false],
            ['name' => 'Stan Florin', 'email' => 'stan.florin@email.ro', 'phone' => '0740111014', 'has_history' => false],
            ['name' => 'Munteanu Laura', 'email' => 'munteanu.laura@email.ro', 'phone' => '0740111015', 'has_history' => false],
            ['name' => 'Nedelcu Adrian', 'email' => 'nedelcu.adrian@email.ro', 'phone' => '0740111016', 'has_history' => false],
            ['name' => 'Tudor Raluca', 'email' => 'tudor.raluca@email.ro', 'phone' => '0740111017', 'has_history' => false],
            ['name' => 'Barbu Marius', 'email' => 'barbu.marius@email.ro', 'phone' => '0740111018', 'has_history' => false],
            ['name' => 'Matei Simona', 'email' => 'matei.simona@email.ro', 'phone' => '0740111019', 'has_history' => false],
            ['name' => 'Cojocaru Bogdan', 'email' => 'cojocaru.bogdan@email.ro', 'phone' => '0740111020', 'has_history' => false],
            ['name' => 'Stanciu Monica', 'email' => 'stanciu.monica@email.ro', 'phone' => '0740111021', 'has_history' => false],
            ['name' => 'Rusu Claudiu', 'email' => 'rusu.claudiu@email.ro', 'phone' => '0740111022', 'has_history' => false],
            ['name' => 'Dragomir Alina', 'email' => 'dragomir.alina@email.ro', 'phone' => '0740111023', 'has_history' => false],
            ['name' => 'Enache Robert', 'email' => 'enache.robert@email.ro', 'phone' => '0740111024', 'has_history' => false],
            ['name' => 'Vlad Oana', 'email' => 'vlad.oana@email.ro', 'phone' => '0740111025', 'has_history' => false],
            ['name' => 'Luca Răzvan', 'email' => 'luca.razvan@email.ro', 'phone' => '0740111026', 'has_history' => false],
            ['name' => 'Petre Nicoleta', 'email' => 'petre.nicoleta@email.ro', 'phone' => '0740111027', 'has_history' => false],
            ['name' => 'Mocanu Andrei', 'email' => 'mocanu.andrei@email.ro', 'phone' => '0740111028', 'has_history' => false],
            ['name' => 'Dima Carmen', 'email' => 'dima.carmen@email.ro', 'phone' => '0740111029', 'has_history' => false],
            ['name' => 'Sandu Valentin', 'email' => 'sandu.valentin@email.ro', 'phone' => '0740111030', 'has_history' => false],
        ];

        $doctors = [1, 2, 3, 4, 5, 6]; // ID-uri doctori
        $departments = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]; // ID-uri departamente
        $appointmentTypes = [1, 2, 3]; // ID-uri tipuri programări
        $statuses = ['confirmed', 'completed', 'cancelled'];

        foreach ($patients as $patient) {
            if ($patient['has_history']) {
                // Pacienți cu istoric - 5-10 programări în trecut
                $appointmentsCount = rand(5, 10);

                for ($i = 0; $i < $appointmentsCount; $i++) {
                    $doctorId = $doctors[array_rand($doctors)];
                    $doctorDeptMap = [
                        1 => 1, // Boghian - Alergologie
                        2 => 1, // Frant - Alergologie
                        3 => 1, // Ungureanu - Alergologie
                        4 => 2, // Popescu - Cardiologie
                        5 => 2, // Ionescu - Cardiologie
                        6 => 10, // Marinescu - Pediatrie
                    ];

                    $departmentId = $doctorDeptMap[$doctorId];

                    // Programări în ultimele 6 luni
                    $daysAgo = rand(1, 180);
                    $date = Carbon::now()->subDays($daysAgo);

                    // Ora între 08:00 și 17:00
                    $hour = rand(8, 16);
                    $minute = rand(0, 1) * 30; // 00 sau 30
                    $time = sprintf('%02d:%02d:00', $hour, $minute);

                    // Status: majoritatea completate, câteva confirmate, puține anulate
                    $statusWeights = ['completed' => 70, 'confirmed' => 20, 'cancelled' => 10];
                    $randomStatus = $this->weightedRandom($statusWeights);

                    DB::table('appointments')->insert([
                        'appointment_type_id' => $appointmentTypes[array_rand($appointmentTypes)],
                        'department_id' => $departmentId,
                        'doctor_id' => $doctorId,
                        'client_name' => $patient['name'],
                        'client_email' => $patient['email'],
                        'client_phone' => $patient['phone'],
                        'appointment_date' => $date->format('Y-m-d'),
                        'appointment_time' => $time,
                        'status' => $randomStatus,
                        'notes' => $randomStatus === 'cancelled' ? 'Anulat de pacient' : null,
                        'created_at' => $date->subDays(rand(1, 5)),
                        'updated_at' => $date,
                    ]);
                }
            } else {
                // Pacienți fără istoric sau cu 1-2 programări
                $appointmentsCount = rand(0, 2);

                for ($i = 0; $i < $appointmentsCount; $i++) {
                    $doctorId = $doctors[array_rand($doctors)];
                    $doctorDeptMap = [
                        1 => 1,
                        2 => 1,
                        3 => 1,
                        4 => 2,
                        5 => 2,
                        6 => 10,
                    ];

                    $departmentId = $doctorDeptMap[$doctorId];

                    // Programări în ultimele 30 zile
                    $daysAgo = rand(1, 30);
                    $date = Carbon::now()->subDays($daysAgo);

                    $hour = rand(8, 16);
                    $minute = rand(0, 1) * 30;
                    $time = sprintf('%02d:%02d:00', $hour, $minute);

                    DB::table('appointments')->insert([
                        'appointment_type_id' => $appointmentTypes[array_rand($appointmentTypes)],
                        'department_id' => $departmentId,
                        'doctor_id' => $doctorId,
                        'client_name' => $patient['name'],
                        'client_email' => $patient['email'],
                        'client_phone' => $patient['phone'],
                        'appointment_date' => $date->format('Y-m-d'),
                        'appointment_time' => $time,
                        'status' => 'completed',
                        'notes' => null,
                        'created_at' => $date->subDays(rand(1, 3)),
                        'updated_at' => $date,
                    ]);
                }
            }
        }
    }

    private function weightedRandom($weights)
    {
        $rand = rand(1, array_sum($weights));

        foreach ($weights as $key => $weight) {
            $rand -= $weight;
            if ($rand <= 0) {
                return $key;
            }
        }
    }
}
