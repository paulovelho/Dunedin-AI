import { createRouter, createWebHistory } from 'vue-router';
import { useAuth } from '../composables/useAuth';
import SearchView from '../views/SearchView.vue';
import LoginView from '../views/LoginView.vue';

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/', name: 'search', component: SearchView, meta: { requiresAuth: true } },
    { path: '/login', name: 'login', component: LoginView },
  ],
});

router.beforeEach(async (to) => {
  const auth = useAuth();
  await auth.whenReady();
  if (to.meta.requiresAuth && !auth.isAuthenticated.value) {
    return { name: 'login' };
  }
  if (to.name === 'login' && auth.isAuthenticated.value) {
    return { name: 'search' };
  }
  return true;
});

export default router;
