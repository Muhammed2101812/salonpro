# System Architecture - SalonPro

## Architecture Overview

SalonPro follows clean architecture principles with clear separation of concerns, implementing a layered architecture that promotes maintainability, testability, and scalability.

---

## Architectural Layers

```
┌─────────────────────────────────────────────────────┐
│                   PRESENTATION LAYER                 │
│  ┌──────────────┐  ┌──────────────┐  ┌───────────┐ │
│  │  Web Client  │  │ Mobile API   │  │  Admin    │ │
│  │  (Vue.js 3)  │  │ (REST/JSON)  │  │  Panel    │ │
│  └──────────────┘  └──────────────┘  └───────────┘ │
└─────────────────────────────────────────────────────┘
                         ↓
┌─────────────────────────────────────────────────────┐
│                  APPLICATION LAYER                   │
│  ┌──────────────────────────────────────────────┐  │
│  │           Controllers (Thin Layer)            │  │
│  └──────────────────────────────────────────────┘  │
│  ┌──────────────────────────────────────────────┐  │
│  │         Services (Business Logic)             │  │
│  └──────────────────────────────────────────────┘  │
│  ┌──────────────────────────────────────────────┐  │
│  │       Repositories (Data Access)              │  │
│  └──────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────┘
                         ↓
┌─────────────────────────────────────────────────────┐
│                   DOMAIN LAYER                       │
│  ┌──────────┐  ┌──────────┐  ┌─────────────────┐  │
│  │ Models   │  │  Events  │  │  Value Objects  │  │
│  └──────────┘  └──────────┘  └─────────────────┘  │
└─────────────────────────────────────────────────────┘
                         ↓
┌─────────────────────────────────────────────────────┐
│              INFRASTRUCTURE LAYER                    │
│  ┌──────────┐  ┌──────────┐  ┌──────────────────┐ │
│  │  MySQL   │  │  Redis   │  │  Queue/Jobs      │ │
│  └──────────┘  └──────────┘  └──────────────────┘ │
│  ┌──────────┐  ┌──────────┐  ┌──────────────────┐ │
│  │ Storage  │  │  Mail    │  │  SMS             │ │
│  └──────────┘  └──────────┘  └──────────────────┘ │
└─────────────────────────────────────────────────────┘
```

---

## Layer Responsibilities

### 1. Presentation Layer

**Purpose:** Handle user interface and external API requests

**Components:**
- **Web Client (Vue.js 3)**
  - Single Page Application (SPA)
  - Responsive design for desktop and mobile
  - Real-time updates via WebSocket
  - State management with Pinia

- **REST API (Laravel)**
  - RESTful endpoints for mobile apps
  - JSON responses
  - API versioning (v1, v2)
  - Rate limiting and throttling

- **Admin Panel**
  - System administration interface
  - Configuration management
  - User management
  - System monitoring

**Key Technologies:**
- Vue.js 3 (Composition API)
- Vue Router
- Pinia
- Tailwind CSS
- Axios

### 2. Application Layer

**Purpose:** Orchestrate business logic and coordinate between presentation and domain layers

#### Controllers
- Thin controllers (minimal logic)
- Request validation
- Response formatting
- Delegation to services
- Exception handling

**Example:**
```php
class AppointmentController extends Controller
{
    public function __construct(
        private AppointmentService $appointmentService
    ) {}

    public function store(CreateAppointmentRequest $request)
    {
        $appointmentData = AppointmentData::from($request->validated());
        $appointment = $this->appointmentService->create($appointmentData);

        return new AppointmentResource($appointment);
    }
}
```

#### Services
- Business logic implementation
- Transaction management
- Event dispatching
- Complex operations coordination
- Cross-entity operations

**Example:**
```php
class AppointmentService
{
    public function __construct(
        private AppointmentRepository $appointmentRepository,
        private ConflictDetectionService $conflictDetection,
        private NotificationService $notificationService
    ) {}

    public function create(AppointmentData $data): Appointment
    {
        // Business logic
        $this->conflictDetection->check($data);

        DB::transaction(function () use ($data) {
            $appointment = $this->appointmentRepository->create($data);
            event(new AppointmentCreated($appointment));
            $this->notificationService->sendConfirmation($appointment);
            return $appointment;
        });
    }
}
```

#### Repositories
- Data access abstraction
- Query building
- Data persistence
- Caching layer
- Search operations

