<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';
import { api } from '../lib/api';
import HighlightItem from '../components/Highlight/HighlightItem.vue';

interface PublicHighlight {
  text: string;
  author: string;
  origin: string;
  title: string;
  date: string;
}

const route = useRoute();
const uuid = route.params.uuid as string;

const description = ref('');
const highlights = ref<PublicHighlight[]>([]);
const loading = ref(true);
const notFound = ref(false);

async function load() {
  loading.value = true;
  try {
    const res = await api(`/shared/${uuid}`);
    if (res.status === 404) {
      notFound.value = true;
      return;
    }
    if (!res.ok) throw new Error(`API ${res.status}`);
    const body = (await res.json()) as {
      success: boolean;
      data: { description: string; highlights: PublicHighlight[] };
    };
    description.value = body.data.description;
    highlights.value = body.data.highlights;

    api(`/shared/${uuid}/visit`, { method: 'POST' }).catch(() => undefined);
  } catch {
    notFound.value = true;
  } finally {
    loading.value = false;
  }
}

onMounted(load);
</script>

<template>
  <main class="shared-public-page">
    <p v-if="loading" class="loading">Loading…</p>

    <template v-else-if="notFound">
      <h1 class="brand">Dunedin AI</h1>
      <p class="not-found">This shared link doesn't exist or is no longer available.</p>
    </template>

    <template v-else>
      <h1 class="brand">Dunedin AI</h1>
      <p class="description">{{ description }}</p>
      <div class="results">
        <HighlightItem v-for="(h, i) in highlights" :key="i" :highlight="h" public />
      </div>
    </template>
  </main>
</template>

<style scoped>
.shared-public-page {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1.25rem;
  width: 100%;
  max-width: 720px;
  margin: 0 auto;
  box-sizing: border-box;
  padding: 3rem 8vw;
}

.brand {
  font-family: var(--font-typewriter);
  font-size: 1.75rem;
  color: var(--color-purple);
  margin: 0;
}

.description {
  color: var(--color-text-muted);
  font-size: 0.95rem;
  text-align: center;
  margin: 0;
}

.loading,
.not-found {
  color: var(--color-text-muted);
  font-size: 0.9rem;
}

.results {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  width: 100%;
}
</style>
