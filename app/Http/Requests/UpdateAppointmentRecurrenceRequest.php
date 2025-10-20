<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentRecurrenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Add validation rules here (use 'sometimes' for optional updates)
        ];
    }

    public function attributes(): array
    {
        return [
            // Add Turkish attribute names here
        ];
    }
}
