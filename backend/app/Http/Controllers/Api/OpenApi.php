<?php

namespace App\Http\Controllers\Api;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="ProjectAtlas API",
 *     version="1.0.0",
 *     description="API powering the ProjectAtlas frontend."
 * )
 *
 * @OA\Server(
 *     url="/api/v1",
 *     description="Version 1 (stable)"
 * )
 */
class OpenApi
{
    // This class only exists to hold OpenAPI annotations.
}
