# Business Rules - SalonPro

## Overview

This document defines the core business rules and policies that govern SalonPro operations.

---

## Appointment Rules

### Booking Rules
1. **Minimum Advance Time:** Configurable per service (default: 1 hour)
2. **Maximum Future Date:** Configurable (default: 90 days)
3. **Slot Duration:** Configurable (default: 15-minute intervals)
4. **Buffer Time:** Configurable time between appointments (default: 0-15 minutes)
5. **Overbooking:** Not allowed by default (configurable)
6. **Concurrent Services:** Allowed if different employees

### Cancellation Rules
1. **Customer Cancellation Window:** Configurable (default: 24 hours before)
2. **Late Cancellation Fee:** Configurable percentage
3. **No-Show Handling:**
   - First offense: Warning
   - Second offense: Deposit required for future bookings
   - Third offense: Temporary booking restriction
4. **Staff Cancellation:** Must notify customer immediately
5. **Emergency Cancellation:** Allowed anytime, no penalty

### Rescheduling Rules
1. **Reschedule Window:** Same as cancellation window
2. **Reschedule Limit:** 2 times per appointment
3. **Automatic Reschedule Offers:** On cancellation
4. **Priority:** Existing customers over new bookings

### Check-in Rules
1. **Early Check-in:** Allowed up to 15 minutes early
2. **Late Arrival Grace Period:** 15 minutes (configurable)
3. **Late Arrival > Grace Period:** Appointment may be cancelled
4. **Auto Check-out:** After appointment end time + 30 minutes

---

## Pricing Rules

### Base Pricing
1. **Price Currency:** Support TRY and USD
2. **Price Updates:** Take effect immediately for new bookings
3. **Existing Appointments:** Keep original price
4. **Tax Inclusion:** Configurable (inclusive or exclusive)

### Discount Rules
1. **Maximum Discount:** Configurable per role (e.g., 20% for receptionists)
2. **Discount Approval:** Required above threshold
3. **Discount Stacking:** Not allowed (choose best discount)
4. **Discount Expiry:** Must have end date
5. **Minimum Purchase:** Can be set for discount eligibility

### Dynamic Pricing
1. **Peak Hours:** Configurable multiplier (e.g., 1.2x)
2. **Off-Peak Hours:** Configurable multiplier (e.g., 0.8x)
3. **Weekend Pricing:** Optional separate pricing
4. **Holiday Pricing:** Optional special pricing
5. **Last-Minute Booking:** Optional discount for same-day

### Package Pricing
1. **Package Discount:** Must be less than individual sum
2. **Package Validity:** 3-12 months
3. **Session Limit:** Must be used within validity
4. **Partial Refund:** Prorated based on used sessions
5. **Package Transfer:** Configurable (allowed/not allowed)

---

## Payment Rules

### Payment Processing
1. **Supported Methods:** Cash, Card, Transfer, Check, Voucher, Points
2. **Split Payment:** Allowed (e.g., part cash, part card)
3. **Partial Payment:** Allowed with balance tracking
4. **Prepayment:** Optional requirement for certain services
5. **Deposit:** 10-50% of total (configurable)

### Refund Rules
1. **Refund Window:** Within 30 days
2. **Refund Method:** Same as original payment method
3. **Partial Refund:** Allowed
4. **Refund Approval:** Manager approval required for > 500 TRY
5. **Service Refund:** Only if not started
6. **Product Refund:** Unopened products only

### Debt Management
1. **Credit Limit:** Configurable per customer
2. **Payment Terms:** 7-30 days (configurable)
3. **Late Payment Fee:** Configurable percentage
4. **Debt Collection:** Auto-reminders at 7, 14, 30 days
5. **Service Restriction:** After 60 days overdue

---

## Inventory Rules

### Stock Management
1. **Minimum Stock Alert:** When stock < minimum level
2. **Auto-Reorder:** When stock ≤ reorder point
3. **Stock Reservation:** Reserve stock on appointment booking
4. **Stock Release:** Release if appointment cancelled
5. **Negative Stock:** Not allowed

