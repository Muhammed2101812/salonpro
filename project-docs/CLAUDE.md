# Claude AI Development Guidelines - SalonPro

## Project Context

**Project Name:** SalonPro - Professional Salon Management System
**Type:** Enterprise-grade SaaS Application
**Tech Stack:** Laravel 11 + Vue.js 3 + MySQL 8.0 + Redis
**Languages:** Turkish (default), English
**Currencies:** TRY (default), USD
**Target:** Beauty salons, barbershops, spas, wellness centers

---

## Core Principles

### 1. Always Follow Clean Architecture
- **Controllers:** Thin layer, only handle HTTP
- **Services:** All business logic goes here
- **Repositories:** Data access abstraction
- **Actions:** Single-purpose operations
- **DTOs:** Use spatie/laravel-data for data transfer

### 2. Multi-Language First
- ALL user-facing text MUST support TR/EN
- Use Laravel's translation system: `__('key')`
- Database text fields MUST be JSON: `{tr: "Türkçe", en: "English"}`
- NEVER hardcode Turkish or English text in code

### 3. Multi-Currency Aware
- ALL prices MUST support TRY and USD
- Database columns: `price_try` and `price_usd`
- NEVER use single `price` column
- Always include currency in API responses

### 4. Branch-Scoped Data
- ALL business data MUST be scoped to branches
- Use `BranchScoped` trait on models
- NEVER allow cross-branch data access without explicit permission
- Always validate `branch_id` in requests

### 5. Document Every Step (Progress Tracking) - **CRITICAL PRIORITY**

**⚠️ THIS IS THE MOST IMPORTANT PRINCIPLE - NEVER SKIP THIS! ⚠️**

- **MUST** create a `PROGRESS.md` file in project root if it doesn't exist
- **MUST** document every completed step IMMEDIATELY after finishing it
- **MUST** update PROGRESS.md VERY FREQUENTLY - after EVERY task completion
- **MUST** mark completed tasks in todo lists/task trackers RIGHT AWAY
- **MUST** include detailed descriptions of what was done
- Write entries in chronological order with timestamps
- Include file names, code changes, decisions made, and reasoning
- This creates a real-time development log for tracking and review

**Update Frequency Requirements:**
- ✅ Update PROGRESS.md immediately after completing ANY task
- ✅ Update PROGRESS.md continuously throughout work sessions
- ✅ Update PROGRESS.md multiple times per hour when actively working
- ✅ Mark tasks as completed the MOMENT they finish
- ❌ NEVER batch multiple completed tasks before documenting
- ❌ NEVER wait until "later" to document progress
- ❌ NEVER skip documentation because task seems "small"

**Why This Matters:**
- Provides real-time visibility into development progress
- Creates accurate historical record of all work
- Prevents loss of context and decisions
- Enables project tracking and review
- Essential for project management and accountability

**Progress Entry Format:**
```markdown
## [YYYY-MM-DD HH:MM] - Brief Description

**Task:** What was being worked on
**Files Modified/Created:**
- path/to/file1.php (created/modified/deleted)
- path/to/file2.vue (created/modified/deleted)

**Changes Made:**
- Detailed description of what was implemented
- Key decisions and why they were made
- Any challenges encountered and how they were solved

**Tests:**
- What tests were added/updated
- Test results (passing/failing)

**Next Steps:**
- What needs to be done next (if applicable)

---
```

**Example Progress Entry:**
```markdown
## [2025-01-15 14:30] - Created Appointment Repository

**Task:** Implement AppointmentRepository with branch scoping
**Files Created:**
- app/Repositories/Contracts/AppointmentRepositoryInterface.php
- app/Repositories/Eloquent/AppointmentRepository.php

**Changes Made:**
- Created repository interface with CRUD methods
- Implemented repository with automatic branch scoping
- Added eager loading for common relationships (customer, employee, services)
- Used AppointmentData DTO for type safety

**Tests:**
- Created tests/Unit/Repositories/AppointmentRepositoryTest.php
- All repository tests passing (10/10)
- Coverage: 100%

**Next Steps:**
- Create AppointmentService to handle business logic
- Implement conflict detection

---
```

---

## Coding Standards

