<script setup>
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import IconTooling from './icons/IconTooling.vue';
import IconCommunity from './icons/IconCommunity.vue';
import IconSupport from './icons/IconSupport.vue';

const { tm } = useI18n();

const iconMap = [IconTooling, IconCommunity, IconSupport];

const tiles = computed(() => {
  const messages = tm('tiles');
  if (!Array.isArray(messages)) {
    return [];
  }
  return messages.map((tile, index) => ({
    ...tile,
    icon: iconMap[index] ?? IconTooling
  }));
});
</script>

<template>
  <section class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
    <article
      v-for="tile in tiles"
      :key="tile.title"
      class="flex min-h-[220px] flex-col rounded-2xl border border-stone-200 bg-white p-6 text-slate-800 shadow-glow"
    >
      <component :is="tile.icon" class="mb-4 h-8 w-8 text-slate-900" />
      <h2 class="mb-2 text-xl font-semibold text-slate-900">{{ tile.title }}</h2>
      <p class="text-sm text-slate-600">{{ tile.copy }}</p>
    </article>
  </section>
</template>
