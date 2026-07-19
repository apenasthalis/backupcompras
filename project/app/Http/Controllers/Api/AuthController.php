<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\JwtService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function __construct(
        private readonly JwtService $jwtService,
    ) {}

    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $this->jwtService->generateToken($user);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $this->userResponse($user),
            'access_token' => $token,
            'token_type' => 'bearer',
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $token = $this->jwtService->generateToken($user);

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => $this->userResponse($user),
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json($this->userResponse($request->user()));
    }

    public function logout(Request $request): JsonResponse
    {
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function googleRedirect(): JsonResponse
    {
        return response()->json([
            'url' => Socialite::driver('google')
                ->stateless()
                ->redirect()
                ->getTargetUrl(),
        ]);
    }

    public function googleCallback(Request $request): JsonResponse|\Illuminate\Http\RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Invalid Google credentials'], 401);
            }
            return redirect('/login?error=google_auth_failed');
        }

        $user = User::where('email', $googleUser->getEmail())->first();

        if (!$user) {
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'google_avatar' => $googleUser->getAvatar(),
                'google_name' => $googleUser->getName(),
                'password' => Hash::make(bin2hex(random_bytes(16))),
            ]);
        } else {
            $user->update([
                'google_id' => $googleUser->getId(),
                'google_avatar' => $googleUser->getAvatar(),
                'google_name' => $googleUser->getName(),
            ]);
        }

        $token = $this->jwtService->generateToken($user);

        if ($request->expectsJson()) {
            return response()->json([
                'access_token' => $token,
                'token_type' => 'bearer',
                'user' => $this->userResponse($user),
            ]);
        }

        return redirect("/login?token={$token}");
    }

    private function userResponse(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'google_avatar' => $user->google_avatar,
            'google_name' => $user->google_name,
        ];
    }
}
