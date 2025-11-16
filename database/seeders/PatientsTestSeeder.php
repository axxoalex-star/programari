<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PatientsTestSeeder extends Seeder
{
    /**
     * Generate 40 test patients with appointments distributed across doctors from Clinica Stomatologica Mihai
     */
    public function run(): void
    {
        // Get Clinica Stomatologica Mihai
        $clinic = DB::table('appointment_types')
            ->where('name', 'Clinica Stomatologica Mihai')
            ->first();

        if (!$clinic) {
            $this->command->error('Clinica Stomatologica Mihai not found!');
            return;
        }

        // Get all active doctors from this clinic
        $doctors = DB::table('doctors')
            ->where('appointment_type_id', $clinic->id)
            ->where('is_active', true)
            ->get();

        if ($doctors->isEmpty()) {
            $this->command->error('No doctors found for Clinica Stomatologica Mihai!');
            return;
        }

        // Romanian first names
        $firstNames = [
            'Ion', 'Maria', 'Gheorghe', 'Elena', 'Vasile', 'Ana', 'Nicolae', 'Ioana',
            'Andrei', 'Mihaela', 'Constantin', 'Andreea', 'Alexandru', 'Cristina', 'Mihai',
            'Gabriela', 'Florin', 'Daniela', 'Adrian', 'Alina', 'Cristian', 'Monica',
            'Daniel', 'Raluca', 'Stefan', 'Carmen', 'Marian', 'Laura', 'Catalin', 'Simona',
            'Ionut', 'Diana', 'Radu', 'Roxana', 'Bogdan', 'Oana', 'George', 'Larisa',
            'Sorin', 'Mirela', 'Dragos', 'Claudia', 'Lucian', 'Ramona', 'Petru', 'Nicoleta'
        ];

        // Romanian last names
        $lastNames = [
            'Popescu', 'Ionescu', 'Popa', 'Pop', 'Radu', 'Gheorghiu', 'Dima', 'Stoica',
            'Constantin', 'Stanciu', 'Munteanu', 'Moldovan', 'Nistor', 'Florea', 'Dobre',
            'Ene', 'Barbu', 'Vasile', 'Luca', 'Dumitru', 'Marin', 'Dragomir', 'Stanescu',
            'Tudor', 'Mihai', 'Georgescu', 'Oprea', 'Serban', 'Matei', 'Mocanu'
        ];

        $appointments = [];
        $startDate = Carbon::today()->subMonths(6); // Start 6 months ago
        $endDate = Carbon::today()->addMonths(2); // Up to 2 months in future

        // Create 40 unique patients with appointments
        for ($i = 0; $i < 40; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $patientName = $firstName . ' ' . $lastName;
            $patientEmail = strtolower(str_replace(' ', '.', $firstName . '.' . $lastName . $i)) . '@test.ro';
            $patientPhone = '07' . str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);

            // Each patient gets between 1-5 appointments
            $numAppointments = rand(1, 5);

            for ($j = 0; $j < $numAppointments; $j++) {
                // Select a random doctor from the clinic
                $doctor = $doctors->random();

                // Get a department for this doctor
                $department = DB::table('doctor_department')
                    ->where('doctor_id', $doctor->id)
                    ->first();

                if (!$department) {
                    continue;
                }

                // Random date between start and end
                $daysOffset = rand(0, $startDate->diffInDays($endDate));
                $appointmentDate = $startDate->copy()->addDays($daysOffset);

                // Random time between 9:00 and 17:00
                $hours = [9, 10, 11, 12, 14, 15, 16, 17];
                $hour = $hours[array_rand($hours)];
                $appointmentTime = sprintf('%02d:00:00', $hour);

                // Determine status based on date
                if ($appointmentDate->isFuture()) {
                    $status = rand(0, 100) < 80 ? 'confirmed' : 'pending';
                } elseif ($appointmentDate->isToday()) {
                    $status = 'confirmed';
                } else {
                    $status = rand(0, 100) < 90 ? 'completed' : 'cancelled';
                }

                $appointments[] = [
                    'appointment_type_id' => $clinic->id,
                    'department_id' => $department->department_id,
                    'doctor_id' => $doctor->id,
                    'client_name' => $patientName,
                    'client_email' => $patientEmail,
                    'client_phone' => $patientPhone,
                    'appointment_date' => $appointmentDate->toDateString(),
                    'appointment_time' => $appointmentTime,
                    'status' => $status,
                    'notes' => $status === 'completed' ? 'Consultație finalizată.' : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Insert all appointments
        DB::table('appointments')->insert($appointments);

        $this->command->info('Successfully created ' . count($appointments) . ' appointments for 40 patients from Clinica Stomatologica Mihai!');
    }
}
