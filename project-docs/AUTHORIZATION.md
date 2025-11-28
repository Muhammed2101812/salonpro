# Authorization System Documentation

## Overview

SalonPro uses Laravel's built-in authorization system with Spatie Permission package for role-based access control (RBAC). The system implements a multi-layered security approach with Policies, Permissions, and Roles.

---

## Architecture

### Components

1. **Policies** - Define fine-grained authorization logic for models
2. **Permissions** - Individual capabilities (e.g., `customers.view`, `appointments.create`)
3. **Roles** - Groups of permissions assigned to users
4. **Guards** - Branch-based data isolation

### Data Flow

```
User Request ’ Middleware (auth + branch) ’ Controller ’ authorize() ’ Policy ’ Permission Check ’ Response
```

---

## Roles and Permissions

### Available Roles

#### 1. Super Admin
- **Description**: Complete system access across all branches
- **Permissions**: ALL
- **Use Cases**: System configuration, global management
- **Branch Access**: All branches

#### 2. Organization Admin
- **Description**: Organization-wide management (cannot modify system settings)
- **Permissions**: ALL except system settings
- **Use Cases**: Multi-branch management, organization setup
- **Branch Access**: All branches

#### 3. Branch Manager
- **Description**: Full management within assigned branch
- **Permissions**:
  - customers.* (view, create, update, export)
  - employees.* (view, manage-schedule, view-performance)
  - appointments.* (view, create, update, cancel, reschedule)
  - inventory.* (view, create, update, adjust)
  - payments.* (view, create, view-reports)
  - expenses.* (view, create, update, approve)
  - reports.* (sales, financial, customer, employee, inventory)
- **Branch Access**: Own branch only

#### 4. Accountant
- **Description**: Financial operations and reporting
- **Permissions**:
  - customers.view
  - appointments.view
  - payments.* (view, create, refund, view-reports)
  - sales.view
  - expenses.* (view, create, update, approve)
  - reports.* (sales, financial, export)
- **Branch Access**: Own branch

#### 5. Receptionist
- **Description**: Front desk operations
- **Permissions**:
  - customers.* (view, create, update)
  - appointments.* (view, create, update, cancel, reschedule)
  - services.view
  - payments.* (view, create)
  - sales.* (view, create)
- **Branch Access**: Own branch

#### 6. Stylist/Beautician
- **Description**: Service providers
- **Permissions**:
  - customers.view
  - appointments.* (view, update)
  - services.view
  - products.view
  - sales.* (view, create)
- **Branch Access**: Own branch

#### 7. Sales Representative
- **Description**: Sales and customer management
- **Permissions**:
  - customers.* (view, create, update)
  - products.view
  - sales.* (view, create, update)
  - payments.* (view, create)
- **Branch Access**: Own branch

#### 8. Inventory Manager
- **Description**: Inventory and stock management
- **Permissions**:
  - products.* (view, create, update, manage-inventory)
  - inventory.* (view, create, update, transfer, adjust)
  - expenses.view
  - reports.* (inventory, export)
- **Branch Access**: Own branch

#### 9. Marketing Manager
- **Description**: Customer analytics and campaigns
- **Permissions**:
  - customers.* (view, export)
  - services.view
  - reports.* (customer, sales, export)
  - dashboard.analytics
- **Branch Access**: All branches

#### 10. HR Manager
- **Description**: Employee management
- **Permissions**:
  - employees.* (view, create, update, manage-schedule, view-performance)
  - users.* (view, create, update)
  - reports.employee
- **Branch Access**: All branches

#### 11. Viewer
- **Description**: Read-only access
- **Permissions**: All `.view` permissions
- **Branch Access**: Own branch

---

## Permission Categories

### Customer Management
- `customers.view` - View customers
- `customers.create` - Create new customers
- `customers.update` - Update customer information
- `customers.delete` - Delete customers
- `customers.export` - Export customer data

### Employee Management
- `employees.view` - View employees
- `employees.create` - Create new employees
- `employees.update` - Update employee information
- `employees.delete` - Delete employees
- `employees.manage-schedule` - Manage employee schedules
- `employees.view-performance` - View performance metrics