### PHP Code Style
```php
// ✅ CORRECT: Use strict types
declare(strict_types=1);

// ✅ CORRECT: Type hints everywhere
public function create(AppointmentData $data): Appointment

// ❌ WRONG: No type hints
public function create($data)

// ✅ CORRECT: Return type declarations
public function getTotal(): Money

// ✅ CORRECT: Use DTOs from spatie/laravel-data
class AppointmentData extends Data
{
    public function __construct(
        public string $customer_id,
        public string $employee_id,
        public Carbon $scheduled_at,
    ) {}
}

// ✅ CORRECT: Service layer
class AppointmentService
{
    public function __construct(
        private AppointmentRepository $repository,
        private NotificationService $notifications
    ) {}
}

// ✅ CORRECT: Repository pattern
interface AppointmentRepositoryInterface
{
    public function create(AppointmentData $data): Appointment;
}
```

### Database Conventions
```php
// ✅ CORRECT: UUID primary keys
$table->uuid('id')->primary();

// ✅ CORRECT: Branch scoping
$table->uuid('branch_id')->index();
$table->foreign('branch_id')->references('id')->on('branches');

// ✅ CORRECT: Multi-language columns (JSON)
$table->json('name'); // {tr: "Saç Kesimi", en: "Haircut"}
$table->json('description');

// ✅ CORRECT: Multi-currency columns
$table->decimal('price_try', 10, 2);
$table->decimal('price_usd', 10, 2);

// ✅ CORRECT: Soft deletes
$table->softDeletes();

// ✅ CORRECT: Timestamps
$table->timestamps();

// ❌ WRONG: Single language column
$table->string('name'); // Only one language

// ❌ WRONG: Single price column
$table->decimal('price', 10, 2); // Which currency?
```

### Vue.js Style
```vue
<!-- ✅ CORRECT: Composition API -->
<script setup>
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
const appointments = ref([])

const filteredAppointments = computed(() => {
  return appointments.value.filter(/* ... */)
})
</script>

<!-- ✅ CORRECT: Use i18n for all text -->
<template>
  <h1>{{ t('appointments.title') }}</h1>
  <button>{{ t('common.save') }}</button>
</template>

<!-- ❌ WRONG: Options API -->
<script>
export default {
  data() {
    return { appointments: [] }
  }
}
</script>

<!-- ❌ WRONG: Hardcoded text -->
<h1>Randevular</h1>
```

---

## Architecture Rules

### 1. Controller Structure
```php
// ✅ CORRECT: Thin controllers
class AppointmentController extends Controller
{
    public function __construct(
        private AppointmentService $service
    ) {}

    public function store(CreateAppointmentRequest $request)
    {
        $data = AppointmentData::from($request->validated());
        $appointment = $this->service->create($data);

        return new AppointmentResource($appointment);
    }
}

// ❌ WRONG: Fat controllers with business logic
class AppointmentController extends Controller
{
    public function store(Request $request)
    {
        // Validation
        // Business logic
        // Database operations
        // Email sending
        // etc. - TOO MUCH!
    }
}
```

### 2. Service Layer
```php
// ✅ CORRECT: Services contain business logic
class AppointmentService
{
    public function create(AppointmentData $data): Appointment
    {
        // Validate business rules
        $this->validateAppointment($data);

        // Check conflicts
        if ($this->hasConflict($data)) {
            throw new AppointmentConflictException();
        }

        // Create within transaction
        return DB::transaction(function () use ($data) {
            $appointment = $this->repository->create($data);

            // Dispatch events
            event(new AppointmentCreated($appointment));

            // Send notifications
            $this->notifications->sendConfirmation($appointment);

            return $appointment;
        });
    }
}
```

### 3. Repository Layer
```php
// ✅ CORRECT: Repository only handles data access
class AppointmentRepository implements AppointmentRepositoryInterface
{
    public function create(AppointmentData $data): Appointment
    {
        return Appointment::create($data->toArray());
    }

    public function findByDateRange(Carbon $start, Carbon $end): Collection
    {
        return Appointment::whereBetween('scheduled_at', [$start, $end])
            ->with(['customer', 'employee', 'services'])
            ->get();
    }
}

// ❌ WRONG: Business logic in repository
class AppointmentRepository
{
    public function create($data)
    {
        // Validation - NO!
        // Sending emails - NO!
        // Complex business logic - NO!
    }
}
```

---

## Security Rules

### MUST Follow
1. **NEVER** store sensitive data unencrypted
   - Social security numbers → `encrypt()`
   - Bank accounts → `encrypt()`
   - Salaries → `encrypt()`

