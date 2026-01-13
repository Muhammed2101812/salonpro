<?php

declare(strict_types=1);

namespace App\Http\Requests\ServiceReview;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', \App\Models\ServiceReview::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'service_id' => ['required', 'uuid', 'exists:services,id'],
            'customer_id' => ['required', 'uuid', 'exists:customers,id'],
            'appointment_id' => ['nullable', 'uuid', 'exists:appointments,id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'review_text' => ['nullable', 'string', 'max:1000'],
            'is_published' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'service_id.required' => 'Please select a service',
            'service_id.exists' => 'Selected service does not exist',
            'customer_id.required' => 'Please select a customer',
            'customer_id.exists' => 'Selected customer does not exist',
            'rating.required' => 'Rating is required',
            'rating.min' => 'Rating must be at least 1',
            'rating.max' => 'Rating cannot exceed 5',
            'review_text.max' => 'Review text cannot exceed 1000 characters',
        ];
    }
}