### Appointment Management
- `appointments.view` - View appointments
- `appointments.create` - Create new appointments
- `appointments.update` - Update appointments
- `appointments.delete` - Delete appointments
- `appointments.cancel` - Cancel appointments
- `appointments.reschedule` - Reschedule appointments
- `appointments.view-all` - View all appointments across branches

### Service Management
- `services.view` - View services
- `services.create` - Create new services
- `services.update` - Update services
- `services.delete` - Delete services
- `services.manage-categories` - Manage service categories
- `services.manage-pricing` - Manage service pricing

### Product Management
- `products.view` - View products
- `products.create` - Create new products
- `products.update` - Update products
- `products.delete` - Delete products
- `products.manage-inventory` - Manage product inventory

### Inventory Management
- `inventory.view` - View inventory movements
- `inventory.create` - Create inventory movements
- `inventory.update` - Update inventory movements
- `inventory.delete` - Delete inventory movements
- `inventory.transfer` - Transfer inventory between branches
- `inventory.adjust` - Adjust inventory quantities

### Payment Management
- `payments.view` - View payments
- `payments.create` - Create payments
- `payments.refund` - Process refunds
- `payments.view-reports` - View payment reports

### Sales Management
- `sales.view` - View sales
- `sales.create` - Create sales
- `sales.update` - Update sales
- `sales.delete` - Delete sales

### Expense Management
- `expenses.view` - View expenses
- `expenses.create` - Create expenses
- `expenses.update` - Update expenses
- `expenses.delete` - Delete expenses
- `expenses.approve` - Approve expenses

### Branch Management
- `branches.view` - View branches
- `branches.create` - Create branches
- `branches.update` - Update branches
- `branches.delete` - Delete branches
- `branches.manage-settings` - Manage branch settings

### Report Access
- `reports.sales` - View sales reports
- `reports.financial` - View financial reports
- `reports.customer` - View customer reports
- `reports.employee` - View employee reports
- `reports.inventory` - View inventory reports
- `reports.export` - Export reports

### User Management
- `users.view` - View users
- `users.create` - Create users
- `users.update` - Update users
- `users.delete` - Delete users
- `users.manage-roles` - Manage user roles
- `users.manage-permissions` - Manage user permissions

### Settings Management
- `settings.view` - View settings
- `settings.update` - Update settings
- `settings.system` - Modify system settings

### Audit & Dashboard
- `audit.view` - View audit logs
- `dashboard.view` - View dashboard
- `dashboard.analytics` - View analytics dashboard

---

## Policy Classes

### Policy Structure

All policy classes follow a consistent pattern:

```php
<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\{Model};
use App\Models\User;

class {Model}Policy
{
    /**
     * Determine if the user can view any {models}.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('{resource}.view');
    }

    /**
     * Determine if the user can view the {model}.
     */
    public function view(User $user, {Model} ${model}): bool
    {
        // Super admin and organization admin can view all
        if ($user->hasRole(['Super Admin', 'Organization Admin'])) {
            return true;
        }

        // Other users can only view {models} in their branch
        return $user->can('{resource}.view') && $user->branch_id === ${model}->branch_id;
    }

    /**
     * Determine if the user can create {models}.
     */
    public function create(User $user): bool
    {
        return $user->can('{resource}.create');
    }

    /**
     * Determine if the user can update the {model}.
     */
    public function update(User $user, {Model} ${model}): bool
    {
        if ($user->hasRole(['Super Admin', 'Organization Admin'])) {
            return true;
        }

        return $user->can('{resource}.update') && $user->branch_id === ${model}->branch_id;
    }

    /**
     * Determine if the user can delete the {model}.
     */
    public function delete(User $user, {Model} ${model}): bool
    {
        if ($user->hasRole(['Super Admin', 'Organization Admin'])) {
            return true;
        }

        return $user->can('{resource}.delete') && $user->branch_id === ${model}->branch_id;
    }

    /**
     * Determine if the user can restore the {model}.
     */
    public function restore(User $user, {Model} ${model}): bool
    {
        return $this->delete($user, ${model});
    }

    /**
     * Determine if the user can permanently delete the {model}.
     */
    public function forceDelete(User $user, {Model} ${model}): bool
    {
        // Only super admin can force delete
        return $user->hasRole('Super Admin');
    }
}
```

