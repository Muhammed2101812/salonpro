<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Service;
use Illuminate\Http\JsonResponse;

class DashboardController extends BaseController
{
    public function index(): JsonResponse
    {
        // There is no Dashboard model, so we authorize based on a permission string directly
        // or check generic view permission.
        // Assuming 'view-dashboard' permission exists.
        $this->authorize('view-dashboard');

        try {
            $stats = [
                'total_customers' => Customer::count(),
                'total_employees' => Employee::count(),
                'total_branches' => Branch::count(),
                'total_services' => Service::count(),
                'total_products' => Product::count(),

                // Randevu istatistikleri
                'appointments' => [
                    'total' => Appointment::count(),
                    'pending' => Appointment::where('status', 'pending')->count(),
                    'confirmed' => Appointment::where('status', 'confirmed')->count(),
                    'completed' => Appointment::where('status', 'completed')->count(),
                    'cancelled' => Appointment::where('status', 'cancelled')->count(),
                ],

                // Finansal istatistikler
                'financial' => [
                    'total_sales' => Sale::sum('total_amount'),
                    'total_expenses' => Expense::sum('amount'),
                    'total_payments' => Payment::sum('amount'),
                    'monthly_sales' => Sale::whereYear('sale_date', date('Y'))
                        ->whereMonth('sale_date', date('m'))
                        ->sum('total_amount'),
                    'monthly_expenses' => Expense::whereYear('expense_date', date('Y'))
                        ->whereMonth('expense_date', date('m'))
                        ->sum('amount'),
                ],

                // Stok istatistikleri
                'inventory' => [
                    'total_products' => Product::count(),
                    'low_stock_products' => Product::whereColumn('stock_quantity', '<=', 'min_stock_quantity')->count(),
                    'out_of_stock_products' => Product::where('stock_quantity', 0)->count(),
                    'total_stock_value' => Product::selectRaw('SUM(stock_quantity * price) as total')->value('total') ?? 0,
                ],

                // Son hareketler
                'recent_appointments' => Appointment::with(['customer', 'employee', 'service', 'branch'])
                    ->latest()
                    ->limit(5)
                    ->get(),

                'recent_sales' => Sale::with('items.product')
                    ->latest()
                    ->limit(5)
                    ->get(),

                // Aylık trend (son 12 ay)
                'monthly_trends' => $this->getMonthlyTrends(),
            ];

            return $this->sendSuccess($stats, 'Dashboard istatistikleri başarıyla alındı');
        } catch (\Exception $e) {
            return $this->sendError('İstatistikler yüklenirken hata oluştu: '.$e->getMessage(), [], 500);
        }
    }

    private function getMonthlyTrends(): array
    {
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthKey = $date->format('Y-m');

            $months[] = [
                'month' => $date->format('M Y'),
                'sales' => (float) Sale::whereYear('sale_date', $date->year)
                    ->whereMonth('sale_date', $date->month)
                    ->sum('total_amount'),
                'expenses' => (float) Expense::whereYear('expense_date', $date->year)
                    ->whereMonth('expense_date', $date->month)
                    ->sum('amount'),
                'appointments' => Appointment::whereYear('appointment_date', $date->year)
                    ->whereMonth('appointment_date', $date->month)
                    ->count(),
            ];
        }

        return $months;
    }
}
