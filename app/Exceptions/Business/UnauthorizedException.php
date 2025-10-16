<?php

declare(strict_types=1);

namespace App\Exceptions\Business;

class UnauthorizedException extends BusinessException
{
    /**
     * Create a new unauthorized exception instance.
     */
    public function __construct(string $message = 'Unauthorized action', ?\Throwable $previous = null)
    {
        parent::__construct(
            message: $message,
            code: 403,
            previous: $previous
        );
    }
}
