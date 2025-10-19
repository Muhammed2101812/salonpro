<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

abstract class BaseException extends Exception
{
    protected int $statusCode = 500;

    protected string $errorCode = 'INTERNAL_ERROR';

    protected array $context = [];

    public function __construct(string $message = '', array $context = [], ?\Throwable $previous = null)
    {
        $this->context = $context;
        parent::__construct($message ?: $this->getDefaultMessage(), 0, $previous);
    }

    abstract protected function getDefaultMessage(): string;

    public function render(Request $request): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $this->getMessage(),
            'error_code' => $this->errorCode,
        ];

        if (config('app.debug')) {
            $response['debug'] = [
                'exception' => get_class($this),
                'file' => $this->getFile(),
                'line' => $this->getLine(),
                'trace' => $this->getTrace(),
                'context' => $this->context,
            ];
        }

        return response()->json($response, $this->statusCode);
    }

    public function context(): array
    {
        return array_merge([
            'error_code' => $this->errorCode,
            'status_code' => $this->statusCode,
        ], $this->context);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }
}
