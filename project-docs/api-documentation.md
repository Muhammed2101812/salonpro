# API Documentation - SalonPro

## API Overview

SalonPro provides a RESTful API for mobile applications and third-party integrations.

**Base URL:** `https://api.salonpro.com/api/v1`

---

## Authentication

### Method: Laravel Sanctum (Token-based)

**Login Endpoint:**
```http
POST /auth/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password",
  "device_name": "iPhone 13"
}
```

**Response:**
```json
{
  "token": "1|abc123...",
  "user": {
    "id": "uuid",
    "name": "John Doe",
    "email": "user@example.com",
    "role": "receptionist"
  }
}
```

**Using Token:**
```http
GET /appointments
Authorization: Bearer 1|abc123...
X-Branch-ID: branch-uuid
Accept: application/json
```

---

## Common Headers

| Header | Required | Description |
|--------|----------|-------------|
| Authorization | Yes | Bearer token |
| X-Branch-ID | Yes | Current branch context |
| Accept | Yes | application/json |
| Content-Type | Yes (POST/PUT) | application/json |
| Accept-Language | No | tr or en (default: tr) |

---

## Response Format

### Success Response
```json
{
  "data": {...},
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 75
  },
  "links": {
    "first": "...",
    "last": "...",
    "prev": null,
    "next": "..."
  }
}
```

### Error Response
```json
{
  "message": "Validation error",
  "errors": {
    "email": ["The email field is required."]
  }
}
```

---

## Status Codes

| Code | Meaning |
|------|---------|
| 200 | OK - Request successful |
| 201 | Created - Resource created |
| 204 | No Content - Successful, no response body |
| 400 | Bad Request - Invalid request |
| 401 | Unauthorized - Authentication required |
| 403 | Forbidden - No permission |
| 404 | Not Found - Resource not found |
| 422 | Validation Error - Invalid data |
| 429 | Too Many Requests - Rate limited |
| 500 | Server Error - Internal error |

---

## API Endpoints

### Authentication

```http
POST   /auth/login            # Login
POST   /auth/logout           # Logout
POST   /auth/register         # Register (customer portal)
POST   /auth/forgot-password  # Request password reset
POST   /auth/reset-password   # Reset password
GET    /auth/user             # Get authenticated user
```

### Appointments

```http
GET    /appointments              # List appointments
GET    /appointments/{id}         # Get appointment
POST   /appointments              # Create appointment
PUT    /appointments/{id}         # Update appointment
DELETE /appointments/{id}         # Delete appointment
POST   /appointments/{id}/checkin # Check-in customer
POST   /appointments/{id}/complete # Mark as completed
POST   /appointments/{id}/cancel  # Cancel appointment
GET    /appointments/availability # Check availability
```

**List Appointments:**
```http
GET /appointments?status=confirmed&employee_id=uuid&date=2024-01-15&per_page=20&page=1
```

**Create Appointment:**
```http
POST /appointments
Content-Type: application/json

{
  "customer_id": "uuid",
  "employee_id": "uuid",
  "service_ids": ["uuid1", "uuid2"],
  "scheduled_at": "2024-01-20 14:00:00",
  "notes": "Customer prefers quiet area"
}
```

### Customers

```http
GET    /customers           # List customers
GET    /customers/{id}      # Get customer
POST   /customers           # Create customer
PUT    /customers/{id}      # Update customer
DELETE /customers/{id}      # Delete customer
GET    /customers/{id}/appointments # Customer appointments
GET    /customers/{id}/history # Customer history
GET    /customers/search    # Search customers
```

### Employees

```http
GET    /employees           # List employees
GET    /employees/{id}      # Get employee
POST   /employees           # Create employee
PUT    /employees/{id}      # Update employee
DELETE /employees/{id}      # Delete employee
GET    /employees/{id}/schedule # Employee schedule
GET    /employees/{id}/appointments # Employee appointments
GET    /employees/{id}/performance # Performance metrics
```

### Services

```http
GET    /services           # List services
GET    /services/{id}      # Get service
POST   /services           # Create service
PUT    /services/{id}      # Update service
DELETE /services/{id}      # Delete service
GET    /services/categories # Service categories
```

### Products

```http
GET    /products           # List products
GET    /products/{id}      # Get product
POST   /products           # Create product
PUT    /products/{id}      # Update product
DELETE /products/{id}      # Delete product
GET    /products/{id}/stock # Product stock levels
```

### Payments

```http
GET    /payments           # List payments
GET    /payments/{id}      # Get payment
POST   /payments           # Process payment
POST   /payments/{id}/refund # Refund payment
GET    /payments/methods   # Available payment methods
```

