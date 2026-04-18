<script setup lang="ts">
import AppHeader from './components/AppHeader.vue';
import { useAuth } from './composables/useAuth';

const { isAuthenticated } = useAuth();
</script>

<template>
  <AppHeader v-if="isAuthenticated" />
  <RouterView />
</template>
