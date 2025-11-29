# Testing Guide - SalonPro

## Overview

This document provides comprehensive guidance on testing the SalonPro application. Our testing strategy includes unit tests, feature tests, and authorization tests to ensure code quality and system reliability.

## Table of Contents

1. [Testing Stack](#testing-stack)
2. [Running Tests](#running-tests)
3. [Test Structure](#test-structure)
4. [Authorization Tests](#authorization-tests)
5. [Writing New Tests](#writing-new-tests)
6. [Best Practices](#best-practices)
7. [Continuous Integration](#continuous-integration)

---

## Testing Stack

- **Framework**: Laravel 12 Testing Suite
- **Test Runner**: PHPUnit 11.5.3 / Pest 3.8
- **Database**: SQLite (in-memory for tests)
- **Authentication**: Laravel Sanctum
- **Authorization**: Spatie Permission + Laravel Policies
- **Factories**: Laravel Model Factories
- **Assertions**: PHPUnit + Laravel Testing Helpers

---

## Running Tests

### Run All Tests

```bash
php artisan test
```

### Run Specific Test Suite

```bash
# Run only unit tests
php artisan test --testsuite=Unit

# Run only feature tests
php artisan test --testsuite=Feature
```

### Run Specific Test File

```bash
php artisan test --filter=CustomerPolicyTest
php artisan test --filter=ProductAuthorizationTest
```

### Run Tests with Coverage (requires Xdebug)

```bash
php artisan test --coverage
php artisan test --coverage-html=coverage
```

### Run Tests in Parallel (faster)

```bash
php artisan test --parallel
```

---

## Test Structure

```
tests/
├── Unit/                           # Unit tests (isolated logic)
│   ├── Policies/                  # Policy authorization logic tests
│   │   ├── CustomerPolicyTest.php
│   │   └── ProductPolicyTest.php
│   ├── Repositories/              # Repository pattern tests
│   ├── Services/                  # Service layer tests
│   └── Models/                    # Model tests
│
├── Feature/                       # Integration tests (HTTP/Database)
│   ├── Authorization/             # API authorization tests
│   │   ├── CustomerAuthorizationTest.php
│   │   └── ProductAuthorizationTest.php
│   ├── API/                       # API endpoint tests
│   └── ...
│
└── TestCase.php                   # Base test case
```

### Test File Naming Conventions

- **Unit Tests**: `{ClassName}Test.php` (e.g., `CustomerPolicyTest.php`)
- **Feature Tests**: `{Feature}Test.php` (e.g., `CustomerAuthorizationTest.php`)
- **Test Methods**: `test_{action}_{expectation}` (e.g., `test_user_can_view_customer_from_same_branch`)

---

## Authorization Tests

### Policy Unit Tests

Policy tests verify that authorization logic works correctly for different user roles and scenarios.

**Example: Customer Policy Test**

```php
public function test_user_can_view_customer_from_same_branch(): void
{
    $user = User::factory()->create(['branch_id' => $this->branch->id]);
    $user->assignRole($this->viewerRole);

    $customer = Customer::factory()->create(['branch_id' => $this->branch->id]);

    $result = $this->policy->view($user, $customer);

    $this->assertTrue($result);
}

public function test_user_cannot_view_customer_from_different_branch(): void
{
    $branch2 = Branch::factory()->create();
    $user = User::factory()->create(['branch_id' => $this->branch->id]);
    $user->assignRole($this->viewerRole);

    $customer = Customer::factory()->create(['branch_id' => $branch2->id]);

    $result = $this->policy->view($user, $customer);

    $this->assertFalse($result);
}
```

### API Authorization Feature Tests

Feature tests verify that HTTP endpoints properly enforce authorization rules.

**Example: Customer Authorization Test**

```php
public function test_branch_manager_can_create_customer(): void
{
    $customerData = [
        'branch_id' => $this->branch->id,
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
        'phone' => '1234567890',
    ];

    $response = $this->actingAs($this->branchManager, 'sanctum')
        ->postJson('/api/v1/customers', $customerData);

    $response->assertStatus(201);
    $this->assertDatabaseHas('customers', [
        'first_name' => 'John',
        'email' => 'john@example.com',
    ]);
}

public function test_viewer_cannot_create_customer(): void
{
    $customerData = [
        'branch_id' => $this->branch->id,
        'first_name' => 'John',
        'last_name' => 'Doe',
    ];

    $response = $this->actingAs($this->viewer, 'sanctum')
        ->postJson('/api/v1/customers', $customerData);

    $response->assertStatus(403); // Forbidden
}
```

### Test Coverage by Role

| Role | Tested Permissions | Test Files |
|------|-------------------|------------|
| Super Admin | All operations, cross-branch access | CustomerPolicyTest, ProductPolicyTest, CustomerAuthorizationTest, ProductAuthorizationTest |
| Branch Manager | CRUD within own branch | CustomerPolicyTest, ProductPolicyTest, CustomerAuthorizationTest |
| Inventory Manager | Product CRUD within own branch | ProductPolicyTest, ProductAuthorizationTest |
| Viewer | Read-only access within own branch | CustomerPolicyTest, ProductPolicyTest, CustomerAuthorizationTest, ProductAuthorizationTest |

---

## Writing New Tests

### 1. Policy Unit Test Template

```php
<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use App\Models\Branch;
use App\Models\{YourModel};
use App\Models\User;
use App\Policies\{YourModel}Policy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class {YourModel}PolicyTest extends TestCase
{
    use RefreshDatabase;

    private {YourModel}Policy $policy;
    private Branch $branch;
    private Role $superAdminRole;
    private Role $viewerRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->policy = new {YourModel}Policy();
        $this->branch = Branch::factory()->create();
        $this->superAdminRole = Role::create(['name' => 'Super Admin']);
        $this->viewerRole = Role::create(['name' => 'Viewer']);
    }

    public function test_user_can_view_model_from_same_branch(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->viewerRole);

        $model = {YourModel}::factory()->create(['branch_id' => $this->branch->id]);

        $result = $this->policy->view($user, $model);

        $this->assertTrue($result);
    }

    // Add more test methods...
}
```

### 2. API Authorization Test Template

```php
<?php

declare(strict_types=1);

namespace Tests\Feature\Authorization;

use App\Models\Branch;
use App\Models\{YourModel};
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class {YourModel}AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private Branch $branch;
    private User $admin;
    private User $viewer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->branch = Branch::factory()->create();

        $adminRole = Role::create(['name' => 'Admin']);
        $viewerRole = Role::create(['name' => 'Viewer']);

        Permission::create(['name' => '{models}.view']);
        Permission::create(['name' => '{models}.create']);

        $adminRole->givePermissionTo(['{models}.view', '{models}.create']);
        $viewerRole->givePermissionTo(['{models}.view']);

        $this->admin = User::factory()->create(['branch_id' => $this->branch->id]);
        $this->admin->assignRole($adminRole);

        $this->viewer = User::factory()->create(['branch_id' => $this->branch->id]);
        $this->viewer->assignRole($viewerRole);
    }

    public function test_authenticated_user_can_view_models(): void
    {
        $response = $this->actingAs($this->viewer, 'sanctum')
            ->getJson('/api/v1/{models}');

        $response->assertStatus(200);
    }

    public function test_unauthenticated_user_cannot_access_models(): void
    {
        $response = $this->getJson('/api/v1/{models}');

        $response->assertStatus(401);
    }

    // Add more test methods...
}
```

---

## Best Practices

### 1. Test Independence

Each test should be completely independent and not rely on other tests:

```php
// ✅ Good - Self-contained test
public function test_can_create_customer(): void
{
    $user = User::factory()->create();
    $data = Customer::factory()->make()->toArray();

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/customers', $data);

    $response->assertStatus(201);
}

// ❌ Bad - Depends on previous test
public function test_can_update_customer(): void
{
    // Assumes customer from previous test exists
    $response = $this->putJson('/api/v1/customers/1', ['name' => 'Updated']);
}
```

### 2. Use Factories

Always use factories instead of manual model creation:

```php
// ✅ Good
$customer = Customer::factory()->create(['branch_id' => $branch->id]);

// ❌ Bad
$customer = new Customer();
$customer->first_name = 'John';
$customer->last_name = 'Doe';
$customer->branch_id = $branch->id;
$customer->save();
```

### 3. Descriptive Test Names

Test names should clearly describe what they test:

```php
// ✅ Good
public function test_branch_manager_cannot_delete_other_branch_customer(): void

// ❌ Bad
public function test_delete(): void
```

### 4. Use RefreshDatabase

Always use `RefreshDatabase` trait to ensure clean database state:

```php
class CustomerTest extends TestCase
{
    use RefreshDatabase; // ✅ Always include this

    // ...
}
```

### 5. Test Both Success and Failure Cases

```php
// ✅ Test success
public function test_user_can_create_product_with_valid_data(): void
{
    // ...
    $response->assertStatus(201);
}

// ✅ Test failure
public function test_user_cannot_create_product_without_required_fields(): void
{
    // ...
    $response->assertStatus(422);
}
```

### 6. Clear Assertions

Use specific assertions that clearly indicate what is being tested:

```php
// ✅ Good - Specific assertions
$response->assertStatus(200);
$response->assertJsonStructure(['data' => ['id', 'name']]);
$this->assertDatabaseHas('customers', ['email' => 'test@example.com']);

// ❌ Bad - Vague assertion
$this->assertTrue($response->isSuccessful());
```

---

## Continuous Integration

### GitHub Actions Example

```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
          extensions: mbstring, pdo_sqlite

      - name: Install Dependencies
        run: composer install

      - name: Run Tests
        run: php artisan test --parallel
```

---

## Test Coverage Goals

- **Policy Tests**: 100% coverage for all authorization methods
- **API Tests**: 80%+ coverage for critical endpoints
- **Unit Tests**: 70%+ coverage for business logic
- **Feature Tests**: Key user flows fully tested

---

## Current Test Status

### Implemented Tests

✅ **Policy Unit Tests**
- CustomerPolicyTest (10 tests)
- ProductPolicyTest (8 tests)

✅ **API Authorization Tests**
- CustomerAuthorizationTest (13 tests)
- ProductAuthorizationTest (9 tests)

### To Be Implemented

⏳ **Policy Tests Needed**
- AppointmentPolicy
- EmployeePolicy
- InvoicePolicy
- PaymentPolicy
- ServicePolicy
- SalePolicy
- BranchPolicy
- ExpensePolicy

⏳ **API Tests Needed**
- Appointment CRUD with authorization
- Employee CRUD with authorization
- Service CRUD with authorization
- Sales CRUD with authorization
- Inventory management with authorization

⏳ **Service Layer Tests**
- CustomerService
- ProductService
- AppointmentService
- InvoiceService

⏳ **Repository Tests**
- CRUD operations
- Query optimization
- Relationship loading

---

## Troubleshooting

### Common Issues

**Issue**: Tests fail with "Table already exists"
**Solution**: Ensure `RefreshDatabase` trait is used and migrations don't have duplicates

**Issue**: Authentication fails in tests
**Solution**: Use `actingAs($user, 'sanctum')` for API tests

**Issue**: Tests are slow
**Solution**: Use SQLite in-memory database (configured in `phpunit.xml`)

---

## Additional Resources

- [Laravel Testing Documentation](https://laravel.com/docs/11.x/testing)
- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [Pest Documentation](https://pestphp.com/)
- [Laravel Factories](https://laravel.com/docs/11.x/eloquent-factories)
- [Spatie Permission Testing](https://spatie.be/docs/laravel-permission/v6/basic-usage/testing)

---

**Last Updated**: Session 13 - November 2025
**Test Files**: 40+ test methods across 4 test files
**Coverage**: Authorization system (Policies + API endpoints)
