# Database Design - SalonPro

## Overview

This document outlines the database schema design for SalonPro, including tables, relationships, indexes, and constraints.

---

## Design Principles

1. **Third Normal Form (3NF)** for transactional data
2. **Soft Deletes** for all major entities
3. **UUID Primary Keys** for distributed system readiness
4. **Timestamps** (created_at, updated_at) on all tables
5. **Multi-tenancy** via branch_id column
6. **Audit Trail** capability
7. **Optimized Indexing** for performance

---

## Core Tables

### organizations
**Purpose:** Multi-tenant organization management

| Column | Type | Notes |
|--------|------|-------|
| id | UUID | PK |
| name | VARCHAR(255) | |
| slug | VARCHAR(255) | Unique |
| email | VARCHAR(255) | |
| phone | VARCHAR(50) | |
| address | TEXT | |
| logo | VARCHAR(255) | |
| settings | JSON | Organization settings |
| is_active | BOOLEAN | Default true |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |
| deleted_at | TIMESTAMP | Nullable |

**Indexes:**
- PRIMARY KEY (id)
- UNIQUE (slug)
- INDEX (is_active)

---

### branches
**Purpose:** Branch/location management

| Column | Type | Notes |
|--------|------|-------|
| id | UUID | PK |
| organization_id | UUID | FK to organizations |
| name | VARCHAR(255) | |
| slug | VARCHAR(255) | |
| email | VARCHAR(255) | |
| phone | VARCHAR(50) | |
| address | TEXT | |
| city | VARCHAR(100) | |
| state | VARCHAR(100) | |
| postal_code | VARCHAR(20) | |
| country | VARCHAR(100) | |
| timezone | VARCHAR(50) | |
| currency | ENUM('TRY','USD') | Default TRY |
| settings | JSON | Branch-specific settings |
| working_hours | JSON | Weekly schedule |
| is_active | BOOLEAN | Default true |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |
| deleted_at | TIMESTAMP | Nullable |

**Indexes:**
- PRIMARY KEY (id)
- FOREIGN KEY (organization_id)
- INDEX (slug)
- INDEX (is_active)

---

### users
**Purpose:** System users (admin, employees)

| Column | Type | Notes |
|--------|------|-------|
| id | UUID | PK |
| branch_id | UUID | FK to branches |
| email | VARCHAR(255) | Unique |
| password | VARCHAR(255) | Hashed |
| first_name | VARCHAR(100) | |
| last_name | VARCHAR(100) | |
| phone | VARCHAR(50) | |
| avatar | VARCHAR(255) | Nullable |
| language | ENUM('tr','en') | Default tr |
| timezone | VARCHAR(50) | |
| two_factor_secret | VARCHAR(255) | Nullable |
| two_factor_enabled | BOOLEAN | Default false |
| email_verified_at | TIMESTAMP | Nullable |
| last_login_at | TIMESTAMP | Nullable |
| last_login_ip | VARCHAR(45) | Nullable |
| failed_login_attempts | INT | Default 0 |
| locked_until | TIMESTAMP | Nullable |
| is_active | BOOLEAN | Default true |
| preferences | JSON | User preferences |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |
| deleted_at | TIMESTAMP | Nullable |

**Indexes:**
- PRIMARY KEY (id)
- UNIQUE (email)
- FOREIGN KEY (branch_id)
- INDEX (is_active, branch_id)

---

### customers
**Purpose:** Customer/client management

| Column | Type | Notes |
|--------|------|-------|
| id | UUID | PK |
| branch_id | UUID | FK to branches |
| customer_number | VARCHAR(50) | Auto-generated, unique |
| first_name | VARCHAR(100) | |
| last_name | VARCHAR(100) | |
| email | VARCHAR(255) | Nullable |
| phone | VARCHAR(50) | |
| secondary_phone | VARCHAR(50) | Nullable |
| birth_date | DATE | Nullable |
| gender | ENUM('male','female','other') | Nullable |
| language | ENUM('tr','en') | Default tr |
| notes | TEXT | Nullable |
| source | VARCHAR(100) | How they found us |
| customer_type | ENUM('normal','vip','potential') | Default normal |
| status | ENUM('active','inactive','lost') | Default active |
| allergies | TEXT | Nullable |
| skin_type | VARCHAR(50) | Nullable |
| hair_type | VARCHAR(50) | Nullable |
| preferred_employee_id | UUID | FK to employees, nullable |
| is_blacklisted | BOOLEAN | Default false |
| blacklist_reason | TEXT | Nullable |
| avatar | VARCHAR(255) | Nullable |
| tags | JSON | Array of tags |
| preferences | JSON | Customer preferences |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |
| deleted_at | TIMESTAMP | Nullable |

