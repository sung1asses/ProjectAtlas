<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { useRoute } from 'vue-router';
import { fetchProjectDetail } from '@/services/projectsApi.js';

const route = useRoute();

const project = ref(null);
const isLoading = ref(true);
const errorKey = ref('');

const slug = computed(() => String(route.params.slug ?? 'project'));
const formattedSlug = computed(() => slug.value.replace(/-/g, ' '));
const displayName = computed(() => project.value?.name ?? formattedSlug.value);

const { t, locale } = useI18n();
const errorMessage = computed(() => (errorKey.value ? t(errorKey.value) : ''));

const loadProject = async () => {
  try {
    errorKey.value = '';
    isLoading.value = true;
    project.value = null;
    const data = await fetchProjectDetail(slug.value);
    if (!data) {
      errorKey.value = 'projectDetail.notFound';
      return;
    }
    project.value = data;
  } catch (err) {
    errorKey.value = 'projectDetail.error';
    console.error(err);
  } finally {
    isLoading.value = false;
  }
};

onMounted(loadProject);
watch(() => route.params.slug, loadProject);
watch(locale, () => {
  loadProject();
});
</script>

<template>
  <div class="flex flex-col gap-8 px-[--shell-padding] pb-12 text-slate-800">
    <header class="max-w-3xl space-y-4">
      <p class="mb-2 text-xs uppercase tracking-[0.35em] text-stone-500">{{ t('projectDetail.eyebrow') }}</p>
      <h1 class="text-4xl sm:text-5xl font-semibold text-slate-900">
        {{ t('projectDetail.title', { name: displayName }) }}
      </h1>
      <p class="text-base md:text-lg text-slate-600">
        {{ t('projectDetail.body') }}
      </p>
      <router-link to="/projects" class="text-base font-medium inline-flex items-center gap-2">
        {{ t('projectDetail.back') }}
      </router-link>
    </header>

    <section class="rounded-[28px] border border-stone-200 bg-white p-6 text-slate-800 shadow-glow">
      <div v-if="isLoading" class="space-y-4">
        <div class="h-6 w-1/2 rounded bg-stone-200" />
        <div class="h-24 rounded bg-stone-100" />
        <div class="h-24 rounded bg-stone-100" />
      </div>

      <div v-else-if="errorKey" class="space-y-3 text-rose-600">
        <p class="font-semibold">{{ errorMessage }}</p>
        <button
          type="button"
          class="inline-flex items-center gap-2 rounded-full border border-rose-200 px-4 py-2 text-sm"
          @click="loadProject"
        >
          {{ t('projectDetail.retry') }}
        </button>
      </div>

      <article v-else class="space-y-4">
        <p class="text-sm uppercase tracking-[0.3em] text-stone-400">{{ project?.industry }}</p>
        <p class="text-lg text-slate-600">{{ project?.summary }}</p>
        <p class="leading-relaxed text-slate-700">{{ project?.body }}</p>
      </article>
    </section>
  </div>
</template>
