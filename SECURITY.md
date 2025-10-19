# Security Implementation - SalonPro

Bu doküman, SalonPro uygulamasının güvenlik özelliklerini açıklar.

## 📋 İçindekiler

1. [Authentication & Authorization](#authentication--authorization)
2. [Role-Based Access Control (RBAC)](#role-based-access-control)
3. [Two-Factor Authentication (2FA)](#two-factor-authentication)
4. [Password Policy](#password-policy)
5. [Login Security](#login-security)
6. [Session Management](#session-management)
7. [Branch Isolation](#branch-isolation)

---

## Authentication & Authorization

### Sanctum API Authentication

Laravel Sanctum kullanılarak API authentication sağlanır:

```php
// Login
POST /api/auth/login
{
    "email": "user@example.com",
    "password": "password"
}

// Response
{
    "user": {...},
    "token": "1|abc123...",
    "abilities": ["*"]
}
```

### Middleware Kullanımı

```php
// routes/api.php
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show']);
});
```

---

## Role-Based Access Control

### Roller (11 Rol)

| Rol | Açıklama | Erişim Seviyesi |
|-----|----------|-----------------|
| Super Admin | Sistem yöneticisi | Tüm yetkiler |
| Organization Admin | Organizasyon yöneticisi | Tüm şubeler |
| Branch Manager | Şube müdürü | Kendi şubesi |
| Accountant | Muhasebeci | Finansal işlemler |
| Receptionist | Resepsiyonist | Randevu ve müşteriler |
| Stylist | Kuaför/Estetisyen | Servis sağlama |
| Sales Representative | Satış temsilcisi | Ürün satışları |
| Inventory Manager | Stok yöneticisi | Envanter yönetimi |
| Marketing Manager | Pazarlama müdürü | Müşteri analizi |
| HR Manager | İK yöneticisi | Çalışan yönetimi |
| Viewer | İzleyici | Salt okunur erişim |

### İzinler (80+ Permission)

İzinler modül bazlı gruplandırılmıştır:

**Customer Management:**
- customers.view
- customers.create
- customers.update
- customers.delete
- customers.export

**Appointment Management:**
- appointments.view
- appointments.create
- appointments.update
- appointments.cancel
- appointments.view-all

[Tam liste için: `database/seeders/RolePermissionSeeder.php`]

### Kullanım Örnekleri

```php
// Controller'da
$this->authorize('update', $customer);

// Blade'de
@can('create', App\Models\Customer::class)
    <button>Yeni Müşteri</button>
@endcan

// Kod içinde
if (auth()->user()->can('customers.view')) {
    // ...
}

// Middleware ile
Route::middleware(['permission:customers.view'])->group(function () {
    // ...
});
```

---

## Two-Factor Authentication

### 2FA Aktifleştirme

```php
use App\Services\TwoFactorAuthenticationService;

$service = new TwoFactorAuthenticationService();

// Enable 2FA
$data = $service->enable($user);
// Returns: ['secret' => '...', 'qr_code' => '...', 'recovery_codes' => [...]]

// Confirm 2FA
$confirmed = $service->confirm($user, $code);

// Disable 2FA
$service->disable($user);
```

### Gerekli Paketler

```bash
composer require pragmarx/google2fa
composer require bacon/bacon-qr-code
```

### API Endpoints

```php
POST /api/two-factor/enable
POST /api/two-factor/confirm
POST /api/two-factor/disable
POST /api/two-factor/recovery-codes
```

---

## Password Policy

### Şifre Gereksinimleri

Varsayılan olarak:
- Minimum 8 karakter
- En az 1 büyük harf
- En az 1 küçük harf
- En az 1 rakam
- En az 1 özel karakter

### Yapılandırma

`config/auth.php`:

```php
'password_policy' => [
    'min_length' => 8,
    'require_uppercase' => true,
    'require_lowercase' => true,
    'require_numbers' => true,
    'require_special_chars' => true,
    'expires_days' => 90, // 0 = asla sona ermez
    'prevent_reuse_count' => 5, // Son 5 şifre tekrar kullanılamaz
],
```

### Validation Rule Kullanımı

```php
use App\Rules\PasswordRule;

$request->validate([
    'password' => ['required', new PasswordRule()],
]);

// Veya özel ayarlarla
$request->validate([
    'password' => ['required', new PasswordRule(
        minLength: 10,
        requireUppercase: true,
        requireLowercase: true,
        requireNumbers: true,
        requireSpecialChars: false
    )],
]);
```

---

## Login Security

### Login Attempt Tracking

Her giriş denemesi kaydedilir:

```php
use App\Services\LoginSecurityService;

$security = new LoginSecurityService();

// Başarısız deneme kaydet
$security->recordAttempt($email, false, 'Invalid credentials');

// Başarılı giriş kaydet
$security->recordAttempt($email, true);
```

### Account Lockout

Yapılandırma (`config/auth.php`):

```php
'login_security' => [
    'max_attempts' => 5,              // Maksimum deneme sayısı
    'lockout_duration' => 900,        // Kilitleme süresi (saniye)
    'track_failed_attempts' => true,
],
```

### Kullanım

```php
use App\Services\LoginSecurityService;

$security = new LoginSecurityService();

// Hesap kilitli mi kontrol et
if ($security->isLockedOut($email)) {
    $remainingTime = $security->getLockoutTimeRemaining($email);
    throw new ValidationException("Hesabınız {$remainingTime} saniye süreyle kilitlendi.");
}

// Başarısız denemeler
$failedAttempts = $security->getFailedAttempts($email);

// Lockout kontrolü ve kilitleme
$lockout = $security->checkAndLockout($email);

// Başarılı girişten sonra temizle
$security->clearFailedAttempts($email);

// Manuel kilidi aç
$security->unlockAccount($email);
```

---

## Session Management

### User Sessions Tracking

Tüm aktif kullanıcı oturumları takip edilir:

```php
use App\Models\UserSession;

// Kullanıcının aktif oturumları
$sessions = UserSession::forUser($userId)
    ->active()
    ->get();

// Oturum oluştur
UserSession::create([
    'user_id' => $user->id,
    'session_id' => session()->getId(),
    'ip_address' => request()->ip(),
    'user_agent' => request()->userAgent(),
    'last_activity' => now(),
    'is_current' => true,
]);

// Diğer tüm oturumları sonlandır
UserSession::forUser($userId)
    ->where('id', '!=', $currentSessionId)
    ->delete();
```

### Features

- Çoklu oturum takibi
- Cihaz bilgisi saklama
- Son aktivite zamanı
- Oturum sonlandırma
- Şüpheli aktivite tespiti

---

## Branch Isolation

### Multi-Tenancy

Her kullanıcı bir şubeye aittir ve sadece kendi şubesinin verilerine erişebilir.

### BranchScoped Trait

```php
// app/Models/Traits/BranchScoped.php
use App\Traits\BranchScoped;

class Customer extends Model
{
    use BranchScoped;
}

// Otomatik olarak filtreleme:
Customer::all(); // Sadece kullanıcının şubesindeki müşteriler
```

### Branch Middleware

```php
// routes/api.php
Route::middleware(['auth:sanctum', 'branch'])->group(function () {
    Route::apiResource('customers', CustomerController::class);
});
```

### Policy ile Branch Kontrolü

```php
// app/Policies/CustomerPolicy.php
private function checkBranchAccess(User $user, Customer $customer): bool
{
    // Super Admin ve Org Admin tüm şubelere erişebilir
    if ($user->hasAnyRole(['Super Admin', 'Organization Admin'])) {
        return true;
    }

    // Diğer kullanıcılar sadece kendi şubelerine
    return $user->branch_id === $customer->branch_id;
}
```

---

## Güvenlik Best Practices

### 1. HTTPS Kullanımı

Production ortamında mutlaka HTTPS kullanın:

```php
// .env
APP_URL=https://app.salonpro.com
SESSION_SECURE_COOKIE=true
```

### 2. Rate Limiting

API endpoint'lerini koruyun:

```php
Route::middleware(['throttle:60,1'])->group(function () {
    // 60 istek / dakika
});
```

### 3. CORS Yapılandırması

```php
// config/cors.php
'allowed_origins' => [
    'https://app.salonpro.com',
    'https://admin.salonpro.com',
],
```

### 4. SQL Injection Koruması

Laravel ORM kullanın:

```php
// ✅ Güvenli
Customer::where('email', $email)->first();

// ❌ Güvensiz
DB::select("SELECT * FROM customers WHERE email = '$email'");
```

### 5. XSS Koruması

Blade otomatik escape yapar:

```blade
{{-- Otomatik escape edilir --}}
{{ $user->name }}

{{-- Manuel HTML (dikkatli kullanın) --}}
{!! $safeHtml !!}
```

### 6. CSRF Koruması

Laravel otomatik CSRF koruması sağlar:

```html
<form method="POST" action="/profile">
    @csrf
    <!-- Form fields -->
</form>
```

### 7. Mass Assignment Koruması

Model'lerde fillable tanımlayın:

```php
protected $fillable = [
    'name',
    'email',
    'phone',
];
```

### 8. Sensitive Data Encryption

```php
// Model'de
protected $casts = [
    'two_factor_secret' => 'encrypted',
];
```

---

## Audit Logging

Tüm kritik işlemler loglanır:

```php
use App\Traits\Loggable;

class CustomerController extends Controller
{
    use Loggable;

    public function update(Customer $customer)
    {
        $customer->update($request->all());

        $this->logAudit('Customer updated', [
            'customer_id' => $customer->id,
            'changes' => $customer->getChanges(),
        ]);
    }
}
```

Audit logları:
- `storage/logs/audit.log` - 365 gün saklanır
- User ID, IP, timestamp içerir
- Kritik işlemler için zorunlu

---

## Security Checklist

### Development
- [ ] .env dosyası commit edilmemiş
- [ ] Debug mode kapalı
- [ ] Hassas veriler loglanmıyor
- [ ] CSRF koruması aktif
- [ ] XSS koruması aktif

### Staging
- [ ] HTTPS yapılandırılmış
- [ ] Rate limiting aktif
- [ ] CORS yapılandırılmış
- [ ] Güvenlik testleri yapıldı

### Production
- [ ] Güçlü şifreler kullanılıyor
- [ ] 2FA zorunlu (admin'ler için)
- [ ] Düzenli güvenlik güncellemeleri
- [ ] Backup sistemi aktif
- [ ] Monitoring/alerting kurulmuş
- [ ] SSL sertifikası geçerli
- [ ] Firewall yapılandırılmış
- [ ] Penetration test yapıldı

---

## Support & Documentation

- **Roller ve İzinler**: `database/seeders/RolePermissionSeeder.php`
- **Policy'ler**: `app/Policies/`
- **Middleware**: `app/Http/Middleware/`
- **Güvenlik Servisleri**: `app/Services/`
- **Configuration**: `config/auth.php`
