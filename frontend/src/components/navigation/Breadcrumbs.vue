<script setup>
import { computed } from 'vue';
import { RouterLink, useRoute, useRouter } from 'vue-router';
import { useI18n } from 'vue-i18n';

const route = useRoute();
const router = useRouter();
const { t } = useI18n();

const routesByName = computed(() => {
  const map = new Map();
  router.getRoutes().forEach(record => {
    if (record.name) {
      map.set(record.name, record);
    }
  });
  return map;
});

const normalizeBreadcrumb = record => {
  const meta = record.meta?.breadcrumb;
  if (meta === false) return null;

  if (typeof meta === 'function') {
    const result = meta(route);
    if (typeof result === 'string') {
      return { labelKey: result };
    }
    return {
      label: result?.label ?? record.name,
      labelKey: result?.labelKey,
      parent: result?.parent
    };
  }

  if (typeof meta === 'object' && meta !== null) {
    return {
      label: meta.label ?? record.name,
      labelKey: meta.labelKey,
      parent: meta.parent
    };
  }

  if (typeof meta === 'string') {
    return { labelKey: meta };
  }

  return { label: record.name };
};

const crumbs = computed(() => {
  const currentName = route.name;
  const map = routesByName.value;

  if (!currentName || !map.has(currentName)) {
    return [];
  }

  const trail = [];
  const visited = new Set();
  let record = map.get(currentName);

  while (record && record.name && !visited.has(record.name)) {
    visited.add(record.name);
    const meta = normalizeBreadcrumb(record);
    if (!meta) break;

    const params = record.name === route.name ? route.params : {};
    const href = router.resolve({ name: record.name, params }).href;
    trail.unshift({ label: meta.label, labelKey: meta.labelKey, href });

    if (!meta.parent) {
      break;
    }

    record = map.get(meta.parent);
  }

  return trail;
});
</script>

<template>
  <nav v-if="crumbs.length > 1" class="text-slate-500" aria-label="Breadcrumb">
    <ol class="flex flex-wrap items-center gap-2 text-xs">
      <li v-for="(crumb, index) in crumbs" :key="crumb.href" class="flex items-center gap-2">
        <RouterLink
          v-if="index < crumbs.length - 1"
          :to="crumb.href"
          class="rounded-full border border-stone-200 px-3 py-1 text-slate-600 hover:border-slate-400"
        >
          {{ crumb.labelKey ? t(crumb.labelKey) : crumb.label }}
        </RouterLink>
        <span v-else class="rounded-full bg-slate-900 px-3 py-1 text-white">
          {{ crumb.labelKey ? t(crumb.labelKey) : crumb.label }}
        </span>
        <span v-if="index < crumbs.length - 1" class="text-slate-300">/</span>
      </li>
    </ol>
  </nav>
</template>
