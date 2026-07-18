<script setup lang="ts">
import { ref, computed } from 'vue';
import { api } from '../../lib/api';

const props = defineProps<{ highlightIds: number[] }>();
const emit = defineEmits<{ close: [] }>();

const description = ref('');
const loading = ref(false);
const error = ref<string | null>(null);
const shareUrl = ref<string | null>(null);
const copied = ref(false);

const itemLabel = computed(() =>
  props.highlightIds.length === 1 ? '1 highlight' : `${props.highlightIds.length} highlights`
);

async function createShare() {
  loading.value = true;
  error.value = null;
  try {
    const body: Record<string, unknown> = { highlight_ids: props.highlightIds };
    if (description.value.trim()) body.description = description.value.trim();

    const res = await api('/shared-links', {
      method: 'POST',
      body: JSON.stringify(body),
    });
    if (!res.ok) throw new Error(`API ${res.status}`);
    const result = (await res.json()) as { success: boolean; data: { uuid: string } };

    shareUrl.value = `${window.location.origin}/shared/${result.data.uuid}`;
  } catch (e) {
    error.value = e instanceof Error ? e.message : 'unknown error';
  } finally {
    loading.value = false;
  }
}

async function copyLink() {
  if (!shareUrl.value) return;
  await navigator.clipboard.writeText(shareUrl.value);
  copied.value = true;
  setTimeout(() => (copied.value = false), 1500);
}
</script>

<template>
  <Teleport to="body">
    <div class="modal-backdrop" @click="emit('close')">
      <div class="modal" role="dialog" aria-modal="true" aria-label="Share highlights" @click.stop>
        <header class="modal-header">
          <span class="modal-title">Share {{ itemLabel }}</span>
          <button type="button" class="close-btn" aria-label="Close" @click="emit('close')">
            <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true">
              <path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
            </svg>
          </button>
        </header>

        <div class="modal-body">
          <template v-if="!shareUrl">
            <label class="field-label" for="share-description">Description (optional)</label>
            <input
              id="share-description"
              v-model="description"
              type="text"
              maxlength="100"
              placeholder="Leave blank to auto-generate"
              @keyup.enter="createShare"
            />
            <p v-if="error" class="error">{{ error }}</p>
            <button class="btn-primary" :disabled="loading" @click="createShare">
              {{ loading ? 'Creating…' : 'Create share link' }}
            </button>
          </template>

          <template v-else>
            <p class="share-ready">Your link is ready:</p>
            <div class="link-row">
              <input class="link-input" type="text" readonly :value="shareUrl" @click="($event.target as HTMLInputElement).select()" />
              <button class="btn-primary" @click="copyLink">{{ copied ? 'Copied!' : 'Copy' }}</button>
            </div>
          </template>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<style scoped>
.modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.35);
  z-index: 200;
  display: flex;
  align-items: center;
  justify-content: center;
}
.modal {
  width: min(420px, 90vw);
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: 0.75rem;
  box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
  font-family: var(--font-typewriter);
}
.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.85rem 1rem;
  border-bottom: 1px solid var(--color-border);
}
.modal-title {
  font-size: 0.95rem;
  color: var(--color-text);
}
.close-btn {
  background: transparent;
  border: none;
  color: var(--color-text-muted);
  cursor: pointer;
  padding: 0.25rem;
  border-radius: 6px;
  display: inline-flex;
}
.close-btn:hover {
  color: var(--color-text);
  background: var(--color-bg);
}
.modal-body {
  padding: 1rem;
  display: flex;
  flex-direction: column;
  gap: 0.6rem;
}
.field-label {
  font-size: 0.8rem;
  color: var(--color-text-muted);
}
input[type='text'] {
  padding: 0.55rem 0.75rem;
  border: 1px solid var(--color-border);
  border-radius: 0.5rem;
  font-family: inherit;
  font-size: 0.9rem;
  color: var(--color-text);
  background: var(--color-bg);
}
.link-row {
  display: flex;
  gap: 0.5rem;
}
.link-input {
  flex: 1;
}
.share-ready {
  font-size: 0.9rem;
  color: var(--color-text-muted);
  margin: 0;
}
.btn-primary {
  padding: 0.55rem 1rem;
  border: 1px solid var(--color-purple);
  background: var(--color-purple);
  color: var(--color-surface);
  border-radius: 999px;
  font-size: 0.9rem;
  font-family: inherit;
  cursor: pointer;
  transition: opacity 0.15s;
}
.btn-primary:hover:not(:disabled) {
  opacity: 0.85;
}
.btn-primary:disabled {
  opacity: 0.6;
  cursor: default;
}
.error {
  color: crimson;
  font-size: 0.85rem;
  margin: 0;
}
</style>