### Product Usage
1. **Service Usage:** Auto-deduct used products
2. **Retail Sales:** Manual or barcode scan
3. **Sample Products:** Special category, not for sale
4. **Damaged Products:** Separate write-off process
5. **Expired Products:** Auto-alert 30 days before expiry

### Stock Transfer
1. **Transfer Request:** Originating branch initiates
2. **Transfer Approval:** Receiving branch confirms
3. **In-Transit Status:** Between approval and receipt
4. **Partial Receipt:** Allowed with adjustment
5. **Transfer Reversal:** Within 24 hours

---

## Employee Rules

### Work Schedule
1. **Minimum Hours:** 20 hours/week (part-time)
2. **Maximum Hours:** 48 hours/week (legal limit)
3. **Break Time:** 1 hour for 8+ hour shifts
4. **Overtime:** Requires approval
5. **Schedule Change:** 48 hours notice required

### Commission Rules
1. **Service Commission:** Configurable per service
2. **Product Commission:** Configurable per product
3. **Package Commission:** Split across sessions
4. **Team Service:** Commission split evenly
5. **Commission Payment:** Paid monthly
6. **Minimum Threshold:** No payment if < 100 TRY

### Leave Rules
1. **Annual Leave:** 14-26 days (based on tenure)
2. **Sick Leave:** With medical certificate
3. **Personal Leave:** Up to 5 days/year
4. **Leave Request:** Minimum 7 days advance
5. **Leave Approval:** Manager approval required

### Performance Rules
1. **Review Period:** Quarterly
2. **Minimum Rating:** 3.0/5.0 to maintain employment
3. **Customer Complaint:** Investigation required
4. **No-Show Rate:** Warning if > 5%
5. **Lateness:** 3 strikes policy

---

## Customer Rules

### Registration
1. **Phone Required:** Mandatory for communication
2. **Email Optional:** But recommended
3. **Duplicate Prevention:** Check phone/email before creating
4. **Verification:** Optional email/SMS verification
5. **Data Retention:** Keep for 7 years after last activity

### Loyalty Program
1. **Points Earning:** 1 point per 10 TRY spent
2. **Points Redemption:** 1 point = 1 TRY discount
3. **Points Expiry:** After 1 year of inactivity
4. **Tier Promotion:** Based on annual spend
5. **Tier Benefits:** Discounts, priority booking, birthday gifts

### Blacklist Rules
1. **Blacklist Reasons:** No-shows, inappropriate behavior, non-payment
2. **Blacklist Duration:** Temporary (30-90 days) or permanent
3. **Blacklist Appeal:** Can be reviewed by manager
4. **Blacklist Effect:** Cannot book appointments
5. **Blacklist Notification:** Customer notified via email

---

## Service Rules

### Service Delivery
1. **Service Start:** Only after check-in
2. **Service Duration:** Buffer allowed (±15 minutes)
3. **Service Quality:** Must meet quality standards
4. **Service Documentation:** Before/after photos (optional)
5. **Customer Satisfaction:** Survey after completion

### Service Modification
1. **Add Service:** Allowed during appointment
2. **Remove Service:** Allowed before start
3. **Change Employee:** Allowed if customer requests
4. **Upgrade Service:** Price difference applies
5. **Downgrade Service:** Refund difference

---

## Financial Rules

### Accounting
1. **Fiscal Year:** January 1 - December 31
2. **Tax Reporting:** Monthly VAT filing
3. **Receipt Required:** For all transactions > 50 TRY
4. **Cash Register:** Daily reconciliation required
5. **Audit Trail:** All transactions logged

### Budget
1. **Budget Period:** Annual with quarterly reviews
2. **Budget Approval:** Organization admin approval
3. **Budget Variance:** Report if > 10% over/under
4. **Budget Adjustment:** Allowed once per quarter

### Expense Approval
1. **< 500 TRY:** Auto-approved
2. **500-2000 TRY:** Branch manager approval
3. **> 2000 TRY:** Organization admin approval
4. **Emergency Expense:** Post-approval within 48 hours