**Indexes:**
- PRIMARY KEY (id)
- UNIQUE (customer_number)
- FOREIGN KEY (branch_id)
- FOREIGN KEY (preferred_employee_id)
- INDEX (email)
- INDEX (phone)
- INDEX (status, branch_id)
- INDEX (customer_type)

---

### employees
**Purpose:** Staff/employee management

| Column | Type | Notes |
|--------|------|-------|
| id | UUID | PK |
| user_id | UUID | FK to users |
| branch_id | UUID | FK to branches |
| employee_number | VARCHAR(50) | Auto-generated, unique |
| hire_date | DATE | |
| birth_date | DATE | Nullable |
| national_id | VARCHAR(50) | Encrypted, nullable |
| emergency_contact_name | VARCHAR(255) | Nullable |
| emergency_contact_phone | VARCHAR(50) | Nullable |
| blood_type | VARCHAR(10) | Nullable |
| marital_status | ENUM('single','married','divorced','widowed') | Nullable |
| education_level | VARCHAR(100) | Nullable |
| certifications | JSON | Array of certifications |
| skills | JSON | Array of skills |
| languages | JSON | Array of languages |
| salary | DECIMAL(10,2) | Encrypted |
| commission_rate | DECIMAL(5,2) | Percentage |
| bank_name | VARCHAR(255) | Nullable |
| bank_account | VARCHAR(255) | Encrypted, nullable |
| social_security_number | VARCHAR(50) | Encrypted, nullable |
| contract_type | ENUM('full_time','part_time','contract') | |
| employment_status | ENUM('active','on_leave','terminated') | Default active |
| weekly_hours | INT | Default 40 |
| daily_capacity | INT | Appointments per day |
| settings | JSON | Employee settings |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |
| deleted_at | TIMESTAMP | Nullable |

**Indexes:**
- PRIMARY KEY (id)
- UNIQUE (employee_number)
- FOREIGN KEY (user_id)
- FOREIGN KEY (branch_id)
- INDEX (employment_status, branch_id)

---

### services
**Purpose:** Service catalog

| Column | Type | Notes |
|--------|------|-------|
| id | UUID | PK |
| category_id | UUID | FK to service_categories |
| name | JSON | Multi-language: {tr, en} |
| slug | VARCHAR(255) | |
| description | JSON | Multi-language |
| short_description | JSON | Multi-language |
| duration_min | INT | Minutes |
| duration_max | INT | Minutes |
| price_try | DECIMAL(10,2) | Turkish Lira |
| price_usd | DECIMAL(10,2) | US Dollar |
| cost_try | DECIMAL(10,2) | Cost in TRY |
| cost_usd | DECIMAL(10,2) | Cost in USD |
| tax_rate | DECIMAL(5,2) | Percentage |
| commission_rate | DECIMAL(5,2) | Percentage |
| required_employees | INT | Default 1 |
| required_materials | JSON | Array of materials |
| preparation_instructions | JSON | Multi-language |
| aftercare_instructions | JSON | Multi-language |
| image | VARCHAR(255) | Nullable |
| gallery | JSON | Array of images |
| gender_restriction | ENUM('none','male','female') | Default none |
| age_restriction | JSON | {min, max} |
| popularity_score | INT | Default 0 |
| is_online_bookable | BOOLEAN | Default true |
| is_active | BOOLEAN | Default true |
| settings | JSON | Service settings |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |
| deleted_at | TIMESTAMP | Nullable |

**Indexes:**
- PRIMARY KEY (id)
- FOREIGN KEY (category_id)
- INDEX (slug)
- INDEX (is_active, is_online_bookable)

---

### appointments
**Purpose:** Appointment/booking management

