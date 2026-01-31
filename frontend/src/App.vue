<script setup>
import TheHero from './components/TheHero.vue';
import TechTiles from './components/TechTiles.vue';
</script>

<template>
  <main>
    <TheHero />
    <TechTiles />
  </main>
</template>

<style scoped>
main {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  gap: 3rem;
  padding: 2rem clamp(1.5rem, 5vw, 6rem);
}
</style>
