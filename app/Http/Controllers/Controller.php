<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     title="Paden API Documentation",
 *     version="1.0.0",
 *     description="API documentation for Paden application"
 * )
 *
 * @OA\SecurityScheme(
 *     type="http",
 *     description="Use Bearer token to access protected routes",
 *     name="Authorization",
 *     in="header",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     securityScheme="bearerAuth"
 * )
 */

class Controller extends BaseController
{
    /**
 * 
 * @OA\Get(
 *     path="/api/user-profile",
 *     tags={"User"},
 *     summary="Get authenticated user",
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Authenticated user returned"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized"
 *     )
 * )
 */
    use AuthorizesRequests, ValidatesRequests;
}
