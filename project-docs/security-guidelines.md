# Security Guidelines - SalonPro

## Security Overview

This document outlines security measures, best practices, and compliance requirements for SalonPro.

---

## Authentication & Authorization

### Password Security
- **Minimum Length:** 12 characters
- **Complexity:** Mix of uppercase, lowercase, numbers, symbols
- **Password History:** Last 5 passwords cannot be reused
- **Expiration:** Optional (recommended 90 days for admins)
- **Hashing:** Bcrypt with appropriate cost factor

### Two-Factor Authentication (2FA)
- **Method:** TOTP (Time-based One-Time Password)
- **Required For:** Super Admin, Organization Admin, Branch Manager
- **Optional For:** Other roles
- **Backup Codes:** Generated during setup

### Session Management
- **Timeout:** 30 minutes of inactivity
- **Concurrent Sessions:** Configurable (default: 3 per user)
- **Session Storage:** Redis (encrypted)
- **Session Hijacking Prevention:** IP validation, user agent check

### Account Lockout
- **Failed Attempts:** 5 consecutive failures
- **Lockout Duration:** 30 minutes
- **Unlock Methods:** Time-based auto-unlock, admin manual unlock
- **Notification:** Email alert on lockout

---

## API Security

### Authentication
- **Method:** Laravel Sanctum tokens
- **Token Expiration:** 30 days (configurable)
- **Token Refresh:** Required after expiration
- **Token Storage:** Securely in database

### Rate Limiting
- **Default:** 60 requests per minute per user
- **Strict Endpoints:** 10 requests per minute (auth, payments)
- **Response:** HTTP 429 (Too Many Requests)

### CORS Policy
- **Allowed Origins:** Whitelisted domains only
- **Allowed Methods:** GET, POST, PUT, DELETE, PATCH
- **Credentials:** Allowed for authenticated requests

---

## Data Protection

### Encryption

**At Rest:**
- Database: Full database encryption (if supported by host)
- Sensitive Fields: Encrypted using Laravel encryption
  - National IDs
  - Bank account numbers
  - Social security numbers
  - Salary information
  - Credit card numbers (if stored)

**In Transit:**
- HTTPS/TLS 1.2+ for all communications
- Certificate validation
- Strong cipher suites

### Sensitive Data Handling
```php
// Encryption example
$encrypted = encrypt($sensitiveData);
$decrypted = decrypt($encrypted);
```

### Data Masking
- Credit card numbers: Show only last 4 digits
- Phone numbers: Mask middle digits for non-authorized users
- Email addresses: Partial masking in logs

---

## Input Validation & Sanitization

### Validation Rules
- All user input validated
- Type checking
- Length limits
- Format validation (email, phone, etc.)
- Whitelist approach

### Protection Against:
- **SQL Injection:** Parameterized queries (Eloquent ORM)
- **XSS:** Input sanitization, output escaping
- **CSRF:** Laravel CSRF tokens on all forms
- **Command Injection:** Avoid shell execution, validate input
- **File Upload Attacks:** File type validation, size limits, virus scanning

---

## Access Control

### Authorization
- Role-Based Access Control (RBAC)
- Attribute-Based Access Control (ABAC)
- Policy-based authorization
- Resource ownership checks

### Branch-Level Isolation
- Automatic branch scoping
- Cross-branch access prevention
- Branch switching with validation

### API Authorization
- Token-based authentication
- Permission checking on every request
- Resource ownership validation

---

## Audit Logging

### What to Log
- Authentication attempts (success and failure)
- Authorization failures
- Data modifications (create, update, delete)
- Sensitive data access
- Configuration changes
- Security-related events

### Log Contents
- User ID
- Action performed
- Timestamp
- IP address
- User agent
- Resource affected
- Old and new values

### Log Storage
- Secure storage
- Tamper-proof
- Retention: 1 year
- Regular review

---

## Database Security

### Connection Security
- Encrypted connections
- Strong password
- Separate user per application
- Minimal privileges

### SQL Injection Prevention
- Use Eloquent ORM
- Parameterized queries
- Input validation
- Escape special characters

### Backup Security
- Encrypted backups
- Secure storage location
- Access control
- Regular testing

---

## File Upload Security

### Validation
- File type whitelist
- File size limits
- MIME type verification
- Extension validation

### Storage
- Outside web root
- Unique file names
- No direct access
- Serve through application

### Scanning
- Virus scanning (recommended)
- Malware detection
- Content validation

---

## Security Headers

### Required Headers
```
X-Content-Type-Options: nosniff
X-Frame-Options: DENY
X-XSS-Protection: 1; mode=block
Strict-Transport-Security: max-age=31536000; includeSubDomains
Content-Security-Policy: (configured)
Referrer-Policy: strict-origin-when-cross-origin
```

---

## Compliance

### KVKK (Turkish Data Protection Law)
- Explicit consent for data processing
- Right to access personal data
- Right to deletion
- Right to data portability
- Data breach notification (72 hours)
- Privacy policy
- Data processing agreement

### GDPR (for EU customers)
- Same as KVKK
- Additional requirements for EU data subjects
- Data protection officer (if required)

### PCI DSS (if processing cards)
- Do NOT store full card numbers
- Do NOT store CVV
- Use certified payment gateways
- Quarterly security scans
- Annual audits

---

## Security Best Practices

### Development
- Code reviews
- Static analysis (PHPStan Level 8)
- Dependency updates
- Security scanning
- Secrets management

### Deployment
- Environment variables for secrets
- No credentials in code
- Secure CI/CD pipeline
- Production hardening
- Regular updates

### Operations
- Regular backups
- Disaster recovery plan
- Incident response plan
- Security monitoring
- Penetration testing (annual)

---

## Incident Response

### Steps
1. **Detection:** Identify security incident
2. **Containment:** Limit damage
3. **Investigation:** Determine cause and scope
4. **Eradication:** Remove threat
5. **Recovery:** Restore normal operations
6. **Lessons Learned:** Document and improve

### Communication
- Internal notification
- Customer notification (if data breach)
- Regulatory notification (if required)
- Public disclosure (if required)

---

## Security Checklist

### Pre-Launch
- [ ] All dependencies updated
- [ ] Security headers configured
- [ ] HTTPS enabled
- [ ] Strong passwords enforced
- [ ] 2FA enabled for admins
- [ ] CSRF protection enabled
- [ ] SQL injection testing passed
- [ ] XSS protection verified
- [ ] File upload security tested
- [ ] Access control tested
- [ ] Audit logging enabled
- [ ] Backup system operational
- [ ] Security scanning completed
- [ ] Penetration testing completed

### Ongoing
- [ ] Monthly security updates
- [ ] Quarterly access reviews
- [ ] Annual penetration testing
- [ ] Security awareness training
- [ ] Incident response drills

---

## Security Contacts

### Reporting Security Issues
Email: security@salonpro.com (setup required)

### Security Team
- Security Officer: TBD
- Technical Lead: TBD
- Compliance Officer: TBD

---

## Updates & Patches

### Security Updates
- Critical: Within 24 hours
- High: Within 1 week
- Medium: Within 1 month
- Low: Next release

### Testing
- Test in staging first
- Verify no breaking changes
- Monitor after deployment

---

**Document Version:** 1.0
**Last Updated:** 2025-01-15
**Classification:** Internal
