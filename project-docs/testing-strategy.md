# Testing Strategy - SalonPro

## Testing Overview

Comprehensive testing strategy to ensure quality, reliability, and performance of SalonPro.

**Target Coverage:** 85%+
**Quality Standard:** PHPStan Level 8

---

## Testing Pyramid

```
        /\
       /E2E\      E2E Tests (5%)
      /------\
     /Integration\ Integration Tests (15%)
    /------------\
   / Feature Tests \ Feature Tests (30%)
  /----------------\
 /   Unit Tests     \ Unit Tests (50%)
/--------------------\
```

---

## Unit Testing

### Purpose
Test individual units of code in isolation

### Scope
- Model methods
- Service methods
- Repository methods
- Action classes
- Utility functions
- Value objects
- DTOs

### Tools
- **Pest PHP 2.0+**
- PHPUnit (underlying)

### Example
```php
test('appointment can calculate duration', function () {
    $appointment = Appointment::factory()->create([
        'scheduled_at' => now(),
        'duration' => 60
    ]);

    expect($appointment->duration)->toBe(60);
    expect($appointment->end_time)->toEqual($appointment->scheduled_at->addMinutes(60));
});
```

### Coverage Target
- Critical business logic: 100%
- Models: 90%
- Services: 90%
- Repositories: 85%
- Overall: 85%+

---

## Feature Testing

### Purpose
Test complete features through HTTP requests

### Scope
- API endpoints
- Web routes
- Authentication
- Authorization
- Validation
- Business workflows

### Example
```php
test('user can create appointment', function () {
    $user = User::factory()->receptionist()->create();
    $customer = Customer::factory()->create();
    $service = Service::factory()->create();
    $employee = Employee::factory()->create();

    actingAs($user)
        ->post('/api/v1/appointments', [
            'customer_id' => $customer->id,
            'service_ids' => [$service->id],
            'employee_id' => $employee->id,
            'scheduled_at' => now()->addDays(1)->format('Y-m-d H:i:s')
        ])
        ->assertStatus(201)
        ->assertJson([
            'data' => [
                'customer_id' => $customer->id
            ]
        ]);

    assertDatabaseHas('appointments', [
        'customer_id' => $customer->id
    ]);
});
```

### Coverage Areas
- All API endpoints
- CRUD operations
- Authentication flows
- Authorization checks
- Validation rules
- Error handling

---

## Integration Testing

### Purpose
Test interaction between multiple components

### Scope
- Multi-module interactions
- Database transactions
- External services (mocked)
- Queue jobs
- Events and listeners
- Cache operations

### Example
```php
test('completing appointment updates inventory and calculates commission', function () {
    // Setup
    $appointment = Appointment::factory()->create();
    $product = Product::factory()->create(['stock' => 10]);

    // Act
    $result = app(AppointmentService::class)
        ->complete($appointment, [
            'products_used' => [
                ['product_id' => $product->id, 'quantity' => 2]
            ]
        ]);

    // Assert
    expect($result->status)->toBe(AppointmentStatus::COMPLETED);
    expect($product->fresh()->stock)->toBe(8);
    assertDatabaseHas('commissions', [
        'appointment_id' => $appointment->id
    ]);
});
```

---

## End-to-End (E2E) Testing

### Purpose
Test complete user workflows in browser

### Tools
- **Laravel Dusk**
- ChromeDriver

### Scope
- Critical user paths
- Appointment booking flow
- Payment processing
- Report generation
- Multi-step forms

### Example
```php
test('customer can book appointment online', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/book')
            ->select('service', $service->id)
            ->click('@next-button')
            ->waitFor('@date-picker')
            ->click('@available-slot')
            ->type('name', 'John Doe')
            ->type('email', 'john@example.com')
            ->type('phone', '+905551234567')
            ->press('Confirm Booking')
            ->waitForText('Appointment confirmed')
            ->assertSee('Your appointment is confirmed');
    });
});
```

---

## Performance Testing

### Load Testing
- **Tool:** Apache JMeter / k6
- **Scenarios:**
  - 100 concurrent users
  - 500 concurrent users
  - 1000+ concurrent users

### Stress Testing
- Find breaking point
- Monitor resource usage
- Identify bottlenecks

### Targets
- API response time < 200ms (95th percentile)
- Page load time < 2 seconds
- Database queries < 50ms
- Support 1000+ concurrent users

---

## Security Testing

### Automated Scans
- **OWASP ZAP** for vulnerability scanning
- **PHPStan** for static analysis
- **Snyk** for dependency vulnerabilities

### Manual Testing
- Authentication bypass attempts
- Authorization checks
- SQL injection tests
- XSS attempts
- CSRF verification
- File upload security
- Session management

### Penetration Testing
- Annual professional pentest
- Remediation of findings
- Re-test after fixes

---

## Accessibility Testing

### Tools
- **Axe DevTools**
- **WAVE**
- **Lighthouse**

### Checks
- WCAG 2.1 AA compliance
- Keyboard navigation
- Screen reader compatibility
- Color contrast
- Focus indicators
- Alt text for images
- ARIA labels

---

## Browser & Device Testing

### Browsers
- Chrome (latest 2 versions)
- Firefox (latest 2 versions)
- Safari (latest 2 versions)
- Edge (latest 2 versions)

