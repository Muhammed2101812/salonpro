<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\EmployeeCertification;
use App\Repositories\Contracts\EmployeeCertificationRepositoryInterface;
use Illuminate\Support\Collection;

class EmployeeCertificationService
{
    public function __construct(
        private EmployeeCertificationRepositoryInterface $repository
    ) {}

    /**
     * Get all certifications
     */
    public function getAllCertifications(): Collection
    {
        return $this->repository->all();
    }

    /**
     * Get certifications for a specific employee
     */
    public function getEmployeeCertifications(string $employeeId): Collection
    {
        return $this->repository->getByEmployee($employeeId);
    }

    /**
     * Get active certifications
     */
    public function getActiveCertifications(): Collection
    {
        return $this->repository->getActive();
    }

    /**
     * Get expired certifications
     */
    public function getExpiredCertifications(): Collection
    {
        return $this->repository->getExpired();
    }

    /**
     * Get certifications expiring soon
     */
    public function getExpiringSoon(int $days = 30): Collection
    {
        return $this->repository->getExpiringSoon($days);
    }

    /**
     * Create a new certification
     */
    public function createCertification(array $data): EmployeeCertification
    {
        // Validate expiry date if provided
        if (isset($data['expiry_date']) && isset($data['issue_date'])) {
            $this->validateDates($data['issue_date'], $data['expiry_date']);
        }

        return $this->repository->create($data);
    }

    /**
     * Update certification
     */
    public function updateCertification(string $id, array $data): bool
    {
        $certification = $this->repository->find($id);

        if (!$certification) {
            throw new \Exception('Sertifika bulunamadı.');
        }

        // Validate dates if both are provided
        if (isset($data['expiry_date']) && isset($data['issue_date'])) {
            $this->validateDates($data['issue_date'], $data['expiry_date']);
        }

        return $this->repository->update($id, $data);
    }

    /**
     * Delete certification
     */
    public function deleteCertification(string $id): bool
    {
        return $this->repository->delete($id);
    }

    /**
     * Find certification by ID
     */
    public function findCertification(string $id): ?EmployeeCertification
    {
        return $this->repository->find($id);
    }

    /**
     * Check if employee has valid certification
     */
    public function hasValidCertification(string $employeeId, string $certificationName): bool
    {
        return $this->repository->getByEmployee($employeeId)
            ->filter(function ($cert) use ($certificationName) {
                return $cert->certification_name === $certificationName && !$cert->isExpired();
            })
            ->isNotEmpty();
    }

    /**
     * Get certifications needing renewal
     */
    public function getCertificationsNeedingRenewal(string $employeeId, int $days = 60): Collection
    {
        return $this->repository->getByEmployee($employeeId)
            ->filter(function ($cert) use ($days) {
                if (!$cert->expiry_date) {
                    return false;
                }

                return $cert->expiry_date->diffInDays(now()) <= $days && !$cert->isExpired();
            });
    }

    /**
     * Get employee's expired certifications
     */
    public function getEmployeeExpiredCertifications(string $employeeId): Collection
    {
        return $this->repository->getByEmployee($employeeId)
            ->filter(fn($cert) => $cert->isExpired());
    }

    /**
     * Send renewal reminders for expiring certifications
     */
    public function sendRenewalReminders(int $days = 30): int
    {
        $expiringCerts = $this->getExpiringSoon($days);
        $remindersSent = 0;

        foreach ($expiringCerts as $cert) {
            // TODO: Implement notification sending logic
            // Event::dispatch(new CertificationExpiringEvent($cert));
            $remindersSent++;
        }

        return $remindersSent;
    }

    /**
     * Validate dates
     */
    private function validateDates(string $issueDate, string $expiryDate): void
    {
        $issue = new \DateTime($issueDate);
        $expiry = new \DateTime($expiryDate);

        if ($expiry <= $issue) {
            throw new \InvalidArgumentException(
                'Son kullanma tarihi veriliş tarihinden sonra olmalıdır.'
            );
        }
    }
}
