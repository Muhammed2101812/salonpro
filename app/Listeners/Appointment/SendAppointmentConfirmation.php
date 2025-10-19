<?php

namespace App\Listeners\Appointment;

use App\Events\Appointment\AppointmentCreated;
use App\Traits\Loggable;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendAppointmentConfirmation implements ShouldQueue
{
    use Loggable;

    public function __construct()
    {
        //
    }

    public function handle(AppointmentCreated $event): void
    {
        $appointment = $event->appointment;

        // Send confirmation email/SMS
        // Mail::to($appointment->customer->email)->send(new AppointmentConfirmationMail($appointment));

        $this->logAppointment('Appointment confirmation sent', [
            'appointment_id' => $appointment->id,
            'customer_id' => $appointment->customer_id,
            'employee_id' => $appointment->employee_id,
        ]);
    }

    public function failed(AppointmentCreated $event, \Throwable $exception): void
    {
        $this->logAppointment('Failed to send appointment confirmation', [
            'appointment_id' => $event->appointment->id,
            'error' => $exception->getMessage(),
        ], 'error');
    }
}
