<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Faker\Factory as Faker;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('ro_RO');

        // Helper: create a doctor with default schedule 09:00-17:00 Mon-Fri
        $createDoctor = function (int $appointmentTypeId, int $departmentId) use ($faker) {
            $name = $faker->name();
            $email = $faker->unique()->safeEmail();
            $doctorId = DB::table('doctors')->insertGetId([
                'appointment_type_id' => $appointmentTypeId,
                'department_id' => $departmentId,
                'name' => $name,
                'title' => 'Dr.',
                'email' => $email,
                'phone' => $faker->numerify('07########'),
                'consultation_duration' => 30,
                'consultation_price' => 200,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // default workdays Mon-Fri 09-17
            foreach (['monday','tuesday','wednesday','thursday','friday'] as $day) {
                DB::table('doctor_schedules')->insert([
                    'doctor_id' => $doctorId,
                    'day_of_week' => $day,
                    'start_time' => '09:00',
                    'end_time' => '17:00',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return $doctorId;
        };

        // Ensure each clinic (1,2,3) has at least 3 doctors
        foreach ([1,2,3] as $clinicId) {
            $deps = DB::table('departments')->where('appointment_type_id', $clinicId)->pluck('id')->all();
            if (empty($deps)) continue;
            $existing = DB::table('doctors')->where('appointment_type_id', $clinicId)->count();
            $toCreate = max(0, 3 - $existing);
            for ($i = 0; $i < $toCreate; $i++) {
                $depId = $deps[array_rand($deps)];
                $createDoctor($clinicId, $depId);
            }
        }

        // 1) Ensure pivot exists, then assign 1-3 specialties per doctor within their clinic
        if (Schema::hasTable('doctor_department')) {
            $doctors = DB::table('doctors')->select('id','appointment_type_id','department_id')->get();

            foreach ($doctors as $doc) {
                // departments from same clinic
                $deps = DB::table('departments')
                    ->where('appointment_type_id', $doc->appointment_type_id)
                    ->pluck('id')
                    ->all();
                if (empty($deps)) {
                    continue;
                }
                shuffle($deps);
                $count = rand(1, min(3, count($deps)));
                $picked = array_slice($deps, 0, $count);

                // Set primary department to the first picked
                $primary = $picked[0];
                DB::table('doctors')->where('id', $doc->id)->update(['department_id' => $primary]);

                // Sync pivot (delete existing to avoid duplicates)
                DB::table('doctor_department')->where('doctor_id', $doc->id)->delete();
                $bulk = [];
                foreach ($picked as $depId) {
                    $bulk[] = ['doctor_id' => $doc->id, 'department_id' => $depId];
                }
                if ($bulk) {
                    DB::table('doctor_department')->insert($bulk);
                }
            }
        }

        // 2) Create exactly 10 appointments per clinic (1,2,3)
        $doctorIds = DB::table('doctors')->where('is_active', true)->pluck('id')->all();
        if (empty($doctorIds)) { return; }

        $statuses = ['confirmed','completed','cancelled'];
        $rows = [];

        $makeAppointment = function ($doctorId) use (&$rows, $statuses, $faker) {
            $doctor = DB::table('doctors')->where('id', $doctorId)->first();
            if (!$doctor) return;
            $depId = $doctor->department_id;
            if (Schema::hasTable('doctor_department')) {
                $pivots = DB::table('doctor_department')->where('doctor_id', $doctorId)->pluck('department_id')->all();
                if (!empty($pivots)) {
                    $depId = $pivots[array_rand($pivots)];
                }
            }
            // Next 7 days so they show in dashboards
            $date = Carbon::today()->addDays(rand(0, 7));
            $startMinutes = 9 * 60; $endMinutes = 17 * 60;
            $minute = rand($startMinutes, $endMinutes - 15); $minute -= $minute % 15;
            $time = sprintf('%02d:%02d:00', intdiv($minute,60), $minute % 60);
            // Prefer confirmed to be visible
            $status = (rand(1,100) <= 80) ? 'confirmed' : $statuses[array_rand($statuses)];
            $rows[] = [
                'appointment_type_id' => $doctor->appointment_type_id,
                'department_id' => $depId,
                'doctor_id' => $doctorId,
                'client_name' => $faker->name(),
                'client_email' => $faker->unique()->safeEmail(),
                'client_phone' => $faker->numerify('07########'),
                'appointment_date' => $date->toDateString(),
                'appointment_time' => $time,
                'status' => $status,
                'notes' => $faker->boolean(30) ? $faker->sentence() : null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        };

        foreach ([1,2,3] as $clinicId) {
            $clinicDoctors = DB::table('doctors')->where('is_active', true)->where('appointment_type_id', $clinicId)->pluck('id')->all();
            if (empty($clinicDoctors)) { continue; }
            // Round-robin assign 10 appts per clinic
            for ($i = 0; $i < 10; $i++) {
                $doctorId = $clinicDoctors[$i % count($clinicDoctors)];
                $makeAppointment($doctorId);
            }
        }

        if (!empty($rows)) {
            DB::table('appointments')->insert($rows);
        }
    }
}
