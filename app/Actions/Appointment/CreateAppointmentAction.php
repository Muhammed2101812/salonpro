<?php

namespace App\Actions\Appointment;

use App\Data\Appointment\AppointmentData;
use App\Events\Appointment\AppointmentCreated;
use App\Exceptions\Appointment\AppointmentConflictException;
use App\Models\Appointment;
use App\Traits\Loggable;
use Illuminate\Support\Facades\DB;

class CreateAppointmentAction
{
    use Loggable;

    public function execute(AppointmentData $data): Appointment
    {
        // Check for conflicts
        if ($this->hasConflict($data)) {
            throw new AppointmentConflictException('Bu tarih ve saatte zaten bir randevu mevcut.', [
                'employee_id' => $data->employee_id,
                'date' => $data->appointment_date,
                'time' => $data->start_time,
            ]);
        }

        // Create appointment in transaction
        return DB::transaction(function () use ($data) {
            $appointment = Appointment::create([
                'customer_id' => $data->customer_id,
                'employee_id' => $data->employee_id,
                'service_id' => $data->service_id,
                'appointment_date' => $data->appointment_date,
                'start_time' => $data->start_time,
                'end_time' => $data->end_time,
                'status' => $data->status,
                'notes' => $data->notes,
                'price' => $data->price,
                'branch_id' => $data->branch_id ?? auth()->user()->branch_id,
            ]);

            // Dispatch event
            event(new AppointmentCreated($appointment));

            $this->logAppointment('Appointment created successfully', [
                'appointment_id' => $appointment->id,
                'customer_id' => $data->customer_id,
                'employee_id' => $data->employee_id,
            ]);

            return $appointment;
        });
    }

    private function hasConflict(AppointmentData $data): bool
    {
        return Appointment::query()
            ->where('employee_id', $data->employee_id)
            ->where('appointment_date', $data->appointment_date)
            ->where('start_time', $data->start_time)
            ->whereIn('status', ['pending', 'confirmed', 'in_progress'])
            ->exists();
    }
}