**Pattern:**
```php
interface AppointmentRepositoryInterface
{
    public function create(AppointmentData $data): Appointment;
    public function find(string $id): ?Appointment;
    public function findByDateRange(Carbon $start, Carbon $end): Collection;
}

class AppointmentRepository implements AppointmentRepositoryInterface
{
    public function create(AppointmentData $data): Appointment
    {
        return Appointment::create($data->toArray());
    }
}
```

### 3. Domain Layer

**Purpose:** Core business entities and rules

#### Models (Eloquent)
- Business entities
- Relationships
- Accessors and mutators
- Scopes
- Traits

**Example:**
```php
class Appointment extends Model
{
    use HasUuid, BranchScoped, LogsActivity;

    protected $fillable = [
        'customer_id',
        'employee_id',
        'service_id',
        'scheduled_at',
        'status',
        'notes'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'status' => AppointmentStatus::class
    ];

    // Relationships
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    // Business logic
    public function canBeCancelled(): bool
    {
        return $this->status->canTransitionTo(AppointmentStatus::CANCELLED);
    }
}
```

#### Events
- Domain events
- Event listeners
- Asynchronous processing
- Cross-module communication

**Example:**
```php
class AppointmentCreated
{
    public function __construct(public Appointment $appointment) {}
}

class SendAppointmentConfirmation
{
    public function handle(AppointmentCreated $event): void
    {
        // Send notification
    }
}
```

#### Value Objects
- Immutable objects
- Business value representation
- Validation logic

**Example:**
```php
class Money
{
    public function __construct(
        private float $amount,
        private Currency $currency
    ) {}

    public function add(Money $other): self
    {
        if (!$this->currency->equals($other->currency)) {
            throw new CurrencyMismatchException();
        }

        return new self(
            $this->amount + $other->amount,
            $this->currency
        );
    }
}
```

### 4. Infrastructure Layer

**Purpose:** Technical implementation and external services

**Components:**
- Database (MySQL)
- Cache (Redis)
- Queue (Redis + Horizon)
- Storage (AWS S3 / MinIO)
- Email service
- SMS service
- Search engine (Meilisearch)
- WebSocket (Laravel Reverb)

---

## Design Patterns

### 1. Repository Pattern

**Purpose:** Abstraction layer between domain and data layers

**Benefits:**
- Decouples business logic from data access
- Easy to test with mocks
- Centralized data access logic
- Flexible data source switching

**Implementation:**
```
app/Repositories/
├── Contracts/
│   ├── AppointmentRepositoryInterface.php
│   ├── CustomerRepositoryInterface.php
│   └── EmployeeRepositoryInterface.php
└── Eloquent/
    ├── AppointmentRepository.php
    ├── CustomerRepository.php
    └── EmployeeRepository.php
```

### 2. Service Layer Pattern

**Purpose:** Encapsulate business logic

**Benefits:**
- Single responsibility
- Reusable business logic
- Testable
- Clear separation of concerns

**Implementation:**
```
app/Services/
├── Appointment/
│   ├── AppointmentService.php
│   ├── SchedulingService.php
│   └── ConflictDetectionService.php
├── Customer/
│   ├── CustomerService.php
│   └── SegmentationService.php
└── Employee/
    ├── EmployeeService.php
    └── PayrollService.php
```

### 3. Data Transfer Object (DTO) Pattern

**Purpose:** Transfer data between layers

**Benefits:**
- Type safety
- Validation
- Documentation
- Transformation

**Implementation:**
```php
use Spatie\LaravelData\Data;

class AppointmentData extends Data
{
    public function __construct(
        public string $customer_id,
        public string $employee_id,
        public string $service_id,
        public Carbon $scheduled_at,
        public ?string $notes = null
    ) {}
}
```

### 4. Action Pattern

**Purpose:** Single-purpose, focused operations

**Benefits:**
- Single responsibility
- Highly testable
- Reusable
- Clear naming

**Example:**
```php
class CreateAppointmentAction
{
    public function execute(AppointmentData $data): Appointment
    {
        // Single, focused operation
        return Appointment::create($data->toArray());
    }
}
```

### 5. Observer Pattern

**Purpose:** React to model events

**Implementation:**
```php
class AppointmentObserver
{
    public function created(Appointment $appointment): void
    {
        // Log activity
        activity()->log('Appointment created');
    }

    public function updating(Appointment $appointment): void
    {
        // Validate changes
    }
}
```