---

## Security Rules

### Access Control
1. **Password Expiry:** 90 days for admins (optional for others)
2. **Password Complexity:** 12+ chars, mixed case, numbers, symbols
3. **2FA Required:** For admin roles
4. **Session Timeout:** 30 minutes inactivity
5. **Account Lockout:** After 5 failed login attempts

### Data Protection
1. **Personal Data:** Encrypted at rest
2. **Sensitive Fields:** Encrypted (salary, bank accounts, IDs)
3. **Data Access:** Role-based, logged
4. **Data Export:** Approval required
5. **Data Deletion:** Soft delete with 90-day retention

### Audit
1. **Activity Logging:** All important actions
2. **Log Retention:** 1 year minimum
3. **Access Review:** Quarterly
4. **Security Audit:** Annual
5. **Incident Response:** Within 24 hours

---

## Reporting Rules

### Report Access
1. **Financial Reports:** Accountant, Manager, Admin only
2. **Employee Reports:** Manager, HR, Admin only
3. **Customer Reports:** Marketing, Manager, Admin
4. **Operational Reports:** All staff (role-based)

### Report Generation
1. **Real-time Data:** Dashboard metrics
2. **Scheduled Reports:** Daily, weekly, monthly
3. **Custom Reports:** Ad-hoc with date range
4. **Report Export:** PDF, Excel, CSV
5. **Report Sharing:** Email, download

---

## Multi-Branch Rules

### Branch Operations
1. **Branch Autonomy:** Configurable (centralized/decentralized)
2. **Cross-Branch Booking:** Allowed with customer consent
3. **Cross-Branch Transfer:** With approval
4. **Central Inventory:** Optional shared stock
5. **Central Reporting:** Organization-wide analytics

### Data Sharing
1. **Customer Data:** Shared across organization branches
2. **Employee Data:** Branch-specific
3. **Financial Data:** Consolidated at organization level
4. **Inventory Data:** Branch-specific or central

---

## Multi-Language Rules

### Language Support
1. **Supported Languages:** Turkish, English
2. **Default Language:** Based on user preference or branch
3. **Language Switching:** Instant, no page reload
4. **Content Translation:** All UI, emails, SMS, reports
5. **User Preference:** Saved per user

---

## Multi-Currency Rules

### Currency Support
1. **Supported Currencies:** TRY, USD
2. **Default Currency:** Based on branch location
3. **Exchange Rate:** Updated daily (auto or manual)
4. **Historical Rates:** Stored for reporting
5. **Transaction Currency:** Locked at time of transaction

### Currency Conversion
1. **Display:** Convert prices to selected currency
2. **Reporting:** Show in multiple currencies
3. **Payment:** Accept in any supported currency
4. **Accounting:** Maintain original currency + converted

---

## Compliance Rules

### KVKK (Turkish Data Protection)
1. **Explicit Consent:** Required for data processing
2. **Right to Access:** Customers can request their data
3. **Right to Deletion:** Within 30 days of request
4. **Data Portability:** Export in machine-readable format
5. **Breach Notification:** Within 72 hours

### GDPR (for EU customers)
1. **Same as KVKK** plus:
2. **Privacy by Design:** Default privacy settings
3. **Data Minimization:** Collect only necessary data
4. **Purpose Limitation:** Use data only for stated purpose

---

## Exception Handling

### Manager Override
- Managers can override certain rules with justification
- All overrides are logged
- Regular review of overrides

### Emergency Procedures
- Emergency cancellations allowed
- Emergency refunds allowed
- Emergency schedule changes allowed
- Post-emergency documentation required

---

## Rule Updates

### Process
1. Proposal with business justification
2. Review by management
3. Technical feasibility assessment
4. Testing in staging
5. Documentation update
6. Training (if needed)
7. Production deployment
8. Communication to users

### Versioning
- Rules versioned with dates
- Historical rules maintained
- Transactions use rules from their date

---

**Document Version:** 1.0
**Last Updated:** 2025-01-15
**Review Date:** Quarterly
