<?php

declare(strict_types=1);

namespace App\Http\Requests\LoyaltyPoint;

use Illuminate\Foundation\Http\FormRequest;

class RedeemPointsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', \App\Models\LoyaltyPoint::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'points' => ['required', 'integer', 'min:1', 'max:100000'],
            'reason' => ['required', 'string', 'max:255'],
            'reference_type' => ['nullable', 'string', 'max:255'],
            'reference_id' => ['nullable', 'uuid'],
        ];
    }

    public function messages(): array
    {
        return [
            'points.required' => 'Points amount is required',
            'points.min' => 'Points must be at least 1',
            'points.max' => 'Points cannot exceed 100,000',
            'reason.required' => 'Please provide a reason for redeeming points',
        ];
    }
}
