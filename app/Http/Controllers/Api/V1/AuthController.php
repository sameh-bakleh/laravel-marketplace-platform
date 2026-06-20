<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $auth,
    ) {}

    #[OA\Post(path: '/auth/register', summary: 'Register', tags: ['Auth'])]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(
        required: ['name', 'email', 'password'],
        properties: [
            new OA\Property(property: 'name', type: 'string'),
            new OA\Property(property: 'email', type: 'string', format: 'email'),
            new OA\Property(property: 'password', type: 'string', format: 'password'),
            new OA\Property(property: 'as_seller', type: 'boolean'),
            new OA\Property(property: 'store_name', type: 'string'),
        ]
    ))]
    #[OA\Response(response: 201, description: 'Created')]
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'as_seller' => ['sometimes', 'boolean'],
            'store_name' => ['nullable', 'string', 'max:255'],
        ]);

        $user = $this->auth->register($data);
        $token = JWTAuth::fromUser($user);

        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
            'user' => new UserResource($user),
        ], 201);
    }

    #[OA\Post(path: '/auth/login', summary: 'Login', tags: ['Auth'])]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(
        required: ['email', 'password'],
        properties: [
            new OA\Property(property: 'email', type: 'string'),
            new OA\Property(property: 'password', type: 'string'),
        ]
    ))]
    #[OA\Response(response: 200, description: 'OK')]
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $token = $this->auth->login($data['email'], $data['password']);
        if (! $token) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        $user = User::query()->where('email', $data['email'])->firstOrFail();

        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
            'user' => new UserResource($user),
        ]);
    }

    #[OA\Post(path: '/auth/refresh', summary: 'Refresh JWT', tags: ['Auth'], security: [['bearerAuth' => []]])]
    #[OA\Response(response: 200, description: 'OK')]
    public function refresh(): JsonResponse
    {
        return response()->json([
            'token' => JWTAuth::refresh(),
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
        ]);
    }

    #[OA\Post(path: '/auth/logout', summary: 'Logout', tags: ['Auth'], security: [['bearerAuth' => []]])]
    #[OA\Response(response: 204, description: 'No content')]
    public function logout(): Response
    {
        $token = JWTAuth::getToken();
        if ($token) {
            JWTAuth::invalidate($token);
        }

        return response()->noContent();
    }

    #[OA\Get(path: '/auth/me', summary: 'Current user', tags: ['Auth'], security: [['bearerAuth' => []]])]
    #[OA\Response(response: 200, description: 'OK')]
    public function me(Request $request): UserResource
    {
        return new UserResource($request->user()->load('sellerProfile'));
    }
}
