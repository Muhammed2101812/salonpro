# Action Classes - SalonPro

Action classes, tek bir iş mantığı işlemini kapsayan bağımsız, yeniden kullanılabilir sınıflardır.

## Action Pattern Nedir?

Action pattern, karmaşık iş mantığını controller'lardan ve service'lerden ayırmak için kullanılır. Her action tek bir işi yapar ve tek sorumluluk prensibini (Single Responsibility Principle) takip eder.

## Avantajları

- **Tek Sorumluluk**: Her action tek bir işi yapar
- **Test Edilebilirlik**: Her action bağımsız olarak test edilebilir
- **Yeniden Kullanılabilirlik**: Action'lar farklı yerlerde kullanılabilir
- **Okunabilirlik**: Kod daha temiz ve anlaşılır olur
- **Bakım Kolaylığı**: İş mantığı değişikliklerini tek bir yerde yapabilirsiniz

## Kullanım

### Controller'da Action Kullanımı

```php
use App\Actions\Appointment\CreateAppointmentAction;
use App\Data\Appointment\AppointmentData;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function __construct(
        private CreateAppointmentAction $createAppointment
    ) {}

    public function store(Request $request)
    {
        $data = AppointmentData::from($request);

        $appointment = $this->createAppointment->execute($data);

        return response()->json($appointment, 201);
    }
}
```

### Service Layer'da Action Kullanımı

```php
use App\Actions\Appointment\CreateAppointmentAction;
use App\Data\Appointment\AppointmentData;

class AppointmentService
{
    public function __construct(
        private CreateAppointmentAction $createAppointment
    ) {}

    public function createWithNotifications(AppointmentData $data)
    {
        $appointment = $this->createAppointment->execute($data);

        // Additional business logic
        $this->sendNotifications($appointment);

        return $appointment;
    }
}
```

### Action Zincirleme

```php
use App\Actions\Appointment\CreateAppointmentAction;
use App\Actions\Payment\CreatePaymentAction;
use App\Data\Appointment\AppointmentData;
use App\Data\Payment\PaymentData;

class BookingService
{
    public function __construct(
        private CreateAppointmentAction $createAppointment,
        private CreatePaymentAction $createPayment
    ) {}

    public function bookAndPay(AppointmentData $appointmentData, PaymentData $paymentData)
    {
        $appointment = $this->createAppointment->execute($appointmentData);

        $payment = $this->createPayment->execute($paymentData);

        return compact('appointment', 'payment');
    }
}
```

## Yeni Action Oluşturma

### 1. Basit Action

```php
<?php

namespace App\Actions\YourModule;

use App\Data\YourModule\YourData;
use App\Models\YourModel;
use App\Traits\Loggable;

class YourAction
{
    use Loggable;

    public function execute(YourData $data): YourModel
    {
        // Your business logic here

        $model = YourModel::create($data->toArray());

        $this->logAudit('Action executed', [
            'model_id' => $model->id,
        ]);

        return $model;
    }
}
```

### 2. Action with Validation

```php
<?php

namespace App\Actions\Appointment;

use App\Data\Appointment\AppointmentData;
use App\Exceptions\Appointment\AppointmentConflictException;
use App\Models\Appointment;
use App\Traits\Loggable;

class CreateAppointmentAction
{
    use Loggable;

    public function execute(AppointmentData $data): Appointment
    {
        $this->validate($data);

        $appointment = Appointment::create($data->toArray());

        $this->logAppointment('Appointment created', [
            'appointment_id' => $appointment->id,
        ]);

        return $appointment;
    }

    private function validate(AppointmentData $data): void
    {
        if ($this->hasConflict($data)) {
            throw new AppointmentConflictException();
        }
    }

    private function hasConflict(AppointmentData $data): bool
    {
        // Conflict checking logic
        return false;
    }
}
```

### 3. Action with Transaction

```php
<?php

namespace App\Actions\Payment;

use App\Data\Payment\PaymentData;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class ProcessPaymentAction
{
    public function execute(PaymentData $data): Payment
    {
        return DB::transaction(function () use ($data) {
            // Create payment
            $payment = Payment::create($data->toArray());

            // Update related records
            $payment->appointment->update(['status' => 'paid']);

            // Send receipt
            $this->sendReceipt($payment);

            return $payment;
        });
    }

    private function sendReceipt(Payment $payment): void
    {
        // Send receipt logic
    }
}
```