### Reports

```http
GET    /reports/sales       # Sales report
GET    /reports/customers   # Customer report
GET    /reports/employees   # Employee report
GET    /reports/financial   # Financial report
GET    /reports/inventory   # Inventory report
POST   /reports/custom      # Custom report
GET    /reports/{id}/export # Export report
```

---

## Filtering & Sorting

### Filtering
```http
GET /appointments?filter[status]=confirmed&filter[employee_id]=uuid&filter[date]=2024-01-15
```

### Sorting
```http
GET /customers?sort=-created_at,name
```
(Prefix with `-` for descending)

### Including Relationships
```http
GET /appointments?include=customer,employee,services
```

### Field Selection
```http
GET /customers?fields=id,name,email,phone
```

---

## Pagination

**Default:** 15 items per page
**Maximum:** 100 items per page

```http
GET /customers?page=2&per_page=25
```

**Response includes:**
```json
{
  "meta": {
    "current_page": 2,
    "last_page": 5,
    "per_page": 25,
    "total": 125
  }
}
```

---

## Rate Limiting

**Default Limit:** 60 requests per minute per user

**Rate Limit Headers:**
```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 45
X-RateLimit-Reset: 1640000000
```

**On Exceed:**
```json
{
  "message": "Too Many Attempts.",
  "retry_after": 60
}
```

---

## Webhooks

### Supported Events
- `appointment.created`
- `appointment.updated`
- `appointment.cancelled`
- `appointment.completed`
- `payment.processed`
- `payment.refunded`
- `customer.created`
- `customer.updated`

### Webhook Payload
```json
{
  "event": "appointment.created",
  "data": {
    "id": "uuid",
    "customer_id": "uuid",
    "scheduled_at": "2024-01-20 14:00:00",
    ...
  },
  "timestamp": "2024-01-15T10:30:00Z"
}
```

### Setup
```http
POST /webhooks
{
  "url": "https://yourdomain.com/webhook",
  "events": ["appointment.created", "appointment.cancelled"],
  "secret": "your-secret-key"
}
```

---

## Versioning

**Current Version:** v1
**Future Versions:** v2 (when breaking changes needed)

**Access older versions:**
```http
GET /api/v1/appointments  # Version 1
GET /api/v2/appointments  # Version 2 (future)
```

---

## Multi-Language Support

**Set Language:**
```http
Accept-Language: tr  # Turkish
Accept-Language: en  # English
```

**All text fields return translations:**
```json
{
  "name": {
    "tr": "Saç Kesimi",
    "en": "Haircut"
  }
}
```

---

## Multi-Currency Support

**Set Currency:**
```http
X-Currency: TRY  # Turkish Lira
X-Currency: USD  # US Dollar
```

**All prices return in specified currency:**
```json
{
  "price": {
    "amount": 150.00,
    "currency": "TRY"
  }
}
```

---

## Error Handling

### Validation Errors (422)
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": [
      "The email field is required.",
      "The email must be a valid email address."
    ],
    "phone": [
      "The phone field is required."
    ]
  }
}
```

### Not Found (404)
```json
{
  "message": "Resource not found."
}
```

### Unauthorized (401)
```json
{
  "message": "Unauthenticated."
}
```

### Forbidden (403)
```json
{
  "message": "This action is unauthorized."
}
```

---

## Best Practices

1. **Always set Accept header** to `application/json`
2. **Use HTTPS** for all requests
3. **Handle rate limits** with exponential backoff
4. **Cache responses** when appropriate
5. **Validate input** before sending
6. **Handle errors gracefully**
7. **Use field selection** to reduce payload
8. **Paginate large datasets**
9. **Set proper timeouts** (30 seconds recommended)
10. **Log API errors** for debugging

---

## SDK & Tools

### Postman Collection
Available at: `/api/documentation/postman.json`

### OpenAPI/Swagger
Available at: `/api/documentation/openapi.json`

### PHP SDK (Future)
```bash
composer require salonpro/php-sdk
```

### JavaScript SDK (Future)
```bash
npm install @salonpro/js-sdk
```

---

## Testing

### Test Environment
**Base URL:** `https://api-test.salonpro.com/api/v1`

### Test Credentials
```
Email: test@example.com
Password: TestPassword123!
```

---

## Support

### API Documentation
- Full docs: https://docs.salonpro.com/api
- Interactive API explorer available

### Contact
- Technical support: api-support@salonpro.com
- Report issues: GitHub Issues

---

**API Version:** 1.0
**Last Updated:** 2025-01-15
