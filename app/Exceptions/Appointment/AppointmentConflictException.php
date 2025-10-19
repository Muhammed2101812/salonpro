<?php

namespace App\Exceptions\Appointment;

use App\Exceptions\BaseException;

class AppointmentConflictException extends BaseException
{
    protected int $statusCode = 409;

    protected string $errorCode = 'APPOINTMENT_CONFLICT';

    protected function getDefaultMessage(): string
    {
        return 'Bu tarih ve saatte zaten bir randevu mevcut.';
    }
}
