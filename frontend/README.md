# Project Atlas Frontend

Vue 3 + Vite single-page shell for the portfolio experience. The `frontend` container mounts this directory, installs dependencies, and runs `npm run dev` automatically when `package.json` exists.

## Available Commands
- `npm install` – install dependencies (run inside the container or locally with Node 18+).
- `npm run dev` – start the Vite dev server on port 5173 (Docker already does this).
- `npm run build` – build production assets that nginx serves from `frontend/dist`.
- `npm run preview` – preview the production build locally.

### Run inside Docker
```bash
docker compose --env-file docker/.env -f docker/docker-compose.yml exec -it frontend sh
npm install
npm run dev
```