2. **ALWAYS** validate user permissions
   ```php
   // ✅ CORRECT
   $this->authorize('update', $appointment);

   // ✅ CORRECT
   if (! auth()->user()->can('appointments.update')) {
       abort(403);
   }
   ```

3. **ALWAYS** scope by branch
   ```php
   // ✅ CORRECT
   $appointment = Appointment::where('branch_id', auth()->user()->branch_id)
       ->findOrFail($id);

   // ❌ WRONG: No branch check
   $appointment = Appointment::findOrFail($id);
   ```

4. **NEVER** expose sensitive data in API
   ```php
   // ✅ CORRECT: Hide sensitive fields
   protected $hidden = ['password', 'two_factor_secret'];

   // ✅ CORRECT: Use API resources
   class EmployeeResource extends JsonResource
   {
       public function toArray($request): array
       {
           return [
               'id' => $this->id,
               'name' => $this->name,
               // Don't include: salary, bank_account, etc.
           ];
       }
   }
   ```

---

## Testing Requirements

### Coverage Targets
- Overall: 85%+
- Critical business logic: 100%
- Controllers: 80%+
- Services: 90%+
- Repositories: 85%+

### Test Structure
```php
// ✅ CORRECT: Pest syntax
test('user can create appointment', function () {
    $user = User::factory()->receptionist()->create();
    $customer = Customer::factory()->create();
    $service = Service::factory()->create();

    actingAs($user)
        ->post('/api/v1/appointments', [
            'customer_id' => $customer->id,
            'service_ids' => [$service->id],
            'scheduled_at' => now()->addDays(1),
        ])
        ->assertStatus(201)
        ->assertJsonStructure(['data' => ['id', 'customer_id']]);

    assertDatabaseHas('appointments', [
        'customer_id' => $customer->id,
    ]);
});

// ✅ CORRECT: Test business rules
test('appointment cannot be double booked', function () {
    // Setup
    // Act
    // Assert exception thrown
});
```

---

## Multi-Language Implementation

### Backend (Laravel)
```php
// ✅ CORRECT: Translation files
// resources/lang/tr/appointments.php
return [
    'title' => 'Randevular',
    'create' => 'Yeni Randevu',
    'status' => [
        'pending' => 'Beklemede',
        'confirmed' => 'Onaylandı',
    ],
];

// resources/lang/en/appointments.php
return [
    'title' => 'Appointments',
    'create' => 'New Appointment',
    'status' => [
        'pending' => 'Pending',
        'confirmed' => 'Confirmed',
    ],
];

// ✅ CORRECT: Usage in code
__('appointments.title');
trans('appointments.status.pending');

// ✅ CORRECT: Database multi-language
Schema::create('services', function (Blueprint $table) {
    $table->json('name'); // {"tr": "Saç Kesimi", "en": "Haircut"}
    $table->json('description');
});

// ✅ CORRECT: Accessor for current language
class Service extends Model
{
    protected $casts = [
        'name' => 'array',
        'description' => 'array',
    ];

    public function getTranslatedNameAttribute(): string
    {
        return $this->name[app()->getLocale()] ?? $this->name['tr'];
    }
}
```

### Frontend (Vue.js)
```vue
<!-- ✅ CORRECT: i18n setup -->
<script setup>
import { useI18n } from 'vue-i18n'

const { t, locale } = useI18n()

const changeLanguage = (lang) => {
  locale.value = lang
}
</script>

<template>
  <div>
    <h1>{{ t('appointments.title') }}</h1>
    <p>{{ t('appointments.description') }}</p>

    <!-- Language switcher -->
    <button @click="changeLanguage('tr')">TR</button>
    <button @click="changeLanguage('en')">EN</button>
  </div>
</template>

<!-- ❌ WRONG: Hardcoded text -->
<h1>Randevular</h1>
```

---

## Multi-Currency Implementation

### Always Include Both Currencies
```php
// ✅ CORRECT: Migration
Schema::create('services', function (Blueprint $table) {
    $table->decimal('price_try', 10, 2);
    $table->decimal('price_usd', 10, 2);
    $table->decimal('cost_try', 10, 2)->nullable();
    $table->decimal('cost_usd', 10, 2)->nullable();
});

// ✅ CORRECT: Model
class Service extends Model
{
    public function getPriceInCurrency(string $currency): float
    {
        return match($currency) {
            'TRY' => $this->price_try,
            'USD' => $this->price_usd,
            default => $this->price_try,
        };
    }
}

// ✅ CORRECT: API Response
class ServiceResource extends JsonResource
{
    public function toArray($request): array
    {
        $currency = $request->header('X-Currency', 'TRY');

        return [
            'id' => $this->id,
            'name' => $this->getTranslatedName(),
            'price' => [
                'amount' => $this->getPriceInCurrency($currency),
                'currency' => $currency,
            ],
            'prices' => [
                'TRY' => $this->price_try,
                'USD' => $this->price_usd,
            ],
        ];
    }
}
```

