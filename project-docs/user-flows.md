# User Flows - SalonPro

## Overview

This document describes key user flows and interactions within the SalonPro system.

---

## 1. Customer Appointment Booking (Online)

**Actor:** Customer

**Flow:**
1. Visit booking page
2. Select service(s)
3. Choose preferred date/time
4. System shows available slots
5. Select employee (optional) or auto-assign
6. Enter customer details (or login)
7. Review booking details
8. Confirm appointment
9. Receive confirmation email/SMS

**Alternative Flows:**
- No available slots → Join waiting list
- Service requires deposit → Process payment
- Existing customer → Auto-fill details

---

## 2. Walk-in Appointment Creation

**Actor:** Receptionist

**Flow:**
1. Click "New Appointment"
2. Search for customer or create new
3. Select service(s)
4. Select employee
5. Select date/time
6. Add notes (optional)
7. Save appointment
8. Print confirmation (optional)

**Alternative Flows:**
- Employee busy → Show conflict warning
- Service unavailable → Suggest alternative
- Customer blacklisted → Show warning

---

## 3. Appointment Check-in

**Actor:** Receptionist/Staff

**Flow:**
1. View today's appointments
2. Identify arriving customer
3. Click "Check-in"
4. Appointment status → "In Progress"
5. Notify assigned employee
6. Customer directed to service area

**Alternative Flows:**
- Customer late → Show warning
- Customer no-show → Mark as no-show
- Wrong appointment → Reschedule

---

## 4. Service Completion & Payment

**Actor:** Receptionist

**Flow:**
1. Employee marks service complete
2. System calculates total
3. Receptionist opens payment screen
4. Select payment method
5. Process payment
6. Generate invoice
7. Ask for feedback
8. Print receipt

**Alternative Flows:**
- Multiple payment methods → Split payment
- Partial payment → Record balance
- Apply discount → Verify authorization
- Use loyalty points → Deduct points

---

## 5. Employee Schedule Management

**Actor:** Branch Manager

**Flow:**
1. Navigate to schedule
2. Select employee
3. Set working hours
4. Add breaks
5. Mark holidays/time-off
6. Set capacity limits
7. Save schedule
8. Notify employee

**Alternative Flows:**
- Recurring schedule → Use template
- Multiple employees → Bulk update
- Emergency change → Send alerts

---

## 6. Inventory Stock Management

**Actor:** Inventory Manager

**Flow:**
1. Navigate to inventory
2. Select product
3. View current stock
4. Record stock movement (in/out)
5. Enter quantity
6. Add notes
7. Save transaction
8. Check low stock alerts

**Alternative Flows:**
- Stock transfer → Select destination branch
- Stock count → Compare with system
- Expired items → Mark as waste
- Reorder needed → Create purchase order

---

## 7. Sales Report Generation

**Actor:** Manager/Accountant

**Flow:**
1. Navigate to reports
2. Select report type (e.g., Sales)
3. Choose date range
4. Apply filters (service, employee, etc.)
5. Select currency (TRY/USD)
6. Generate report
7. View results
8. Export (PDF/Excel) if needed

**Alternative Flows:**
- Schedule report → Set frequency
- Compare periods → Select comparison
- Drill down → View details

---

## 8. Customer Loyalty Points

**Actor:** Customer (Portal) / Receptionist

**Flow:**
1. Customer makes purchase
2. System calculates points earned
3. Points added to customer account
4. Customer views point balance
5. Customer chooses to redeem
6. Select reward/discount
7. Apply to transaction
8. Deduct points

**Alternative Flows:**
- Tier upgrade → Notify customer
- Bonus points event → Apply multiplier
- Points expiring → Send reminder

---

## 9. Marketing Campaign Creation

**Actor:** Marketing Manager

**Flow:**
1. Navigate to campaigns
2. Click "New Campaign"
3. Select campaign type
4. Define target audience (segmentation)
5. Set discount/offer rules
6. Create message (email/SMS)
7. Choose language (TR/EN)
8. Schedule or send immediately
9. Monitor performance

