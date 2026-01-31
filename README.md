# ProjectAtlas

Portfolio playground that combines a PHP backend with a Vue-powered frontend.

## Repository Layout
- `backend/` – PHP sources (currently a placeholder in `public/index.php`).
- `frontend/` – Vue application scaffold (add your Vite project here).
- `docker/` – self-contained development environment (compose file, Dockerfiles, helper scripts).

## Bootstrap The Containers
1. Copy the default docker environment file and adjust it to your host settings (UID/GID, ports, timezone):
	```bash
	cd docker
	bash ./bin/bootstrap.sh
	# or copy .env.example manually if you prefer
	```
2. Open `docker/.env` and review every value. At minimum make sure `HOST_UID`/`HOST_GID` match your local user, tweak the exposed ports if they clash, and set `HOST_TZ`/`HOST_LOCALE` to whatever your workstation uses. If you want to re-use your existing SSH keys, mount them by adding a bind volume for `/home/dev/.ssh` (see the note below).
3. Start the stack from the `docker/` folder to keep mounts scoped correctly:
	```bash
	docker compose --env-file docker/.env -f docker/docker-compose.yml up -d
	```
	Stop it with:
	```bash
	docker compose --env-file docker/.env -f docker/docker-compose.yml down
	```

### Optional host SSH mount
Add the snippet below under the `volumes:` section of any service that needs SSH keys:

```yaml
    - type: bind
      source: /home/you/.ssh     # replace with an absolute path on the host
      target: /home/dev/.ssh
      read_only: true
```

## Virtual Hosts & DNS
- Frontend resolves via `project-atlas.test` (served from the compiled Vite build with a fallback to the dev server). Add `127.0.0.1 project-atlas.test` to `/etc/hosts`.
- Backend (Laravel + Swagger) resolves via `api.project-atlas.test` on the same public port (default `NGINX_PORT=8080`). Add `127.0.0.1 api.project-atlas.test` as well.
- The frontend host redirects any `/api*` hits to `api.project-atlas.test` so you never accidentally mix the two.

## Automation Scripts
- `bash ./scripts local` – end-to-end bootstrap for local machines. It creates `docker/.env` (if missing), scaffolds the Laravel backend in `backend/` from inside the PHP container without nesting, copies `.env`, generates the app key, fixes `storage` permissions, and finally runs `docker compose up -d --build`.
- `bash ./scripts server` – deploy helper for remote hosts. It optionally builds frontend assets, pulls the latest images, recreates the stack, runs artisan cache optimizations, and executes migrations. Toggle pieces with env flags (`SKIP_FRONTEND_BUILD=true`, `SKIP_BACKEND_CACHE=true`, `SKIP_MIGRATIONS=true`).

Both commands rely on the docker compose file under `./docker/`, so run them from the repo root (or prefix with `bash`).

## Services
- `php` – PHP 8.3 FPM with Composer, Git, and UID/GID matching the host for safe volume mounts. Git identity is injected from the `.env` values so commits inside the container use your name.
- `frontend` – Node 20 + pnpm/npm ready image that installs dependencies and runs `npm run dev` once a valid Vue project exists. Until `package.json` appears it simply waits, allowing you to scaffold the app without noisy restarts.
- `nginx` – serves built Vue assets from `frontend/dist`, proxies fallbacks to the Vite dev server, and forwards `/api` traffic to the PHP backend (`backend/public`).

## Runtime Flow
- `docker compose up` binds the entire project into `/workspace` and builds the custom PHP/Node images so files created inside containers keep the same UID/GID as on the host.
- The PHP container boots `php-fpm`, pre-creates `/workspace/backend` if it was missing, configures Git author info from `.env`, and exposes port `9000` for internal Nginx traffic.
- The Node container waits for `/workspace/frontend/package.json`, installs dependencies when needed, then launches the Vite dev server on `VITE_PORT` with `--host 0.0.0.0` so both Nginx and your browser can reach it.
- Nginx serves static files from `frontend/dist` when they exist, otherwise proxies `/` to the Vite dev server and `/api` to PHP-FPM. Everything shares the `atlas` bridge network, so service names resolve to containers automatically.

## Mounts & Shared State
- Project code: `${PROJECT_ROOT}` → `/workspace` (one bind mount shared by every service).
- Composer cache: named volume `composer_cache` → `/home/dev/.composer` so repeated `composer install` commands stay fast.
- Node dependencies: named volume `frontend_node_modules` → `/workspace/frontend/node_modules` to keep `node_modules` stable across container rebuilds.
- Optional SSH keys: bind `/home/you/.ssh` → `/home/dev/.ssh` (read only) if you added the snippet shown above.

## Networking & Env Passthrough
- Ports forwarded: `NGINX_PORT`→80 (public entry point), `VITE_PORT`→5173 (hot module dev server), `PHP_FPM_PORT`→9000 (only if you want to connect an IDE/Xdebug client directly).
- Shared environment: timezone, locale, and Git identity from `.env` propagate into every container via the `x-shared-env` anchor.
- Extra host `host.docker.internal` allows containers to reach services on your workstation (e.g., database or API mocks) without additional wiring.

## Git & Host Integration
- Host repo is bind-mounted into `/workspace`, so containers always work with your current checkout (including the `.git` folder).
- `HOST_UID` / `HOST_GID` inside `docker/.env` should match your local user to avoid permission issues when the containers write files.
- Git author data is set at runtime from `GIT_USER_NAME` / `GIT_USER_EMAIL`. If you also need SSH-based workflows, re-use the optional bind snippet or forward `SSH_AUTH_SOCK` before running compose.

## Swagger on the Backend Host
1. Require a documentation package (e.g., `composer require darkaonline/l5-swagger` inside the PHP container).
2. Publish its config/UI assets: `php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"`.
3. Expose the docs route (defaults to `/api/documentation`); it will be visible on `http://api.project-atlas.test:${NGINX_PORT:-8080}/swagger` because the backend virtual host already whitelists `/swagger*` paths.
4. Keep the docs behind auth if needed (e.g., middleware or basic auth) since they are now directly reachable via the dedicated host.

With the Docker environment running you can iterate on the PHP backend inside `backend/` and your Vue app inside `frontend/` while sharing the same Git history from either the host or containers.
