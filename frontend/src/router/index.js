import { createRouter, createWebHistory } from 'vue-router';
import HomeView from '@/views/HomeView.vue';
import AboutView from '@/views/AboutView.vue';
import ProjectsView from '@/views/ProjectsView.vue';
const ProjectDetailView = () => import('@/views/ProjectDetailView.vue');

const routes = [
  {
    path: '/',
    name: 'home',
    component: HomeView,
    meta: { breadcrumb: { labelKey: 'nav.home' } }
  },
  {
    path: '/about',
    name: 'about',
    component: AboutView,
    meta: { breadcrumb: { labelKey: 'nav.about', parent: 'home' } }
  },
  {
    path: '/projects',
    name: 'projects',
    component: ProjectsView,
    meta: { breadcrumb: { labelKey: 'nav.projects', parent: 'home' } }
  },
  {
    path: '/projects/:slug',
    name: 'project-detail',
    component: ProjectDetailView,
    meta: {
      breadcrumb(route) {
        const slug = String(route.params.slug ?? 'Project');
        return {
          label: slug.replace(/-/g, ' '),
          parent: 'projects'
        };
      }
    }
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior() {
    return { top: 0 };
  }
});

export default router;
