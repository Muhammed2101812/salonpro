<?php

namespace App\Exceptions;

class ValidationException extends BaseException
{
    protected int $statusCode = 422;

    protected string $errorCode = 'VALIDATION_ERROR';

    protected function getDefaultMessage(): string
    {
        return 'Geçersiz veri gönderildi.';
    }
}
