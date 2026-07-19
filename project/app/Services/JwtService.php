<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class JwtService
{
    private string $secret;
    private int $ttl;
    private string $algo = 'HS256';

    public function __construct()
    {
        $this->secret = config('app.jwt_secret');
        $this->ttl = config('app.jwt_ttl', 1440);
    }

    public function generateToken(User $user): string
    {
        $issuedAt = time();
        $expiresAt = $issuedAt + ($this->ttl * 60);

        $payload = [
            'sub' => $user->id,
            'iat' => $issuedAt,
            'exp' => $expiresAt,
            'jti' => bin2hex(random_bytes(16)),
        ];

        return $this->encode($payload);
    }

    public function encode(array $payload): string
    {
        $header = ['alg' => $this->algo, 'typ' => 'JWT'];

        $segments = [];
        $segments[] = $this->base64UrlEncode(json_encode($header));
        $segments[] = $this->base64UrlEncode(json_encode($payload));

        $signingInput = implode('.', $segments);
        $signature = $this->sign($signingInput);
        $segments[] = $this->base64UrlEncode($signature);

        return implode('.', $segments);
    }

    public function decode(string $token): ?object
    {
        $segments = explode('.', $token);
        if (count($segments) !== 3) {
            return null;
        }

        [$headerB64, $payloadB64, $signatureB64] = $segments;

        $header = json_decode($this->base64UrlDecode($headerB64));
        if (!$header || !isset($header->alg)) {
            return null;
        }

        if ($header->alg !== $this->algo) {
            return null;
        }

        $signingInput = "$headerB64.$payloadB64";
        $signature = $this->base64UrlDecode($signatureB64);

        if (!$this->verify($signingInput, $signature)) {
            return null;
        }

        $payload = json_decode($this->base64UrlDecode($payloadB64));
        if (!$payload || !isset($payload->exp) || !isset($payload->sub)) {
            return null;
        }

        if ($payload->exp < time()) {
            return null;
        }

        return $payload;
    }

    public function getUserFromToken(string $token): ?User
    {
        $payload = $this->decode($token);
        if (!$payload) {
            return null;
        }

        return User::find($payload->sub);
    }

    private function sign(string $input): string
    {
        return hash_hmac('sha256', $input, $this->secret, true);
    }

    private function verify(string $input, string $signature): bool
    {
        $expected = $this->sign($input);
        return hash_equals($expected, $signature);
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function base64UrlDecode(string $data): string
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
