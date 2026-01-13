<?php

declare(strict_types=1);

namespace App\Http\Requests\ServiceReview;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        $review = $this->route('review');
        return $this->user()?->can('update', $review) ?? false;
    }

    public function rules(): array
    {
        return [
            'rating' => ['sometimes', 'integer', 'min:1', 'max:5'],
            'review_text' => ['sometimes', 'string', 'max:1000'],
            'is_published' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'rating.min' => 'Rating must be at least 1',
            'rating.max' => 'Rating cannot exceed 5',
            'review_text.max' => 'Review text cannot exceed 1000 characters',
        ];
    }
}
