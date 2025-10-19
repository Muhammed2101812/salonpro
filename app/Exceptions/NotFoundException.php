<?php

namespace App\Exceptions;

class NotFoundException extends BaseException
{
    protected int $statusCode = 404;

    protected string $errorCode = 'NOT_FOUND';

    protected function getDefaultMessage(): string
    {
        return 'İstenen kayıt bulunamadı.';
    }
}
