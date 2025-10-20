# Salon Manager API Documentation

## Overview

Comprehensive REST API for Salon Management System built with Laravel 11.

**Base URL:** `http://localhost:8000/api/v1`

**Authentication:** Bearer Token (Laravel Sanctum)

## Quick Start

### Authentication

```bash
# Register
POST /api/v1/register
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password",
  "password_confirmation": "password"
}

# Login
POST /api/v1/login
{
  "email": "john@example.com",
  "password": "password"
}

# Response
{
  "success": true,
  "data": {
    "user": {...},
    "token": "1|abc123..."
  }
}
```

### Using the API

Include the token in all requests:

```bash
Authorization: Bearer {your-token}
```

## API Statistics

- **Total Endpoints:** 597
- **Resources:** 114
- **Controllers:** 124
- **Authentication:** Required for all endpoints (except login/register)

## Resource Categories

### 1. Branches & Settings (4 resources)
```
GET    /api/v1/branches
POST   /api/v1/branches
GET    /api/v1/branches/{id}
PUT    /api/v1/branches/{id}
DELETE /api/v1/branches/{id}
```

### 2. Customers (8 resources)
- customers
- customer-categories
- customer-tags
- customer-notes
- customer-feedback
- customer-segments
- customer-segment-members
- customer-rfm-analysis

### 3. Employees (9 resources)
- employees
- employee-skills
- employee-certifications
- employee-schedules
- employee-shifts
- employee-attendance
- employee-performance
- employee-commissions
- employee-leave

### 4. Appointments (10 resources)
- appointments
- appointment-cancellations
- appointment-cancellation-reasons
- appointment-conflicts
- appointment-groups
- appointment-group-participants
- appointment-history
- appointment-recurrences
- appointment-reminders
- appointment-waitlist

### 5. Services (9 resources)
- service-categories
- services
- service-addons
- service-packages
- service-pricing-rules
- service-price-history
- service-templates
- service-requirements
- service-reviews

### 6. Products (13 resources)
- products
- product-bundles
- product-bundle-items
- product-variants
- product-attributes
- product-attribute-values
- product-barcodes
- product-discounts
- product-images
- product-price-history
- product-stock-history
- product-supplier-prices
- product-category-hierarchy

### 7. Inventory & Supply Chain (8 resources)
- inventory-movements
- suppliers
- purchase-orders
- purchase-order-items
- stock-alerts
- stock-audits
- stock-audit-items
- stock-transfers

### 8. Financial Management (16 resources)
- invoices, invoice-items
- payments, expenses
- bank-accounts, bank-transactions
- budget-plans, budget-items
- cash-registers, cash-register-sessions, cash-register-transactions
- chart-of-accounts
- journal-entries, journal-entry-lines
- tax-rates, currencies, exchange-rates

### 9. Marketing & CRM (10 resources)
- marketing-campaigns
- campaign-statistics
- coupons, coupon-usage
- loyalty-programs, loyalty-points, loyalty-point-transactions
- referrals, referral-programs
- leads, lead-activities

### 10. Notifications (8 resources)
- notification-campaigns
- notification-templates
- notification-queue
- notification-logs
- notification-preferences
- sms-providers
- email-providers
- push-notification-tokens

### 11. Analytics & Reporting (8 resources)
- analytics-events
- analytics-sessions
- kpi-definitions
- kpi-values
- performance-metrics
- report-templates
- report-schedules
- report-executions

### 12. System & Utilities (10 resources)
- activity-logs (read-only)
- audit-logs (read-only)
- system-backups
- webhooks
- integrations
- custom-fields, custom-field-values
- translations
- feature-flags
- user-preferences

## Standard Response Format

### Success Response
```json
{
  "success": true,
  "message": "Operation successful",
  "data": {
    "id": "uuid",
    ...
  }
}
```

### Paginated Response
```json
{
  "success": true,
  "message": "Data retrieved",
  "data": [...],
  "meta": {
    "current_page": 1,
    "last_page": 10,
    "per_page": 15,
    "total": 150
  }
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    "field": ["Validation error"]
  }
}
```

## Common Query Parameters

- `per_page`: Items per page (default: 15)
- `page`: Page number
- `sort`: Sort field
- `order`: asc|desc
- `search`: Search term
- `with`: Load relationships (e.g., `?with=customer,service`)

## Example Requests

### Create Appointment
```bash
POST /api/v1/appointments
Content-Type: application/json
Authorization: Bearer {token}

{
  "customer_id": "uuid",
  "service_id": "uuid",
  "employee_id": "uuid",
  "branch_id": "uuid",
  "scheduled_at": "2025-12-01 10:00:00",
  "duration": 60,
  "notes": "Customer notes"
}
```

### Get Appointments with Relationships
```bash
GET /api/v1/appointments?with=customer,service,employee&per_page=20
```

### Update Customer
```bash
PUT /api/v1/customers/{id}
Content-Type: application/json
Authorization: Bearer {token}

{
  "first_name": "Jane",
  "last_name": "Doe",
  "phone": "+90 555 123 4567",
  "email": "jane@example.com"
}
```

## Rate Limiting

- **Rate Limit:** 60 requests per minute per user
- **Header:** `X-RateLimit-Remaining`

## Error Codes

- `200`: OK
- `201`: Created
- `400`: Bad Request
- `401`: Unauthorized
- `403`: Forbidden
- `404`: Not Found
- `422`: Validation Error
- `429`: Too Many Requests
- `500`: Internal Server Error

## Testing

Run the test suite:

```bash
php artisan test
```

- **Feature Tests:** 120 files
- **Unit Tests:** 176 files (122 services + 54 repositories)
- **Total Tests:** ~1,800 test methods

## Events & Jobs

### Available Events
- AppointmentCreated, AppointmentUpdated, AppointmentCancelled
- CustomerCreated, PaymentReceived
- StockLevelLow, ProductReceived
- And 12 more business events

### Available Jobs
- SendEmailJob, SendSmsJob
- ProcessPaymentJob, GenerateReportJob
- BackupDatabaseJob, SyncInventoryJob
- And 4 more async jobs

## Permissions

Use Spatie Laravel Permission for authorization:

### Roles
- super-admin
- admin
- manager
- employee
- receptionist

### Permissions Format
`{action}-{resource}` (e.g., `view-appointment`, `create-customer`)

## Development Tools

### Artisan Commands
```bash
# Generate API scaffold
php artisan make:api-scaffold {Model}

# Generate tests
php artisan make:api-tests

# Generate policies
php artisan make:policies

# Generate events
php artisan make:events

# Generate factories
php artisan make:factories
```

## Support

For issues and questions:
- GitHub: [Repository URL]
- Email: support@salonmanager.com
- Documentation: [Docs URL]

---

**Version:** 1.0.0
**Last Updated:** 2025-10-20
**Built with:** Laravel 11, PHP 8.3
