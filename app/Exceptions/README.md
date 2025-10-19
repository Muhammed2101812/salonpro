# Custom Exceptions - SalonPro

Bu dizin, uygulamanın özel exception sınıflarını içerir.

## Exception Hiyerarşisi

```
BaseException (Abstract)
├── ValidationException
├── NotFoundException
├── UnauthorizedException
├── Appointment/
│   └── AppointmentConflictException
└── Payment/
    └── InsufficientFundsException
```

## Kullanım

### Exception Fırlatma

```php
use App\Exceptions\NotFoundException;
use App\Exceptions\Appointment\AppointmentConflictException;

// Basit kullanım
throw new NotFoundException();

// Özel mesaj ile
throw new NotFoundException('Müşteri bulunamadı.');

// Context ile (loglama için)
throw new NotFoundException('Müşteri bulunamadı.', [
    'customer_id' => $customerId,
    'requested_by' => auth()->id(),
]);
```

### Service Layer'da Kullanım

```php
use App\Exceptions\Appointment\AppointmentConflictException;
use App\Services\AppointmentService;

class AppointmentService
{
    public function create(AppointmentData $data): Appointment
    {
        // Çakışma kontrolü
        if ($this->hasConflict($data)) {
            throw new AppointmentConflictException('Bu saatte başka randevu var.', [
                'employee_id' => $data->employee_id,
                'date' => $data->appointment_date,
                'time' => $data->start_time,
            ]);
        }

        return Appointment::create($data->toArray());
    }
}
```

### Controller'da Yakalama

```php
use App\Exceptions\Appointment\AppointmentConflictException;
use Illuminate\Http\JsonResponse;

class AppointmentController extends Controller
{
    public function store(AppointmentData $data): JsonResponse
    {
        try {
            $appointment = $this->service->create($data);

            return response()->json($appointment, 201);
        } catch (AppointmentConflictException $e) {
            // Exception otomatik olarak JSON response döndürür
            throw $e;
        }
    }
}
```

## Yeni Exception Oluşturma

```php
<?php

namespace App\Exceptions\YourModule;

use App\Exceptions\BaseException;

class YourCustomException extends BaseException
{
    // HTTP status code
    protected int $statusCode = 400;

    // Uygulama error code
    protected string $errorCode = 'YOUR_ERROR_CODE';

    // Varsayılan mesaj
    protected function getDefaultMessage(): string
    {
        return 'Varsayılan hata mesajınız.';
    }
}
```

## Exception Response Formatı

Tüm custom exception'lar JSON response döndürür:

```json
{
  "success": false,
  "message": "Bu tarih ve saatte zaten bir randevu mevcut.",
  "error_code": "APPOINTMENT_CONFLICT"
}
```

Debug mode aktifken ek bilgiler eklenir:

```json
{
  "success": false,
  "message": "Bu tarih ve saatte zaten bir randevu mevcut.",
  "error_code": "APPOINTMENT_CONFLICT",
  "debug": {
    "exception": "App\\Exceptions\\Appointment\\AppointmentConflictException",
    "file": "/app/Services/AppointmentService.php",
    "line": 42,
    "trace": [...],
    "context": {
      "employee_id": 5,
      "date": "2025-10-20",
      "time": "14:00"
    }
  }
}
```

## Error Codes

| Code | Exception | Açıklama |
|------|-----------|----------|
| `NOT_FOUND` | NotFoundException | Kayıt bulunamadı |
| `VALIDATION_ERROR` | ValidationException | Geçersiz veri |
| `UNAUTHORIZED` | UnauthorizedException | Yetkisiz işlem |
| `APPOINTMENT_CONFLICT` | AppointmentConflictException | Randevu çakışması |
| `INSUFFICIENT_FUNDS` | InsufficientFundsException | Yetersiz bakiye |

## Loglama

Exception'lar otomatik olarak context bilgileriyle loglanır:

```php
// Exception fırlatıldığında
throw new NotFoundException('Müşteri bulunamadı.', [
    'customer_id' => 123,
    'user_id' => auth()->id(),
]);

// Log çıktısı
[2025-10-18 10:30:00] local.ERROR: Müşteri bulunamadı.
{
    "error_code": "NOT_FOUND",
    "status_code": 404,
    "customer_id": 123,
    "user_id": 5
}
```

## Best Practices

1. **Spesifik Exception'lar Kullanın**: Genel exception yerine spesifik exception kullanın
2. **Context Ekleyin**: Debug için yararlı context bilgileri ekleyin
3. **Türkçe Mesajlar**: Kullanıcıya gösterilecek mesajlar Türkçe olmalı
4. **Error Kodları**: Her exception için unique error code kullanın
5. **HTTP Status Kodları**: Doğru HTTP status code kullanın

## Testing

```php
use App\Exceptions\NotFoundException;
use Tests\TestCase;

class ExceptionTest extends TestCase
{
    public function test_exception_returns_correct_status_code()
    {
        $exception = new NotFoundException();

        $this->assertEquals(404, $exception->getStatusCode());
        $this->assertEquals('NOT_FOUND', $exception->getErrorCode());
    }

    public function test_exception_includes_context()
    {
        $exception = new NotFoundException('Test', [
            'id' => 123,
        ]);

        $context = $exception->context();

        $this->assertArrayHasKey('id', $context);
        $this->assertEquals(123, $context['id']);
    }
}
```
