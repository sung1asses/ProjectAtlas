<?php

namespace App\Http\Resources;

use App\Models\Project;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="ProjectDetailResource",
 *     title="Project Detail",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/ProjectResource"),
 *         @OA\Schema(
 *             @OA\Property(property="previewImage", type="string", format="uri", nullable=true),
 *             @OA\Property(
 *                 property="gallery",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="url", type="string", format="uri"),
 *                     @OA\Property(property="alt", type="string", nullable=true)
 *                 )
 *             ),
 *             @OA\Property(property="descriptionHtml", type="string", description="Localized long-form description"),
 *             @OA\Property(property="descriptionText", type="string", description="Plain-text fallback version of the description")
 *         )
 *     }
 * )
 */
class ProjectDetailResource extends ProjectResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale() ?: Project::DEFAULT_LOCALE;

        $descriptionHtml = $this->getTranslation('description', $locale)
            ?? $this->getTranslation('description', Project::DEFAULT_LOCALE)
            ?? '';

        $descriptionText = trim(strip_tags($descriptionHtml));

        if ($descriptionText === '') {
            $descriptionText = (string) ($this->getTranslation('summary', $locale)
                ?? $this->getTranslation('summary', Project::DEFAULT_LOCALE)
                ?? '');
        }

        return array_merge(parent::toArray($request), [
            'previewImage' => $this->resolvePreviewImageUrl(),
            'gallery' => $this->formatGalleryImages(),
            'descriptionHtml' => $descriptionHtml,
            'descriptionText' => $descriptionText,
        ]);
    }
}
