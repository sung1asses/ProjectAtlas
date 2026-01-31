<script setup>
import IconTooling from './icons/IconTooling.vue';
import IconCommunity from './icons/IconCommunity.vue';
import IconSupport from './icons/IconSupport.vue';

const tiles = [
  {
    icon: IconTooling,
    title: 'Unified Tooling',
    copy: 'Dockerized PHP + Node stack with matching UID/GID so local and server behave the same.'
  },
  {
    icon: IconCommunity,
    title: 'Vue 3 + Vite',
    copy: 'Hot-module reload during development and lean production builds served by nginx.'
  },
  {
    icon: IconSupport,
    title: 'Documented API',
    copy: 'Swagger-ready Laravel backend exposed on api.project-atlas.test with dedicated host.'
  }
];
</script>

<template>
  <section class="tiles">
    <article v-for="tile in tiles" :key="tile.title" class="tile">
      <component :is="tile.icon" class="icon" />
      <h2>{{ tile.title }}</h2>
      <p>{{ tile.copy }}</p>
    </article>
  </section>
</template>

<style scoped>
.tiles {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 1.25rem;
}

.tile {
  background: rgba(11, 16, 32, 0.75);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 20px;
  padding: 1.5rem;
  color: #dde6f5;
  min-height: 220px;
}

.icon {
  width: 2rem;
  height: 2rem;
  margin-bottom: 1rem;
  color: #3e5cff;
}

h2 {
  margin: 0 0 0.5rem;
  font-size: 1.25rem;
}

p {
  margin: 0;
  color: #9ba7bd;
  line-height: 1.5;
}
</style>
