<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { api } from '../../lib/api';
import { useAuth } from '../../composables/useAuth';
import FileUploader from './FileUploader.vue';
import FileModal from './FileModal.vue';

interface FileRecord {
  id: number;
  filename: string;
  type: string;
  status: string;
  size: number;
  created_at: string;
}

const router = useRouter();
const { whenReady } = useAuth();

const files = ref<FileRecord[]>([]);
const showUpload = ref(false);
const selectedFile = ref<FileRecord | null>(null);

async function loadFiles() {
  try {
    await whenReady();
    const res = await api('/files');
    if (!res.ok) return;
    const body = await res.json() as { success: boolean; data: FileRecord[] };
    if (body.success) files.value = body.data;
  } catch {
    // silent
  }
}

function openFile(f: FileRecord) {
  selectedFile.value = f;
}

function closeModal() {
  selectedFile.value = null;
}

async function onUploaded() {
  showUpload.value = false;
  await loadFiles();
}

async function onImported() {
  await loadFiles();
  const updated = files.value.find(f => f.id === selectedFile.value?.id);
  if (updated) selectedFile.value = updated;
}

function displayName(filename: string) {
  return filename.replace(/^\d+_[a-f0-9]+_/, '');
}

function formatDate(iso: string) {
  return new Date(iso).toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric' });
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
      <div class="title-group">
        <button class="btn-back" type="button" aria-label="Back to search" @click="router.push({ name: 'search' })">
          &larr;
        </button>
        <h1 class="page-title">My Clippings</h1>
      </div>
      <button class="btn-new" @click="showUpload = !showUpload">
        <span v-if="!showUpload">+ New Upload</span>
        <span v-else>✕ Cancel</span>
      </button>
    </div>

    <FileUploader v-if="showUpload" @uploaded="onUploaded" />

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
        <tr v-for="f in files" :key="f.id" class="file-row" @click="openFile(f)">
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

    <FileModal
      v-if="selectedFile"
      :file="selectedFile"
      @close="closeModal"
      @imported="onImported"
    />
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

.title-group {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.page-title {
  font-size: 1.25rem;
  font-family: var(--font-typewriter);
  color: var(--color-text);
  margin: 0;
}

.btn-back {
  background: none;
  border: none;
  font-size: 1.75rem;
  line-height: 1;
  cursor: pointer;
  color: var(--color-text);
  padding: 0.25rem;
  display: flex;
  align-items: center;
}
.btn-back:hover {
  color: var(--color-purple);
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
.file-table tbody tr:last-child td { border-bottom: none; }
.file-row { cursor: pointer; }
.file-row:hover td { background: var(--color-bg); }

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
.status-pending    { background: #fef3c7; color: #92400e; }
.status-uploaded   { background: #e0f2fe; color: #075985; }
.status-processing { background: #ede9fe; color: #5b21b6; }
.status-imported   { background: #d1fae5; color: #065f46; }
.status-active     { background: #f1f5f9; color: #475569; }
.status-error      { background: #fee2e2; color: #991b1b; }

.empty-state {
  color: var(--color-text-muted);
  font-size: 0.9rem;
  margin: 0;
}
</style>
