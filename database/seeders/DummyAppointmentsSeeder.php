<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Doctor;
use App\Models\Appointment;
use App\Models\AppointmentType;

class DummyAppointmentsSeeder extends Seeder
{
    public function run(): void
    {
        $types = AppointmentType::query()->pluck('id')->all();
        if (empty($types)) {
            $this->command?->warn('No appointment types found. Skipping seeding.');
            return;
        }

        $today = Carbon::today();

        Doctor::query()->with('department')->chunk(100, function ($doctors) use ($types, $today) {
            foreach ($doctors as $doctor) {
                // create 10 appointments per doctor
                for ($i = 0; $i < 10; $i++) {
                    $date = $today->copy()->addDays(rand(0, 30));
                    // 09:00, 09:30, 10:00, ... spread slots
                    $minuteStep = [0, 30][rand(0,1)];
                    $hour = 9 + ($i % 8); // between 09 and 16
                    $time = sprintf('%02d:%02d:00', $hour, $minuteStep);

                    $name = fake()->name();
                    $email = Str::slug($name, '.') . "." . Str::random(4) . '@example.test';
                    $phone = '07' . str_pad((string)rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);

                    Appointment::create([
                        'appointment_type_id' => $types[array_rand($types)],
                        'department_id' => $doctor->department_id,
                        'doctor_id' => $doctor->id,
                        'client_name' => $name,
                        'client_email' => $email,
                        'client_phone' => $phone,
                        'appointment_date' => $date->format('Y-m-d'),
                        'appointment_time' => $time,
                        'status' => ['confirmed','cancelled','completed'][array_rand(['confirmed','cancelled','completed'])],
                        'notes' => rand(0,1) ? 'Notă generată pentru simulare.' : null,
                    ]);
                }
            }
        });

        $this->command?->info('Dummy appointments seeded: 10 per doctor.');
    }
}
