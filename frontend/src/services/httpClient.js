import axios from 'axios';

const httpClient = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL ?? 'http://api.project-atlas.test/api',
  timeout: 10000
});

export default httpClient;
