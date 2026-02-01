<script setup>
import { computed } from 'vue';
import { RouterLink, RouterView, useRoute } from 'vue-router';
import { useI18n } from 'vue-i18n';
import Breadcrumbs from './components/navigation/Breadcrumbs.vue';
import { LOCALE_STORAGE_KEY } from './i18n';

const route = useRoute();
const { t, locale } = useI18n();

const navLinks = computed(() => [
  { to: '/', label: t('nav.home') },
  { to: '/about', label: t('nav.about') },
  { to: '/projects', label: t('nav.projects') }
]);

const isActive = to => {
  if (to === '/') return route.path === '/';
  return route.path.startsWith(to);
};

const activeLabel = computed(() => navLinks.value.find(link => isActive(link.to))?.label ?? t('nav.home'));
const currentYear = new Date().getFullYear();

const navClasses = to => [
  'rounded-full px-4 py-2 text-sm font-medium transition-colors',
  isActive(to) ? 'bg-slate-900 text-white' : 'text-slate-500 hover:bg-slate-100'
];

const languages = [
  { code: 'en', labelKey: 'lang.en' },
  { code: 'ru', labelKey: 'lang.ru' }
];

const switchLocale = lang => {
  locale.value = lang;
  if (typeof window !== 'undefined') {
    window.localStorage.setItem(LOCALE_STORAGE_KEY, lang);
  }
};
</script>

<template>
  <div class="min-h-screen flex flex-col gap-8 px-[--shell-padding] pt-6 pb-10 text-slate-800">
    <header
      class="flex flex-col gap-4 rounded-[28px] border border-stone-200 bg-white/80 px-5 py-4 shadow-glow backdrop-blur-lg sm:flex-row sm:items-center sm:justify-between"
      :aria-label="`Current section: ${activeLabel}`"
    >
      <RouterLink to="/" class="text-lg font-semibold tracking-[0.3em] uppercase text-slate-600" aria-label="Atlas home">
        Atlas
      </RouterLink>
      <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:gap-4">
        <nav class="flex flex-wrap gap-2">
          <RouterLink
            v-for="link in navLinks"
            :key="link.to"
            :to="link.to"
            :class="navClasses(link.to)"
            :aria-current="isActive(link.to) ? 'page' : null"
          >
            {{ link.label }}
          </RouterLink>
        </nav>
        <div class="flex items-center gap-1 rounded-full border border-stone-200 px-1 py-1">
          <button
            v-for="option in languages"
            :key="option.code"
            type="button"
            class="rounded-full px-3 py-1 text-xs font-semibold transition-colors"
            :class="option.code === locale ? 'bg-slate-900 text-white' : 'text-slate-500 hover:bg-slate-100'"
            @click="switchLocale(option.code)"
          >
            {{ t(option.labelKey) }}
          </button>
        </div>
      </div>
    </header>

    <Breadcrumbs />

    <main class="flex-1">
      <RouterView />
    </main>

    <footer class="border-t border-stone-200 pt-6 text-center text-sm text-slate-500">
      <p>© {{ currentYear }} {{ t('footer.tagline') }}</p>
    </footer>
  </div>
</template>
