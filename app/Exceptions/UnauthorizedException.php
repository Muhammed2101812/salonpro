<?php

namespace App\Exceptions;

class UnauthorizedException extends BaseException
{
    protected int $statusCode = 401;

    protected string $errorCode = 'UNAUTHORIZED';

    protected function getDefaultMessage(): string
    {
        return 'Bu işlem için yetkiniz bulunmamaktadır.';
    }
}
