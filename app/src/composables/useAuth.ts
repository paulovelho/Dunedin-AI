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
    const unsub = onAuthStateChanged(auth, async (u: User | null) => {
      if (u && !u.photoURL) {
        try {
          await u.reload();
        } catch (err) {
          console.warn('[auth] user.reload() failed', err);
        }
      }
      currentUser.value = auth.currentUser;
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
    console.log('[auth] signInWithGoogle: opening popup');
    let cred;
    try {
      cred = await signInWithPopup(auth, googleProvider);
    } catch (err: any) {
      console.error('[auth] signInWithPopup failed', {
        code: err?.code,
        message: err?.message,
        customData: err?.customData,
        err,
      });
      throw err;
    }
    console.log('[auth] popup resolved, uid=', cred.user.uid);

    let res: Response;
    try {
      res = await api('/auth/login', { method: 'POST' });
    } catch (err: any) {
      console.error('[auth] /auth/login fetch threw', err);
      throw err;
    }
    if (!res.ok) {
      const body = await res.text().catch(() => '<no body>');
      console.error('[auth] /auth/login non-ok', { status: res.status, body });
      throw new Error(`login failed: ${res.status}`);
    }
    console.log('[auth] /auth/login ok, reloading');
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
