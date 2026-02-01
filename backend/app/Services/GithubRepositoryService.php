<?php

namespace App\Services;

use App\Models\Project;
use Carbon\CarbonInterface;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GithubRepositoryService
{
    public function __construct(private ?CacheRepository $cache = null)
    {
        $defaultStore = config('cache.default', 'file');
        $this->cache = $cache ?? Cache::store($defaultStore);
    }

    public function fetchRepository(Project $project): array
    {
        return $this->remember($project, 'repo', function () use ($project) {
            $response = $this->client()
                ->get("/repos/{$project->repository}")
                ->throw();

            return $response->json();
        });
    }

    public function fetchReadme(Project $project): ?string
    {
        return $this->remember($project, 'readme', function () use ($project) {
            $response = $this->client()
                ->withHeaders(['Accept' => 'application/vnd.github.v3.html'])
                ->get("/repos/{$project->repository}/readme");

            if ($response->successful()) {
                return $response->body();
            }

            Log::warning('Unable to fetch README', [
                'project' => $project->repository,
                'status' => $response->status(),
            ]);

            return null;
        });
    }

    public function fetchLanguages(Project $project): array
    {
        return $this->remember($project, 'languages', function () use ($project) {
            $response = $this->client()
                ->get("/repos/{$project->repository}/languages")
                ->throw();

            return $response->json();
        });
    }

    public function fetchLastCommit(Project $project): ?array
    {
        return $this->remember($project, 'last-commit', function () use ($project) {
            $response = $this->client()
                ->get("/repos/{$project->repository}/commits", [
                    'per_page' => 1,
                    'sha' => $project->default_branch ?? null,
                ]);

            if ($response->failed()) {
                Log::warning('Unable to fetch last commit', [
                    'project' => $project->repository,
                    'status' => $response->status(),
                ]);

                return null;
            }

            $first = Arr::first($response->json());

            return is_array($first) ? $first : null;
        });
    }

    public function syncProject(Project $project): Project
    {
        $repo = $this->fetchRepository($project);
        $readme = $this->fetchReadme($project);
        $languages = $this->fetchLanguages($project);
        $lastCommit = $this->fetchLastCommit($project);

        $description = Arr::get($repo, 'description');
        $hasSummary = (bool) $project->getTranslation('summary', Project::DEFAULT_LOCALE, fallback: false);

        if (! $hasSummary && $description) {
            $project->setTranslation('summary', Project::DEFAULT_LOCALE, $description);
        }

        $project->fill([
            'default_branch' => Arr::get($repo, 'default_branch', $project->default_branch),
            'readme_html' => $readme,
            'languages' => $languages,
            'last_commit_at' => $this->extractCommitDate($lastCommit),
            'synced_at' => now(),
            'github_meta' => [
                'stargazers' => Arr::get($repo, 'stargazers_count'),
                'forks' => Arr::get($repo, 'forks_count'),
                'open_issues' => Arr::get($repo, 'open_issues_count'),
                'topics' => Arr::get($repo, 'topics', []),
                'license' => Arr::get($repo, 'license.spdx_id'),
                'homepage' => Arr::get($repo, 'homepage'),
                'last_commit_sha' => Arr::get($lastCommit, 'sha'),
            ],
        ])->save();

        return $project->fresh();
    }

    public function clearCache(Project $project): void
    {
        foreach (['repo', 'readme', 'languages', 'last-commit'] as $suffix) {
            $this->cache->forget($this->cacheKey($project, $suffix));
        }
    }

    protected function extractCommitDate(?array $commit): ?CarbonInterface
    {
        $date = Arr::get($commit, 'commit.committer.date') ?? Arr::get($commit, 'commit.author.date');

        return $date ? CarbonImmutable::parse($date) : null;
    }

    protected function remember(Project $project, string $suffix, callable $callback)
    {
        $ttl = max(1, (int) config('github.cache_ttl', 300));
        $key = $this->cacheKey($project, $suffix);

        return $this->cache->remember($key, $ttl, $callback);
    }

    protected function cacheKey(Project $project, string $suffix): string
    {
        return sprintf('github:%s:%s', $project->repository, $suffix);
    }

    protected function client(): PendingRequest
    {
        $request = Http::baseUrl(config('github.base_url', 'https://api.github.com'))
            ->acceptJson()
            ->withHeaders([
                'User-Agent' => 'ProjectAtlasBackend/1.0',
            ]);

        $token = config('github.token');
        if ($token) {
            $request = $request->withToken($token);
        }

        return $request;
    }
}
