<script setup>
const links = [
  { label: 'Backend API', href: 'http://api.project-atlas.test', external: false },
  { label: 'Swagger Docs', href: 'http://api.project-atlas.test/swagger', external: false },
  { label: 'GitHub Repo', href: 'https://github.com/sung1asses/ProjectAtlas', external: true }
];
</script>

<template>
  <section class="hero">
    <div>
      <p class="eyebrow">Project Atlas</p>
      <h1>Craft your portfolio in one stack</h1>
      <p class="lede">
        Vue powers the interactive portfolio while Laravel handles content, auth, and integrations.
        Both live inside the same Docker environment so deployments stay predictable.
      </p>
    </div>
    <nav>
      <a
        v-for="link in links"
        :key="link.label"
        class="cta"
        :href="link.href"
        :target="link.external ? '_blank' : undefined"
        >{{ link.label }}</a
      >
    </nav>
  </section>
</template>

<style scoped>
.hero {
  display: grid;
  gap: 1.5rem;
}

.eyebrow {
  text-transform: uppercase;
  letter-spacing: 0.4em;
  font-size: 0.75rem;
  color: #7f8ea3;
}

h1 {
  font-size: clamp(2.25rem, 6vw, 3.75rem);
  margin: 0.5rem 0;
  color: #f4f7fb;
}

.lede {
  color: #bcc5d3;
  line-height: 1.6;
  max-width: 40rem;
}

nav {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
}

.cta {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0.85rem 1.4rem;
  border-radius: 999px;
  border: 1px solid #3e5cff;
  color: #f4f7fb;
  font-weight: 600;
  text-decoration: none;
  transition: background 0.25s ease, color 0.25s ease;
}

.cta:hover {
  background: #3e5cff;
  color: #0b1020;
}
</style>
