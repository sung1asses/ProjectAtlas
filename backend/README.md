## ProjectAtlas Backend

This Laravel 12 service powers the SPA with real project data pulled from GitHub. The backend is responsible for:

- Persisting curated project entries (slug, repo, marketing blurb, etc.).
- Syncing supplemental metadata from GitHub (README, dominant languages, last commit date).
- Exposing an HTTP API documented with Swagger so the Vue app can read stable JSON.
- Providing a Filament-powered admin surface to create or edit showcased projects without touching code.

### Planned architecture

| Layer | Responsibility |
| --- | --- |
| Database | `projects` table stores slug, display name, repository owner/name, visibility flags, and cached GitHub payloads (README HTML, languages JSON, last commit timestamp). |
| Services | `GithubRepositoryService` wraps the REST API (`/readme`, `/languages`, `/commits`) through Laravel's HTTP client, handles auth token injection, and caches responses per repo/locale. |
| API | `ProjectController@index/show` returns summary/detail payloads consumed by the SPA. Routes live in `routes/api.php`, and each action is annotated for Swagger. |
| Admin | Filament panel at `/admin` surfaces CRUD for `Project` records plus quick actions to sync README/language data from GitHub. |

### Environment variables

```
GITHUB_TOKEN=ghp_xxx                 # PAT or GitHub App installation token
GITHUB_CACHE_TTL=300                 # seconds for API response cache
```

### Local development

1. `composer install` (or `composer update` after adding packages).
2. Copy `.env.example` → `.env`, fill the GitHub variables, and run `php artisan key:generate`.
3. `php artisan migrate` (will create the `projects` table once migration is added).
4. `php artisan serve` and `npm run dev` (or rely on Sail/Docker).

### API & documentation

- Swagger UI is served from `/api/documentation` (powered by `l5-swagger`).
- Regenerate the OpenAPI schema after code changes with `php artisan l5-swagger:generate`.
- Schema source files live under `app/Http/Controllers` (see `ProjectController` annotations) and `app/Http/Resources`.

### Admin panel

- Filament lives at `/admin` and uses the default web guard. Create an account with `php artisan make:filament-user` (or seed a user) to log in.
- The `Project` resource exposes forms/table actions mirroring the previous Blade UI, including a "Sync GitHub" action that clears caches and refreshes metadata.
- Customize navigation, colors, and widgets via `App\Providers\Filament\AdminPanelProvider`.

This document will evolve as soon as the above pieces land in code.