---

## Business Rules to Enforce

### Appointments
1. ✅ Cannot book past appointments
2. ✅ Cannot double-book same employee
3. ✅ Must respect minimum advance booking time
4. ✅ Must validate employee availability
5. ✅ Cancellation requires reason if < 24 hours
6. ✅ No-show marks customer record after 3 times

### Payments
1. ✅ Cannot refund more than paid
2. ✅ Refunds require manager approval > 500 TRY
3. ✅ Must record payment method
4. ✅ Split payments must equal total
5. ✅ Currency must match at transaction time

### Inventory
1. ✅ Cannot sell with insufficient stock
2. ✅ Stock cannot go negative
3. ✅ Transfer requires both branch confirmations
4. ✅ Low stock alerts at threshold
5. ✅ Expired product auto-alerts 30 days before

### Employees
1. ✅ Cannot delete employee with future appointments
2. ✅ Commission only on completed services
3. ✅ Working hours must not overlap
4. ✅ Overtime requires approval
5. ✅ Minimum 8 hours between shifts

---

## Forbidden Practices

### ❌ NEVER Do These
1. **NEVER** use `SELECT *` - Always specify columns
2. **NEVER** commit secrets to git
3. **NEVER** skip validation
4. **NEVER** use raw queries without parameterization
5. **NEVER** ignore N+1 query issues
6. **NEVER** hardcode Turkish/English text
7. **NEVER** use single currency column
8. **NEVER** bypass branch scoping
9. **NEVER** expose sensitive data in API
10. **NEVER** skip writing tests

### ❌ Code Smells to Avoid
```php
// ❌ WRONG: Magic numbers
if ($user->role_id === 3) {}

// ✅ CORRECT: Constants or enums
if ($user->role === Role::BRANCH_MANAGER) {}

// ❌ WRONG: Nested ifs
if ($x) {
    if ($y) {
        if ($z) {
            // ...
        }
    }
}

// ✅ CORRECT: Early returns
if (! $x) return;
if (! $y) return;
if (! $z) return;
// ...

// ❌ WRONG: Large functions
public function processAppointment() {
    // 200 lines of code
}

// ✅ CORRECT: Small, focused functions
public function processAppointment() {
    $this->validate();
    $this->checkAvailability();
    $this->create();
    $this->notify();
}
```

---

## File Organization

### Backend Structure
```
app/
├── Actions/                 # Single-purpose actions
│   └── Appointments/
│       └── CreateAppointmentAction.php
├── Data/                    # DTOs
│   └── AppointmentData.php
├── Domain/                  # Domain logic (if using DDD)
├── Http/
│   ├── Controllers/
│   │   ├── API/V1/         # API endpoints
│   │   └── Web/            # Web routes
│   ├── Requests/           # Form requests
│   │   └── CreateAppointmentRequest.php
│   └── Resources/          # API resources
│       └── AppointmentResource.php
├── Models/                 # Eloquent models
│   └── Appointment.php
├── Repositories/
│   ├── Contracts/          # Interfaces
│   │   └── AppointmentRepositoryInterface.php
│   └── Eloquent/           # Implementations
│       └── AppointmentRepository.php
├── Services/               # Business logic
│   └── Appointment/
│       ├── AppointmentService.php
│       └── ConflictDetectionService.php
└── Traits/                 # Reusable traits
    └── BranchScoped.php
```

### Frontend Structure
```
resources/js/
├── components/             # Reusable components
│   ├── common/
│   └── appointments/
├── composables/            # Composition functions
│   ├── useAppointments.js
│   └── useCurrency.js
├── layouts/                # Page layouts
│   ├── AppLayout.vue
│   └── AuthLayout.vue
├── pages/                  # Page components
│   └── appointments/
│       ├── Index.vue
│       └── Create.vue
├── router/                 # Vue Router
│   └── index.js
├── stores/                 # Pinia stores
│   ├── auth.js
│   └── appointments.js
└── utils/                  # Utility functions
    └── formatters.js
```

