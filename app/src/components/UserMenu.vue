<script setup lang="ts">
import { computed, ref, watch, onBeforeUnmount } from 'vue';
import { useAuth } from '../composables/useAuth';

const { user, signOut } = useAuth();

const label = computed(() => user.value?.displayName || user.value?.email || '');
const email = computed(() => user.value?.email || '');

const open = ref(false);

function openDrawer() {
  open.value = true;
}
function closeDrawer() {
  open.value = false;
}
function toggleDrawer() {
  open.value = !open.value;
}

function onKeydown(e: KeyboardEvent) {
  if (e.key === 'Escape') closeDrawer();
}

watch(open, (isOpen: boolean) => {
  if (isOpen) {
    document.addEventListener('keydown', onKeydown);
    document.body.style.overflow = 'hidden';
  } else {
    document.removeEventListener('keydown', onKeydown);
    document.body.style.overflow = '';
  }
});

onBeforeUnmount(() => {
  document.removeEventListener('keydown', onKeydown);
  document.body.style.overflow = '';
});
</script>

<template>
  <div v-if="user" class="user-menu">
    <button
      type="button"
      class="trigger identity"
      :aria-expanded="open"
      aria-label="Open user menu"
      @click="openDrawer"
    >
      <img
        v-if="user.photoURL"
        :src="user.photoURL"
        :alt="label"
        class="avatar"
        referrerpolicy="no-referrer"
      />
      <span class="name">{{ label }}</span>
    </button>
    <button
      type="button"
      class="trigger hamburger"
      :aria-expanded="open"
      aria-label="Open menu"
      @click="toggleDrawer"
    >
      <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true">
        <path
          d="M3 6h18M3 12h18M3 18h18"
          stroke="currentColor"
          stroke-width="2"
          stroke-linecap="round"
        />
      </svg>
    </button>

    <Teleport to="body">
      <Transition name="drawer-fade">
        <div v-if="open" class="drawer-backdrop" @click="closeDrawer" />
      </Transition>
      <Transition name="drawer-slide">
        <aside
          v-if="open"
          class="drawer"
          role="dialog"
          aria-modal="true"
          aria-label="User menu"
        >
          <header class="drawer-header">
            <span class="drawer-title">Menu</span>
            <button
              type="button"
              class="close-btn"
              aria-label="Close menu"
              @click="closeDrawer"
            >
              <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true">
                <path
                  d="M6 6l12 12M18 6L6 18"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                />
              </svg>
            </button>
          </header>

          <section class="drawer-user">
            <img
              v-if="user.photoURL"
              :src="user.photoURL"
              :alt="label"
              class="drawer-avatar"
              referrerpolicy="no-referrer"
            />
            <div class="drawer-user-text">
              <div class="drawer-name">{{ label }}</div>
              <div v-if="email && email !== label" class="drawer-email">{{ email }}</div>
            </div>
          </section>

          <nav class="drawer-nav" aria-label="Account">
            <RouterLink class="nav-item" to="/upload" @click="closeDrawer">Upload clippings</RouterLink>
            <a class="nav-item disabled" aria-disabled="true">Settings</a>
            <a class="nav-item disabled" aria-disabled="true">About</a>
          </nav>

          <div class="drawer-spacer" />

          <footer class="drawer-footer">
            <button class="sign-out" @click="signOut">Sign out</button>
          </footer>
        </aside>
      </Transition>
    </Teleport>
  </div>
</template>

<style scoped>
.user-menu {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
}
.trigger {
  display: inline-flex;
  align-items: center;
  gap: 0.6rem;
  background: transparent;
  border: 1px solid transparent;
  border-radius: 999px;
  padding: 0.25rem 0.6rem 0.25rem 0.25rem;
  cursor: pointer;
  color: var(--color-text-muted);
  font-size: inherit;
  font-family: inherit;
  transition: background 0.15s, border-color 0.15s, color 0.15s;
}
.trigger:hover {
  background: var(--color-bg);
  border-color: var(--color-border);
  color: var(--color-text);
}
.trigger.hamburger {
  padding: 0.35rem;
  color: var(--color-text-muted);
}
.avatar {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  object-fit: cover;
}
.name {
  color: inherit;
}

.drawer-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.35);
  z-index: 100;
}
.drawer {
  position: fixed;
  top: 0;
  right: 0;
  height: 100vh;
  width: min(340px, 90vw);
  background: var(--color-surface);
  border-left: 1px solid var(--color-border);
  box-shadow: -8px 0 24px rgba(0, 0, 0, 0.08);
  z-index: 101;
  display: flex;
  flex-direction: column;
  font-family: var(--font-typewriter);
}
.drawer-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.75rem 1rem;
  border-bottom: 1px solid var(--color-border);
}
.drawer-title {
  font-size: 0.95rem;
  letter-spacing: 0.04em;
  color: var(--color-text-muted);
  text-transform: uppercase;
}
.close-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: transparent;
  border: none;
  color: var(--color-text-muted);
  cursor: pointer;
  padding: 0.25rem;
  border-radius: 6px;
}
.close-btn:hover {
  color: var(--color-text);
  background: var(--color-bg);
}

.drawer-user {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1rem;
  border-bottom: 1px solid var(--color-border);
}
.drawer-avatar {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  object-fit: cover;
}
.drawer-user-text {
  display: flex;
  flex-direction: column;
  min-width: 0;
}
.drawer-name {
  color: var(--color-text);
  font-size: 0.95rem;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.drawer-email {
  color: var(--color-text-muted);
  font-size: 0.8rem;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.drawer-nav {
  display: flex;
  flex-direction: column;
  padding: 0.5rem 0;
}
.nav-item {
  padding: 0.65rem 1rem;
  font-size: 0.9rem;
  color: var(--color-text);
  text-decoration: none;
  cursor: pointer;
}
.nav-item:hover {
  background: var(--color-bg);
}
.nav-item.disabled {
  color: var(--color-text-muted);
  cursor: not-allowed;
  pointer-events: none;
  opacity: 0.6;
}

.drawer-spacer {
  flex: 1 1 auto;
}

.drawer-footer {
  padding: 1rem;
  border-top: 1px solid var(--color-border);
}
.sign-out {
  width: 100%;
  padding: 0.55rem 1rem;
  cursor: pointer;
  border: 1px solid var(--color-purple);
  background: var(--color-surface);
  color: var(--color-purple);
  border-radius: 999px;
  font-size: 0.9rem;
  font-family: inherit;
  transition: background 0.15s, color 0.15s;
}
.sign-out:hover {
  background: var(--color-purple);
  color: var(--color-surface);
}

.drawer-fade-enter-active,
.drawer-fade-leave-active {
  transition: opacity 0.18s ease;
}
.drawer-fade-enter-from,
.drawer-fade-leave-to {
  opacity: 0;
}

.drawer-slide-enter-active,
.drawer-slide-leave-active {
  transition: transform 0.22s ease;
}
.drawer-slide-enter-from,
.drawer-slide-leave-to {
  transform: translateX(100%);
}
</style>
