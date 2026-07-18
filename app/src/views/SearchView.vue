<script setup lang="ts">
import { ref } from 'vue';
import HighlightList from '../components/Highlight/HighlightList.vue';

const qInput = ref('');
const authorInput = ref('');
const showAuthor = ref(false);

const submittedQ = ref('');
const submittedAuthor = ref('');
const hasSearched = ref(false);

function submit() {
  if (!qInput.value.trim() && !authorInput.value.trim()) return;
  submittedQ.value = qInput.value.trim();
  submittedAuthor.value = authorInput.value.trim();
  hasSearched.value = true;
}

function onAuthorClick(author: string) {
  authorInput.value = author;
  showAuthor.value = true;
  window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>

<template>
  <main class="search-page" :class="{ compact: hasSearched }">
    <h1 class="brand">Dunedin Search</h1>

    <div class="search-box">
      <input
        v-model="qInput"
        type="text"
        class="search-input"
        placeholder="Search your highlights…"
        @keyup.enter="submit"
      />
      <button class="btn-search" @click="submit">Search</button>
    </div>

    <div class="author-toggle">
      <a href="#" @click.prevent="showAuthor = !showAuthor">
        {{ showAuthor ? '- hide author search' : '+ search by author' }}
      </a>
    </div>

    <div v-if="showAuthor" class="search-box author-box">
      <input
        v-model="authorInput"
        type="text"
        class="search-input"
        placeholder="Author…"
        @keyup.enter="submit"
      />
    </div>

    <div v-if="hasSearched" class="results-wrap">
      <HighlightList :q="submittedQ" :author="submittedAuthor" @author-click="onAuthorClick" />
    </div>
  </main>
</template>

<style scoped>
.search-page {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1.25rem;
  width: 100%;
  box-sizing: border-box;
  margin: 0 auto;
  padding: 2rem 8vw;
  transition: padding-top 0.2s;
}

.search-page:not(.compact) {
  padding-top: 18vh;
}

.brand {
  font-family: var(--font-typewriter);
  font-size: 2.25rem;
  color: var(--color-purple);
  margin: 0;
}

.search-box {
  display: flex;
  gap: 0.5rem;
  width: 100%;
}

.search-input {
  flex: 1;
  padding: 1rem 1.5rem;
  border: 1px solid var(--color-border);
  border-radius: 999px;
  font-family: inherit;
  font-size: 1.15rem;
  color: var(--color-text);
  background: var(--color-surface);
}
.search-input:focus {
  outline: none;
  border-color: var(--color-purple);
}

.btn-search {
  padding: 1rem 2rem;
  border: 1px solid var(--color-purple);
  background: var(--color-purple);
  color: var(--color-surface);
  font-size: 1rem;
  border-radius: 999px;
  font-family: inherit;
  cursor: pointer;
  transition: opacity 0.15s;
}
.btn-search:hover {
  opacity: 0.85;
}

.author-toggle {
  align-self: center;
  font-size: 0.85rem;
}
.author-toggle a {
  color: var(--color-text-muted);
  text-decoration: none;
}
.author-toggle a:hover {
  color: var(--color-purple);
}

.author-box {
  width: 100%;
}

.results-wrap {
  width: 100%;
  margin-top: 1rem;
}
</style>
