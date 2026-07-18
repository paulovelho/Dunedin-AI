<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { api } from '../lib/api';

interface SharedLink {
  id: number;
  uuid: string;
  active: boolean;
  description: string;
  visits: number;
  created_at: string;
  highlights_count: number;
}

const links = ref<SharedLink[]>([]);
const loading = ref(false);
const error = ref<string | null>(null);
const copiedId = ref<number | null>(null);

function shareUrl(uuid: string) {
  return `${window.location.origin}/shared/${uuid}`;
}

async function load() {
  loading.value = true;
  error.value = null;
  try {
    const res = await api('/shared-links');
    if (!res.ok) throw new Error(`API ${res.status}`);
    const body = (await res.json()) as { success: boolean; data: SharedLink[] };
    links.value = body.data;
  } catch (e) {
    error.value = e instanceof Error ? e.message : 'unknown error';
  } finally {
    loading.value = false;
  }
}

async function toggleActive(link: SharedLink) {
  const next = !link.active;
  try {
    const res = await api(`/shared-links/${link.id}`, {
      method: 'PUT',
      body: JSON.stringify({ active: next }),
    });
    if (!res.ok) throw new Error(`API ${res.status}`);
    link.active = next;
  } catch (e) {
    error.value = e instanceof Error ? e.message : 'unknown error';
  }
}

async function remove(link: SharedLink) {
  if (!confirm('Delete this shared link? Anyone with the link will lose access.')) return;
  try {
    const res = await api(`/shared-links/${link.id}`, { method: 'DELETE' });
    if (!res.ok) throw new Error(`API ${res.status}`);
    links.value = links.value.filter((l) => l.id !== link.id);
  } catch (e) {
    error.value = e instanceof Error ? e.message : 'unknown error';
  }
}

async function copyLink(link: SharedLink) {
  await navigator.clipboard.writeText(shareUrl(link.uuid));
  copiedId.value = link.id;
  setTimeout(() => (copiedId.value = null), 1500);
}

function formatDate(iso: string) {
  if (!iso) return '';
  return new Date(iso).toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric' });
}

onMounted(load);
</script>

<template>
  <main class="shared-links-page">
    <h1 class="page-title">Shared Links</h1>

    <p v-if="error" class="error">API error: {{ error }}</p>
    <p v-if="loading" class="loading">Loading…</p>

    <p v-if="!loading && !links.length && !error" class="empty-state">
      You haven't shared any highlights yet.
    </p>

    <div class="links">
      <article v-for="link in links" :key="link.id" class="link-card" :class="{ inactive: !link.active }">
        <div class="link-main">
          <p class="link-description">{{ link.description }}</p>
          <div class="link-meta">
            <span>{{ link.highlights_count }} highlight{{ link.highlights_count === 1 ? '' : 's' }}</span>
            <span>{{ link.visits }} visit{{ link.visits === 1 ? '' : 's' }}</span>
            <span>{{ formatDate(link.created_at) }}</span>
            <span v-if="!link.active" class="status-inactive">inactive</span>
          </div>
        </div>
        <div class="link-actions">
          <button type="button" class="btn-secondary" @click="copyLink(link)">
            {{ copiedId === link.id ? 'Copied!' : 'Copy link' }}
          </button>
          <button type="button" class="btn-secondary" @click="toggleActive(link)">
            {{ link.active ? 'Deactivate' : 'Activate' }}
          </button>
          <button type="button" class="btn-danger" @click="remove(link)">Delete</button>
        </div>
      </article>
    </div>
  </main>
</template>

<style scoped>
.shared-links-page {
  padding: 2rem;
  width: 100%;
  max-width: 720px;
  margin: 0 auto;
  box-sizing: border-box;
}

.page-title {
  font-family: var(--font-typewriter);
  font-size: 1.75rem;
  color: var(--color-purple);
  margin: 0 0 1.5rem;
}

.error {
  color: crimson;
}

.loading,
.empty-state {
  color: var(--color-text-muted);
  font-size: 0.9rem;
}

.links {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.link-card {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  padding: 1rem 1.25rem;
  border: 1px solid var(--color-border);
  border-radius: 0.75rem;
  background: var(--color-surface);
}
.link-card.inactive {
  opacity: 0.6;
}

.link-main {
  min-width: 0;
  flex: 1;
}

.link-description {
  margin: 0 0 0.35rem;
  color: var(--color-text);
  font-size: 0.95rem;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.link-meta {
  display: flex;
  gap: 0.75rem;
  font-size: 0.78rem;
  color: var(--color-text-muted);
}

.status-inactive {
  color: crimson;
}

.link-actions {
  display: flex;
  gap: 0.5rem;
  flex-shrink: 0;
}

.btn-secondary,
.btn-danger {
  padding: 0.4rem 0.85rem;
  border-radius: 999px;
  font-size: 0.8rem;
  font-family: inherit;
  cursor: pointer;
  white-space: nowrap;
}
.btn-secondary {
  border: 1px solid var(--color-purple);
  background: transparent;
  color: var(--color-purple);
}
.btn-secondary:hover {
  background: var(--color-purple);
  color: var(--color-surface);
}
.btn-danger {
  border: 1px solid crimson;
  background: transparent;
  color: crimson;
}
.btn-danger:hover {
  background: crimson;
  color: var(--color-surface);
}
</style>
