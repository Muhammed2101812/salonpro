<?php

declare(strict_types=1);

namespace App\Exceptions\Business;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BusinessException extends Exception
{
    /**
     * Create a new business exception instance.
     */
    public function __construct(
        string $message = 'A business rule was violated',
        int $code = 422,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Render the exception as an HTTP response.
     */
    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
            'errors' => [],
        ], $this->getCode());
    }
}
