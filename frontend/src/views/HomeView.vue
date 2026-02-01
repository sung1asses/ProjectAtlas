<template>
  <div class="flex flex-col gap-12 px-[--shell-padding] pb-12 text-slate-800">
    <header class="max-w-3xl space-y-4">
      <p class="mb-2 text-xs uppercase tracking-[0.35em] text-stone-500">{{ t('home.eyebrow') }}</p>
      <h1 class="text-4xl sm:text-5xl lg:text-6xl font-semibold leading-tight text-slate-900">
        {{ t('home.title') }}
      </h1>
      <p class="text-base md:text-lg text-slate-600">
        {{ t('home.body') }}
      </p>
      <router-link
        to="/projects"
        class="inline-flex items-center justify-center rounded-full bg-gradient-to-r from-slate-900 via-slate-700 to-slate-500 px-6 py-3 text-base font-semibold text-white shadow-glow"
      >
        {{ t('home.cta') }}
      </router-link>
    </header>

    <section class="grid gap-6 md:grid-cols-3">
      <article
        v-for="card in highlights"
        :key="card.title"
        class="space-y-3 rounded-[28px] border border-stone-200 bg-white p-6 text-slate-800 shadow-glow"
      >
        <p class="mb-2 text-xs uppercase tracking-[0.35em] text-slate-400">{{ card.tag }}</p>
        <h3 class="text-2xl font-semibold text-slate-900">{{ card.title }}</h3>
        <p class="text-slate-600">{{ card.copy }}</p>
      </article>
    </section>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const { t, tm } = useI18n();

const highlights = computed(() => {
  const cards = tm('home.cards');
  return Array.isArray(cards) ? cards : [];
});
</script>
