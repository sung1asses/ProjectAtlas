<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectDetailResource;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Annotations as OA;

class ProjectController extends Controller
{
    /**
     * @OA\Get(
     *     path="/projects",
     *     summary="List featured projects",
    *     tags={"Projects"},
    *     @OA\Parameter(
    *         name="lang",
    *         in="query",
    *         required=false,
    *         description="Locale code (en or ru)",
    *         @OA\Schema(type="string", enum={"en","ru"})
    *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Collection of projects",
    *         @OA\JsonContent(
    *             type="array",
    *             @OA\Items(ref="#/components/schemas/ProjectResource")
    *         )
     *     )
     * )
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $locale = $this->resolveLocale($request);
        app()->setLocale($locale);

        $projects = Project::query()
            ->published()
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->get();

        return ProjectResource::collection($projects)
            ->additional(['meta' => ['locale' => $locale]]);
    }

    /**
     * @OA\Get(
     *     path="/projects/{slug}",
     *     summary="Show a single project",
    *     tags={"Projects"},
     *     @OA\Parameter(name="slug", in="path", required=true, @OA\Schema(type="string")),
    *     @OA\Parameter(
    *         name="lang",
    *         in="query",
    *         required=false,
    *         description="Locale code (en or ru)",
    *         @OA\Schema(type="string", enum={"en","ru"})
    *     ),
     *     @OA\Response(
     *         response=200,
    *         description="Project detail",
    *         @OA\JsonContent(ref="#/components/schemas/ProjectDetailResource")
     *     ),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function show(Request $request, Project $project): ProjectDetailResource
    {
        abort_unless($project->is_published, 404);

        $locale = $this->resolveLocale($request);
        app()->setLocale($locale);

        return (new ProjectDetailResource($project))
            ->additional(['meta' => ['locale' => $locale]]);
    }

    protected function resolveLocale(Request $request): string
    {
        $queryLocale = $request->query('lang');
        if ($queryLocale && in_array($queryLocale, Project::AVAILABLE_LOCALES, true)) {
            return $queryLocale;
        }

        $preferred = $request->getPreferredLanguage(Project::AVAILABLE_LOCALES);

        return in_array($preferred, Project::AVAILABLE_LOCALES, true)
            ? $preferred
            : Project::DEFAULT_LOCALE;
    }
}