### 4. Action with Events

```php
<?php

namespace App\Actions\Customer;

use App\Data\Customer\CustomerData;
use App\Events\Customer\CustomerCreated;
use App\Models\Customer;

class CreateCustomerAction
{
    public function execute(CustomerData $data): Customer
    {
        $customer = Customer::create($data->toArray());

        // Dispatch event
        event(new CustomerCreated($customer));

        return $customer;
    }
}
```

## Best Practices

### 1. Naming Convention
- Action class isimleri fiil ile başlamalı: `CreateX`, `UpdateX`, `DeleteX`, `ProcessX`
- Tek bir işlemi tanımlayan net isimler kullanın

### 2. Single Responsibility
Her action tek bir işi yapmalı:

```php
// ✅ İyi
class CreateAppointmentAction {}
class CancelAppointmentAction {}
class RescheduleAppointmentAction {}

// ❌ Kötü
class AppointmentAction {
    public function create() {}
    public function cancel() {}
    public function reschedule() {}
}
```

### 3. Dependency Injection
Constructor injection kullanın:

```php
public function __construct(
    private PaymentGateway $gateway,
    private NotificationService $notifier
) {}
```

### 4. Return Types
Her zaman dönüş tipi belirtin:

```php
public function execute(CustomerData $data): Customer
{
    // ...
}
```

### 5. Error Handling
Custom exception'lar kullanın:

```php
if ($this->hasError($data)) {
    throw new YourCustomException('Error message', $context);
}
```

### 6. Logging
İşlemleri loglay in:

```php
use App\Traits\Loggable;

class YourAction
{
    use Loggable;

    public function execute($data)
    {
        $this->logAudit('Action executed', $context);
    }
}
```

## Testing

```php
use App\Actions\Appointment\CreateAppointmentAction;
use App\Data\Appointment\AppointmentData;
use Tests\TestCase;

class CreateAppointmentActionTest extends TestCase
{
    public function test_creates_appointment()
    {
        $data = AppointmentData::from([
            'customer_id' => 1,
            'employee_id' => 1,
            'service_id' => 1,
            'appointment_date' => '2025-10-20',
            'start_time' => '14:00',
        ]);

        $action = new CreateAppointmentAction();
        $appointment = $action->execute($data);

        $this->assertDatabaseHas('appointments', [
            'customer_id' => 1,
            'employee_id' => 1,
        ]);
    }

    public function test_throws_exception_on_conflict()
    {
        $this->expectException(AppointmentConflictException::class);

        // Create conflicting appointment
        // ...

        $action = new CreateAppointmentAction();
        $action->execute($data);
    }
}
```

## Dizin Yapısı

```
app/Actions/
├── Appointment/
│   ├── CreateAppointmentAction.php
│   ├── CancelAppointmentAction.php
│   └── RescheduleAppointmentAction.php
├── Customer/
│   ├── CreateCustomerAction.php
│   └── UpdateCustomerAction.php
└── Payment/
    ├── ProcessPaymentAction.php
    └── RefundPaymentAction.php
```

## Action vs Service

**Ne zaman Action kullanmalı:**
- Tek bir iş mantığı işlemi
- Yeniden kullanılabilir operasyon
- Test edilmesi gereken spesifik bir işlem

**Ne zaman Service kullanmalı:**
- Birden fazla action'ı koordine etmek
- Karmaşık iş akışları
- Repository'lerle etkileşim

```php
// Action - Tek işlem
class CreatePaymentAction
{
    public function execute(PaymentData $data): Payment
    {
        return Payment::create($data->toArray());
    }
}

// Service - Birden fazla işlemi koordine eder
class CheckoutService
{
    public function __construct(
        private CreateAppointmentAction $createAppointment,
        private ProcessPaymentAction $processPayment,
        private SendConfirmationAction $sendConfirmation
    ) {}

    public function checkout($data)
    {
        $appointment = $this->createAppointment->execute($data->appointment);
        $payment = $this->processPayment->execute($data->payment);
        $this->sendConfirmation->execute($appointment, $payment);

        return compact('appointment', 'payment');
    }
}
```
