<?php

declare(strict_types=1);

use App\Http\Controllers\API\{AuthController,BranchController,CustomerController,EmployeeController};
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    // Auth routes (public)
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function (): void {
        // Auth
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('profile', [AuthController::class, 'profile']);

        // Branches
        Route::apiResource('branches', BranchController::class);
        Route::post('branches/{branch}/restore', [BranchController::class, 'restore'])->name('branches.restore');
        Route::delete('branches/{branch}/force', [BranchController::class, 'forceDestroy'])->name('branches.force-destroy');

        // Customers
        Route::apiResource('customers', CustomerController::class);
        Route::post('customers/{customer}/restore', [CustomerController::class, 'restore'])->name('customers.restore');
        Route::delete('customers/{customer}/force', [CustomerController::class, 'forceDestroy'])->name('customers.force-destroy');

        // Employees
        Route::apiResource('employees', EmployeeController::class);
        Route::post('employees/{employee}/restore', [EmployeeController::class, 'restore'])->name('employees.restore');
        Route::delete('employees/{employee}/force', [EmployeeController::class, 'forceDestroy'])->name('employees.force-destroy');
    });
});
