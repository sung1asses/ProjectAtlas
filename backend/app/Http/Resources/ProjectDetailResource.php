<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="ProjectDetailResource",
 *     title="Project Detail",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/ProjectResource"),
 *         @OA\Schema(
 *             @OA\Property(
 *                 property="readmeHtml",
 *                 type="string",
 *                 description="Rendered README contents in HTML"
 *             )
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
        return array_merge(parent::toArray($request), [
            'readmeHtml' => $this->readme_html,
        ]);
    }
}
