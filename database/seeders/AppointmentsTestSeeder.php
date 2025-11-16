<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AppointmentsTestSeeder extends Seeder
{
    public function run(): void
    {
        // Șterge toate programările existente
        DB::table('appointments')->truncate();

        // Obține doctorii grupați pe clinici
        $doctorsByClinic = DB::table('doctors')
            ->select('id', 'appointment_type_id', 'department_id', 'consultation_duration')
            ->where('is_active', true)
            ->get()
            ->groupBy('appointment_type_id');

        $appointments = [];
        $clientCounter = 1;
        $startDate = Carbon::today()->addDay(); // Începe de mâine

        // Pentru fiecare clinică
        foreach ($doctorsByClinic as $clinicId => $doctors) {
            if ($doctors->isEmpty()) continue;

            $doctorList = $doctors->toArray();
            $doctorCount = count($doctorList);

            // Creează 10 programări pentru această clinică
            for ($i = 0; $i < 10; $i++) {
                // Distribuie uniform pe doctori, asigurând că fiecare are cel puțin una
                $doctorIndex = $i % $doctorCount;
                $doctor = $doctorList[$doctorIndex];

                // Variază zilele (distribuie pe următoarele 2 săptămâni)
                $daysToAdd = ($i % 14);
                $appointmentDate = $startDate->copy()->addDays($daysToAdd);

                // Variază orele (9:00, 10:00, 11:00, 14:00, 15:00, 16:00)
                $hours = [9, 10, 11, 14, 15, 16];
                $hour = $hours[$i % count($hours)];
                $appointmentTime = sprintf('%02d:00:00', $hour);

                // Statusuri diferite pentru teste
                $statuses = ['confirmed', 'confirmed', 'confirmed', 'confirmed', 'completed'];
                $status = $statuses[$i % count($statuses)];

                $appointments[] = [
                    'appointment_type_id' => $clinicId,
                    'department_id' => $doctor->department_id,
                    'doctor_id' => $doctor->id,
                    'client_name' => 'Pacient Test ' . $clientCounter,
                    'client_email' => 'pacient' . $clientCounter . '@test.ro',
                    'client_phone' => '074' . str_pad($clientCounter, 7, '0', STR_PAD_LEFT),
                    'appointment_date' => $appointmentDate->format('Y-m-d'),
                    'appointment_time' => $appointmentTime,
                    'status' => $status,
                    'notes' => $status === 'completed' ? 'Consultație finalizată cu succes.' : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $clientCounter++;
            }
        }

        // Inserează toate programările
        DB::table('appointments')->insert($appointments);

        $this->command->info('✓ Șters toate programările vechi');
        $this->command->info('✓ Creat ' . count($appointments) . ' programări de test');
        $this->command->info('✓ Distribuite pe ' . $doctorsByClinic->count() . ' clinici');
    }
}
