<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait Loggable
{
    protected function logAppointment(string $message, array $context = [], string $level = 'info'): void
    {
        Log::channel('appointments')->{$level}($message, $context);
    }

    protected function logPayment(string $message, array $context = [], string $level = 'info'): void
    {
        Log::channel('payments')->{$level}($message, $context);
    }

    protected function logCustomer(string $message, array $context = [], string $level = 'info'): void
    {
        Log::channel('customers')->{$level}($message, $context);
    }

    protected function logSecurity(string $message, array $context = [], string $level = 'warning'): void
    {
        Log::channel('security')->{$level}($message, $context);
    }

    protected function logPerformance(string $message, array $context = [], string $level = 'info'): void
    {
        Log::channel('performance')->{$level}($message, $context);
    }

    protected function logApi(string $message, array $context = [], string $level = 'info'): void
    {
        Log::channel('api')->{$level}($message, $context);
    }

    protected function logAudit(string $message, array $context = []): void
    {
        $context = array_merge([
            'user_id' => auth()->id(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toIso8601String(),
        ], $context);

        Log::channel('audit')->info($message, $context);
    }
}
