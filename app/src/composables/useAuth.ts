import { ref, computed } from 'vue';
import {
  onAuthStateChanged,
  signInWithPopup,
  signOut as fbSignOut,
  type User,
} from 'firebase/auth';
import { auth, googleProvider } from '../lib/firebase';
import { api } from '../lib/api';

const currentUser = ref<User | null>(null);
const loading = ref(true);

let readyPromise: Promise<void> | null = null;

function init(): Promise<void> {
  if (readyPromise) return readyPromise;
  readyPromise = new Promise<void>((resolve) => {
    const unsub = onAuthStateChanged(auth, (u) => {
      currentUser.value = u;
      loading.value = false;
      resolve();
    });
    void unsub;
  });
  return readyPromise;
}

export function useAuth() {
  init();

  const user = computed<User | null>(() => currentUser.value);
  const isAuthenticated = computed(() => !!currentUser.value);

  async function signInWithGoogle() {
    await signInWithPopup(auth, googleProvider);
    const res = await api('/auth/login', { method: 'POST' });
    if (!res.ok) throw new Error(`login failed: ${res.status}`);
    window.location.reload();
  }

  async function signOut() {
    await fbSignOut(auth);
    window.location.reload();
  }

  function whenReady(): Promise<void> {
    return init();
  }

  return {
    user,
    isAuthenticated,
    loading,
    signInWithGoogle,
    signOut,
    whenReady,
  };
}
