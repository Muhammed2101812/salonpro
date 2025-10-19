# Data Transfer Objects (DTOs) - SalonPro

Bu dizin, Spatie Laravel Data paketi kullanılarak oluşturulmuş DTO'ları içerir.

## DTO Nedir?

Data Transfer Objects (DTO), farklı katmanlar arasında veri taşımak için kullanılan yapılardır. DTOlar şu avantajları sağlar:

- **Type Safety**: Tip güvenliği sağlar
- **Validation**: Otomatik validasyon
- **Transformation**: Kolay veri dönüşümü
- **Immutability**: Değiştirilemez veri yapıları
- **IDE Support**: Autocomplete ve type hinting

## Kullanım Örnekleri

### Controller'da DTO Kullanımı

```php
use App\Data\Customer\CustomerData;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function store(Request $request)
    {
        // Request'ten otomatik DTO oluşturma ve validasyon
        $customerData = CustomerData::from($request);

        // Service'e DTO göndererek müşteri oluşturma
        $customer = $this->customerService->create($customerData);

        return response()->json($customer, 201);
    }

    public function update(Request $request, Customer $customer)
    {
        $customerData = CustomerData::from($request);

        $this->customerService->update($customer, $customerData);

        return response()->json($customer);
    }
}
```

### Service Layer'da DTO Kullanımı

```php
use App\Data\Customer\CustomerData;
use App\Models\Customer;
use App\Services\CustomerService;

class CustomerService
{
    public function create(CustomerData $data): Customer
    {
        return Customer::create([
            'first_name' => $data->first_name,
            'last_name' => $data->last_name,
            'email' => $data->email,
            'phone' => $data->phone,
            'birth_date' => $data->birth_date,
            'gender' => $data->gender,
            'notes' => $data->notes,
            'address' => $data->address,
            'city' => $data->city,
            'country' => $data->country,
            'branch_id' => $data->branch_id ?? auth()->user()->branch_id,
        ]);
    }

    public function update(Customer $customer, CustomerData $data): Customer
    {
        $customer->update($data->toArray());

        return $customer->fresh();
    }
}
```

### Model'den DTO Oluşturma

```php
use App\Data\Customer\CustomerData;
use App\Models\Customer;

$customer = Customer::find(1);

// Model'den DTO oluşturma
$customerData = CustomerData::fromModel($customer);

// DTO metodlarını kullanma
$fullName = $customerData->getFullName();
```

### API Resource ile Kullanım

```php
use App\Data\Customer\CustomerData;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    public function toArray($request)
    {
        $data = CustomerData::fromModel($this->resource);

        return [
            'id' => $this->id,
            'full_name' => $data->getFullName(),
            'email' => $data->email,
            'phone' => $data->phone,
            'created_at' => $this->created_at,
        ];
    }
}
```

## Yeni DTO Oluşturma

### Basit DTO

```php
<?php

namespace App\Data\YourModule;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;

class YourData extends Data
{
    public function __construct(
        #[Required, Max(255)]
        public string $name,

        #[Required, Email]
        public string $email,

        public ?string $description = null,
    ) {}

    public static function fromModel(YourModel $model): self
    {
        return new self(
            name: $model->name,
            email: $model->email,
            description: $model->description,
        );
    }
}
```

### İç İçe DTO'lar

```php
use Spatie\LaravelData\Data;

class OrderData extends Data
{
    public function __construct(
        public CustomerData $customer,
        /** @var array<ProductData> */
        public array $products,
        public float $total,
    ) {}
}
```

### Collection DTO

```php
use Spatie\LaravelData\DataCollection;

// DTO collection oluşturma
$customers = CustomerData::collection($customersFromDatabase);

// Collection üzerinde işlem yapma
$customers->filter(fn($customer) => $customer->city === 'Istanbul');
```

## Validasyon Kuralları

Spatie Data paketi, PHP attributes kullanarak validasyon kuralları tanımlamanıza olanak sağlar:

```php
use Spatie\LaravelData\Attributes\Validation\*;

class ExampleData extends Data
{
    public function __construct(
        #[Required]
        public string $required_field,

        #[Email, Max(255)]
        public string $email,

        #[Min(18), Max(100)]
        public int $age,

        #[In(['male', 'female', 'other'])]
        public string $gender,

        #[Url]
        public string $website,

        #[Nullable, Date]
        public ?string $birth_date,

        #[Between(0, 100)]
        public float $percentage,
    ) {}
}
```

## Best Practices

### 1. Her Modül İçin Ayrı DTO Dizini
```
app/Data/
├── Customer/
│   ├── CustomerData.php
│   └── CustomerSearchData.php
├── Appointment/
│   ├── AppointmentData.php
│   └── AppointmentFilterData.php
└── Service/
    └── ServiceData.php
```

### 2. İsimlendirme Konvansiyonu
- Class isimleri `*Data` ile bitsin
- Her DTO için `fromModel()` metodu oluşturun
- İş mantığı içeren yardımcı metodlar ekleyin

### 3. Readonly Properties
```php
public function __construct(
    public readonly string $name,
    public readonly string $email,
) {}
```

### 4. Transformation
```php
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
class CustomerData extends Data
{
    public function __construct(
        public string $firstName,  // Otomatik olarak first_name'den map edilir
        public string $lastName,   // last_name
    ) {}
}
```

### 5. Lazy Properties
```php
use Spatie\LaravelData\Lazy;

class CustomerData extends Data
{
    public function __construct(
        public string $name,
        public Lazy|AddressData $address,
    ) {}

    public static function fromModel(Customer $customer): self
    {
        return new self(
            name: $customer->name,
            address: Lazy::create(fn() => AddressData::from($customer->address)),
        );
    }
}
```

## Dokümantasyon

Spatie Laravel Data paketi hakkında daha fazla bilgi için:
- [Official Documentation](https://spatie.be/docs/laravel-data)
- [GitHub Repository](https://github.com/spatie/laravel-data)

## Örnekler

### Form Request Replacement

DTO'lar Form Request'lerin yerine kullanılabilir:

```php
// Önce
class StoreCustomerRequest extends FormRequest
{
    public function rules()
    {
        return [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'nullable|email',
        ];
    }
}

// Sonra
class CustomerData extends Data
{
    public function __construct(
        #[Required, Max(255)]
        public string $first_name,

        #[Required, Max(255)]
        public string $last_name,

        #[Nullable, Email]
        public ?string $email,
    ) {}
}

// Controller'da
public function store(CustomerData $data)
{
    // Otomatik validasyon ve type-safe data
    $customer = Customer::create($data->toArray());
}
```

## Testing

```php
use App\Data\Customer\CustomerData;
use Tests\TestCase;

class CustomerDataTest extends TestCase
{
    public function test_customer_data_can_be_created_from_array()
    {
        $data = CustomerData::from([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'phone' => '+905551234567',
        ]);

        $this->assertEquals('John Doe', $data->getFullName());
    }

    public function test_customer_data_validates_email()
    {
        $this->expectException(ValidationException::class);

        CustomerData::from([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'invalid-email',
        ]);
    }
}
```
