<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { api } from '../lib/api';

type ApiUser = {
  id: number;
  email: string | null;
  display_name: string | null;
};

const me = ref<ApiUser | null>(null);
const error = ref<string | null>(null);

onMounted(async () => {
  try {
    const res = await api('/me');
    if (!res.ok) throw new Error(`API ${res.status}`);
    me.value = await res.json();
  } catch (e) {
    error.value = e instanceof Error ? e.message : 'unknown error';
  }
});
</script>

<template>
  <main>
    <h1>Dunedin AI</h1>
    <p>Search your highlights.</p>
    <p v-if="me" class="auth">
      Authenticated as {{ me.display_name || me.email }} (#{{ me.id }})
    </p>
    <p v-if="error" class="error">API error: {{ error }}</p>
  </main>
</template>

<style scoped>
main {
  padding: 2rem;
  max-width: 800px;
  margin: 0 auto;
}
.auth {
  color: var(--color-green);
  font-size: 0.875rem;
}
.error {
  color: crimson;
}
</style>
