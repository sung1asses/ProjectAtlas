<script setup>
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const linkBlueprint = [
  { labelKey: 'landing.links.backend', href: 'http://api.project-atlas.test', external: false },
  { labelKey: 'landing.links.swagger', href: 'http://api.project-atlas.test/swagger', external: false },
  { labelKey: 'landing.links.github', href: 'https://github.com/sung1asses/ProjectAtlas', external: true }
];

const links = computed(() =>
  linkBlueprint.map(link => ({
    ...link,
    label: t(link.labelKey)
  }))
);
</script>

<template>
  <section class="grid gap-6 rounded-[28px] border border-stone-200 bg-white/90 p-6 text-slate-800 shadow-glow">
    <div class="space-y-4">
      <p class="mb-2 text-xs uppercase tracking-[0.4em] text-stone-500">{{ t('landing.eyebrow') }}</p>
      <h1 class="text-[clamp(2.25rem,6vw,3.75rem)] font-semibold text-slate-900">{{ t('landing.title') }}</h1>
      <p class="text-base text-slate-600">
        {{ t('landing.body') }}
      </p>
    </div>
    <nav class="flex flex-wrap gap-4">
      <a
        v-for="link in links"
        :key="link.label"
        class="inline-flex items-center justify-center rounded-full border border-slate-300 px-4 py-2 font-semibold text-slate-600 transition-colors hover:bg-slate-900 hover:text-white"
        :href="link.href"
        :target="link.external ? '_blank' : undefined"
      >
        {{ link.label }}
      </a>
    </nav>
  </section>
</template>
