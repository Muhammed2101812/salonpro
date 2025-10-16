# User Roles & Permissions - SalonPro

## Role Hierarchy

```
Super Admin (System Administrator)
└── Organization Admin (Business Owner)
    └── Branch Manager
        ├── Accountant
        ├── Inventory Manager
        ├── Marketing Manager
        └── Staff
            ├── Receptionist
            ├── Senior Stylist
            ├── Junior Stylist
            ├── Beautician
            └── Massage Therapist
```

---

## Role Definitions

### 1. Super Admin
**Purpose:** System-wide administration and support

**Permissions:**
- Full system access
- Manage all organizations
- System configuration
- User impersonation
- Debug and monitoring tools

**Access Level:** All modules, all branches

---

### 2. Organization Admin
**Purpose:** Business owner/administrator

**Permissions:**
- Manage organization settings
- Create/manage branches
- Manage all users within organization
- View all reports across branches
- Financial overview
- Subscription management

**Access Level:** All branches within organization

---

### 3. Branch Manager
**Purpose:** Branch operations management

**Permissions:**
- Manage branch settings
- Manage branch users
- Approve expenses
- View branch reports
- Manage inventory
- Approve refunds
- Schedule management

**Access Level:** Assigned branch(es)

**Cannot:**
- Delete branch
- Access other branches
- Change organization settings

---

### 4. Accountant
**Purpose:** Financial management

**Permissions:**
- View financial reports
- Process payments
- Manage invoices
- View expenses
- View revenue
- Export financial data
- Manage debt/credit

**Access Level:** Assigned branch(es)

**Cannot:**
- Manage inventory
- Manage staff
- Approve own expenses

---

### 5. Inventory Manager
**Purpose:** Stock and product management

**Permissions:**
- Manage products
- Manage inventory
- Stock movements
- Purchase orders
- Supplier management
- Stock reports

**Access Level:** Assigned branch(es)

**Cannot:**
- View financial reports
- Manage users
- Process payments

---

### 6. Marketing Manager
**Purpose:** Marketing and campaigns

**Permissions:**
- Create campaigns
- Send email/SMS
- Manage loyalty program
- Create gift vouchers
- View marketing reports
- Customer segmentation

**Access Level:** Assigned branch(es)

**Cannot:**
- Process payments
- Manage inventory
- View detailed financials

---

### 7. Receptionist
**Purpose:** Front desk operations

**Permissions:**
- Create/manage appointments
- Check-in customers
- View schedule
- Create customers
- Basic customer info edit
- Process payments
- Print invoices

**Access Level:** Assigned branch

**Cannot:**
- View financial reports
- Manage inventory
- Manage users
- Cancel completed appointments
- Refund payments

---

### 8. Senior Stylist
**Purpose:** Experienced service provider

**Permissions:**
- View own schedule
- View assigned appointments
- Update appointment status
- Add appointment notes
- View customer history
- View own commissions
- Block calendar dates

**Access Level:** Assigned branch, own data

**Cannot:**
- Create appointments
- View other staff data
- Process payments
- Manage inventory

---

### 9. Junior Stylist
**Purpose:** Service provider

**Permissions:**
- View own schedule
- View assigned appointments
- Update appointment status (limited)
- Add appointment notes
- View customer basic info

**Access Level:** Assigned branch, own data

**Cannot:**
- Block calendar dates
- View commissions
- Create appointments
- Modify prices

---

### 10. Beautician
**Purpose:** Beauty services provider

**Permissions:** Same as Senior Stylist
- Specialized for beauty services
- View own schedule and appointments
- Update appointment status
- Add service notes

---

### 11. Massage Therapist
**Purpose:** Massage services provider

**Permissions:** Same as Senior Stylist
- Specialized for massage services
- View own schedule and appointments
- Update appointment status
- Add service notes

---

## Permission Categories

### 1. Dashboard
- `dashboard.view` - View dashboard
- `dashboard.analytics` - View analytics

### 2. Appointments
- `appointments.view` - View appointments
- `appointments.create` - Create appointments
- `appointments.update` - Update appointments
- `appointments.delete` - Delete appointments
- `appointments.cancel` - Cancel appointments
- `appointments.reschedule` - Reschedule appointments
- `appointments.checkin` - Check-in customers
- `appointments.view_all` - View all appointments
- `appointments.view_own` - View own appointments

### 3. Customers
- `customers.view` - View customers
- `customers.create` - Create customers
- `customers.update` - Update customers
- `customers.delete` - Delete customers
- `customers.export` - Export customer data
- `customers.import` - Import customer data
- `customers.segment` - Create segments
- `customers.blacklist` - Manage blacklist

### 4. Employees
- `employees.view` - View employees
- `employees.create` - Create employees
- `employees.update` - Update employees
- `employees.delete` - Delete employees
- `employees.schedule` - Manage schedules
- `employees.performance` - View performance
- `employees.payroll` - Manage payroll

