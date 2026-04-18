<script setup lang="ts">
import logo from '../assets/logo.svg';
import { useAuth } from '../composables/useAuth';

const { signInWithGoogle } = useAuth();
</script>

<template>
  <main class="login">
    <img :src="logo" alt="Dunedin AI" class="logo" />
    <h1>Dunedin AI</h1>
    <p>Sign in to access your highlights.</p>
    <button @click="signInWithGoogle">Sign in with Google</button>
  </main>
</template>

<style scoped>
.login {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  gap: 1.25rem;
  padding: 2rem;
  text-align: center;
}
.logo {
  width: 220px;
  max-width: 60vw;
  height: auto;
}
h1 {
  font-size: 2rem;
  color: var(--color-text);
}
p {
  margin: 0;
  color: var(--color-text-muted);
}
button {
  margin-top: 0.5rem;
  padding: 0.75rem 1.5rem;
  font-size: 1rem;
  cursor: pointer;
  border: 2px solid var(--color-purple);
  background: var(--color-purple);
  color: var(--color-surface);
  border-radius: 0.375rem;
  font-family: inherit;
  transition: background 0.15s, border-color 0.15s;
}
button:hover {
  background: var(--color-green);
  border-color: var(--color-green);
}
</style>
