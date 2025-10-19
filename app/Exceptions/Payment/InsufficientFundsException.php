<?php

namespace App\Exceptions\Payment;

use App\Exceptions\BaseException;

class InsufficientFundsException extends BaseException
{
    protected int $statusCode = 402;

    protected string $errorCode = 'INSUFFICIENT_FUNDS';

    protected function getDefaultMessage(): string
    {
        return 'Yetersiz bakiye.';
    }
}
