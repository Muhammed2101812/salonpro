<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\EmployeeCertification;
use App\Repositories\Contracts\EmployeeCertificationRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class EmployeeCertificationRepository implements EmployeeCertificationRepositoryInterface
{
    public function all(): Collection
    {
        return EmployeeCertification::with('employee')->get();
    }

    public function find(string $id): ?EmployeeCertification
    {
        return EmployeeCertification::with('employee')->find($id);
    }

    public function create(array $data): EmployeeCertification
    {
        return EmployeeCertification::create($data);
    }

    public function update(string $id, array $data): bool
    {
        return EmployeeCertification::where('id', $id)->update($data);
    }

    public function delete(string $id): bool
    {
        return EmployeeCertification::where('id', $id)->delete();
    }

    public function getByEmployee(string $employeeId): Collection
    {
        return EmployeeCertification::where('employee_id', $employeeId)
            ->orderBy('expiry_date', 'desc')
            ->get();
    }

    public function getExpiringSoon(int $days = 30): Collection
    {
        $futureDate = Carbon::now()->addDays($days);

        return EmployeeCertification::with('employee')
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '>', Carbon::now())
            ->where('expiry_date', '<=', $futureDate)
            ->orderBy('expiry_date')
            ->get();
    }

    public function getExpired(): Collection
    {
        return EmployeeCertification::with('employee')
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<', Carbon::now())
            ->orderBy('expiry_date', 'desc')
            ->get();
    }

    public function getActive(): Collection
    {
        return EmployeeCertification::with('employee')
            ->where(function ($query) {
                $query->whereNull('expiry_date')
                    ->orWhere('expiry_date', '>=', Carbon::now());
            })
            ->orderBy('issue_date', 'desc')
            ->get();
    }
}
