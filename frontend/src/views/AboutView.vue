<template>
  <div class="flex flex-col gap-12 px-[--shell-padding] pb-12 text-slate-800">
    <header class="max-w-3xl space-y-4">
      <p class="mb-2 text-xs uppercase tracking-[0.35em] text-stone-500">{{ t('about.eyebrow') }}</p>
      <h1 class="text-4xl sm:text-5xl font-semibold text-slate-900">
        {{ t('about.title') }}
      </h1>
      <p class="text-base md:text-lg text-slate-600">
        {{ t('about.body') }}
      </p>
    </header>

    <section class="grid gap-6 md:grid-cols-2">
      <article class="space-y-4 rounded-[28px] border border-stone-200 bg-white p-6 text-slate-800 shadow-glow">
        <h3 class="text-2xl font-semibold text-slate-900">{{ t('about.approachTitle') }}</h3>
        <p class="text-slate-600">
          {{ t('about.approachBody') }}
        </p>
      </article>
      <article class="space-y-4 rounded-[28px] border border-stone-200 bg-white p-6 text-slate-800 shadow-glow">
        <h3 class="text-2xl font-semibold text-slate-900">{{ t('about.principlesTitle') }}</h3>
        <ul class="space-y-2 text-slate-600 list-disc pl-5">
          <li v-for="(principle, index) in principles" :key="index">{{ principle }}</li>
        </ul>
      </article>
    </section>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const { t, tm } = useI18n();

const principles = computed(() => {
  const list = tm('about.principles');
  return Array.isArray(list) ? list : [];
});
</script>