### 5. Services
- `services.view` - View services
- `services.create` - Create services
- `services.update` - Update services
- `services.delete` - Delete services
- `services.pricing` - Manage pricing

### 6. Products & Inventory
- `products.view` - View products
- `products.create` - Create products
- `products.update` - Update products
- `products.delete` - Delete products
- `inventory.view` - View inventory
- `inventory.manage` - Manage stock
- `inventory.transfer` - Transfer stock
- `inventory.count` - Stock counting

### 7. Payments & Finance
- `payments.view` - View payments
- `payments.create` - Process payments
- `payments.refund` - Refund payments
- `invoices.view` - View invoices
- `invoices.create` - Create invoices
- `expenses.view` - View expenses
- `expenses.create` - Create expenses
- `expenses.approve` - Approve expenses
- `financial_reports.view` - View financial reports

### 8. Reports
- `reports.sales` - Sales reports
- `reports.customers` - Customer reports
- `reports.employees` - Employee reports
- `reports.financial` - Financial reports
- `reports.inventory` - Inventory reports
- `reports.export` - Export reports

### 9. Marketing
- `campaigns.view` - View campaigns
- `campaigns.create` - Create campaigns
- `campaigns.update` - Update campaigns
- `campaigns.send` - Send campaigns
- `loyalty.manage` - Manage loyalty
- `vouchers.manage` - Manage vouchers

### 10. Settings
- `settings.view` - View settings
- `settings.update` - Update settings
- `settings.branch` - Branch settings
- `settings.organization` - Organization settings

### 11. Users & Roles
- `users.view` - View users
- `users.create` - Create users
- `users.update` - Update users
- `users.delete` - Delete users
- `roles.manage` - Manage roles
- `permissions.manage` - Manage permissions

---

## Permission Matrix

| Permission Group | Super Admin | Org Admin | Branch Manager | Accountant | Inventory | Marketing | Receptionist | Stylist |
|-----------------|-------------|-----------|----------------|------------|-----------|-----------|--------------|---------|
| Dashboard | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Limited |
| Appointments | ✓ | ✓ | ✓ | View | View | View | ✓ | Own Only |
| Customers | ✓ | ✓ | ✓ | View | View | ✓ | ✓ | View Only |
| Employees | ✓ | ✓ | ✓ | View | View | View | View | Own Only |
| Services | ✓ | ✓ | ✓ | View | View | View | View | View |
| Products | ✓ | ✓ | ✓ | View | ✓ | View | View | ✗ |
| Inventory | ✓ | ✓ | ✓ | View | ✓ | ✗ | ✗ | ✗ |
| Payments | ✓ | ✓ | ✓ | ✓ | ✗ | ✗ | Create | ✗ |
| Financial Reports | ✓ | ✓ | ✓ | ✓ | ✗ | Limited | ✗ | Own Only |
| Marketing | ✓ | ✓ | ✓ | ✗ | ✗ | ✓ | ✗ | ✗ |
| Settings | ✓ | Org Only | Branch Only | ✗ | ✗ | ✗ | ✗ | ✗ |
| Users | ✓ | ✓ | ✓ | ✗ | ✗ | ✗ | ✗ | ✗ |

✓ = Full Access | Limited = Restricted Access | View = Read-only | Own Only = Own data | ✗ = No Access

---

## Special Permissions

### Time-based Permissions
- Appointments can only be cancelled within X hours (configurable)
- Financial data access restricted by date range
- Reports limited to recent periods for lower roles

### IP-based Restrictions
- Admin access requires whitelisted IP (optional)
- Financial operations from specific IPs only (optional)

### Device-based Restrictions
- 2FA required for sensitive operations
- Trusted device management

---

## Implementation

### Using Spatie Permission Package

```php
// Assign role
$user->assignRole('branch_manager');

// Check permission
if ($user->can('appointments.create')) {
    // Allow
}

// Check multiple permissions
if ($user->hasAnyPermission(['appointments.create', 'appointments.update'])) {
    // Allow
}

// Check role
if ($user->hasRole('super_admin')) {
    // Allow
}
```

### Policy Example

```php
class AppointmentPolicy
{
    public function view(User $user, Appointment $appointment): bool
    {
        // Super admin can view all
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Check branch access
        if ($user->branch_id !== $appointment->branch_id) {
            return false;
        }

        // Check permission
        return $user->can('appointments.view') ||
               ($user->can('appointments.view_own') && $appointment->employee_id === $user->employee->id);
    }
}
```

---

## Best Practices

1. **Principle of Least Privilege** - Give minimum required permissions
2. **Role-based Access** - Use roles, not individual permissions
3. **Regular Audits** - Review permissions quarterly
4. **Separation of Duties** - No single user should have conflicting permissions
5. **Document Changes** - Log all permission changes

---

**Document Version:** 1.0
**Last Updated:** 2025-01-15
