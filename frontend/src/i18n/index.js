import { createI18n } from 'vue-i18n';
import en from '@/locales/en.json';
import ru from '@/locales/ru.json';

export const LOCALE_STORAGE_KEY = 'atlas_locale';

function resolveInitialLocale() {
  if (typeof window === 'undefined') {
    return 'en';
  }

  const saved = window.localStorage.getItem(LOCALE_STORAGE_KEY);
  if (saved) {
    return saved;
  }

  const browser = window.navigator.language?.toLowerCase() ?? 'en';
  if (browser.startsWith('ru')) {
    return 'ru';
  }

  return 'en';
}

const i18n = createI18n({
  legacy: false,
  locale: resolveInitialLocale(),
  fallbackLocale: 'en',
  messages: {
    en,
    ru
  }
});

export default i18n;
