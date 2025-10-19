# Authorization Policies - SalonPro

Bu dizin, Laravel authorization policy sınıflarını içerir. Policy'ler, kullanıcıların belirli işlemleri yapma yetkisini kontrol eder.

## Policy Yapısı

Her policy sınıfı Spatie Permission paketi ile entegre çalışır ve branch-based yetkilendirme uygular.

## Mevcut Policy'ler

1. **CustomerPolicy** - Müşteri yönetimi yetkilendirmesi
2. **AppointmentPolicy** - Randevu yönetimi yetkilendirmesi
3. **EmployeePolicy** - Çalışan yönetimi yetkilendirmesi
4. **ServicePolicy** - Hizmet yönetimi yetkilendirmesi
5. **ProductPolicy** - Ürün yönetimi yetkilendirmesi
6. **PaymentPolicy** - Ödeme yönetimi yetkilendirmesi
7. **ExpensePolicy** - Gider yönetimi yetkilendirmesi
8. **BranchPolicy** - Şube yönetimi yetkilendirmesi
9. **UserPolicy** - Kullanıcı yönetimi yetkilendirmesi
10. **ReportPolicy** - Rapor erişim yetkilendirmesi

## Kullanım

### Controller'da Policy Kullanımı

```php
use App\Models\Customer;

class CustomerController extends Controller
{
    public function index()
    {
        // Check if user can view any customers
        $this->authorize('viewAny', Customer::class);

        $customers = Customer::all();

        return view('customers.index', compact('customers'));
    }

    public function show(Customer $customer)
    {
        // Check if user can view this specific customer
        $this->authorize('view', $customer);

        return view('customers.show', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $this->authorize('update', $customer);

        $customer->update($request->validated());

        return redirect()->route('customers.show', $customer);
    }
}
```

### Blade'de Policy Kullanımı

```blade
@can('create', App\Models\Customer::class)
    <a href="{{ route('customers.create') }}">Yeni Müşteri Ekle</a>
@endcan

@can('update', $customer)
    <a href="{{ route('customers.edit', $customer) }}">Düzenle</a>
@endcan

@can('delete', $customer)
    <form action="{{ route('customers.destroy', $customer) }}" method="POST">
        @csrf
        @method('DELETE')
        <button type="submit">Sil</button>
    </form>
@endcan
```

### API Route'larında Middleware

```php
// routes/api.php
Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('customers', CustomerController::class)
        ->middleware('can:viewAny,App\Models\Customer'); // Apply to index
});
```

### Manuel Policy Kontrolü

```php
if (auth()->user()->can('create', Customer::class)) {
    // User can create customers
}

if (auth()->user()->cannot('delete', $customer)) {
    abort(403, 'Bu işlem için yetkiniz bulunmamaktadır.');
}

// Gate facade ile
if (Gate::allows('update', $customer)) {
    // User can update this customer
}

// Response ile
$response = Gate::inspect('update', $customer);

if ($response->allowed()) {
    // Allowed
} else {
    echo $response->message();
}
```

## Yetkilendirme Mantığı

### 1. Permission-Based Authorization

Her policy metodu önce kullanıcının gerekli permission'a sahip olup olmadığını kontrol eder:

```php
public function update(User $user, Customer $customer): bool
{
    if (!$user->can('customers.update')) {
        return false;
    }

    // Additional checks...
}
```

### 2. Branch-Based Authorization

Şube bazlı yetkilendirme, multi-tenancy için kritiktir:

```php
private function checkBranchAccess(User $user, Customer $customer): bool
{
    // Super Admin and Org Admin have access to all branches
    if ($user->hasAnyRole(['Super Admin', 'Organization Admin'])) {
        return true;
    }

    // Other users can only access their branch's data
    return $user->branch_id === $customer->branch_id;
}
```

### 3. Role-Based Special Rules

Bazı işlemler sadece belirli roller tarafından yapılabilir:

```php
public function forceDelete(User $user, Customer $customer): bool
{
    if (!$user->can('customers.delete')) {
        return false;
    }

    // Only Super Admin and Org Admin can force delete
    return $user->hasAnyRole(['Super Admin', 'Organization Admin']);
}
```

## Yeni Policy Oluşturma

```bash
php artisan make:policy YourModelPolicy --model=YourModel
```

Örnek policy yapısı:

