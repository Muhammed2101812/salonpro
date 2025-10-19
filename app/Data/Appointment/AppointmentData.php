<?php

namespace App\Data\Appointment;

use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class AppointmentData extends Data
{
    public function __construct(
        #[Required]
        public int $customer_id,

        #[Required]
        public int $employee_id,

        #[Required]
        public int $service_id,

        #[Required, Date]
        public string $appointment_date,

        #[Required]
        public string $start_time,

        #[Nullable]
        public ?string $end_time,

        #[Required, In(['pending', 'confirmed', 'in_progress', 'completed', 'cancelled', 'no_show'])]
        public string $status = 'pending',

        #[Nullable]
        public ?string $notes = null,

        #[Nullable]
        public ?float $price = null,

        #[Nullable]
        public ?int $branch_id = null,
    ) {}

    public static function fromModel(\App\Models\Appointment $appointment): self
    {
        return new self(
            customer_id: $appointment->customer_id,
            employee_id: $appointment->employee_id,
            service_id: $appointment->service_id,
            appointment_date: $appointment->appointment_date->format('Y-m-d'),
            start_time: $appointment->start_time,
            end_time: $appointment->end_time,
            status: $appointment->status,
            notes: $appointment->notes,
            price: $appointment->price,
            branch_id: $appointment->branch_id,
        );
    }

    public function isUpcoming(): bool
    {
        $appointmentDateTime = \Carbon\Carbon::parse("{$this->appointment_date} {$this->start_time}");

        return $appointmentDateTime->isFuture();
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }
}
