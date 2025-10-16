# Contributing to SalonPro

First off, thank you for considering contributing to SalonPro! 🎉

Following these guidelines helps to communicate that you respect the time of the developers managing and developing this open source project. In return, they should reciprocate that respect in addressing your issue, assessing changes, and helping you finalize your pull requests.

## Table of Contents

- [Code of Conduct](#code-of-conduct)
- [Getting Started](#getting-started)
- [How Can I Contribute?](#how-can-i-contribute)
- [Development Workflow](#development-workflow)
- [Coding Standards](#coding-standards)
- [Testing Requirements](#testing-requirements)
- [Commit Message Guidelines](#commit-message-guidelines)
- [Pull Request Process](#pull-request-process)
- [Architecture Guidelines](#architecture-guidelines)
- [Documentation](#documentation)

## Code of Conduct

This project and everyone participating in it is governed by our Code of Conduct. By participating, you are expected to uphold this code. Please report unacceptable behavior to conduct@salonpro.com.

### Our Pledge

- Be respectful and inclusive
- Welcome newcomers warmly
- Be patient with questions
- Assume good intentions
- Focus on what is best for the community
- Show empathy towards other community members

## Getting Started

### Prerequisites

Before you begin, ensure you have the following installed:
- PHP 8.3 or higher
- Composer 2.x
- Node.js 18.x or higher
- MySQL 8.0+ or MariaDB 10.6+
- Redis 7+
- Git

### Development Setup

1. **Fork the repository** on GitHub

2. **Clone your fork:**
```bash
git clone https://github.com/YOUR_USERNAME/salonpro.git
cd salonpro
```

3. **Add upstream remote:**
```bash
git remote add upstream https://github.com/salonpro/salonpro.git
```

4. **Install dependencies:**
```bash
composer install
npm install
```

5. **Copy environment file:**
```bash
cp .env.example .env
php artisan key:generate
```

6. **Configure database in `.env`**

7. **Run migrations with seeders:**
```bash
php artisan migrate --seed
```

8. **Build frontend assets:**
```bash
npm run dev
```

9. **Start development server:**
```bash
php artisan serve
```

10. **Run tests to verify setup:**
```bash
php artisan test
```

## How Can I Contribute?

### Reporting Bugs

Before creating bug reports, please check existing issues to avoid duplicates.

**When creating a bug report, include:**

- **Clear title and description**
- **Steps to reproduce** the issue
- **Expected behavior** vs **actual behavior**
- **Screenshots** if applicable
- **Environment details:**
  - SalonPro version
  - PHP version
  - Database version
  - Operating system
  - Browser (for frontend issues)
- **Error messages and logs**
- **Possible solution** (if you have one)

**Use this template:**

```markdown
## Bug Description
A clear and concise description of the bug.

## Steps to Reproduce
1. Go to '...'
2. Click on '...'
3. Scroll down to '...'
4. See error

## Expected Behavior
What you expected to happen.

## Actual Behavior
What actually happened.

## Screenshots
If applicable, add screenshots.

## Environment
- SalonPro Version: [e.g., 1.0.0]
- PHP Version: [e.g., 8.3.0]
- Database: [e.g., MySQL 8.0.35]
- OS: [e.g., Ubuntu 22.04]
- Browser: [e.g., Chrome 120]

## Additional Context
Any other relevant information.
```

### Suggesting Enhancements

Enhancement suggestions are tracked as GitHub issues.

**When suggesting enhancements, include:**

- **Clear title and description**
- **Use case** - Why is this enhancement needed?
- **Proposed solution** - How should it work?
- **Alternative solutions** considered
- **Additional context** - Mockups, examples, etc.

### Your First Code Contribution

Unsure where to begin? Look for issues labeled:

- `good first issue` - Simple issues perfect for newcomers
- `help wanted` - Issues we need help with
- `documentation` - Documentation improvements

### Pull Requests

We actively welcome your pull requests!

## Development Workflow

### 1. Create a Branch

Always create a new branch for your work:

```bash
# Update your fork
git checkout main
git pull upstream main

# Create feature branch
git checkout -b feature/amazing-feature

# Or bug fix branch
git checkout -b fix/bug-description
```

**Branch naming conventions:**
- `feature/feature-name` - New features
- `fix/bug-description` - Bug fixes
- `docs/what-changed` - Documentation
- `refactor/what-refactored` - Code refactoring
- `test/what-tested` - Adding tests
- `chore/what-changed` - Maintenance tasks

### 2. Make Your Changes

Follow our [Coding Standards](#coding-standards) and [Architecture Guidelines](#architecture-guidelines).

### 3. Test Your Changes

```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage --min=85

# Run specific tests
php artisan test --filter=AppointmentTest
```

### 4. Commit Your Changes

Follow our [Commit Message Guidelines](#commit-message-guidelines).

```bash
git add .
git commit -m "feat(appointments): add conflict detection"
```

### 5. Push to Your Fork

```bash
git push origin feature/amazing-feature
```

### 6. Submit a Pull Request

Go to GitHub and create a pull request from your branch to our `main` branch.

## Coding Standards

### PHP Standards

SalonPro follows **PSR-12** coding standard with Laravel conventions.

#### Use Strict Types

```php
<?php

declare(strict_types=1);

namespace App\Services;
```

#### Type Hints Everywhere

```php
// ✅ CORRECT
public function create(AppointmentData $data): Appointment
{
    return $this->repository->create($data);
}

// ❌ WRONG
public function create($data)
{
    return $this->repository->create($data);
}
```

#### Use DTOs with Spatie Laravel Data

```php
use Spatie\LaravelData\Data;

class AppointmentData extends Data
{
    public function __construct(
        public string $customer_id,
        public string $employee_id,
        public Carbon $scheduled_at,
        public int $duration,
    ) {}
}
```

#### Follow Clean Architecture

**Controller (Thin):**
```php
public function store(CreateAppointmentRequest $request)
{
    $data = AppointmentData::from($request->validated());
    $appointment = $this->service->create($data);

    return new AppointmentResource($appointment);
}
```

**Service (Business Logic):**
```php
public function create(AppointmentData $data): Appointment
{
    $this->validateBusinessRules($data);

    return DB::transaction(function () use ($data) {
        $appointment = $this->repository->create($data);
        event(new AppointmentCreated($appointment));
        return $appointment;
    });
}
```

**Repository (Data Access):**
```php
public function create(AppointmentData $data): Appointment
{
    return Appointment::create($data->toArray());
}
```

### Multi-Language Support

**ALWAYS** support both Turkish and English:

```php
// ✅ CORRECT: Use translation keys
return response()->json([
    'message' => __('appointments.created_successfully')
]);

// ❌ WRONG: Hardcoded text
return response()->json([
    'message' => 'Randevu başarıyla oluşturuldu'
]);
```

**Database fields:**
```php
// Migration
$table->json('name'); // {"tr": "Saç Kesimi", "en": "Haircut"}

// Model accessor
public function getTranslatedNameAttribute(): string
{
    return $this->name[app()->getLocale()] ?? $this->name['tr'];
}
```

### Multi-Currency Support

**ALWAYS** support both TRY and USD:

```php
// ✅ CORRECT: Dual columns
Schema::create('services', function (Blueprint $table) {
    $table->decimal('price_try', 10, 2);
    $table->decimal('price_usd', 10, 2);
});

// ❌ WRONG: Single column
$table->decimal('price', 10, 2); // Which currency?
```

### Branch Scoping

**ALWAYS** scope business data by branch:

```php
// ✅ CORRECT: Branch scoping
$appointments = Appointment::where('branch_id', auth()->user()->branch_id)
    ->get();

// ❌ WRONG: No branch check
$appointments = Appointment::all(); // Access to all branches!
```

### Vue.js Standards

**Use Composition API:**

```vue
<script setup>
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
const appointments = ref([])

const filteredAppointments = computed(() => {
  return appointments.value.filter(/* ... */)
})
</script>

<template>
  <h1>{{ t('appointments.title') }}</h1>
</template>
```

### Code Quality Tools

**Before committing, run:**

```bash
# Fix code style
./vendor/bin/pint

# Check code style
./vendor/bin/pint --test

# Static analysis
./vendor/bin/phpstan analyse

# Run tests
php artisan test
```

## Testing Requirements

### Coverage Target: 85%+

All new features MUST include tests.

### Test Types

#### Unit Tests (50%)
```php
test('appointment calculates duration correctly', function () {
    $appointment = Appointment::factory()->create([
        'scheduled_at' => now(),
        'duration' => 60
    ]);

    expect($appointment->duration)->toBe(60);
    expect($appointment->end_time)->toEqual(
        $appointment->scheduled_at->addMinutes(60)
    );
});
```

#### Feature Tests (30%)
```php
test('user can create appointment', function () {
    $user = User::factory()->receptionist()->create();

    actingAs($user)
        ->post('/api/v1/appointments', [
            'customer_id' => Customer::factory()->create()->id,
            'service_ids' => [Service::factory()->create()->id],
            'scheduled_at' => now()->addDays(1),
        ])
        ->assertStatus(201);
});
```

#### Integration Tests (15%)
```php
test('completing appointment updates inventory and commission', function () {
    $appointment = Appointment::factory()->create();
    $product = Product::factory()->create(['stock' => 10]);

    app(AppointmentService::class)->complete($appointment, [
        'products_used' => [['product_id' => $product->id, 'quantity' => 2]]
    ]);

    expect($product->fresh()->stock)->toBe(8);
    assertDatabaseHas('commissions', ['appointment_id' => $appointment->id]);
});
```

### Test Multi-Language

```php
test('service displays name in turkish', function () {
    app()->setLocale('tr');
    $service = Service::factory()->create();
    expect($service->translated_name)->toContain('tr');
});

test('service displays name in english', function () {
    app()->setLocale('en');
    $service = Service::factory()->create();
    expect($service->translated_name)->toContain('en');
});
```

## Commit Message Guidelines

We follow the [Conventional Commits](https://www.conventionalcommits.org/) specification.

### Format

```
<type>(<scope>): <subject>

<body>

<footer>
```

### Types

- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation only
- `style`: Code style (formatting, missing semicolons, etc.)
- `refactor`: Code refactoring (no functional changes)
- `test`: Adding or updating tests
- `chore`: Maintenance tasks (dependencies, build, etc.)
- `perf`: Performance improvements
- `ci`: CI/CD changes

### Scopes

- `appointments`
- `customers`
- `employees`
- `services`
- `products`
- `payments`
- `reports`
- `auth`
- `api`
- `ui`

### Examples

```bash
feat(appointments): add conflict detection

Implemented intelligent conflict detection for appointments.
Checks employee availability and existing bookings.

Closes #123

---

fix(payments): correct refund calculation

Fixed issue where refund amount was incorrectly calculated
for split payments.

Fixes #456

---

docs(api): update appointment endpoints

Added examples for all appointment endpoints.
Updated authentication section.

---

test(customers): add RFM analysis tests

Added comprehensive tests for RFM (Recency, Frequency, Monetary)
analysis functionality.

Coverage: 95%
```

### Rules

1. Use present tense: "add" not "added"
2. Don't capitalize first letter
3. No period (.) at the end
4. Keep subject line under 72 characters
5. Reference issues and PRs in footer
6. Use body to explain **what** and **why**, not **how**

## Pull Request Process

### Before Submitting

- [ ] Tests pass locally (`php artisan test`)
- [ ] Code follows style guide (`./vendor/bin/pint --test`)
- [ ] Static analysis passes (`./vendor/bin/phpstan analyse`)
- [ ] Test coverage meets 85%+ target
- [ ] Documentation updated (if needed)
- [ ] CHANGELOG.md updated (if needed)
- [ ] Multi-language support implemented (TR/EN)
- [ ] Multi-currency support implemented (TRY/USD)
- [ ] Branch scoping applied (where applicable)

### PR Template

```markdown
## Description
Brief description of changes.

## Type of Change
- [ ] Bug fix (non-breaking change which fixes an issue)
- [ ] New feature (non-breaking change which adds functionality)
- [ ] Breaking change (fix or feature that would cause existing functionality to not work as expected)
- [ ] Documentation update

## Related Issues
Closes #(issue number)

## How Has This Been Tested?
Describe the tests you ran.

## Screenshots (if applicable)
Add screenshots for UI changes.

## Checklist
- [ ] Tests pass
- [ ] Code style follows guidelines
- [ ] Documentation updated
- [ ] Multi-language support (TR/EN)
- [ ] Multi-currency support (TRY/USD)
- [ ] Branch scoping applied
```

### Review Process

1. **Automated Checks:** CI/CD pipeline runs tests and quality checks
2. **Code Review:** At least one maintainer reviews your code
3. **Feedback:** Address any requested changes
4. **Approval:** Once approved, your PR will be merged
5. **Release:** Changes included in next release

### After Your PR is Merged

- Delete your feature branch
- Update your fork's main branch
- Celebrate! 🎉

## Architecture Guidelines

See [CLAUDE.md](./CLAUDE.md) for comprehensive architecture guidelines.

### Key Principles

1. **Clean Architecture** - Separate concerns into layers
2. **Multi-Language First** - Never hardcode text
3. **Multi-Currency Always** - Support TRY and USD
4. **Branch Scoping** - Isolate data by branch
5. **Test Everything** - 85%+ coverage target
6. **Security First** - Authorize, validate, encrypt

### Design Patterns

- **Repository Pattern** - Data access abstraction
- **Service Layer** - Business logic encapsulation
- **DTO Pattern** - Type-safe data transfer
- **Action Pattern** - Single-purpose operations
- **Observer Pattern** - Event-driven architecture

## Documentation

### Code Documentation

Use PHPDoc for all public methods:

```php
/**
 * Create a new appointment with conflict checking.
 *
 * @param AppointmentData $data The appointment data
 * @return Appointment The created appointment
 * @throws AppointmentConflictException When appointment conflicts
 * @throws EmployeeUnavailableException When employee not available
 */
public function create(AppointmentData $data): Appointment
{
    // Implementation
}
```

### Project Documentation

Update relevant documentation in `/project-docs` if your changes affect:
- Architecture
- API endpoints
- Business rules
- Security
- Deployment

## Getting Help

### Resources

- [Project Documentation](./project-docs)
- [Architecture Guide](./project-docs/architecture.md)
- [API Documentation](./project-docs/api-documentation.md)
- [CLAUDE.md - Development Guidelines](./CLAUDE.md)

### Communication

- **GitHub Issues** - Bug reports and feature requests
- **GitHub Discussions** - Questions and general discussion
- **Email** - dev@salonpro.com
- **Discord** - [Join our server](https://discord.gg/salonpro)

## Recognition

Contributors will be:
- Listed in CONTRIBUTORS.md
- Mentioned in release notes
- Credited in documentation (for major contributions)

## License

By contributing, you agree that your contributions will be licensed under the MIT License.

---

Thank you for contributing to SalonPro! 🚀

**Questions?** Feel free to ask in GitHub Discussions or email us at dev@salonpro.com