| Column | Type | Notes |
|--------|------|-------|
| id | UUID | PK |
| branch_id | UUID | FK to branches |
| customer_id | UUID | FK to customers |
| employee_id | UUID | FK to employees |
| appointment_number | VARCHAR(50) | Auto-generated, unique |
| scheduled_at | DATETIME | |
| end_time | DATETIME | Calculated |
| duration | INT | Minutes |
| status | ENUM | pending/confirmed/in_progress/completed/cancelled/no_show |
| cancellation_reason | TEXT | Nullable |
| cancelled_at | DATETIME | Nullable |
| cancelled_by | UUID | FK to users, nullable |
| checked_in_at | DATETIME | Nullable |
| started_at | DATETIME | Nullable |
| completed_at | DATETIME | Nullable |
| notes | TEXT | Nullable |
| internal_notes | TEXT | Staff only |
| reminder_sent_at | DATETIME | Nullable |
| is_recurring | BOOLEAN | Default false |
| recurring_pattern | JSON | Nullable |
| parent_appointment_id | UUID | FK to appointments, nullable |
| total_amount | DECIMAL(10,2) | |
| currency | ENUM('TRY','USD') | |
| deposit_amount | DECIMAL(10,2) | Default 0 |
| deposit_paid | BOOLEAN | Default false |
| source | ENUM('web','phone','walk_in','app') | |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |
| deleted_at | TIMESTAMP | Nullable |

**Indexes:**
- PRIMARY KEY (id)
- UNIQUE (appointment_number)
- FOREIGN KEY (branch_id, customer_id, employee_id)
- INDEX (scheduled_at, branch_id)
- INDEX (status, branch_id)
- INDEX (employee_id, scheduled_at)
- INDEX (customer_id, scheduled_at)

---

### appointment_services
**Purpose:** Services linked to appointment (many-to-many)

| Column | Type | Notes |
|--------|------|-------|
| id | UUID | PK |
| appointment_id | UUID | FK to appointments |
| service_id | UUID | FK to services |
| employee_id | UUID | FK to employees |
| quantity | INT | Default 1 |
| duration | INT | Minutes |
| price | DECIMAL(10,2) | |
| cost | DECIMAL(10,2) | |
| commission_rate | DECIMAL(5,2) | |
| commission_amount | DECIMAL(10,2) | |
| discount_type | ENUM('none','percentage','fixed') | Default none |
| discount_value | DECIMAL(10,2) | Default 0 |
| final_price | DECIMAL(10,2) | After discount |
| notes | TEXT | Nullable |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

**Indexes:**
- PRIMARY KEY (id)
- FOREIGN KEY (appointment_id, service_id, employee_id)
- INDEX (appointment_id)

---

### payments
**Purpose:** Payment transactions

| Column | Type | Notes |
|--------|------|-------|
| id | UUID | PK |
| branch_id | UUID | FK to branches |
| appointment_id | UUID | FK to appointments, nullable |
| customer_id | UUID | FK to customers |
| invoice_id | UUID | FK to invoices, nullable |
| payment_number | VARCHAR(50) | Auto-generated, unique |
| payment_date | DATETIME | |
| amount | DECIMAL(10,2) | |
| currency | ENUM('TRY','USD') | |
| exchange_rate | DECIMAL(10,4) | If conversion |
| payment_method | ENUM | cash/card/transfer/check/voucher/points |
| payment_status | ENUM('pending','completed','failed','refunded') | |
| transaction_id | VARCHAR(255) | External transaction ID |
| reference_number | VARCHAR(255) | Nullable |
| notes | TEXT | Nullable |
| processed_by | UUID | FK to users |
| refund_amount | DECIMAL(10,2) | Default 0 |
| refunded_at | DATETIME | Nullable |
| refund_reason | TEXT | Nullable |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |
| deleted_at | TIMESTAMP | Nullable |

**Indexes:**
- PRIMARY KEY (id)
- UNIQUE (payment_number)
- FOREIGN KEY (branch_id, customer_id, appointment_id, invoice_id, processed_by)
- INDEX (payment_date, branch_id)
- INDEX (payment_status)
- INDEX (customer_id)

---

### products
**Purpose:** Product catalog for retail sales

