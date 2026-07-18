<script setup lang="ts">
interface Highlight {
  id: number;
  text: string;
  author: string;
  origin: string;
  title: string;
  date: string;
}

defineProps<{ highlight: Highlight }>();

function formatDate(iso: string) {
  if (!iso) return '';
  return new Date(iso).toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric' });
}
</script>

<template>
  <article class="highlight-panel">
    <p class="highlight-text">{{ highlight.text }}</p>
    <div class="highlight-meta">
      <span v-if="highlight.author" class="meta-author">{{ highlight.author }}</span>
      <span v-if="highlight.title" class="meta-title">{{ highlight.title }}</span>
      <span class="status-badge" :class="`status-${highlight.origin}`">{{ highlight.origin }}</span>
      <span class="meta-date">{{ formatDate(highlight.date) }}</span>
    </div>
  </article>
</template>

<style scoped>
.highlight-panel {
  padding: 1rem 1.25rem;
  border: 1px solid var(--color-border);
  border-radius: 0.75rem;
  background: var(--color-surface);
  display: flex;
  flex-direction: column;
  gap: 0.6rem;
}

.highlight-text {
  margin: 0;
  color: var(--color-text);
  font-size: 0.95rem;
  line-height: 1.5;
}

.highlight-meta {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 0.6rem;
  font-size: 0.8rem;
  color: var(--color-text-muted);
}

.meta-author {
  font-weight: 600;
  color: var(--color-purple);
}

.meta-title {
  font-style: italic;
}

.meta-date {
  margin-left: auto;
  white-space: nowrap;
}

.status-badge {
  display: inline-block;
  padding: 0.15rem 0.5rem;
  border-radius: 999px;
  font-size: 0.72rem;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  font-weight: 600;
}
.status-kindle  { background: #ede9fe; color: #5b21b6; }
.status-web     { background: #e0f2fe; color: #075985; }
.status-twitter { background: #d1fae5; color: #065f46; }
</style>
