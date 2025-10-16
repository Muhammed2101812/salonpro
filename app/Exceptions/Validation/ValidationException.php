<?php

declare(strict_types=1);

namespace App\Exceptions\Validation;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ValidationException extends Exception
{
    /**
     * The validation errors.
     *
     * @var array<string, array<int, string>>
     */
    protected array $errors;

    /**
     * Create a new validation exception instance.
     *
     * @param array<string, array<int, string>> $errors
     */
    public function __construct(
        array $errors = [],
        string $message = 'The given data was invalid',
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, 422, $previous);
        $this->errors = $errors;
    }

    /**
     * Get the validation errors.
     *
     * @return array<string, array<int, string>>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Render the exception as an HTTP response.
     */
    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
            'errors' => $this->errors,
        ], 422);
    }
}
