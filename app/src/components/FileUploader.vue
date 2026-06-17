<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { api } from '../lib/api';

interface FileRecord {
  id: number;
  filename: string;
  type: string;
  status: string;
  size: number;
  created_at: string;
}

const files = ref<FileRecord[]>([]);
const uploading = ref(false);
const error = ref('');
const dragOver = ref(false);
const fileInput = ref<HTMLInputElement | null>(null);
const showUpload = ref(false);

async function loadFiles() {
  try {
    const res = await api('/files');
    if (!res.ok) return;
    const body = await res.json() as { success: boolean; data: FileRecord[] };
    if (body.success) files.value = body.data;
  } catch {
    // silent — list is non-critical
  }
}

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
    showUpload.value = false;
    await loadFiles();
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

function formatDate(iso: string) {
  return new Date(iso).toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric' });
}

function displayName(filename: string) {
  return filename.replace(/^\d+_[a-f0-9]+_/, '');
}

function formatSize(bytes: number) {
  if (bytes < 1024) return `${bytes} B`;
  if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`;
  return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
}

onMounted(loadFiles);
</script>

<template>
  <div class="clippings">
    <div class="clippings-header">
      <h1 class="page-title">My Clippings</h1>
      <button class="btn-new" @click="showUpload = !showUpload">
        <span v-if="!showUpload">+ New Upload</span>
        <span v-else>✕ Cancel</span>
      </button>
    </div>

    <div v-if="showUpload" class="upload-section">
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

    <table v-if="files.length" class="file-table">
      <thead>
        <tr>
          <th>File</th>
          <th>Type</th>
          <th>Status</th>
          <th>Size</th>
          <th>Uploaded</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="f in files" :key="f.id">
          <td class="col-name">{{ displayName(f.filename) }}</td>
          <td class="col-type">{{ f.type }}</td>
          <td class="col-status">
            <span class="status-badge" :class="`status-${f.status}`">{{ f.status }}</span>
          </td>
          <td class="col-size">{{ formatSize(f.size) }}</td>
          <td class="col-date">{{ formatDate(f.created_at) }}</td>
        </tr>
      </tbody>
    </table>

    <p v-else-if="!showUpload" class="empty-state">
      No clippings yet. Click <strong>+ New Upload</strong> to add your first file.
    </p>
  </div>
</template>

<style scoped>
.clippings {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  width: 100%;
  box-sizing: border-box;
}

.clippings-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.page-title {
  font-size: 1.25rem;
  font-family: var(--font-typewriter);
  color: var(--color-text);
  margin: 0;
}

.btn-new {
  padding: 0.4rem 1rem;
  border: 1px solid var(--color-purple);
  background: var(--color-surface);
  color: var(--color-purple);
  border-radius: 999px;
  font-size: 0.875rem;
  font-family: inherit;
  cursor: pointer;
  transition: background 0.15s, color 0.15s;
}
.btn-new:hover {
  background: var(--color-purple);
  color: var(--color-surface);
}

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
.drop-zone.is-uploading {
  cursor: default;
}

.hidden-input { display: none; }

.drop-icon {
  font-size: 1.75rem;
  line-height: 1;
}
.drop-hint {
  font-size: 0.8rem;
  opacity: 0.7;
}
code {
  font-family: monospace;
  font-size: 0.85rem;
}

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

.file-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.875rem;
}
.file-table th {
  text-align: left;
  padding: 0.5rem 0.75rem;
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: var(--color-text-muted);
  border-bottom: 1px solid var(--color-border);
  font-weight: 600;
}
.file-table td {
  padding: 0.65rem 0.75rem;
  border-bottom: 1px solid var(--color-border);
  color: var(--color-text);
  vertical-align: middle;
}
.file-table tbody tr:last-child td {
  border-bottom: none;
}
.file-table tbody tr:hover td {
  background: var(--color-bg);
}

.col-name { font-family: monospace; font-size: 0.85rem; }
.col-type { color: var(--color-text-muted); }
.col-size { color: var(--color-text-muted); white-space: nowrap; }
.col-date { color: var(--color-text-muted); white-space: nowrap; }

.status-badge {
  display: inline-block;
  padding: 0.15rem 0.5rem;
  border-radius: 999px;
  font-size: 0.72rem;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  font-weight: 600;
}
.status-pending  { background: #fef3c7; color: #92400e; }
.status-imported { background: #d1fae5; color: #065f46; }
.status-error    { background: #fee2e2; color: #991b1b; }

.empty-state {
  color: var(--color-text-muted);
  font-size: 0.9rem;
  margin: 0;
}
</style>
