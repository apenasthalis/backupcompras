<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordMail;
use App\Mail\WelcomeMail;
use App\Models\User;
use App\Services\JwtService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function __construct(
        private readonly JwtService $jwtService,
    ) {}

    public function register(Request $request): JsonResponse
    {
        try {
            $data = $this->validateOrThrow($request, [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
                'endereco' => 'nullable|string|max:255',
                'cidade' => 'nullable|string|max:255',
                'estado' => 'nullable|string|max:255',
            ]);

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'contad' => $this->gerarContad(),
                'endereco' => $data['endereco'] ?? null,
                'cidade' => $data['cidade'] ?? null,
                'estado' => $data['estado'] ?? null,
            ]);

            try {
                Mail::to($user)->send(new WelcomeMail($user));
            } catch (Exception $e) {
            }

            $token = $this->jwtService->generateToken($user);

            return response()->json([
                'message' => 'Conta criada com sucesso',
                'user' => $this->userResponse($user),
                'access_token' => $token,
                'token_type' => 'bearer',
            ], 201);
        } catch (ApiException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new ApiException('Erro ao criar conta: ' . $e->getMessage(), 500);
        }
    }

    public function login(Request $request): JsonResponse
    {
        try {
            $data = $this->validateOrThrow($request, [
                'email' => 'required|email',
                'password' => 'required|string|min:6',
            ]);

            $user = User::where('email', $data['email'])->first();

            if (!$user || !Hash::check($data['password'], $user->password)) {
                throw new ApiException('Email ou senha inválidos', 401);
            }

            $token = $this->jwtService->generateToken($user);

            return response()->json([
                'access_token' => $token,
                'token_type' => 'bearer',
                'user' => $this->userResponse($user),
            ]);
        } catch (ApiException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new ApiException('Erro ao fazer login: ' . $e->getMessage(), 500);
        }
    }

    public function me(Request $request): JsonResponse
    {
        try {
            return response()->json($this->userResponse($request->user()));
        } catch (Exception $e) {
            throw new ApiException('Erro ao carregar usuário: ' . $e->getMessage(), 500);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        return response()->json(['message' => 'Logout realizado com sucesso']);
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        try {
            $data = $this->validateOrThrow($request, [
                'email' => 'required|email|exists:users,email',
            ]);

            $token = bin2hex(random_bytes(32));

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $data['email']],
                ['token' => $token, 'created_at' => now()]
            );

            $user = User::where('email', $data['email'])->first();

            try {
                Mail::to($user)->send(new ResetPasswordMail($user, $token));
            } catch (Exception $e) {
            }

            return response()->json([
                'message' => 'Token de recuperação enviado para seu email',
                'reset_token' => $token,
            ]);
        } catch (ApiException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new ApiException('Erro ao solicitar recuperação: ' . $e->getMessage(), 500);
        }
    }

    public function resetPassword(Request $request): JsonResponse
    {
        try {
            $data = $this->validateOrThrow($request, [
                'email' => 'required|email|exists:users,email',
                'token' => 'required|string',
                'password' => 'required|string|min:6',
            ]);

            $record = DB::table('password_reset_tokens')
                ->where('email', $data['email'])
                ->first();

            if (!$record || $record->token !== $data['token']) {
                throw new ApiException('Token inválido', 400);
            }

            $createdAt = \Carbon\Carbon::parse($record->created_at);
            if ($createdAt->diffInMinutes(now()) > 60) {
                DB::table('password_reset_tokens')->where('email', $data['email'])->delete();
                throw new ApiException('Token expirado. Solicite um novo.', 400);
            }

            $user = User::where('email', $data['email'])->first();
            $user->password = Hash::make($data['password']);
            $user->save();

            DB::table('password_reset_tokens')->where('email', $data['email'])->delete();

            return response()->json(['message' => 'Senha redefinida com sucesso']);
        } catch (ApiException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new ApiException('Erro ao redefinir senha: ' . $e->getMessage(), 500);
        }
    }

    private function gerarContad(): string
    {
        return 'C' . now()->format('YmdHis') . random_int(100, 999);
    }

    private function userResponse(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ];
    }
}
