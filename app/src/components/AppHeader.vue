<script setup lang="ts">
import { RouterLink } from 'vue-router';
import logo from '../assets/logo.svg';
import UserMenu from './UserMenu.vue';
</script>

<template>
  <header class="app-header">
    <RouterLink to="/" class="brand" aria-label="Dunedin AI home">
      <img :src="logo" alt="Dunedin AI" />
    </RouterLink>
    <UserMenu />
  </header>
</template>

<style scoped>
.app-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.75rem 1.5rem;
  background: var(--color-surface);
  border-bottom: 1px solid var(--color-border);
  position: sticky;
  top: 0;
  z-index: 10;
}
.brand {
  display: inline-flex;
  align-items: center;
  text-decoration: none;
}
.brand img {
  height: 36px;
  width: auto;
  display: block;
}
</style>
