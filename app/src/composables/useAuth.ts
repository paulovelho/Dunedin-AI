import { ref, computed } from 'vue';
import type { Session, User } from '@supabase/supabase-js';
import { supabase } from '../lib/supabase';

const session = ref<Session | null>(null);
const loading = ref(true);

let readyPromise: Promise<void> | null = null;

function init(): Promise<void> {
  if (readyPromise) return readyPromise;
  readyPromise = (async () => {
    const { data } = await supabase.auth.getSession();
    session.value = data.session;
    loading.value = false;
    supabase.auth.onAuthStateChange((_event, newSession) => {
      session.value = newSession;
    });
  })();
  return readyPromise;
}

export function useAuth() {
  init();

  const user = computed<User | null>(() => session.value?.user ?? null);
  const isAuthenticated = computed(() => !!session.value);

  async function signInWithGoogle() {
    await supabase.auth.signInWithOAuth({
      provider: 'google',
      options: {
        redirectTo: `${window.location.origin}/auth/callback`,
      },
    });
  }

  async function signOut() {
    await supabase.auth.signOut();
  }

  function whenReady(): Promise<void> {
    return init();
  }

  return {
    user,
    session,
    isAuthenticated,
    loading,
    signInWithGoogle,
    signOut,
    whenReady,
  };
}
