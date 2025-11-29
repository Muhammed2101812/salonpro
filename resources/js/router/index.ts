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
    path: '/customers/:id',
    name: 'CustomerShow',
    component: () => import('../views/Customers/Show.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/employees',
    name: 'Employees',
    component: () => import('../views/Employees/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/employees/schedule',
    name: 'EmployeeSchedule',
    component: () => import('../views/Employees/Schedule.vue'),
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
  {
    path: '/notifications/templates',
    name: 'NotificationTemplates',
    component: () => import('../views/Notifications/Templates.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/appointments/group-participants',
    name: 'AppointmentGroupParticipants',
    component: () => import('../views/AppointmentGroupParticipants/Index.vue'),
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
  {
    path: '/mobile-devices',
    name: 'MobileDevices',
    component: () => import('../views/MobileDevices/Index.vue'),
    meta: { requiresAuth: true }
  },
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

// Prefetch all route components after initial load
if (typeof window !== 'undefined') {
  window.addEventListener('load', () => {
    // Wait a bit for initial page to settle
    setTimeout(() => {
      routes.forEach(route => {
        if (typeof route.component === 'function') {
          // Trigger lazy loading in background
          route.component();
        }
      });
    }, 1000);
  });
}

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