```php
<?php

namespace App\Policies;

use App\Models\YourModel;
use App\Models\User;

class YourModelPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('yourmodels.view');
    }

    public function view(User $user, YourModel $model): bool
    {
        if (!$user->can('yourmodels.view')) {
            return false;
        }

        return $this->checkBranchAccess($user, $model);
    }

    public function create(User $user): bool
    {
        return $user->can('yourmodels.create');
    }

    public function update(User $user, YourModel $model): bool
    {
        if (!$user->can('yourmodels.update')) {
            return false;
        }

        return $this->checkBranchAccess($user, $model);
    }

    public function delete(User $user, YourModel $model): bool
    {
        if (!$user->can('yourmodels.delete')) {
            return false;
        }

        return $this->checkBranchAccess($user, $model);
    }

    private function checkBranchAccess(User $user, YourModel $model): bool
    {
        if ($user->hasAnyRole(['Super Admin', 'Organization Admin'])) {
            return true;
        }

        return $user->branch_id === $model->branch_id;
    }
}
```

## Policy Kayıt

Policy'ler otomatik olarak keşfedilir. Manuel kayıt için `AuthServiceProvider`'ı kullanın:

```php
// app/Providers/AuthServiceProvider.php
protected $policies = [
    Customer::class => CustomerPolicy::class,
    Appointment::class => AppointmentPolicy::class,
];
```

## Best Practices

### 1. Tutarlı İsimlendirme
```php
// ✅ İyi
public function viewAny(User $user): bool
public function view(User $user, Model $model): bool
public function create(User $user): bool
public function update(User $user, Model $model): bool
public function delete(User $user, Model $model): bool

// ❌ Kötü
public function canView(User $user): bool
public function allowUpdate(User $user, Model $model): bool
```

### 2. Erken Return
```php
// ✅ İyi
public function update(User $user, Customer $customer): bool
{
    if (!$user->can('customers.update')) {
        return false;
    }

    return $this->checkBranchAccess($user, $customer);
}

// ❌ Kötü
public function update(User $user, Customer $customer): bool
{
    if ($user->can('customers.update')) {
        if ($this->checkBranchAccess($user, $customer)) {
            return true;
        }
    }
    return false;
}
```

### 3. Helper Metodlar
```php
private function checkBranchAccess(User $user, $model): bool
{
    if ($user->hasAnyRole(['Super Admin', 'Organization Admin'])) {
        return true;
    }

    return $user->branch_id === $model->branch_id;
}

private function isAdminUser(User $user): bool
{
    return $user->hasAnyRole(['Super Admin', 'Organization Admin']);
}
```

### 4. Custom Policy Metodları
```php
// Standart CRUD'un ötesinde custom metodlar ekleyebilirsiniz
public function export(User $user): bool
{
    return $user->can('customers.export');
}

public function viewReports(User $user): bool
{
    return $user->can('reports.customer');
}

public function bulkDelete(User $user): bool
{
    return $user->hasAnyRole(['Super Admin', 'Organization Admin'])
        && $user->can('customers.delete');
}
```

## Testing

```php
use App\Models\Customer;
use App\Models\User;
use Tests\TestCase;

class CustomerPolicyTest extends TestCase
{
    public function test_user_with_permission_can_view_customers()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('customers.view');

        $this->assertTrue($user->can('viewAny', Customer::class));
    }

    public function test_user_without_permission_cannot_view_customers()
    {
        $user = User::factory()->create();

        $this->assertFalse($user->can('viewAny', Customer::class));
    }

    public function test_user_can_only_access_own_branch_customers()
    {
        $branch1 = Branch::factory()->create();
        $branch2 = Branch::factory()->create();

        $user = User::factory()->create(['branch_id' => $branch1->id]);
        $user->givePermissionTo('customers.view');

        $ownCustomer = Customer::factory()->create(['branch_id' => $branch1->id]);
        $otherCustomer = Customer::factory()->create(['branch_id' => $branch2->id]);

        $this->assertTrue($user->can('view', $ownCustomer));
        $this->assertFalse($user->can('view', $otherCustomer));
    }

    public function test_admin_can_access_all_branches()
    {
        $user = User::factory()->create();
        $user->assignRole('Super Admin');

        $customer = Customer::factory()->create();

        $this->assertTrue($user->can('view', $customer));
    }
}
```

## Hata Ayıklama

Policy'nin neden çalışmadığını anlamak için:

```php
// Policy sonucunu inspect et
$response = Gate::inspect('update', $customer);

if ($response->denied()) {
    dd($response->message(), $response->code());
}

// Policy'nin kayıtlı olduğunu kontrol et
dd(Gate::getPolicyFor(Customer::class));

// Kullanıcının permission'larını kontrol et
dd(auth()->user()->getAllPermissions());

// Kullanıcının rollerini kontrol et
dd(auth()->user()->getRoleNames());
```

## İlgili Dosyalar

- Permissions: `database/seeders/RolePermissionSeeder.php`
- Roles: 11 role tanımlı
- Permissions: 80+ permission tanımlı
- Middleware: `app/Http/Middleware/`