**Alternative Flows:**
- A/B testing → Create variants
- Coupon codes → Generate codes
- Recurring campaign → Set frequency

---

## 10. User Login with 2FA

**Actor:** User

**Flow:**
1. Enter email and password
2. Click login
3. System validates credentials
4. If 2FA enabled → Prompt for code
5. User enters TOTP code
6. System verifies code
7. Login successful
8. Redirect to dashboard

**Alternative Flows:**
- Wrong password → Show error, track attempt
- Account locked → Show lockout message
- First login → Setup 2FA
- Forgot password → Password reset flow

---

## 11. Product Sale (No Appointment)

**Actor:** Receptionist

**Flow:**
1. Click "New Sale"
2. Search for customer (optional)
3. Scan barcode or search product
4. Add to cart
5. Adjust quantity if needed
6. Apply discount (if authorized)
7. Calculate total
8. Select payment method
9. Process payment
10. Print receipt
11. Update inventory automatically

**Alternative Flows:**
- Out of stock → Notify and suggest alternative
- Bulk discount → Apply automatically
- Gift wrapping → Add extra charge

---

## 12. Employee Commission Calculation

**Actor:** Accountant/Manager

**Flow:**
1. Navigate to payroll
2. Select pay period
3. System calculates:
   - Completed appointments
   - Services performed
   - Products sold
   - Commission rates
4. Review calculations
5. Make adjustments (if needed)
6. Approve commission
7. Generate payroll report
8. Export for payment processing

**Alternative Flows:**
- Dispute → Review with employee
- Bonus → Add manually
- Deduction → Record and explain

---

## 13. Backup & Export Data

**Actor:** System Admin

**Flow:**
1. Navigate to settings
2. Select backup option
3. Choose data scope
4. Choose format (SQL/ZIP)
5. Initiate backup
6. System creates backup
7. Store in off-site location
8. Verify backup integrity
9. Notify admin of completion

**Alternative Flows:**
- Scheduled backup → Automatic daily
- Failed backup → Alert and retry
- Restore from backup → Select backup file

---

## 14. Multi-Branch Transfer

**Actor:** Branch Manager

**Flow:**
1. Source branch: Navigate to inventory
2. Select products to transfer
3. Choose destination branch
4. Enter quantities
5. Add transfer notes
6. Initiate transfer
7. System creates transfer record
8. Destination branch receives notification
9. Destination: Confirm receipt
10. Inventory updated in both branches

**Alternative Flows:**
- Partial receipt → Record difference
- Damaged goods → Note and adjust
- Reject transfer → Return to source

---

## 15. Customer Self-Service Portal

**Actor:** Customer

**Flow:**
1. Visit customer portal
2. Register/Login
3. View dashboard
4. Book appointment
5. View appointment history
6. Check loyalty points
7. Update profile
8. View payment history
9. Download invoices
10. Provide feedback

**Alternative Flows:**
- Cancel appointment → Within allowed time
- Forgot password → Reset via email
- Update preferences → Communication channels

---

## Error Handling

### Common Errors
1. **Network Error** → Retry with exponential backoff
2. **Validation Error** → Show inline error messages
3. **Permission Denied** → Redirect with explanation
4. **Not Found** → Show 404 page with navigation
5. **Server Error** → Show generic error, log details

### User Feedback
- Success: Green toast notification
- Error: Red toast with error message
- Warning: Yellow toast with warning
- Info: Blue toast with information

---

## Navigation Patterns

### Main Navigation
- Dashboard
- Calendar/Appointments
- Customers
- Employees (if authorized)
- Services
- Products/Inventory (if authorized)
- Payments/Finance (if authorized)
- Reports (if authorized)
- Marketing (if authorized)
- Settings (if authorized)

### Contextual Actions
- Quick actions in header
- Bulk actions in tables
- Right-click context menus
- Keyboard shortcuts

---

**Document Version:** 1.0
**Last Updated:** 2025-01-15
