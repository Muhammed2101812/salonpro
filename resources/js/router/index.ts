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