### Devices
- Desktop (1920x1080, 1366x768)
- Tablet (iPad, Android tablets)
- Mobile (iPhone, Android phones)

### Tools
- BrowserStack
- Manual testing

---

## Multi-Language Testing

### Test Cases
- All pages in Turkish
- All pages in English
- Language switching
- Translation completeness
- Right-to-left layout (future)

### Automated
```php
test('appointment page loads in turkish', function () {
    app()->setLocale('tr');

    $this->get('/appointments')
        ->assertSee('Randevular')
        ->assertSee('Yeni Randevu');
});

test('appointment page loads in english', function () {
    app()->setLocale('en');

    $this->get('/appointments')
        ->assertSee('Appointments')
        ->assertSee('New Appointment');
});
```

---

## Multi-Currency Testing

### Test Cases
- Prices display in TRY
- Prices display in USD
- Currency conversion accuracy
- Historical rates
- Reports in both currencies

### Automated
```php
test('service displays price in selected currency', function () {
    $service = Service::factory()->create([
        'price_try' => 100,
        'price_usd' => 3.5
    ]);

    $response = $this->withHeader('X-Currency', 'TRY')
        ->get("/api/v1/services/{$service->id}");

    expect($response->json('data.price.amount'))->toBe(100.0);
    expect($response->json('data.price.currency'))->toBe('TRY');
});
```

---

## Regression Testing

### Automated Regression Suite
- Run on every commit
- Critical path tests
- Major feature tests

### Manual Regression
- Before each release
- After major changes
- Following bug fixes

---

## Test Data Management

### Factories
```php
Customer::factory()->create();
Customer::factory()->vip()->create();
Customer::factory()->count(10)->create();
```

### Seeders
- Development environment
- Test environment
- Demo data

### Database Refresh
```php
use RefreshDatabase;
```

---

## Continuous Integration

### GitHub Actions Workflow

```yaml
name: Tests

on: [push, pull_request]

jobs:
  tests:
    runs-on: ubuntu-latest

    services:
      mysql:
        image: mysql:8.0
        env:
          MYSQL_DATABASE: salonpro_test
      redis:
        image: redis:7

    steps:
      - uses: actions/checkout@v3

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.3
          extensions: mbstring, pdo, mysql, redis
          coverage: xdebug

      - name: Install Dependencies
        run: composer install

      - name: Copy Environment
        run: cp .env.testing .env

      - name: Generate Key
        run: php artisan key:generate

      - name: Run Migrations
        run: php artisan migrate --seed

      - name: Run Tests
        run: php artisan test --coverage --min=85

      - name: PHPStan
        run: vendor/bin/phpstan analyse

      - name: Laravel Pint
        run: vendor/bin/pint --test
```

---

## Test Organization

### Directory Structure
```
tests/
├── Feature/
│   ├── API/
│   │   ├── AppointmentTest.php
│   │   ├── CustomerTest.php
│   │   └── ...
│   ├── Web/
│   │   ├── DashboardTest.php
│   │   └── ...
│   └── ...
├── Integration/
│   ├── AppointmentWorkflowTest.php
│   ├── PaymentProcessingTest.php
│   └── ...
├── Unit/
│   ├── Models/
│   ├── Services/
│   ├── Repositories/
│   └── ...
└── Browser/
    ├── AppointmentBookingTest.php
    └── ...
```

---

## Test Execution

### Run All Tests
```bash
php artisan test
```

### Run Specific Suite
```bash
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit
```

### Run Specific File
```bash
php artisan test tests/Feature/API/AppointmentTest.php
```

### Run with Coverage
```bash
php artisan test --coverage --min=85
```

### Parallel Execution
```bash
php artisan test --parallel
```

---

## Code Quality Checks

### PHPStan (Level 8)
```bash
vendor/bin/phpstan analyse
```

### Laravel Pint (Code Style)
```bash
vendor/bin/pint
vendor/bin/pint --test  # Check only
```

---

## Bug Tracking

### Bug Report Template
- **Title:** Clear, descriptive
- **Environment:** Dev/Staging/Prod
- **Steps to Reproduce:** Detailed
- **Expected Result:** What should happen
- **Actual Result:** What happened
- **Screenshots:** If applicable
- **Logs:** Error messages
- **Priority:** Critical/High/Medium/Low

### Bug Fix Workflow
1. Create failing test
2. Fix the bug
3. Verify test passes
4. Add regression test
5. Code review
6. Merge to main

---

## Testing Metrics

### Track
- Test coverage percentage
- Test execution time
- Flaky test count
- Bug escape rate
- Mean time to detection
- Mean time to resolution

### Goals
- Coverage: 85%+
- Execution time: < 5 minutes
- Flaky tests: 0
- Bug escape rate: < 5%

---

## Best Practices

1. **Write tests first** (TDD where appropriate)
2. **Keep tests fast**
3. **Test behavior, not implementation**
4. **Use descriptive test names**
5. **Arrange, Act, Assert pattern**
6. **One assertion per test** (when practical)
7. **Avoid test interdependence**
8. **Use factories for test data**
9. **Mock external services**
10. **Keep tests maintainable**

---

**Document Version:** 1.0
**Last Updated:** 2025-01-15
