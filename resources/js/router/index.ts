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
