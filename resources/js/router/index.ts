import { createRouter, createWebHistory } from 'vue-router';
import type { RouteRecordRaw } from 'vue-router';

// Lazy load with chunk names for better debugging and caching
const routes: RouteRecordRaw[] = [
  // ─────────────────────────────────────────────────────────────
  // Auth
  // ─────────────────────────────────────────────────────────────
  {
    path: '/login',
    name: 'Login',
    component: () => import(/* webpackChunkName: "auth" */ '../views/Auth/Login.vue')
  },

  // ─────────────────────────────────────────────────────────────
  // Dashboard & Branches
  // ─────────────────────────────────────────────────────────────
  {
    path: '/',
    name: 'Dashboard',
    component: () => import(/* webpackChunkName: "dashboard" */ '../views/Dashboard.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/branches',
    name: 'Branches',
    component: () => import(/* webpackChunkName: "branches" */ '../views/Branches/Index.vue'),
    meta: { requiresAuth: true, breadcrumb: ['Şubeler'] }
  },

  // ─────────────────────────────────────────────────────────────
  // Müşteri Yönetimi
  // ─────────────────────────────────────────────────────────────
  {
    path: '/customers',
    name: 'Customers',
    component: () => import(/* webpackChunkName: "customers" */ '../views/Customers/Index.vue'),
    meta: { requiresAuth: true, breadcrumb: ['Müşteriler'] }
  },
  {
    path: '/customers/:id',
    name: 'CustomerShow',
    component: () => import(/* webpackChunkName: "customers" */ '../views/Customers/Show.vue'),
    meta: { requiresAuth: true, breadcrumb: ['Müşteriler', 'Müşteri Detayı'] }
  },
  {
    path: '/customer-categories',
    name: 'CustomerCategories',
    component: () => import(/* webpackChunkName: "customers" */ '../views/CustomerCategories/Index.vue'),
    meta: { requiresAuth: true, breadcrumb: ['Müşteriler', 'Kategoriler'] }
  },
  {
    path: '/customer-tags',
    name: 'CustomerTags',
    component: () => import(/* webpackChunkName: "customers" */ '../views/CustomerTags/Index.vue'),
    meta: { requiresAuth: true, breadcrumb: ['Müşteriler', 'Etiketler'] }
  },
  {
    path: '/customer-segments',
    name: 'CustomerSegments',
    component: () => import(/* webpackChunkName: "customers" */ '../views/CustomerSegments/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/customer-segment-members',
    name: 'CustomerSegmentMembers',
    component: () => import(/* webpackChunkName: "customers" */ '../views/CustomerSegmentMembers/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/customer-feedbacks',
    name: 'CustomerFeedbacks',
    component: () => import(/* webpackChunkName: "customers" */ '../views/CustomerFeedbacks/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/customer-notes',
    name: 'CustomerNotes',
    component: () => import(/* webpackChunkName: "customers" */ '../views/CustomerNotes/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/customer-rfm-analyses',
    name: 'CustomerRfmAnalyses',
    component: () => import(/* webpackChunkName: "customers" */ '../views/CustomerRfmAnalyses/Index.vue'),
    meta: { requiresAuth: true }
  },

  // ─────────────────────────────────────────────────────────────
  // Çalışan Yönetimi
  // ─────────────────────────────────────────────────────────────
  {
    path: '/employees',
    name: 'Employees',
    component: () => import(/* webpackChunkName: "employees" */ '../views/Employees/Index.vue'),
    meta: { requiresAuth: true, breadcrumb: ['Çalışanlar'] }
  },
  {
    path: '/employees/schedule',
    name: 'EmployeeSchedule',
    component: () => import(/* webpackChunkName: "employees" */ '../views/Employees/Schedule.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/employee-shifts',
    name: 'EmployeeShifts',
    component: () => import(/* webpackChunkName: "employees" */ '../views/EmployeeShifts/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/employee-leaves',
    name: 'EmployeeLeaves',
    component: () => import(/* webpackChunkName: "employees" */ '../views/EmployeeLeaves/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/employee-attendances',
    name: 'EmployeeAttendances',
    component: () => import(/* webpackChunkName: "employees" */ '../views/EmployeeAttendances/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/employee-certifications',
    name: 'EmployeeCertifications',
    component: () => import(/* webpackChunkName: "employees" */ '../views/EmployeeCertifications/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/employee-skills',
    name: 'EmployeeSkills',
    component: () => import(/* webpackChunkName: "employees" */ '../views/EmployeeSkills/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/employee-commissions',
    name: 'EmployeeCommissions',
    component: () => import(/* webpackChunkName: "employees" */ '../views/EmployeeCommissions/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/employee-performances',
    name: 'EmployeePerformances',
    component: () => import(/* webpackChunkName: "employees" */ '../views/EmployeePerformances/Index.vue'),
    meta: { requiresAuth: true }
  },

  // ─────────────────────────────────────────────────────────────
  // Hizmet Yönetimi
  // ─────────────────────────────────────────────────────────────
  {
    path: '/services',
    name: 'Services',
    component: () => import(/* webpackChunkName: "services" */ '../views/Services/Index.vue'),
    meta: { requiresAuth: true, breadcrumb: ['Hizmetler'] }
  },
  {
    path: '/service-categories',
    name: 'ServiceCategories',
    component: () => import(/* webpackChunkName: "services" */ '../views/ServiceCategories/Index.vue'),
    meta: { requiresAuth: true, breadcrumb: ['Hizmetler', 'Kategoriler'] }
  },
  {
    path: '/service-packages',
    name: 'ServicePackages',
    component: () => import(/* webpackChunkName: "services" */ '../views/ServicePackages/Index.vue'),
    meta: { requiresAuth: true, breadcrumb: ['Hizmetler', 'Paketler'] }
  },
  {
    path: '/service-addons',
    name: 'ServiceAddons',
    component: () => import(/* webpackChunkName: "services" */ '../views/ServiceAddons/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/service-templates',
    name: 'ServiceTemplates',
    component: () => import(/* webpackChunkName: "services" */ '../views/ServiceTemplates/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/service-requirements',
    name: 'ServiceRequirements',
    component: () => import(/* webpackChunkName: "services" */ '../views/ServiceRequirements/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/service-reviews',
    name: 'ServiceReviews',
    component: () => import(/* webpackChunkName: "services" */ '../views/ServiceReviews/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/service-pricing-rules',
    name: 'ServicePricingRules',
    component: () => import(/* webpackChunkName: "services" */ '../views/ServicePricingRules/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/service-price-histories',
    name: 'ServicePriceHistories',
    component: () => import(/* webpackChunkName: "services" */ '../views/ServicePriceHistories/Index.vue'),
    meta: { requiresAuth: true }
  },

  // ─────────────────────────────────────────────────────────────
  // Randevu Yönetimi
  // ─────────────────────────────────────────────────────────────
  {
    path: '/appointments',
    name: 'Appointments',
    component: () => import(/* webpackChunkName: "appointments" */ '../views/Appointments/Index.vue'),
    meta: { requiresAuth: true, breadcrumb: ['Randevular'] }
  },
  {
    path: '/appointment-groups',
    name: 'AppointmentGroups',
    component: () => import(/* webpackChunkName: "appointments" */ '../views/AppointmentGroups/Index.vue'),
    meta: { requiresAuth: true, breadcrumb: ['Randevular', 'Grup Randevuları'] }
  },
  {
    path: '/appointments/group-participants',
    name: 'AppointmentGroupParticipants',
    component: () => import(/* webpackChunkName: "appointments" */ '../views/AppointmentGroupParticipants/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/appointment-recurrences',
    name: 'AppointmentRecurrences',
    component: () => import(/* webpackChunkName: "appointments" */ '../views/AppointmentRecurrences/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/appointment-reminders',
    name: 'AppointmentReminders',
    component: () => import(/* webpackChunkName: "appointments" */ '../views/AppointmentReminders/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/appointment-waitlists',
    name: 'AppointmentWaitlists',
    component: () => import(/* webpackChunkName: "appointments" */ '../views/AppointmentWaitlists/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/appointment-cancellations',
    name: 'AppointmentCancellations',
    component: () => import(/* webpackChunkName: "appointments" */ '../views/AppointmentCancellations/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/appointment-cancellation-reasons',
    name: 'AppointmentCancellationReasons',
    component: () => import(/* webpackChunkName: "appointments" */ '../views/AppointmentCancellationReasons/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/appointment-conflicts',
    name: 'AppointmentConflicts',
    component: () => import(/* webpackChunkName: "appointments" */ '../views/AppointmentConflicts/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/appointment-histories',
    name: 'AppointmentHistories',
    component: () => import(/* webpackChunkName: "appointments" */ '../views/AppointmentHistories/Index.vue'),
    meta: { requiresAuth: true }
  },

  // ─────────────────────────────────────────────────────────────
  // Satış & Kasa
  // ─────────────────────────────────────────────────────────────
  {
    path: '/sales',
    name: 'Sales',
    component: () => import(/* webpackChunkName: "sales" */ '../views/Sales/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/cash-registers',
    name: 'CashRegisters',
    component: () => import(/* webpackChunkName: "sales" */ '../views/CashRegisters/Index.vue'),
    meta: { requiresAuth: true, breadcrumb: ['Satış', 'Kasalar'] }
  },
  {
    path: '/cash-register-sessions',
    name: 'CashRegisterSessions',
    component: () => import(/* webpackChunkName: "sales" */ '../views/CashRegisterSessions/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/cash-register-transactions',
    name: 'CashRegisterTransactions',
    component: () => import(/* webpackChunkName: "sales" */ '../views/CashRegisterTransactions/Index.vue'),
    meta: { requiresAuth: true }
  },

  // ─────────────────────────────────────────────────────────────
  // Ürün & Stok
  // ─────────────────────────────────────────────────────────────
  {
    path: '/products',
    name: 'Products',
    component: () => import(/* webpackChunkName: "products" */ '../views/Products/Index.vue'),
    meta: { requiresAuth: true, breadcrumb: ['Ürünler'] }
  },
  {
    path: '/inventory',
    name: 'Inventory',
    component: () => import(/* webpackChunkName: "inventory" */ '../views/Inventory/Index.vue'),
    meta: { requiresAuth: true, breadcrumb: ['Stok', 'Stok Hareketleri'] }
  },
  {
    path: '/stock-transfers',
    name: 'StockTransfers',
    component: () => import(/* webpackChunkName: "inventory" */ '../views/StockTransfers/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/stock-alerts',
    name: 'StockAlerts',
    component: () => import(/* webpackChunkName: "inventory" */ '../views/StockAlerts/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/stock-audits',
    name: 'StockAudits',
    component: () => import(/* webpackChunkName: "inventory" */ '../views/StockAudits/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/product-variants',
    name: 'ProductVariants',
    component: () => import(/* webpackChunkName: "products" */ '../views/ProductVariants/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/product-bundles',
    name: 'ProductBundles',
    component: () => import(/* webpackChunkName: "products" */ '../views/ProductBundles/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/product-discounts',
    name: 'ProductDiscounts',
    component: () => import(/* webpackChunkName: "products" */ '../views/ProductDiscounts/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/product-barcodes',
    name: 'ProductBarcodes',
    component: () => import(/* webpackChunkName: "products" */ '../views/ProductBarcodes/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/product-price-histories',
    name: 'ProductPriceHistories',
    component: () => import(/* webpackChunkName: "products" */ '../views/ProductPriceHistories/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/product-stock-histories',
    name: 'ProductStockHistories',
    component: () => import(/* webpackChunkName: "products" */ '../views/ProductStockHistories/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/product-attributes',
    name: 'ProductAttributes',
    component: () => import(/* webpackChunkName: "products" */ '../views/ProductAttributes/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/product-attribute-values',
    name: 'ProductAttributeValues',
    component: () => import(/* webpackChunkName: "products" */ '../views/ProductAttributeValues/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/product-category-hierarchies',
    name: 'ProductCategoryHierarchies',
    component: () => import(/* webpackChunkName: "products" */ '../views/ProductCategoryHierarchies/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/product-images',
    name: 'ProductImages',
    component: () => import(/* webpackChunkName: "products" */ '../views/ProductImages/Index.vue'),
    meta: { requiresAuth: true }
  },

  // ─────────────────────────────────────────────────────────────
  // Tedarik
  // ─────────────────────────────────────────────────────────────
  {
    path: '/suppliers',
    name: 'Suppliers',
    component: () => import(/* webpackChunkName: "supply" */ '../views/Suppliers/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/purchase-orders',
    name: 'PurchaseOrders',
    component: () => import(/* webpackChunkName: "supply" */ '../views/PurchaseOrders/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/purchase-order-items',
    name: 'PurchaseOrderItems',
    component: () => import(/* webpackChunkName: "supply" */ '../views/PurchaseOrderItems/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/product-supplier-prices',
    name: 'ProductSupplierPrices',
    component: () => import(/* webpackChunkName: "supply" */ '../views/ProductSupplierPrices/Index.vue'),
    meta: { requiresAuth: true }
  },

  // ─────────────────────────────────────────────────────────────
  // Finans
  // ─────────────────────────────────────────────────────────────
  {
    path: '/invoices',
    name: 'Invoices',
    component: () => import(/* webpackChunkName: "finance" */ '../views/Invoices/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/invoice-items',
    name: 'InvoiceItems',
    component: () => import(/* webpackChunkName: "finance" */ '../views/InvoiceItems/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/payments',
    name: 'Payments',
    component: () => import(/* webpackChunkName: "finance" */ '../views/Payments/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/expenses',
    name: 'Expenses',
    component: () => import(/* webpackChunkName: "finance" */ '../views/Expenses/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/bank-accounts',
    name: 'BankAccounts',
    component: () => import(/* webpackChunkName: "finance" */ '../views/BankAccounts/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/bank-transactions',
    name: 'BankTransactions',
    component: () => import(/* webpackChunkName: "finance" */ '../views/BankTransactions/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/journal-entries',
    name: 'JournalEntries',
    component: () => import(/* webpackChunkName: "finance" */ '../views/JournalEntries/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/chart-of-accounts',
    name: 'ChartOfAccounts',
    component: () => import(/* webpackChunkName: "finance" */ '../views/ChartOfAccounts/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/tax-rates',
    name: 'TaxRates',
    component: () => import(/* webpackChunkName: "finance" */ '../views/TaxRates/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/currencies',
    name: 'Currencies',
    component: () => import(/* webpackChunkName: "finance" */ '../views/Currencies/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/exchange-rates',
    name: 'ExchangeRates',
    component: () => import(/* webpackChunkName: "finance" */ '../views/ExchangeRates/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/budget-plans',
    name: 'BudgetPlans',
    component: () => import(/* webpackChunkName: "finance" */ '../views/BudgetPlans/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/budget-items',
    name: 'BudgetItems',
    component: () => import(/* webpackChunkName: "finance" */ '../views/BudgetItems/Index.vue'),
    meta: { requiresAuth: true }
  },

  // ─────────────────────────────────────────────────────────────
  // Pazarlama & CRM
  // ─────────────────────────────────────────────────────────────
  {
    path: '/marketing-campaigns',
    name: 'MarketingCampaigns',
    component: () => import(/* webpackChunkName: "marketing" */ '../views/MarketingCampaigns/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/campaign-statistics',
    name: 'CampaignStatistics',
    component: () => import(/* webpackChunkName: "marketing" */ '../views/CampaignStatistics/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/coupons',
    name: 'Coupons',
    component: () => import(/* webpackChunkName: "marketing" */ '../views/Coupons/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/coupon-usages',
    name: 'CouponUsages',
    component: () => import(/* webpackChunkName: "marketing" */ '../views/CouponUsages/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/loyalty-programs',
    name: 'LoyaltyPrograms',
    component: () => import(/* webpackChunkName: "marketing" */ '../views/LoyaltyPrograms/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/loyalty-points',
    name: 'LoyaltyPoints',
    component: () => import(/* webpackChunkName: "marketing" */ '../views/LoyaltyPoints/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/loyalty-point-transactions',
    name: 'LoyaltyPointTransactions',
    component: () => import(/* webpackChunkName: "marketing" */ '../views/LoyaltyPointTransactions/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/leads',
    name: 'Leads',
    component: () => import(/* webpackChunkName: "marketing" */ '../views/Leads/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/lead-activities',
    name: 'LeadActivities',
    component: () => import(/* webpackChunkName: "marketing" */ '../views/LeadActivities/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/referral-programs',
    name: 'ReferralPrograms',
    component: () => import(/* webpackChunkName: "marketing" */ '../views/ReferralPrograms/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/referrals',
    name: 'Referrals',
    component: () => import(/* webpackChunkName: "marketing" */ '../views/Referrals/Index.vue'),
    meta: { requiresAuth: true }
  },

  // ─────────────────────────────────────────────────────────────
  // Raporlar & Analiz
  // ─────────────────────────────────────────────────────────────
  {
    path: '/report-templates',
    name: 'ReportTemplates',
    component: () => import(/* webpackChunkName: "reports" */ '../views/ReportTemplates/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/report-executions',
    name: 'ReportExecutions',
    component: () => import(/* webpackChunkName: "reports" */ '../views/ReportExecutions/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/report-schedules',
    name: 'ReportSchedules',
    component: () => import(/* webpackChunkName: "reports" */ '../views/ReportSchedules/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/kpi-definitions',
    name: 'KpiDefinitions',
    component: () => import(/* webpackChunkName: "reports" */ '../views/KpiDefinitions/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/kpi-values',
    name: 'KpiValues',
    component: () => import(/* webpackChunkName: "reports" */ '../views/KpiValues/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/performance-metrics',
    name: 'PerformanceMetrics',
    component: () => import(/* webpackChunkName: "reports" */ '../views/PerformanceMetrics/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/analytics-sessions',
    name: 'AnalyticsSessions',
    component: () => import(/* webpackChunkName: "reports" */ '../views/AnalyticsSessions/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/analytics-events',
    name: 'AnalyticsEvents',
    component: () => import(/* webpackChunkName: "reports" */ '../views/AnalyticsEvents/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/rfm-analyses',
    name: 'RfmAnalyses',
    component: () => import(/* webpackChunkName: "reports" */ '../views/RfmAnalyses/Index.vue'),
    meta: { requiresAuth: true }
  },

  // ─────────────────────────────────────────────────────────────
  // Bildirimler & İletişim
  // ─────────────────────────────────────────────────────────────
  {
    path: '/notifications/templates',
    name: 'NotificationTemplates',
    component: () => import(/* webpackChunkName: "notifications" */ '../views/Notifications/Templates.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/notification-logs',
    name: 'NotificationLogs',
    component: () => import(/* webpackChunkName: "notifications" */ '../views/NotificationLogs/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/notification-queues',
    name: 'NotificationQueues',
    component: () => import(/* webpackChunkName: "notifications" */ '../views/NotificationQueues/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/notification-preferences',
    name: 'NotificationPreferences',
    component: () => import(/* webpackChunkName: "notifications" */ '../views/NotificationPreferences/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/email-providers',
    name: 'EmailProviders',
    component: () => import(/* webpackChunkName: "notifications" */ '../views/EmailProviders/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/sms-providers',
    name: 'SmsProviders',
    component: () => import(/* webpackChunkName: "notifications" */ '../views/SmsProviders/Index.vue'),
    meta: { requiresAuth: true }
  },

  // ─────────────────────────────────────────────────────────────
  // Anketler
  // ─────────────────────────────────────────────────────────────
  {
    path: '/surveys',
    name: 'Surveys',
    component: () => import(/* webpackChunkName: "surveys" */ '../views/Surveys/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/survey-responses',
    name: 'SurveyResponses',
    component: () => import(/* webpackChunkName: "surveys" */ '../views/SurveyResponses/Index.vue'),
    meta: { requiresAuth: true }
  },

  // ─────────────────────────────────────────────────────────────
  // Sistem Ayarları
  // ─────────────────────────────────────────────────────────────
  {
    path: '/settings',
    name: 'Settings',
    component: () => import(/* webpackChunkName: "settings" */ '../views/Settings/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/custom-fields',
    name: 'CustomFields',
    component: () => import(/* webpackChunkName: "settings" */ '../views/CustomFields/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/custom-field-values',
    name: 'CustomFieldValues',
    component: () => import(/* webpackChunkName: "settings" */ '../views/CustomFieldValues/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/document-templates',
    name: 'DocumentTemplates',
    component: () => import(/* webpackChunkName: "settings" */ '../views/DocumentTemplates/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/translations',
    name: 'Translations',
    component: () => import(/* webpackChunkName: "settings" */ '../views/Translations/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/feature-flags',
    name: 'FeatureFlags',
    component: () => import(/* webpackChunkName: "settings" */ '../views/FeatureFlags/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/webhooks',
    name: 'Webhooks',
    component: () => import(/* webpackChunkName: "settings" */ '../views/Webhooks/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/integrations',
    name: 'Integrations',
    component: () => import(/* webpackChunkName: "settings" */ '../views/Integrations/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/system-backups',
    name: 'SystemBackups',
    component: () => import(/* webpackChunkName: "settings" */ '../views/SystemBackups/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/audit-logs',
    name: 'AuditLogs',
    component: () => import(/* webpackChunkName: "settings" */ '../views/AuditLogs/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/activity-logs',
    name: 'ActivityLogs',
    component: () => import(/* webpackChunkName: "settings" */ '../views/ActivityLogs/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/oauth-providers',
    name: 'OauthProviders',
    component: () => import(/* webpackChunkName: "settings" */ '../views/OauthProviders/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/oauth-tokens',
    name: 'OauthTokens',
    component: () => import(/* webpackChunkName: "settings" */ '../views/OauthTokens/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/mobile-devices',
    name: 'MobileDevices',
    component: () => import(/* webpackChunkName: "settings" */ '../views/MobileDevices/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/user-preferences',
    name: 'UserPreferences',
    component: () => import(/* webpackChunkName: "settings" */ '../views/UserPreferences/Index.vue'),
    meta: { requiresAuth: true }
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    // Smooth scroll to saved position or top
    if (savedPosition) {
      return savedPosition;
    }
    return { top: 0, behavior: 'smooth' };
  }
});

// Intelligent prefetch - load critical routes first
const prefetchRoutes = () => {
  const criticalRoutes = ['Dashboard', 'Customers', 'Appointments', 'Sales'];
  const otherRoutes = routes.filter(r => !criticalRoutes.includes(r.name as string));

  // Prefetch critical routes immediately
  criticalRoutes.forEach(name => {
    const route = routes.find(r => r.name === name);
    if (route && typeof route.component === 'function') {
      (route.component as () => Promise<unknown>)();
    }
  });

  // Prefetch other routes with delay
  setTimeout(() => {
    otherRoutes.forEach(route => {
      if (typeof route.component === 'function') {
        (route.component as () => Promise<unknown>)();
      }
    });
  }, 3000);
};

// Start prefetching after page load
if (typeof window !== 'undefined') {
  window.addEventListener('load', () => {
    if ('requestIdleCallback' in window) {
      (window as Window & { requestIdleCallback: (cb: () => void) => void }).requestIdleCallback(prefetchRoutes);
    } else {
      setTimeout(prefetchRoutes, 1000);
    }
  });
}

// Navigation guard
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('auth_token');

  if (to.meta.requiresAuth && !token) {
    next('/login');
  } else {
    next();
  }
});

export default router;