### Implemented Policy Classes

1. **CustomerPolicy** - `app/Policies/CustomerPolicy.php`
   - Branch-based access control
   - Export permission check
   - Cross-branch view for admins and marketing managers

2. **AppointmentPolicy** - `app/Policies/AppointmentPolicy.php`
   - Branch-based access control
   - Cancel and reschedule specific checks
   - View-all permission for cross-branch access

3. **EmployeePolicy** - `app/Policies/EmployeePolicy.php`
   - Branch-based access control
   - Schedule management permission
   - Performance view permission
   - HR manager special access

4. **BranchPolicy** - `app/Policies/BranchPolicy.php`
   - Only admins can create/delete branches
   - Branch managers can update own branch
   - Settings management permission

5. **ProductPolicy** - `app/Policies/ProductPolicy.php`
   - Organization-wide view access (not branch-scoped)
   - Inventory management permission

6. **ServicePolicy** - `app/Policies/ServicePolicy.php`
   - Organization-wide access (not branch-scoped)
   - Category management permission
   - Pricing management permission

7. **PaymentPolicy** - `app/Policies/PaymentPolicy.php`
   - Branch-based through appointment/sale relationship
   - Refund permission check
   - Payment reports access

8. **SalePolicy** - `app/Policies/SalePolicy.php`
   - Branch-based through employee relationship

9. **ExpensePolicy** - `app/Policies/ExpensePolicy.php`
   - Branch-based access control
   - Approval permission check

10. **InvoicePolicy** - `app/Policies/InvoicePolicy.php`
    - Branch-based access control
    - Uses payment permissions

11. **InventoryPolicy** - `app/Policies/InventoryPolicy.php`
    - Transfer and adjust permissions
    - Not strictly branch-scoped (for transfers)

12. **PurchaseOrderPolicy** - `app/Policies/PurchaseOrderPolicy.php`
    - Branch-based access control
    - Uses inventory permissions

13. **StockAuditPolicy** - `app/Policies/StockAuditPolicy.php`
    - Branch-based access control
    - Approval permission for branch managers

---

## Using Authorization in Controllers

### Example: CustomerController with Authorization

```php
<?php

namespace App\Http\Controllers\API;

use App\Models\Customer;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;

class CustomerController extends BaseController
{
    public function index(Request $request)
    {
        // Check if user can view any customers
        $this->authorize('viewAny', Customer::class);

        // Your logic here
    }

    public function store(StoreCustomerRequest $request)
    {
        // Check if user can create customers
        $this->authorize('create', Customer::class);

        // Your logic here
    }

    public function show(string $id)
    {
        $customer = $this->customerService->findByIdOrFail($id);

        // Check if user can view this specific customer
        $this->authorize('view', $customer);

        return new CustomerResource($customer);
    }

    public function update(UpdateCustomerRequest $request, string $id)
    {
        $customer = $this->customerService->findByIdOrFail($id);

        // Check if user can update this customer
        $this->authorize('update', $customer);

        // Your logic here
    }

    public function destroy(string $id)
    {
        $customer = $this->customerService->findByIdOrFail($id);

        // Check if user can delete this customer
        $this->authorize('delete', $customer);

        // Your logic here
    }
}
```

### Using authorize() Method

```php
// Class-based authorization (for viewAny, create)
$this->authorize('create', Customer::class);

// Instance-based authorization (for view, update, delete)
$this->authorize('update', $customer);

// Custom policy method
$this->authorize('export', Customer::class);
```

### Using Gate Facade

```php
use Illuminate\Support\Facades\Gate;

// Check if user has permission
if (Gate::allows('view', $customer)) {
    // User can view
}

if (Gate::denies('update', $customer)) {
    // User cannot update
}

// Throw authorization exception if denied
Gate::authorize('delete', $customer);
```

### Using can() Helper

```php
// On user instance
if ($user->can('view', $customer)) {
    // User can view
}

// In Blade templates
@can('update', $customer)
    <button>Edit</button>
@endcan

// In Vue components (through API)
<button v-if="customer.can_edit">Edit</button>
```

---

## Branch-Based Authorization

### The BranchScoped Trait

Many models use the `BranchScoped` trait to automatically filter queries by branch:

