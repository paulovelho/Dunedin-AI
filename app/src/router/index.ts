import { createRouter, createWebHistory } from 'vue-router';
import { useAuth } from '../composables/useAuth';
import SearchView from '../views/SearchView.vue';
import LoginView from '../views/LoginView.vue';
import UploadView from '../views/UploadView.vue';
import SharedLinksView from '../views/SharedLinksView.vue';
import SharedPublicView from '../views/SharedPublicView.vue';

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/', name: 'search', component: SearchView, meta: { requiresAuth: true } },
    { path: '/upload', name: 'upload', component: UploadView, meta: { requiresAuth: true } },
    { path: '/shared-links', name: 'shared-links', component: SharedLinksView, meta: { requiresAuth: true } },
    { path: '/shared/:uuid', name: 'shared-public', component: SharedPublicView },
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
