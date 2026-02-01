import i18n from '@/i18n/index.js';
import httpClient from './httpClient.js';

const MOCK_PROJECT_BLUEPRINTS = [
  {
    slug: 'supply-chain-ai',
    fallback: {
      name: 'Northwind Logistics',
      industry: 'Supply Chain',
      summary: 'Data platform redesign with predictive recommendations and fleet visibility.',
      body: 'We re-platformed the ops console, adding AI-driven alerts and real-time telemetry.'
    },
    keys: {
      name: 'mockProjects.supplyChainAi.name',
      industry: 'mockProjects.supplyChainAi.industry',
      summary: 'mockProjects.supplyChainAi.summary',
      body: 'mockProjects.supplyChainAi.body'
    }
  },
  {
    slug: 'creator-passport',
    fallback: {
      name: 'Beacon Studio',
      industry: 'Creator Economy',
      summary: 'Subscription platform with onboarding flows, identity, and billing automation.',
      body: 'Multi-tenant architecture supported regional tax rules and multi-currency checkout.'
    },
    keys: {
      name: 'mockProjects.creatorPassport.name',
      industry: 'mockProjects.creatorPassport.industry',
      summary: 'mockProjects.creatorPassport.summary',
      body: 'mockProjects.creatorPassport.body'
    }
  },
  {
    slug: 'finops-suite',
    fallback: {
      name: 'Halo Finance',
      industry: 'FinTech',
      summary: 'FinOps dashboard consolidating spend, alerts, and compliance workflows.',
      body: 'Automated reconciliation pipelines cut manual reporting by 80% and surfaced insights.'
    },
    keys: {
      name: 'mockProjects.finopsSuite.name',
      industry: 'mockProjects.finopsSuite.industry',
      summary: 'mockProjects.finopsSuite.summary',
      body: 'mockProjects.finopsSuite.body'
    }
  }
];

const translateField = (key, fallback, translator) => {
  if (typeof translator === 'function') {
    const value = translator(key);
    return value === key ? fallback : value;
  }
  return fallback;
};

const buildLocalizedMocks = translator =>
  MOCK_PROJECT_BLUEPRINTS.map(project => ({
    slug: project.slug,
    name: translateField(project.keys.name, project.fallback.name, translator),
    industry: translateField(project.keys.industry, project.fallback.industry, translator),
    summary: translateField(project.keys.summary, project.fallback.summary, translator),
    body: translateField(project.keys.body, project.fallback.body, translator)
  }));

const getLocaleValue = () => {
  const localeRef = i18n?.global?.locale;
  if (localeRef && typeof localeRef === 'object' && 'value' in localeRef) {
    return localeRef.value;
  }
  return localeRef ?? 'en';
};

let mockCache = {
  locale: null,
  list: [],
  map: new Map()
};

const ensureMockCache = () => {
  const locale = getLocaleValue();
  if (mockCache.locale === locale && mockCache.list.length) {
    return;
  }

  const translator = i18n?.global?.t;
  const list = buildLocalizedMocks(translator);
  mockCache = {
    locale,
    list,
    map: new Map(list.map(project => [project.slug, project]))
  };
};

const getLocalizedMocks = () => {
  ensureMockCache();
  return mockCache.list;
};

const findLocalizedMock = slug => {
  ensureMockCache();
  return mockCache.map.get(slug) ?? null;
};

const shouldUseMock = import.meta.env.VITE_USE_API_MOCK !== 'false';
const delay = ms => new Promise(resolve => setTimeout(resolve, ms));

export async function fetchProjects() {
  if (shouldUseMock) {
    await delay(350);
    return getLocalizedMocks();
  }

  try {
    const { data } = await httpClient.get('/projects');
    return data?.data ?? data;
  } catch (error) {
    console.warn('Falling back to mock projects data', error);
    await delay(200);
    return getLocalizedMocks();
  }
}

export async function fetchProjectDetail(slug) {
  if (!slug) return null;

  if (shouldUseMock) {
    await delay(250);
    return findLocalizedMock(slug);
  }

  try {
    const { data } = await httpClient.get(`/projects/${slug}`);
    return data?.data ?? data;
  } catch (error) {
    console.warn(`Falling back to mock detail for ${slug}`, error);
    await delay(200);
    return findLocalizedMock(slug);
  }
}