```php
use App\Traits\BranchScoped;

class Customer extends Model
{
    use BranchScoped;
}

// Automatically scopes queries
Customer::all(); // Only returns customers in user's branch
```

### Multi-Branch Access

Users with `Super Admin`, `Organization Admin`, `Marketing Manager`, or `HR Manager` roles can access data across all branches:

```php
public function view(User $user, Customer $customer): bool
{
    // Super admin and org admin can view all
    if ($user->hasRole(['Super Admin', 'Organization Admin'])) {
        return true;
    }

    // Other users only see their branch
    return $user->can('customers.view') && $user->branch_id === $customer->branch_id;
}
```

---

## Authorization in API Responses

### Including Authorization Flags

API resources should include authorization flags for frontend use:

```php
class CustomerResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            // ... other fields

            // Authorization flags
            'can_edit' => $request->user()->can('update', $this->resource),
            'can_delete' => $request->user()->can('delete', $this->resource),
            'can_restore' => $request->user()->can('restore', $this->resource),
        ];
    }
}
```

### Frontend Usage

```vue
<template>
  <div>
    <button v-if="customer.can_edit" @click="edit">Edit</button>
    <button v-if="customer.can_delete" @click="remove">Delete</button>
  </div>
</template>
```

---

## Testing Authorization

### Policy Tests

```php
use App\Models\User;
use App\Models\Customer;
use App\Policies\CustomerPolicy;

test('super admin can view all customers', function () {
    $admin = User::factory()->create();
    $admin->assignRole('Super Admin');

    $customer = Customer::factory()->create();

    $policy = new CustomerPolicy();

    expect($policy->view($admin, $customer))->toBeTrue();
});

test('receptionist can only view customers in their branch', function () {
    $receptionist = User::factory()->create(['branch_id' => 'branch-1']);
    $receptionist->assignRole('Receptionist');

    $customerInSameBranch = Customer::factory()->create(['branch_id' => 'branch-1']);
    $customerInOtherBranch = Customer::factory()->create(['branch_id' => 'branch-2']);

    $policy = new CustomerPolicy();

    expect($policy->view($receptionist, $customerInSameBranch))->toBeTrue();
    expect($policy->view($receptionist, $customerInOtherBranch))->toBeFalse();
});
```

### Controller Authorization Tests

```php
test('unauthorized user cannot create customer', function () {
    $user = User::factory()->create();
    // User has no permissions

    actingAs($user)
        ->post('/api/v1/customers', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            // ...
        ])
        ->assertStatus(403); // Forbidden
});

test('receptionist can create customer', function () {
    $receptionist = User::factory()->create();
    $receptionist->assignRole('Receptionist');

    actingAs($receptionist)
        ->post('/api/v1/customers', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            // ...
        ])
        ->assertStatus(201); // Created
});
```

---

## Common Authorization Patterns

### 1. Resource Authorization
```php
// Check before showing resource
$this->authorize('view', $customer);

// Check before updating
$this->authorize('update', $customer);

// Check before deleting
$this->authorize('delete', $customer);
```

### 2. Action Authorization
```php
// Check specific actions
$this->authorize('export', Customer::class);
$this->authorize('approve', $expense);
$this->authorize('refund', $payment);
```

### 3. Conditional Authorization
```php
if ($user->can('update', $customer)) {
    // Show edit form
} else {
    // Show read-only view
}
```

### 4. Multiple Permission Check
```php
if ($user->hasAnyPermission(['customers.view', 'customers.create'])) {
    // User has at least one permission
}

if ($user->hasAllPermissions(['customers.view', 'customers.export'])) {
    // User has all permissions
}
```

---

## Troubleshooting

### Common Issues

#### 1. Authorization Always Fails
**Problem**: User can't access resources they should be able to
**Solutions**:
- Check if user has correct role: `$user->roles`
- Check if role has correct permissions: `$role->permissions`
- Check if policy is registered in `AuthServiceProvider`
- Clear permission cache: `php artisan permission:cache-reset`

#### 2. Branch Scoping Not Working
**Problem**: User sees data from other branches
**Solutions**:
- Check if model uses `BranchScoped` trait
- Check if `branch_id` is set correctly
- Check policy's branch comparison logic

