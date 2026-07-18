<script setup lang="ts">
import { onMounted, ref, watch } from 'vue';
import { api } from '../../lib/api';
import HighlightItem from './HighlightItem.vue';
import ShareModal from '../SharedLink/ShareModal.vue';

interface Highlight {
  id: number;
  text: string;
  author: string;
  origin: string;
  title: string;
  date: string;
}

interface SearchResult {
  page: number;
  limit: number;
  total: number;
  items: Highlight[];
}

const props = defineProps<{ q: string; author: string }>();
const emit = defineEmits<{ 'author-click': [author: string] }>();

const highlights = ref<Highlight[]>([]);
const total = ref(0);
const page = ref(1);
const count = ref(20);
const loading = ref(false);
const error = ref<string | null>(null);

async function search(reset: boolean) {
  if (reset) {
    page.value = 1;
    highlights.value = [];
  }

  loading.value = true;
  error.value = null;
  try {
    const params = new URLSearchParams();
    if (props.q) params.set('q', props.q);
    if (props.author) params.set('author', props.author);
    params.set('page', String(page.value));
    params.set('count', String(count.value));

    const res = await api(`/search?${params.toString()}`);
    if (!res.ok) throw new Error(`API ${res.status}`);
    const body = (await res.json()) as { success: boolean; data: SearchResult };

    if (reset) {
      highlights.value = body.data.items;
    } else {
      highlights.value = [...highlights.value, ...body.data.items];
    }
    total.value = body.data.total;
  } catch (e) {
    error.value = e instanceof Error ? e.message : 'unknown error';
  } finally {
    loading.value = false;
  }
}

function loadMore() {
  page.value += 1;
  search(false);
}

function onCountChange() {
  search(true);
}

const shareModalIds = ref<number[] | null>(null);

function shareOne(id: number) {
  shareModalIds.value = [id];
}

function shareList() {
  if (highlights.value.length < total.value) {
    const proceed = confirm(
      `Only the ${highlights.value.length} loaded highlights (out of ${total.value}) will be shared. Continue?`
    );
    if (!proceed) return;
  }
  shareModalIds.value = highlights.value.map((h) => h.id);
}

defineExpose({ search });

onMounted(() => search(true));
watch(() => [props.q, props.author], () => search(true));
</script>

<template>
  <div class="highlight-list">
    <div v-if="highlights.length || loading" class="list-header">
      <span class="result-count">{{ total }} result{{ total === 1 ? '' : 's' }}</span>
      <div class="list-header-actions">
        <button
          v-if="highlights.length"
          type="button"
          class="btn-share-list"
          aria-label="Share this list of highlights"
          @click="shareList"
        >
          Share list
        </button>
        <label class="page-size">
          Show
          <select v-model.number="count" @change="onCountChange">
            <option :value="20">20</option>
            <option :value="50">50</option>
            <option :value="100">100</option>
          </select>
        </label>
      </div>
    </div>

    <p v-if="error" class="error">API error: {{ error }}</p>

    <div class="results">
      <HighlightItem
        v-for="h in highlights"
        :key="h.id"
        :highlight="h"
        @author-click="emit('author-click', $event)"
        @share="shareOne(h.id)"
      />
    </div>

    <ShareModal v-if="shareModalIds" :highlight-ids="shareModalIds" @close="shareModalIds = null" />

    <p v-if="!loading && !highlights.length && !error" class="empty-state">
      No highlights found.
    </p>

    <button
      v-if="highlights.length < total"
      class="btn-load-more"
      :disabled="loading"
      @click="loadMore"
    >
      {{ loading ? 'Loading…' : 'Load more' }}
    </button>
  </div>
</template>

<style scoped>
.highlight-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  width: 100%;
}

.list-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 0.85rem;
  color: var(--color-text-muted);
}

.page-size select {
  margin-left: 0.4rem;
  border: 1px solid var(--color-border);
  border-radius: 0.4rem;
  padding: 0.2rem 0.4rem;
  font-family: inherit;
  color: var(--color-text);
  background: var(--color-surface);
}

.list-header-actions {
  display: flex;
  align-items: center;
  gap: 0.9rem;
}

.btn-share-list {
  padding: 0.3rem 0.75rem;
  border: 1px solid var(--color-purple);
  background: transparent;
  color: var(--color-purple);
  border-radius: 999px;
  font-size: 0.8rem;
  font-family: inherit;
  cursor: pointer;
  transition: background 0.15s, color 0.15s;
}
.btn-share-list:hover {
  background: var(--color-purple);
  color: var(--color-surface);
}

.results {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.btn-load-more {
  align-self: center;
  padding: 0.5rem 1.25rem;
  border: 1px solid var(--color-purple);
  background: var(--color-surface);
  color: var(--color-purple);
  border-radius: 999px;
  font-size: 0.875rem;
  font-family: inherit;
  cursor: pointer;
  transition: background 0.15s, color 0.15s;
}
.btn-load-more:hover:not(:disabled) {
  background: var(--color-purple);
  color: var(--color-surface);
}
.btn-load-more:disabled {
  opacity: 0.6;
  cursor: default;
}

.empty-state {
  color: var(--color-text-muted);
  font-size: 0.9rem;
  text-align: center;
}

.error {
  color: crimson;
}
</style>
