<script setup lang="ts">
import { ref } from 'vue';
import { api } from '../../lib/api';

const emit = defineEmits<{ uploaded: [] }>();

const uploading = ref(false);
const error = ref('');
const dragOver = ref(false);
const fileInput = ref<HTMLInputElement | null>(null);

async function uploadFile(file: File) {
  error.value = '';
  if (!file.name.endsWith('.txt')) {
    error.value = 'Only .txt files are supported.';
    return;
  }
  uploading.value = true;
  try {
    const form = new FormData();
    form.append('file', file);
    const res = await api('/files/upload', { method: 'POST', body: form });
    const body = await res.json();
    if (!res.ok || !body.success) {
      error.value = body.error ?? `Upload failed (${res.status})`;
      return;
    }
    emit('uploaded');
  } catch {
    error.value = 'Upload failed. Please try again.';
  } finally {
    uploading.value = false;
    if (fileInput.value) fileInput.value.value = '';
  }
}

function onFileInput(e: Event) {
  const file = (e.target as HTMLInputElement).files?.[0];
  if (file) uploadFile(file);
}

function onDrop(e: DragEvent) {
  dragOver.value = false;
  const file = e.dataTransfer?.files?.[0];
  if (file) uploadFile(file);
}
</script>

<template>
  <div class="upload-section">
    <div
      class="drop-zone"
      :class="{ 'drag-active': dragOver, 'is-uploading': uploading }"
      @dragover.prevent="dragOver = true"
      @dragleave.prevent="dragOver = false"
      @drop.prevent="onDrop"
      @click="!uploading && fileInput?.click()"
    >
      <input
        ref="fileInput"
        type="file"
        accept=".txt"
        class="hidden-input"
        @change="onFileInput"
      />
      <template v-if="uploading">
        <span class="spinner" aria-hidden="true" />
        <span>Uploading…</span>
      </template>
      <template v-else>
        <span class="drop-icon" aria-hidden="true">📄</span>
        <span>Drop <code>My Clippings.txt</code> here</span>
        <span class="drop-hint">or click to browse</span>
      </template>
    </div>
    <p v-if="error" class="upload-error">{{ error }}</p>
  </div>
</template>

<style scoped>
.upload-section {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.drop-zone {
  border: 1.5px dashed var(--color-border);
  border-radius: 8px;
  padding: 3rem 1rem;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.25rem;
  cursor: pointer;
  font-size: 0.875rem;
  color: var(--color-text-muted);
  text-align: center;
  transition: border-color 0.15s, background 0.15s;
  user-select: none;
  width: 100%;
  box-sizing: border-box;
}
.drop-zone:hover {
  border-color: var(--color-purple);
  background: color-mix(in srgb, var(--color-purple) 4%, transparent);
}
.drop-zone.drag-active {
  border-color: var(--color-purple);
  background: color-mix(in srgb, var(--color-purple) 8%, transparent);
  color: var(--color-text);
}
.drop-zone.is-uploading { cursor: default; }

.hidden-input { display: none; }
.drop-icon { font-size: 1.75rem; line-height: 1; }
.drop-hint { font-size: 0.8rem; opacity: 0.7; }
code { font-family: monospace; font-size: 0.85rem; }

.spinner {
  display: inline-block;
  width: 20px;
  height: 20px;
  border: 2px solid var(--color-border);
  border-top-color: var(--color-purple);
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

.upload-error {
  margin: 0;
  font-size: 0.85rem;
  color: #c0392b;
}
</style>