#### 3. Super Admin Gets 403 Errors
**Problem**: Super Admin is denied access
**Solutions**:
- Ensure super admin check comes FIRST in policy
- Check role name spelling (case-sensitive)
- Verify super admin has all permissions

#### 4. Permission Cache Issues
**Problem**: Changes to permissions don't take effect
**Solutions**:
```bash
# Clear permission cache
php artisan permission:cache-reset

# Clear application cache
php artisan cache:clear

# Clear config cache
php artisan config:clear
```

---

## Best Practices

### 1. Always Use Policies
L **Don't**: Check permissions directly in controllers
```php
if ($user->can('customers.update')) {
    // Business logic
}
```

 **Do**: Use policies and authorize
```php
$this->authorize('update', $customer);
```

### 2. Check Instance-Based Authorization
L **Don't**: Only check permission
```php
$this->authorize('update', Customer::class);
```

 **Do**: Check with specific instance
```php
$this->authorize('update', $customer);
```

### 3. Include Authorization in Resources
 **Do**: Provide frontend with authorization flags
```php
'can_edit' => $request->user()->can('update', $this->resource),
'can_delete' => $request->user()->can('delete', $this->resource),
```

### 4. Test Authorization Thoroughly
 **Do**: Test all authorization scenarios
- Unauthorized users (403)
- Authorized users (200/201)
- Cross-branch access attempts
- Role-specific access

---

## Security Considerations

### 1. Never Trust Client-Side Authorization
- Always authorize on server side
- Frontend flags are for UX only
- Don't rely on hidden buttons

### 2. Always Check Branch Scope
- Validate `branch_id` in requests
- Use `BranchScoped` trait where applicable
- Don't allow cross-branch access unless authorized

### 3. Protect Sensitive Operations
- Require explicit permissions for:
  - Force deletes
  - Data exports
  - Financial operations
  - System settings

### 4. Audit Authorization Failures
- Log 403 errors
- Monitor for suspicious patterns
- Alert on repeated authorization failures

---

## Migration Guide for Controllers

To add authorization to existing controllers:

### Step 1: Import Model
```php
use App\Models\Customer;
```

### Step 2: Add viewAny to index()
```php
public function index(Request $request)
{
    $this->authorize('viewAny', Customer::class);
    // ... rest of method
}
```

### Step 3: Add create to store()
```php
public function store(StoreCustomerRequest $request)
{
    $this->authorize('create', Customer::class);
    // ... rest of method
}
```

### Step 4: Add view/update/delete to other methods
```php
public function show(string $id)
{
    $customer = $this->customerService->findByIdOrFail($id);
    $this->authorize('view', $customer);
    // ... rest of method
}

public function update(UpdateCustomerRequest $request, string $id)
{
    $customer = $this->customerService->findByIdOrFail($id);
    $this->authorize('update', $customer);
    // ... rest of method
}

public function destroy(string $id)
{
    $customer = $this->customerService->findByIdOrFail($id);
    $this->authorize('delete', $customer);
    // ... rest of method
}
```

### Step 5: Test
```bash
php artisan test --filter=CustomerController
```

---

## Quick Reference

### Check User Role
```php
$user->hasRole('Super Admin')
$user->hasRole(['Super Admin', 'Organization Admin'])
$user->hasAnyRole(['Branch Manager', 'Receptionist'])
```

### Check Permission
```php
$user->can('customers.view')
$user->hasPermissionTo('customers.create')
$user->hasAnyPermission(['customers.view', 'customers.create'])
```

### Authorize in Controller
```php
$this->authorize('viewAny', Customer::class);  // For listing
$this->authorize('create', Customer::class);   // For creating
$this->authorize('view', $customer);           // For viewing specific
$this->authorize('update', $customer);         // For updating specific
$this->authorize('delete', $customer);         // For deleting specific
```

### Clear Caches
```bash
php artisan permission:cache-reset
php artisan cache:clear
php artisan config:clear
```

---

## Related Documentation

- [User Roles Documentation](./user-roles.md)
- [Security Guidelines](./security-guidelines.md)
- [API Documentation](./api-documentation.md)
- [Testing Strategy](./testing-strategy.md)

---

**Last Updated**: 2025-11-28
**Version**: 1.0
