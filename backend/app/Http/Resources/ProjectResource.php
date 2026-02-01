<?php

namespace App\Http\Resources;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/** @mixin \App\Models\Project */
/**
 * @OA\Schema(
 *     schema="ProjectResource",
 *     title="Project",
 *     description="Lightweight representation of a published project",
 *     required={"slug","title","summary","repository","tags","languages","isFeatured"},
 *     @OA\Property(property="slug", type="string", description="Human-readable unique identifier"),
 *     @OA\Property(property="title", type="string", description="Project title"),
 *     @OA\Property(property="summary", type="string", description="Short marketing description"),
 *     @OA\Property(property="repository", type="string", format="uri", description="GitHub repository URL"),
 *     @OA\Property(property="tags", type="array", @OA\Items(type="string"), description="Free-form topic labels"),
 *     @OA\Property(property="languages", type="array", @OA\Items(type="string"), description="Detected programming languages"),
 *     @OA\Property(property="lastCommitAt", type="string", format="date-time", nullable=true, description="Timestamp of the latest commit pulled from GitHub"),
 *     @OA\Property(property="syncedAt", type="string", format="date-time", nullable=true, description="When the GitHub data was last synchronized"),
 *     @OA\Property(
 *         property="github",
 *         type="object",
 *         nullable=true,
 *         description="Raw GitHub metadata snapshot",
 *         @OA\Property(property="stars", type="integer", nullable=true),
 *         @OA\Property(property="forks", type="integer", nullable=true),
 *         @OA\Property(property="issues", type="integer", nullable=true)
 *     ),
 *     @OA\Property(property="isFeatured", type="boolean", description="Marks whether item surfaces in featured rails"),
 *     @OA\Property(
 *         property="translations",
 *         type="object",
 *         description="Raw localized copy",
 *         @OA\Property(
 *             property="title",
 *             type="object",
 *             @OA\AdditionalProperties(type="string")
 *         ),
 *         @OA\Property(
 *             property="summary",
 *             type="object",
 *             @OA\AdditionalProperties(type="string")
 *         )
 *     ),
 *     @OA\Property(property="locale", type="string", description="Locale used to render the scalar fields"),
 *     @OA\Property(property="availableLocales", type="array", @OA\Items(type="string")),
 *     @OA\Property(property="defaultLocale", type="string")
 * )
 */
class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale() ?: Project::DEFAULT_LOCALE;

        return [
            'slug' => $this->slug,
            'title' => $this->getTranslation('title', $locale),
            'summary' => $this->getTranslation('summary', $locale),
            'repository' => $this->repository,
            'tags' => $this->tags ?? [],
            'languages' => $this->languages ?? [],
            'lastCommitAt' => optional($this->last_commit_at)->toIso8601String(),
            'syncedAt' => optional($this->synced_at)->toIso8601String(),
            'github' => $this->github_meta ?? [],
            'isFeatured' => (bool) $this->is_featured,
            'translations' => [
                'title' => $this->title_translations ?? [],
                'summary' => $this->summary_translations ?? [],
            ],
            'locale' => $locale,
            'availableLocales' => Project::AVAILABLE_LOCALES,
            'defaultLocale' => Project::DEFAULT_LOCALE,
        ];
    }
}