| Column | Type | Notes |
|--------|------|-------|
| id | UUID | PK |
| category_id | UUID | FK to product_categories |
| name | JSON | Multi-language |
| slug | VARCHAR(255) | |
| description | JSON | Multi-language |
| sku | VARCHAR(100) | Unique |
| barcode | VARCHAR(100) | Nullable, unique |
| supplier_id | UUID | FK to suppliers, nullable |
| brand | VARCHAR(100) | Nullable |
| unit_type | ENUM('piece','kg','liter','box') | |
| purchase_price_try | DECIMAL(10,2) | |
| purchase_price_usd | DECIMAL(10,2) | |
| selling_price_try | DECIMAL(10,2) | |
| selling_price_usd | DECIMAL(10,2) | |
| wholesale_price_try | DECIMAL(10,2) | Nullable |
| wholesale_price_usd | DECIMAL(10,2) | Nullable |
| tax_rate | DECIMAL(5,2) | |
| profit_margin | DECIMAL(5,2) | Calculated |
| min_stock_level | INT | Default 0 |
| max_stock_level | INT | Nullable |
| reorder_point | INT | Default 0 |
| reorder_quantity | INT | Default 0 |
| shelf_life_days | INT | Nullable |
| ingredients | TEXT | Nullable |
| warnings | TEXT | Nullable |
| images | JSON | Array of images |
| is_active | BOOLEAN | Default true |
| is_featured | BOOLEAN | Default false |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |
| deleted_at | TIMESTAMP | Nullable |

**Indexes:**
- PRIMARY KEY (id)
- UNIQUE (sku)
- UNIQUE (barcode)
- FOREIGN KEY (category_id, supplier_id)
- INDEX (slug)
- INDEX (is_active)

---

### stock_movements
**Purpose:** Inventory tracking

| Column | Type | Notes |
|--------|------|-------|
| id | UUID | PK |
| branch_id | UUID | FK to branches |
| product_id | UUID | FK to products |
| movement_type | ENUM | in/out/transfer/adjustment/waste |
| quantity | DECIMAL(10,2) | |
| unit_price | DECIMAL(10,2) | |
| total_value | DECIMAL(10,2) | |
| currency | ENUM('TRY','USD') | |
| from_branch_id | UUID | FK to branches, nullable |
| to_branch_id | UUID | FK to branches, nullable |
| reference_type | VARCHAR(100) | appointment/sale/purchase |
| reference_id | UUID | Nullable |
| lot_number | VARCHAR(100) | Nullable |
| expiry_date | DATE | Nullable |
| notes | TEXT | Nullable |
| performed_by | UUID | FK to users |
| performed_at | DATETIME | |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

**Indexes:**
- PRIMARY KEY (id)
- FOREIGN KEY (branch_id, product_id, performed_by)
- INDEX (movement_type, branch_id)
- INDEX (product_id, performed_at)
- INDEX (reference_type, reference_id)

---

## Supporting Tables

### roles & permissions
- Managed by Spatie Permission package
- Standard tables: roles, permissions, model_has_roles, model_has_permissions, role_has_permissions

### activity_log
- Managed by Spatie Activity Log package
- Tracks all model changes and user actions

### sessions
- Laravel session storage

### jobs & failed_jobs
- Laravel queue tables

### cache
- Laravel cache table (optional, using Redis primarily)

---

## Relationships Summary

### One-to-Many
- Organization → Branches
- Branch → Users, Customers, Employees, Appointments, Payments
- Customer → Appointments
- Employee → Appointments
- Service → Appointments (via appointment_services)

### Many-to-Many
- Appointments ↔ Services (via appointment_services)
- Users ↔ Roles (via model_has_roles)
- Roles ↔ Permissions (via role_has_permissions)

### One-to-One
- Employee → User
- Customer → User (for customer portal)

---

## Indexing Strategy

### Primary Indexes
- All primary keys (UUID)
- All foreign keys
- Unique constraints (email, phone, sku, barcode, etc.)

### Composite Indexes
- (branch_id, created_at) - For branch-scoped queries
- (branch_id, status) - For filtered lists
- (customer_id, scheduled_at) - For customer history
- (employee_id, scheduled_at) - For employee schedule

### Full-Text Indexes
- customers (first_name, last_name, email, phone)
- services (name, description)
- products (name, description, sku, barcode)

---

## Data Retention Policy

### Active Data
- Keep indefinitely while business operates

### Soft Deleted Data
- Keep for 90 days
- Permanent deletion after 90 days

### Activity Logs
- Keep for 1 year
- Archive older logs

### Backups
- Daily backups for 30 days
- Weekly backups for 1 year
- Monthly backups for 7 years

---

## Migration Strategy

### Phase 1: Core Tables
- organizations, branches, users
- roles, permissions

### Phase 2: Business Entities
- customers, employees, services
- categories

### Phase 3: Transactions
- appointments, payments, invoices

### Phase 4: Inventory
- products, stock_movements, suppliers

### Phase 5: Supporting
- notifications, campaigns, loyalty

---

**Document Version:** 1.0
**Last Updated:** 2025-01-15
**Maintained By:** Database Administrator