### 6. Strategy Pattern

**Purpose:** Interchangeable algorithms

**Example:**
```php
interface PricingStrategy
{
    public function calculate(Service $service, Carbon $date): Money;
}

class PeakPricingStrategy implements PricingStrategy
{
    public function calculate(Service $service, Carbon $date): Money
    {
        return $service->price->multiply(1.5);
    }
}

class OffPeakPricingStrategy implements PricingStrategy
{
    public function calculate(Service $service, Carbon $date): Money
    {
        return $service->price->multiply(0.8);
    }
}
```

---

## Multi-Tenancy Architecture

### Approach: Single Database with Branch Scoping

**Implementation:**
- All tables include `branch_id` column
- Global scope applies branch filter automatically
- Middleware sets current branch context
- Data isolation at application level

**Branch Scope:**
```php
trait BranchScoped
{
    protected static function bootBranchScoped(): void
    {
        static::addGlobalScope('branch', function (Builder $builder) {
            if (auth()->check() && auth()->user()->current_branch_id) {
                $builder->where('branch_id', auth()->user()->current_branch_id);
            }
        });

        static::creating(function ($model) {
            if (auth()->check() && !$model->branch_id) {
                $model->branch_id = auth()->user()->current_branch_id;
            }
        });
    }
}
```

### Branch Context Middleware

```php
class SetBranchContext
{
    public function handle(Request $request, Closure $next)
    {
        if ($branchId = $request->header('X-Branch-ID')) {
            auth()->user()->switchBranch($branchId);
        }

        return $next($request);
    }
}
```

---

## Event-Driven Architecture

### Events & Listeners

**Purpose:** Decouple modules and enable asynchronous processing

**Flow:**
```
Action → Event Dispatched → Listeners Execute → Side Effects
```

**Example:**
```php
// Event
class AppointmentCompleted
{
    public function __construct(public Appointment $appointment) {}
}

// Listeners (async)
class UpdateCustomerStats
{
    public function handle(AppointmentCompleted $event): void
    {
        // Update customer statistics
    }
}

class CalculateEmployeeCommission
{
    public function handle(AppointmentCompleted $event): void
    {
        // Calculate and record commission
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

### Event Registration

```php
protected $listen = [
    AppointmentCompleted::class => [
        UpdateCustomerStats::class,
        CalculateEmployeeCommission::class,
        SendFeedbackRequest::class,
    ],
];
```

---

## Queue Architecture

### Queue Structure

**Queues:**
- `default` - Standard operations
- `high` - Critical operations (payments, bookings)
- `low` - Background tasks (reports, cleanup)
- `notifications` - Email/SMS sending

**Jobs:**
```php
class SendAppointmentReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;

    public function __construct(private Appointment $appointment) {}

    public function handle(NotificationService $service): void
    {
        $service->sendReminder($this->appointment);
    }
}
```

### Horizon Configuration

```php
'environments' => [
    'production' => [
        'supervisor-1' => [
            'connection' => 'redis',
            'queue' => ['high', 'default'],
            'balance' => 'auto',
            'processes' => 10,
            'tries' => 3,
        ],
        'supervisor-2' => [
            'connection' => 'redis',
            'queue' => ['low', 'notifications'],
            'balance' => 'auto',
            'processes' => 3,
            'tries' => 3,
        ],
    ],
],
```

---

## Caching Strategy

### Cache Layers

1. **Configuration Cache**
   - Laravel config cache
   - Route cache
   - View cache

2. **Application Cache**
   - Query results
   - Computed values
   - API responses

3. **Model Cache**
   - Frequently accessed models
   - Lookup data
   - Settings

### Cache Keys Structure

```
{app}:{module}:{entity}:{identifier}:{attribute}

Examples:
salonpro:appointment:schedule:2024-01-15
salonpro:customer:12345:appointments
salonpro:service:catalog:active
```

### Cache Invalidation

**Strategy:** Event-based invalidation

```php
class AppointmentObserver
{
    public function saved(Appointment $appointment): void
    {
        Cache::tags([
            'appointments',
            "customer:{$appointment->customer_id}",
            "employee:{$appointment->employee_id}"
        ])->flush();
    }
}
```

---

## API Architecture

### RESTful Design

**Endpoint Structure:**
```
/api/v1/{resource}
/api/v1/{resource}/{id}
/api/v1/{resource}/{id}/{sub-resource}
```

**Examples:**
```
GET    /api/v1/appointments
POST   /api/v1/appointments
GET    /api/v1/appointments/{id}
PUT    /api/v1/appointments/{id}
DELETE /api/v1/appointments/{id}
GET    /api/v1/customers/{id}/appointments
```

### API Versioning

**Strategy:** URL versioning

```php
Route::prefix('v1')->group(function () {
    // Version 1 routes
});

