<script setup lang="ts">
import { ref, watch } from 'vue';
import { api } from '../../lib/api';

interface FileRecord {
  id: number;
  filename: string;
  type: string;
  status: string;
  size: number;
  created_at: string;
}

interface ImportRecord {
  id: number;
  file_id: number;
  status: string;
  highlight_count: number;
  execution_time: number;
  info: string;
  imported_date: string;
}

const props = defineProps<{ file: FileRecord }>();
const emit = defineEmits<{ close: []; imported: [] }>();

const selectedImport = ref<ImportRecord | null>(null);
const loadingImport = ref(false);
const importing = ref(false);
const importError = ref('');

async function loadImport() {
  selectedImport.value = null;
  importError.value = '';
  loadingImport.value = true;
  try {
    const res = await api(`/files/${props.file.id}/import`);
    const body = await res.json() as { success: boolean; data: ImportRecord | null };
    if (body.success) selectedImport.value = body.data;
  } catch {
    // silent
  } finally {
    loadingImport.value = false;
  }
}

async function runImport() {
  importing.value = true;
  importError.value = '';
  try {
    const res = await api(`/files/${props.file.id}/import`, { method: 'POST' });
    const body = await res.json() as { success: boolean; data: ImportRecord; error?: string };
    if (!res.ok || !body.success) {
      importError.value = body.error ?? `Import failed (${res.status})`;
      return;
    }
    selectedImport.value = body.data;
    emit('imported');
  } catch {
    importError.value = 'Import failed. Please try again.';
  } finally {
    importing.value = false;
  }
}

function displayName(filename: string) {
  return filename.replace(/^\d+_[a-f0-9]+_/, '');
}

function formatDate(iso: string) {
  return new Date(iso).toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric' });
}

function formatDateTime(iso: string) {
  return new Date(iso).toLocaleString(undefined, { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}

function formatSize(bytes: number) {
  if (bytes < 1024) return `${bytes} B`;
  if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`;
  return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
}

function skippedCount(imp: ImportRecord): number {
  try {
    return JSON.parse(imp.info ?? '{}').skipped ?? 0;
  } catch {
    return 0;
  }
}

watch(() => props.file.id, loadImport, { immediate: true });
</script>

<template>
  <Teleport to="body">
    <div class="modal-overlay" @click.self="emit('close')">
      <div class="modal">
        <button class="modal-close" @click="emit('close')">✕</button>

        <h2 class="modal-title">{{ displayName(file.filename) }}</h2>

        <div class="modal-meta">
          <div class="meta-row">
            <span class="meta-label">Type</span>
            <span>{{ file.type }}</span>
          </div>
          <div class="meta-row">
            <span class="meta-label">Size</span>
            <span>{{ formatSize(file.size) }}</span>
          </div>
          <div class="meta-row">
            <span class="meta-label">Status</span>
            <span class="status-badge" :class="`status-${file.status}`">{{ file.status }}</span>
          </div>
          <div class="meta-row">
            <span class="meta-label">Uploaded</span>
            <span>{{ formatDate(file.created_at) }}</span>
          </div>
        </div>

        <div class="modal-section">
          <h3 class="section-title">Import</h3>

          <div v-if="loadingImport" class="import-loading">
            <span class="spinner" />
          </div>

          <div v-else-if="selectedImport" class="import-result">
            <div class="meta-row">
              <span class="meta-label">Date</span>
              <span>{{ formatDateTime(selectedImport.imported_date) }}</span>
            </div>
            <div class="meta-row">
              <span class="meta-label">Highlights</span>
              <span>{{ selectedImport.highlight_count }}</span>
            </div>
            <div class="meta-row">
              <span class="meta-label">Skipped</span>
              <span>{{ skippedCount(selectedImport) }}</span>
            </div>
            <div class="meta-row">
              <span class="meta-label">Time</span>
              <span>{{ selectedImport.execution_time }} ms</span>
            </div>
          </div>

          <p v-else class="no-import">This file has not been imported yet.</p>

          <p v-if="importError" class="import-error">{{ importError }}</p>

          <button class="btn-import" :disabled="importing" @click="runImport">
            <span v-if="importing"><span class="spinner spinner-sm" /> Importing…</span>
            <span v-else>{{ selectedImport ? 'Re-run Import' : 'Run Import' }}</span>
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<style scoped>
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.45);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 100;
}

.modal {
  background: var(--color-surface);
  border-radius: 10px;
  padding: 2rem;
  width: 100%;
  max-width: 480px;
  position: relative;
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
  box-shadow: 0 8px 32px rgba(0,0,0,0.18);
}

.modal-close {
  position: absolute;
  top: 1rem;
  right: 1rem;
  background: none;
  border: none;
  font-size: 1rem;
  color: var(--color-text-muted);
  cursor: pointer;
  line-height: 1;
}
.modal-close:hover { color: var(--color-text); }

.modal-title {
  font-size: 1rem;
  font-family: monospace;
  margin: 0;
  padding-right: 1.5rem;
  color: var(--color-text);
  word-break: break-all;
}

.modal-meta {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.meta-row {
  display: flex;
  justify-content: space-between;
  font-size: 0.875rem;
  gap: 1rem;
}

.meta-label {
  color: var(--color-text-muted);
  flex-shrink: 0;
}

.modal-section {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  border-top: 1px solid var(--color-border);
  padding-top: 1.25rem;
}

.section-title {
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: var(--color-text-muted);
  margin: 0;
  font-weight: 600;
}

.import-loading {
  display: flex;
  justify-content: center;
  padding: 0.5rem 0;
}

.import-result {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.no-import {
  margin: 0;
  font-size: 0.875rem;
  color: var(--color-text-muted);
}

.import-error {
  margin: 0;
  font-size: 0.85rem;
  color: #c0392b;
}

.spinner {
  display: inline-block;
  width: 20px;
  height: 20px;
  border: 2px solid var(--color-border);
  border-top-color: var(--color-purple);
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
  vertical-align: middle;
}
.spinner-sm { width: 14px; height: 14px; }
@keyframes spin { to { transform: rotate(360deg); } }

.btn-import {
  padding: 0.5rem 1.25rem;
  background: var(--color-purple);
  color: #fff;
  border: none;
  border-radius: 999px;
  font-size: 0.875rem;
  font-family: inherit;
  cursor: pointer;
  align-self: flex-start;
  transition: opacity 0.15s;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
.btn-import:disabled { opacity: 0.6; cursor: default; }
.btn-import:not(:disabled):hover { opacity: 0.85; }

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
</style>
