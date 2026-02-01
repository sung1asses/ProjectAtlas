<template>
  <div class="flex flex-col gap-12 px-[--shell-padding] pb-12 text-slate-800">
    <header class="max-w-3xl space-y-4">
      <p class="mb-2 text-xs uppercase tracking-[0.35em] text-stone-500">{{ t('projects.eyebrow') }}</p>
      <h1 class="text-4xl sm:text-5xl font-semibold text-slate-900">
        {{ t('projects.title') }}
      </h1>
      <p class="text-base md:text-lg text-slate-600">
        {{ t('projects.body') }}
      </p>
    </header>

    <section v-if="isLoading" class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
      <article
        v-for="n in 3"
        :key="n"
        class="animate-pulse rounded-[28px] border border-stone-200 bg-white p-6"
      >
        <div class="mb-4 h-6 w-24 rounded-full bg-stone-200" />
        <div class="mb-3 h-6 w-3/4 rounded bg-stone-100" />
        <div class="h-24 rounded bg-stone-50" />
      </article>
    </section>

    <section v-else-if="errorKey" class="rounded-2xl border border-rose-200 bg-rose-50 p-6 text-rose-600">
      <p class="font-semibold">{{ errorMessage }}</p>
      <button
        type="button"
        class="mt-4 inline-flex items-center gap-2 rounded-full border border-rose-200 px-4 py-2 text-sm text-rose-600"
        @click="loadProjects"
      >
        {{ t('projects.retry') }}
      </button>
    </section>

    <section v-else class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
        <article
        v-for="project in projects"
        :key="project.slug"
        class="flex flex-col space-y-3 rounded-[28px] border border-stone-200 bg-white p-6 text-slate-800 shadow-glow"
      >
        <p class="mb-2 text-xs uppercase tracking-[0.35em] text-stone-400">{{ project.industry }}</p>
        <h3 class="text-2xl font-semibold text-slate-900">{{ project.name }}</h3>
        <p class="text-slate-600 flex-1">{{ project.summary }}</p>
        <router-link
          :to="`/projects/${project.slug}`"
          class="text-slate-600 font-medium inline-flex items-center gap-2"
        >
          {{ t('projects.cta') }}
          <span aria-hidden="true">→</span>
        </router-link>
      </article>
    </section>
  </div>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { fetchProjects } from '@/services/projectsApi.js';

const projects = ref([]);
const isLoading = ref(true);
const errorKey = ref('');

const { t, locale } = useI18n();
const errorMessage = computed(() => (errorKey.value ? t(errorKey.value) : ''));

const loadProjects = async () => {
  try {
    errorKey.value = '';
    isLoading.value = true;
    projects.value = await fetchProjects();
  } catch (err) {
    errorKey.value = 'projects.error';
    console.error(err);
  } finally {
    isLoading.value = false;
  }
};

onMounted(loadProjects);
watch(locale, () => {
  loadProjects();
});
</script>
