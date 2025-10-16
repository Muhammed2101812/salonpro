<?php

declare(strict_types=1);

namespace App\Exceptions\Business;

class ResourceNotFoundException extends BusinessException
{
    /**
     * Create a new resource not found exception instance.
     */
    public function __construct(string $resource = 'Resource', ?\Throwable $previous = null)
    {
        parent::__construct(
            message: "{$resource} not found",
            code: 404,
            previous: $previous
        );
    }
}