Route::prefix('v2')->group(function () {
    // Version 2 routes
});
```

### API Resources

```php
class AppointmentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'employee' => new EmployeeResource($this->whenLoaded('employee')),
            'service' => new ServiceResource($this->whenLoaded('service')),
            'scheduled_at' => $this->scheduled_at->toIso8601String(),
            'status' => $this->status->value,
            'total' => $this->total->format(),
        ];
    }
}
```

### Rate Limiting

```php
RateLimiter::for('api', function (Request $request) {
    return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
});

RateLimiter::for('api-strict', function (Request $request) {
    return Limit::perMinute(10)->by($request->user()?->id);
});
```

---

## Security Architecture

### Authentication

**Method:** Laravel Sanctum for API, Session for Web

**Flow:**
```
User → Login → Credentials Validation → Token/Session → Authenticated
```

### Authorization

**Method:** Spatie Permission (RBAC + ABAC)

**Hierarchy:**
```
Super Admin
└── Organization Admin
    └── Branch Manager
        ├── Accountant
        ├── Inventory Manager
        ├── Marketing Manager
        └── Staff
            ├── Senior Stylist
            ├── Junior Stylist
            ├── Beautician
            ├── Massage Therapist
            └── Receptionist
```

### Policies

```php
class AppointmentPolicy
{
    public function view(User $user, Appointment $appointment): bool
    {
        return $user->branch_id === $appointment->branch_id &&
               $user->can('appointments.view');
    }

    public function update(User $user, Appointment $appointment): bool
    {
        return $user->branch_id === $appointment->branch_id &&
               $user->can('appointments.update');
    }
}
```

---

## Database Architecture

### Schema Design Principles

1. **Normalization:** 3NF for transactional data
2. **Denormalization:** For reporting and read-heavy tables
3. **Soft Deletes:** For all major entities
4. **Timestamps:** created_at, updated_at on all tables
5. **UUIDs:** For primary keys in distributed system readiness

### Key Tables Structure

**Core Tables:**
- organizations
- branches
- users
- customers
- employees
- services
- appointments
- payments
- invoices
- products
- stock_movements

**Relationship Tables:**
- customer_addresses
- employee_schedules
- service_packages
- appointment_services
- loyalty_points
- campaigns

### Indexing Strategy

**Primary Indexes:**
- Primary keys (UUID)
- Foreign keys
- Unique constraints

**Composite Indexes:**
- (branch_id, created_at)
- (customer_id, status)
- (employee_id, scheduled_at)

**Full-Text Indexes:**
- Customer search
- Service search
- Product search

---

## Performance Optimization

### Database Level
- Query optimization
- Index optimization
- Connection pooling
- Read replicas (future)

### Application Level
- Eager loading (N+1 prevention)
- Query result caching
- Model caching
- Response caching

### Frontend Level
- Code splitting
- Lazy loading
- Asset optimization
- CDN usage

### Infrastructure Level
- Redis caching
- Queue workers
- Horizontal scaling capability
- Load balancing (future)

---

## Scalability Considerations

### Horizontal Scaling

**Stateless Application:**
- Session in Redis
- File uploads to S3
- No local state

**Database Scaling:**
- Master-slave replication
- Read/write splitting
- Sharding capability

**Cache Scaling:**
- Redis cluster
- Multiple cache nodes

### Vertical Scaling

- Optimized queries
- Efficient algorithms
- Resource management

---

## Monitoring & Logging

### Application Monitoring
- Laravel Telescope (development/staging)
- Performance metrics
- Database queries
- API requests

### Error Tracking
- Sentry integration
- Exception logging
- Stack traces
- User context

### Business Metrics
- Custom metrics
- KPI tracking
- Real-time dashboards

---

**Document Version:** 1.0
**Last Updated:** 2025-01-15
**Maintained By:** Technical Architect