---

## API Design Rules

### Endpoint Structure
```
GET    /api/v1/appointments              # List
POST   /api/v1/appointments              # Create
GET    /api/v1/appointments/{id}         # Show
PUT    /api/v1/appointments/{id}         # Update
DELETE /api/v1/appointments/{id}         # Delete
POST   /api/v1/appointments/{id}/cancel  # Custom action
```

### Request Headers
```http
Authorization: Bearer {token}    # Required
X-Branch-ID: {uuid}             # Required
Accept: application/json         # Required
Content-Type: application/json   # Required for POST/PUT
Accept-Language: tr|en          # Optional (default: tr)
X-Currency: TRY|USD             # Optional (default: TRY)
```

### Response Format
```json
{
  "data": {
    "id": "uuid",
    "customer": { ... },
    "scheduled_at": "2024-01-20T14:00:00Z",
    "price": {
      "amount": 150.00,
      "currency": "TRY"
    }
  },
  "meta": {
    "current_page": 1,
    "total": 100
  }
}
```

---

## Performance Guidelines

### Database Queries
```php
// ✅ CORRECT: Eager loading
$appointments = Appointment::with(['customer', 'employee', 'services'])
    ->where('branch_id', $branchId)
    ->get();

// ❌ WRONG: N+1 queries
$appointments = Appointment::all();
foreach ($appointments as $appointment) {
    echo $appointment->customer->name; // N+1!
}

// ✅ CORRECT: Chunking large datasets
Appointment::where('status', 'completed')
    ->chunk(100, function ($appointments) {
        // Process chunk
    });

// ✅ CORRECT: Select only needed columns
$appointments = Appointment::select(['id', 'customer_id', 'scheduled_at'])
    ->get();
```

### Caching Strategy
```php
// ✅ CORRECT: Cache expensive queries
$services = Cache::remember('services.active', 3600, function () {
    return Service::where('is_active', true)->get();
});

// ✅ CORRECT: Cache tags for easy invalidation
Cache::tags(['appointments', "customer:{$customerId}"])
    ->put('appointments', $data, 3600);

// Invalidate when needed
Cache::tags(['appointments'])->flush();
```

---

## Event-Driven Architecture

### Events and Listeners
```php
// ✅ CORRECT: Dispatch events for important actions
class AppointmentService
{
    public function complete(Appointment $appointment): void
    {
        $appointment->update(['status' => 'completed']);

        // Dispatch event
        event(new AppointmentCompleted($appointment));
    }
}

// ✅ CORRECT: Async listeners
class UpdateCustomerStats
{
    public function handle(AppointmentCompleted $event): void
    {
        // Update customer statistics
    }
}

class CalculateCommission
{
    public function handle(AppointmentCompleted $event): void
    {
        // Calculate employee commission
    }
}

class SendFeedbackRequest
{
    public function handle(AppointmentCompleted $event): void
    {
        // Send satisfaction survey
    }
}
```

---

## Documentation Requirements

### Code Documentation
```php
/**
 * Create a new appointment with conflict checking and notifications.
 *
 * @param AppointmentData $data The appointment data
 * @return Appointment The created appointment
 * @throws AppointmentConflictException When appointment conflicts with existing
 * @throws EmployeeUnavailableException When employee is not available
 */
public function create(AppointmentData $data): Appointment
{
    // Implementation
}
```

### API Documentation
- Use Scribe for automatic documentation
- Add examples to all endpoints
- Document all error responses
- Include authentication requirements

---

## When Implementing New Features

### Checklist

**🔴 CRITICAL - DO FIRST:**
- [ ] **Document in PROGRESS.md** (IMMEDIATELY after EACH AND EVERY step - NO EXCEPTIONS!)
- [ ] **Mark completed tasks** (Update task status the MOMENT you finish)

**Core Requirements:**
- [ ] Follow clean architecture (Controller → Service → Repository)
- [ ] Support both TR and EN languages
- [ ] Support both TRY and USD currencies
- [ ] Apply branch scoping where needed
- [ ] Add proper authorization checks

**Testing:**
- [ ] Write unit tests (target: 85%+ coverage)
- [ ] Write feature tests for API endpoints
- [ ] Test in both languages
- [ ] Test with both currencies

