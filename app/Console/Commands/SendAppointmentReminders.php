<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use App\Mail\AppointmentReminder;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendAppointmentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Trimite mementouri email pentru programările de mâine';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tomorrow = Carbon::tomorrow()->format('Y-m-d');

        // Găsește toate programările confirmate pentru mâine
        $appointments = Appointment::with('service')
            ->where('appointment_date', $tomorrow)
            ->whereIn('status', ['pending', 'confirmed'])
            ->get();

        $sentCount = 0;

        foreach ($appointments as $appointment) {
            try {
                Mail::to($appointment->client_email)->send(new AppointmentReminder($appointment));
                $sentCount++;
                $this->info("Memento trimis pentru: {$appointment->client_name} ({$appointment->client_email})");
            } catch (\Exception $e) {
                $this->error("Eroare la trimiterea mementoului pentru {$appointment->client_email}: " . $e->getMessage());
            }
        }

        $this->info("Total mementouri trimise: {$sentCount}/{$appointments->count()}");

        return Command::SUCCESS;
    }
}
