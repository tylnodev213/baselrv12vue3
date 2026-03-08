// resources/js/router/index.js
import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/useAuthStore';

// Layouts
import LayoutDefault from '@/layouts/LayoutDefault.vue';
import LayoutAuth from '@/layouts/LayoutAuth.vue';

// Pages
import HomePage from '@/pages/HomePage.vue';
import LoginPage from '@/pages/LoginPage.vue';
import RegisterPage from '@/pages/RegisterPage.vue';
import ProductPage from '@/pages/ProductPage.vue';

const routes = [
  {
    path: '/',
    component: LayoutDefault,
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        name: 'home',
        component: HomePage,
      },
      {
        path: 'products',
        name: 'products',
        component: ProductPage,
      },
    ],
  },
  {
    path: '/auth',
    component: LayoutAuth,
    children: [
      {
        path: 'login',
        name: 'login',
        component: LoginPage,
        meta: { requiresGuest: true },
      },
      {
        path: 'register',
        name: 'register',
        component: RegisterPage,
        meta: { requiresGuest: true },
      },
    ],
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: { name: 'home' },
  },
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
});

/**
 * Navigation guard
 */
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();

  // Kiểm tra nếu route cần authentication
  if (to.meta.requiresAuth) {
    // Kiểm tra xem đã đăng nhập chưa
    if (!authStore.isAuthenticated) {
      // Nếu chưa, redirect đến login
      next({ name: 'login' });
      return;
    }

    // Kiểm tra xem user info đã được load chưa
    if (!authStore.user) {
      const result = await authStore.fetchUser();
      if (!result.success) {
        authStore.logout();
        next({ name: 'login' });
        return;
      }
    }
  }

  // Kiểm tra nếu route chỉ dành cho guest
  if (to.meta.requiresGuest && authStore.isAuthenticated) {
    next({ name: 'home' });
    return;
  }

  next();
});

export default router;
