<script setup lang="ts">
import { onMounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import { useAuth } from '../composables/useAuth';

const router = useRouter();
const { isAuthenticated, loading } = useAuth();

function maybeRedirect() {
  if (loading.value) return;
  router.replace(isAuthenticated.value ? '/' : '/login');
}

onMounted(maybeRedirect);
watch([isAuthenticated, loading], maybeRedirect);
</script>

<template>
  <main class="callback">
    <p>Signing you in…</p>
  </main>
</template>

<style scoped>
.callback {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
}
</style>