**Implementation:**
- [ ] Update API documentation
- [ ] Add translation keys to lang files
- [ ] Update relevant models and migrations
- [ ] Create/update form requests with validation
- [ ] Create/update API resources
- [ ] Dispatch relevant events
- [ ] Add to appropriate Pinia store (if frontend)
- [ ] Create Vue components (if frontend)

**Quality Checks:**
- [ ] Run PHPStan Level 8
- [ ] Run Laravel Pint

**🔴 REMINDER: Update PROGRESS.md after completing EACH of the above items!**

---

## Common Patterns

### Creating a New Module

1. **Create Migration**
```bash
php artisan make:migration create_table_name_table
```

2. **Create Model with Traits**
```php
class ModelName extends Model
{
    use HasFactory, SoftDeletes, BranchScoped, LogsActivity;
}
```

3. **Create Repository Interface and Implementation**
```php
interface ModelRepositoryInterface { }
class ModelRepository implements ModelRepositoryInterface { }
```

4. **Create Service**
```php
class ModelService
{
    public function __construct(
        private ModelRepository $repository
    ) {}
}
```

5. **Create Form Request**
```php
class CreateModelRequest extends FormRequest { }
```

6. **Create API Resource**
```php
class ModelResource extends JsonResource { }
```

7. **Create Controller**
```php
class ModelController extends Controller { }
```

8. **Create Tests**
```php
test('can create model', function () { });
test('can update model', function () { });
```

---

## Quality Checks Before Committing

```bash
# Run tests
php artisan test

# Check code style
./vendor/bin/pint --test

# Static analysis
./vendor/bin/phpstan analyse

# Frontend build
npm run build

# Check for N+1 queries (in telescope)
# Check for missing translations
# Check for unhanded exceptions
```

---

## Getting Help

### Documentation References
- [Project Overview](./project-docs/project-overview.md)
- [Architecture](./project-docs/architecture.md)
- [Tech Stack](./project-docs/tech-stack.md)
- [Database Design](./project-docs/database-design.md)
- [API Documentation](./project-docs/api-documentation.md)
- [User Roles](./project-docs/user-roles.md)
- [Security Guidelines](./project-docs/security-guidelines.md)
- [Testing Strategy](./project-docs/testing-strategy.md)
- [Business Rules](./project-docs/business-rules.md)

### External Documentation
- Laravel 11: https://laravel.com/docs/11.x
- Vue.js 3: https://vuejs.org/guide/
- Pinia: https://pinia.vuejs.org/
- Spatie Packages: https://spatie.be/docs

---

## Version Control

### Commit Message Format
```
type(scope): subject

body

footer
```

**Types:** feat, fix, docs, style, refactor, test, chore

**Examples:**
```
feat(appointments): add conflict detection
fix(payments): correct refund calculation
docs(api): update appointment endpoints
test(customers): add RFM analysis tests
```

---

## Final Reminders

### 🔴 **CRITICAL - ABSOLUTE TOP PRIORITY:**

1. **📋 PROGRESS TRACKING IS MANDATORY**
   - Update PROGRESS.md IMMEDIATELY after EVERY task
   - Mark tasks as completed THE MOMENT they finish
   - Update VERY FREQUENTLY - multiple times per hour
   - NEVER batch updates - document continuously
   - This is NON-NEGOTIABLE and MUST be done consistently

### Core Development Principles:

2. 🌍 **Multi-Language First** - Never hardcode text
3. 💰 **Multi-Currency Always** - Always both TRY and USD
4. 🏢 **Branch Scope Everything** - Data isolation is critical
5. 🧪 **Test Everything** - 85%+ coverage target
6. 🔒 **Security First** - Authorize, validate, encrypt
7. 📝 **Document Well** - Clear, concise documentation
8. 🎯 **Clean Code** - Follow SOLID principles
9. ⚡ **Performance Matters** - Avoid N+1, use caching
10. 🐛 **Handle Errors** - Graceful error handling
11. ✨ **Code Quality** - PHPStan Level 8, Laravel Pint

**⚠️ Remember: If you're not updating PROGRESS.md continuously, you're doing it wrong! ⚠️**

---

**Last Updated:** 2025-10-18
**Version:** 1.2
**Maintainer:** Development Team

**Change Log:**
- v1.2 (2025-10-18): Significantly strengthened progress tracking requirements - emphasized continuous and very frequent PROGRESS.md updates
- v1.1 (2025-10-16): Added comprehensive progress tracking section
- v1.0 (2025-01-15): Initial version
