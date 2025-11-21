<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Appointment;
use App\Models\Payment;
use App\Models\Sale;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CustomerService extends BaseService
{
    public function __construct(CustomerRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    public function getTimeline(string $customerId, int $page = 1, int $perPage = 20): array
    {
        $customer = $this->findByIdOrFail($customerId);

        $timeline = [];

        // Get appointments
        $appointments = Appointment::where('customer_id', $customerId)
            ->orderBy('appointment_date', 'desc')
            ->get();

        foreach ($appointments as $appointment) {
            $timeline[] = [
                'id' => 'apt-' . $appointment->id,
                'type' => 'appointment',
                'title' => 'Randevu',
                'description' => 'Randevu oluşturuldu',
                'date' => $appointment->appointment_date,
                'amount' => $appointment->price,
                'status' => $appointment->status,
                'details' => [
                    'Hizmet' => $appointment->service->name ?? '-',
                    'Çalışan' => $appointment->employee ? $appointment->employee->first_name . ' ' . $appointment->employee->last_name : '-',
                    'Süre' => $appointment->duration_minutes . ' dakika',
                ],
            ];
        }

        // Get payments
        $payments = Payment::where('customer_id', $customerId)
            ->orderBy('payment_date', 'desc')
            ->get();

        foreach ($payments as $payment) {
            $timeline[] = [
                'id' => 'pay-' . $payment->id,
                'type' => 'payment',
                'title' => 'Ödeme',
                'description' => ucfirst($payment->payment_method) . ' ile ödeme yapıldı',
                'date' => $payment->payment_date,
                'amount' => $payment->amount,
                'status' => $payment->status,
                'details' => [
                    'Ödeme Yöntemi' => ucfirst($payment->payment_method),
                ],
            ];
        }

        // Get sales
        $sales = Sale::where('customer_id', $customerId)
            ->orderBy('sale_date', 'desc')
            ->get();

        foreach ($sales as $sale) {
            $timeline[] = [
                'id' => 'sale-' . $sale->id,
                'type' => 'sale',
                'title' => 'Satış',
                'description' => 'Ürün satışı yapıldı',
                'date' => $sale->sale_date,
                'amount' => $sale->total_amount,
                'status' => null,
                'details' => [
                    'Toplam' => number_format($sale->total_amount, 2) . ' TL',
                ],
            ];
        }

        // Sort by date
        usort($timeline, function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        // Paginate manually
        $offset = ($page - 1) * $perPage;
        $paginatedTimeline = array_slice($timeline, $offset, $perPage);

        return $paginatedTimeline;
    }

    public function getStats(string $customerId): array
    {
        $customer = $this->findByIdOrFail($customerId);

        // Count appointments
        $totalAppointments = Appointment::where('customer_id', $customerId)->count();

        // Sum total spent
        $totalSpent = Payment::where('customer_id', $customerId)
            ->where('status', 'paid')
            ->sum('amount');

        // Get last appointment
        $lastAppointment = Appointment::where('customer_id', $customerId)
            ->orderBy('appointment_date', 'desc')
            ->first();

        return [
            'total_appointments' => $totalAppointments,
            'total_spent' => (float) $totalSpent,
            'last_appointment' => $lastAppointment?->appointment_date,
        ];
    }
}
