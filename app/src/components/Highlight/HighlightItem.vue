<script setup lang="ts">
interface Highlight {
  id?: number;
  text: string;
  author: string;
  origin: string;
  title: string;
  date: string;
}

withDefaults(defineProps<{ highlight: Highlight; public?: boolean }>(), {
  public: false,
});

const emit = defineEmits<{ 'author-click': [author: string]; share: [] }>();

function formatDate(iso: string) {
  if (!iso) return '';
  return new Date(iso).toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric' });
}
</script>

<template>
  <article class="highlight-panel">
    <p class="highlight-text">{{ highlight.text }}</p>
    <div class="highlight-meta">
      <span
        v-if="highlight.author"
        class="meta-author"
        :class="{ 'meta-author-static': $props.public }"
        @click="!$props.public && emit('author-click', highlight.author)"
      >{{ highlight.author }}</span>
      <span v-if="highlight.title" class="meta-title">{{ highlight.title }}</span>
      <span class="status-badge" :class="`status-${highlight.origin}`">{{ highlight.origin }}</span>
      <span class="meta-date">{{ formatDate(highlight.date) }}</span>
      <button
        v-if="!$props.public"
        type="button"
        class="btn-share"
        aria-label="Share this highlight"
        @click="emit('share')"
      >
        <svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true">
          <circle cx="6" cy="12" r="2.5" stroke="currentColor" stroke-width="1.6" fill="none" />
          <circle cx="18" cy="6" r="2.5" stroke="currentColor" stroke-width="1.6" fill="none" />
          <circle cx="18" cy="18" r="2.5" stroke="currentColor" stroke-width="1.6" fill="none" />
          <path d="M8.2 10.8L15.8 7.2M8.2 13.2L15.8 16.8" stroke="currentColor" stroke-width="1.6" />
        </svg>
      </button>
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
  cursor: pointer;
}
.meta-author:hover {
  text-decoration: underline;
}
.meta-author-static {
  cursor: default;
}
.meta-author-static:hover {
  text-decoration: none;
}

.btn-share {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: transparent;
  border: none;
  color: var(--color-text-muted);
  cursor: pointer;
  padding: 0.15rem;
  border-radius: 6px;
}
.btn-share:hover {
  color: var(--color-purple);
  background: var(--color-bg);
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
