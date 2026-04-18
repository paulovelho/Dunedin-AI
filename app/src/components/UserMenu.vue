<script setup lang="ts">
import { useAuth } from '../composables/useAuth';

const { user, signOut } = useAuth();
</script>

<template>
  <div v-if="user" class="user-menu">
    <span class="email">{{ user.email }}</span>
    <button @click="signOut">Sign out</button>
  </div>
</template>

<style scoped>
.user-menu {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-size: 0.875rem;
}
.email {
  color: var(--color-text-muted);
}
button {
  padding: 0.35rem 0.9rem;
  cursor: pointer;
  border: 1px solid var(--color-purple);
  background: var(--color-surface);
  color: var(--color-purple);
  border-radius: 999px;
  font-size: 0.875rem;
  transition: background 0.15s, color 0.15s;
}
button:hover {
  background: var(--color-purple);
  color: var(--color-surface);
}
</style>
