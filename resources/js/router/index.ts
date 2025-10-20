import { createRouter, createWebHistory } from 'vue-router';
import type { RouteRecordRaw } from 'vue-router';

console.log('========== ROUTER INDEX.TS IS LOADING ==========');

const routes: RouteRecordRaw[] = [
  {
    path: '/login',
    name: 'Login',
    component: () => import('../views/Auth/Login.vue')
  },
  {
    path: '/',
    name: 'Dashboard',
    component: () => import('../views/Dashboard.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/branches',
    name: 'Branches',
    component: () => import('../views/Branches/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/customers',
    name: 'Customers',
    component: () => import('../views/Customers/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/employees',
    name: 'Employees',
    component: () => import('../views/Employees/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/products',
    name: 'Products',
    component: () => import('../views/Products/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/inventory',
    name: 'Inventory',
    component: () => import('../views/Inventory/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/expenses',
    name: 'Expenses',
    component: () => import('../views/Expenses/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/payments',
    name: 'Payments',
    component: () => import('../views/Payments/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/sales',
    name: 'Sales',
    component: () => import('../views/Sales/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/services',
    name: 'Services',
    component: () => import('../views/Services/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/appointments',
    name: 'Appointments',
    component: () => import('../views/Appointments/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/settings',
    name: 'Settings',
    component: () => import('../views/Settings/Index.vue'),
    meta: { requiresAuth: true }
  },
  // Financial
  {
    path: '/invoices',
    name: 'Invoices',
    component: () => import('../views/Invoices/Index.vue'),
    meta: { requiresAuth: true }
  },
  // Inventory & Supply Chain
  {
    path: '/stock-audits',
    name: 'StockAudits',
    component: () => import('../views/StockAudits/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/stock-transfers',
    name: 'StockTransfers',
    component: () => import('../views/StockTransfers/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/suppliers',
    name: 'Suppliers',
    component: () => import('../views/Suppliers/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/purchase-orders',
    name: 'PurchaseOrders',
    component: () => import('../views/PurchaseOrders/Index.vue'),
    meta: { requiresAuth: true }
  },
  // Marketing
  {
    path: '/marketing-campaigns',
    name: 'MarketingCampaigns',
    component: () => import('../views/MarketingCampaigns/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/coupons',
    name: 'Coupons',
    component: () => import('../views/Coupons/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/loyalty-programs',
    name: 'LoyaltyPrograms',
    component: () => import('../views/LoyaltyPrograms/Index.vue'),
    meta: { requiresAuth: true }
  },
  // Customer Management
  {
    path: '/customer-categories',
    name: 'CustomerCategories',
    component: () => import('../views/CustomerCategories/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/customer-tags',
    name: 'CustomerTags',
    component: () => import('../views/CustomerTags/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/customer-notes',
    name: 'CustomerNotes',
    component: () => import('../views/CustomerNotes/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/customer-segments',
    name: 'CustomerSegments',
    component: () => import('../views/CustomerSegments/Index.vue'),
    meta: { requiresAuth: true }
  },
  // Employee Management
  {
    path: '/employee-schedules',
    name: 'EmployeeSchedules',
    component: () => import('../views/EmployeeSchedules/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/employee-shifts',
    name: 'EmployeeShifts',
    component: () => import('../views/EmployeeShifts/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/employee-skills',
    name: 'EmployeeSkills',
    component: () => import('../views/EmployeeSkills/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/employee-leaves',
    name: 'EmployeeLeaves',
    component: () => import('../views/EmployeeLeaves/Index.vue'),
    meta: { requiresAuth: true }
  },
  // Service Management
  {
    path: '/service-categories',
    name: 'ServiceCategories',
    component: () => import('../views/ServiceCategories/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/service-addons',
    name: 'ServiceAddons',
    component: () => import('../views/ServiceAddons/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/service-packages',
    name: 'ServicePackages',
    component: () => import('../views/ServicePackages/Index.vue'),
    meta: { requiresAuth: true }
  },
  // Product Management
  {
    path: '/product-bundles',
    name: 'ProductBundles',
    component: () => import('../views/ProductBundles/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/product-variants',
    name: 'ProductVariants',
    component: () => import('../views/ProductVariants/Index.vue'),
    meta: { requiresAuth: true }
  },
  // System & Settings
  {
    path: '/notification-templates',
    name: 'NotificationTemplates',
    component: () => import('../views/NotificationTemplates/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/report-templates',
    name: 'ReportTemplates',
    component: () => import('../views/ReportTemplates/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/webhooks',
    name: 'Webhooks',
    component: () => import('../views/Webhooks/Index.vue'),
    meta: { requiresAuth: true }
  },
  // Appointments Extended
  {
    path: '/appointment-cancellations',
    name: 'AppointmentCancellations',
    component: () => import('../views/AppointmentCancellations/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/appointment-reminders',
    name: 'AppointmentReminders',
    component: () => import('../views/AppointmentReminders/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/appointment-waitlists',
    name: 'AppointmentWaitlists',
    component: () => import('../views/AppointmentWaitlists/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/appointment-recurrences',
    name: 'AppointmentRecurrences',
    component: () => import('../views/AppointmentRecurrences/Index.vue'),
    meta: { requiresAuth: true }
  },
  // Financial Extended
  {
    path: '/bank-accounts',
    name: 'BankAccounts',
    component: () => import('../views/BankAccounts/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/budget-plans',
    name: 'BudgetPlans',
    component: () => import('../views/BudgetPlans/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/cash-registers',
    name: 'CashRegisters',
    component: () => import('../views/CashRegisters/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/tax-rates',
    name: 'TaxRates',
    component: () => import('../views/TaxRates/Index.vue'),
    meta: { requiresAuth: true }
  },
  // Employee Extended
  {
    path: '/employee-attendances',
    name: 'EmployeeAttendances',
    component: () => import('../views/EmployeeAttendances/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/employee-certifications',
    name: 'EmployeeCertifications',
    component: () => import('../views/EmployeeCertifications/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/employee-commissions',
    name: 'EmployeeCommissions',
    component: () => import('../views/EmployeeCommissions/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/employee-performances',
    name: 'EmployeePerformances',
    component: () => import('../views/EmployeePerformances/Index.vue'),
    meta: { requiresAuth: true }
  },
  // Customer Extended
  {
    path: '/customer-feedbacks',
    name: 'CustomerFeedbacks',
    component: () => import('../views/CustomerFeedbacks/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/leads',
    name: 'Leads',
    component: () => import('../views/Leads/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/referrals',
    name: 'Referrals',
    component: () => import('../views/Referrals/Index.vue'),
    meta: { requiresAuth: true }
  },
  // Product Extended
  {
    path: '/product-attributes',
    name: 'ProductAttributes',
    component: () => import('../views/ProductAttributes/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/product-discounts',
    name: 'ProductDiscounts',
    component: () => import('../views/ProductDiscounts/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/product-images',
    name: 'ProductImages',
    component: () => import('../views/ProductImages/Index.vue'),
    meta: { requiresAuth: true }
  },
  // Service Extended
  {
    path: '/service-pricing-rules',
    name: 'ServicePricingRules',
    component: () => import('../views/ServicePricingRules/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/service-reviews',
    name: 'ServiceReviews',
    component: () => import('../views/ServiceReviews/Index.vue'),
    meta: { requiresAuth: true }
  },
  // Inventory Extended
  {
    path: '/inventory-movements',
    name: 'InventoryMovements',
    component: () => import('../views/InventoryMovements/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/stock-alerts',
    name: 'StockAlerts',
    component: () => import('../views/StockAlerts/Index.vue'),
    meta: { requiresAuth: true }
  },
  // Product Extended (More)
  {
    path: '/product-barcodes',
    name: 'ProductBarcodes',
    component: () => import('../views/ProductBarcodes/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/product-stock-histories',
    name: 'ProductStockHistories',
    component: () => import('../views/ProductStockHistories/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/product-price-histories',
    name: 'ProductPriceHistories',
    component: () => import('../views/ProductPriceHistories/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/product-supplier-prices',
    name: 'ProductSupplierPrices',
    component: () => import('../views/ProductSupplierPrices/Index.vue'),
    meta: { requiresAuth: true }
  },
  // Financial Extended (More)
  {
    path: '/invoice-items',
    name: 'InvoiceItems',
    component: () => import('../views/InvoiceItems/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/bank-transactions',
    name: 'BankTransactions',
    component: () => import('../views/BankTransactions/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/budget-items',
    name: 'BudgetItems',
    component: () => import('../views/BudgetItems/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/cash-register-sessions',
    name: 'CashRegisterSessions',
    component: () => import('../views/CashRegisterSessions/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/currencies',
    name: 'Currencies',
    component: () => import('../views/Currencies/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/exchange-rates',
    name: 'ExchangeRates',
    component: () => import('../views/ExchangeRates/Index.vue'),
    meta: { requiresAuth: true }
  },
  // Marketing Extended (More)
  {
    path: '/campaign-statistics',
    name: 'CampaignStatistics',
    component: () => import('../views/CampaignStatistics/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/coupon-usages',
    name: 'CouponUsages',
    component: () => import('../views/CouponUsages/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/loyalty-points',
    name: 'LoyaltyPoints',
    component: () => import('../views/LoyaltyPoints/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/referral-programs',
    name: 'ReferralPrograms',
    component: () => import('../views/ReferralPrograms/Index.vue'),
    meta: { requiresAuth: true }
  },
  // Service Extended (More)
  {
    path: '/service-templates',
    name: 'ServiceTemplates',
    component: () => import('../views/ServiceTemplates/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/service-requirements',
    name: 'ServiceRequirements',
    component: () => import('../views/ServiceRequirements/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/service-price-histories',
    name: 'ServicePriceHistories',
    component: () => import('../views/ServicePriceHistories/Index.vue'),
    meta: { requiresAuth: true }
  },
  // Appointments Extended (More)
  {
    path: '/appointment-conflicts',
    name: 'AppointmentConflicts',
    component: () => import('../views/AppointmentConflicts/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/appointment-groups',
    name: 'AppointmentGroups',
    component: () => import('../views/AppointmentGroups/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/appointment-histories',
    name: 'AppointmentHistories',
    component: () => import('../views/AppointmentHistories/Index.vue'),
    meta: { requiresAuth: true }
  },
  // System & Settings Extended (More)
  {
    path: '/notification-queues',
    name: 'NotificationQueues',
    component: () => import('../views/NotificationQueues/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/notification-logs',
    name: 'NotificationLogs',
    component: () => import('../views/NotificationLogs/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/activity-logs',
    name: 'ActivityLogs',
    component: () => import('../views/ActivityLogs/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/audit-logs',
    name: 'AuditLogs',
    component: () => import('../views/AuditLogs/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/system-backups',
    name: 'SystemBackups',
    component: () => import('../views/SystemBackups/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/integrations',
    name: 'Integrations',
    component: () => import('../views/Integrations/Index.vue'),
    meta: { requiresAuth: true }
  },
  // Analytics & Reporting
  {
    path: '/analytics-events',
    name: 'AnalyticsEvents',
    component: () => import('../views/AnalyticsEvents/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/analytics-sessions',
    name: 'AnalyticsSessions',
    component: () => import('../views/AnalyticsSessions/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/kpi-definitions',
    name: 'KpiDefinitions',
    component: () => import('../views/KpiDefinitions/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/kpi-values',
    name: 'KpiValues',
    component: () => import('../views/KpiValues/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/performance-metrics',
    name: 'PerformanceMetrics',
    component: () => import('../views/PerformanceMetrics/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/report-schedules',
    name: 'ReportSchedules',
    component: () => import('../views/ReportSchedules/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/report-executions',
    name: 'ReportExecutions',
    component: () => import('../views/ReportExecutions/Index.vue'),
    meta: { requiresAuth: true }
  },
  // Notification Providers
  {
    path: '/sms-providers',
    name: 'SmsProviders',
    component: () => import('../views/SmsProviders/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/email-providers',
    name: 'EmailProviders',
    component: () => import('../views/EmailProviders/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/notification-preferences',
    name: 'NotificationPreferences',
    component: () => import('../views/NotificationPreferences/Index.vue'),
    meta: { requiresAuth: true }
  },
  // Product Sub-modules
  {
    path: '/product-category-hierarchies',
    name: 'ProductCategoryHierarchies',
    component: () => import('../views/ProductCategoryHierarchies/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/product-attribute-values',
    name: 'ProductAttributeValues',
    component: () => import('../views/ProductAttributeValues/Index.vue'),
    meta: { requiresAuth: true }
  },
  // Appointments Sub-modules
  {
    path: '/appointment-cancellation-reasons',
    name: 'AppointmentCancellationReasons',
    component: () => import('../views/AppointmentCancellationReasons/Index.vue'),
    meta: { requiresAuth: true }
  },
  // Customer Sub-modules
  {
    path: '/customer-rfm-analyses',
    name: 'CustomerRfmAnalyses',
    component: () => import('../views/CustomerRfmAnalyses/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/customer-segment-members',
    name: 'CustomerSegmentMembers',
    component: () => import('../views/CustomerSegmentMembers/Index.vue'),
    meta: { requiresAuth: true }
  },
  // Inventory Sub-modules
  {
    path: '/purchase-order-items',
    name: 'PurchaseOrderItems',
    component: () => import('../views/PurchaseOrderItems/Index.vue'),
    meta: { requiresAuth: true }
  },
  // Financial Sub-modules
  {
    path: '/chart-of-accounts',
    name: 'ChartOfAccounts',
    component: () => import('../views/ChartOfAccounts/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/journal-entries',
    name: 'JournalEntries',
    component: () => import('../views/JournalEntries/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/cash-register-transactions',
    name: 'CashRegisterTransactions',
    component: () => import('../views/CashRegisterTransactions/Index.vue'),
    meta: { requiresAuth: true }
  },
  // Marketing Sub-modules
  {
    path: '/loyalty-point-transactions',
    name: 'LoyaltyPointTransactions',
    component: () => import('../views/LoyaltyPointTransactions/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/lead-activities',
    name: 'LeadActivities',
    component: () => import('../views/LeadActivities/Index.vue'),
    meta: { requiresAuth: true }
  },
  // System Utilities
  {
    path: '/custom-fields',
    name: 'CustomFields',
    component: () => import('../views/CustomFields/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/translations',
    name: 'Translations',
    component: () => import('../views/Translations/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/feature-flags',
    name: 'FeatureFlags',
    component: () => import('../views/FeatureFlags/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/user-preferences',
    name: 'UserPreferences',
    component: () => import('../views/UserPreferences/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/custom-field-values',
    name: 'CustomFieldValues',
    component: () => import('../views/CustomFieldValues/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/document-templates',
    name: 'DocumentTemplates',
    component: () => import('../views/DocumentTemplates/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/mobile-devices',
    name: 'MobileDevices',
    component: () => import('../views/MobileDevices/Index.vue'),
    meta: { requiresAuth: true }
  },
  // OAuth & Authentication
  {
    path: '/oauth-providers',
    name: 'OauthProviders',
    component: () => import('../views/OauthProviders/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/oauth-tokens',
    name: 'OauthTokens',
    component: () => import('../views/OauthTokens/Index.vue'),
    meta: { requiresAuth: true }
  },
  // Surveys & Feedback
  {
    path: '/surveys',
    name: 'Surveys',
    component: () => import('../views/Surveys/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/survey-responses',
    name: 'SurveyResponses',
    component: () => import('../views/SurveyResponses/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/appointment-group-participants',
    name: 'AppointmentGroupParticipants',
    component: () => import('../views/AppointmentGroupParticipants/Index.vue'),
    meta: { requiresAuth: true }
  }
];

console.log('Routes array created:', routes);
console.log('Number of routes:', routes.length);

const router = createRouter({
  history: createWebHistory(),
  routes
});

console.log('Router instance created');
console.log('Router has these routes:', router.getRoutes());

router.beforeEach((to, from, next) => {
  console.log(`[Router Guard] Navigating from ${from.path} to ${to.path}`);
  const token = localStorage.getItem('auth_token');

  if (to.meta.requiresAuth && !token) {
    console.log('[Router Guard] No token found, redirecting to /login');
    next('/login');
  } else {
    console.log('[Router Guard] Access granted');
    next();
  }
});

export default router;
