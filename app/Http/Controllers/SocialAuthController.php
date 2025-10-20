<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SocialAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;
use Laravel\Socialite\Facades\Socialite;
use GuzzleHttp\Exception\ClientException;

class SocialAuthController extends Controller
{
    public function redirect()
    {
        // Set redirect URI di runtime (hindari redirectUrl())
        config()->set('services.google.redirect', route('google.callback'));

        return Socialite::driver('google')
            ->redirect();
    }

    public function callback(Request $request)
    {
        try {
            /** @var \Laravel\Socialite\Two\User $oauthUser */
            $oauthUser = Socialite::driver('google')->user(); // stateful (aman utk Blade)
        } catch (ClientException $e) {
            // Tangkap error dari Google saat exchange code->token (mis: invalid_client)
            $body = (string) $e->getResponse()->getBody();
            // Log internal (tidak ditunjukkan ke user)
            logger()->error('Google OAuth token exchange failed', ['body' => $body]);

            return redirect()->route('login.show')->withErrors([
                'email' => 'Login Google gagal: konfigurasi OAuth salah atau redirect URI tidak cocok. Periksa client_id/secret & Authorized redirect URI.',
            ]);
        } catch (\Throwable $e) {
            logger()->error('Google OAuth unexpected error', ['err' => $e->getMessage()]);
            return redirect()->route('login.show')->withErrors([
                'email' => 'Terjadi kesalahan saat memproses login Google.',
            ]);
        }

        $provider   = 'google';
        $providerId = $oauthUser->getId();
        $email      = $oauthUser->getEmail();
        $name       = $oauthUser->getName() ?: ($email ? Str::before($email,'@') : 'Google User');

        // Validasi email dari Google (umumnya selalu ada, tapi amankan saja)
        // Jika email tidak ada, kita tidak bisa autolink ke users.email.
        if (! $email) {
            return redirect()->route('login.show')->withErrors([
                'email' => 'Akun Google tidak mengembalikan email. Pastikan izin email diberikan.',
            ]);
        }

        // (Opsional) pastikan email verified, Google biasanya punya 'email_verified' atau 'verified_email'
        $raw = method_exists($oauthUser, 'getRaw') ? $oauthUser->getRaw() : ($oauthUser->user ?? []);
        $emailVerified = (bool)($raw['email_verified'] ?? $raw['verified_email'] ?? true);
        if (! $emailVerified) {
            return redirect()->route('login.show')->withErrors([
                'email' => 'Email Google Anda belum terverifikasi. Silakan verifikasi email di Google terlebih dahulu.',
            ]);
        }

        try {
            // Retry 3x untuk antisipasi deadlock
            $user = DB::transaction(function () use ($provider, $providerId, $email, $name, $oauthUser) {

                // 1) Sudah tertaut?
                $link = SocialAccount::where('provider', $provider)
                    ->where('provider_user_id', $providerId)
                    ->first();

                if ($link) {
                    $link->update([
                        'access_token'  => $oauthUser->token ?? $link->access_token,
                        'refresh_token' => $oauthUser->refreshToken ?? $link->refresh_token,
                        'expires_at'    => isset($oauthUser->expiresIn)
                            ? now()->addSeconds($oauthUser->expiresIn)
                            : $link->expires_at,
                    ]);
                    return $link->user;
                }

                // 2) Belum tertaut: cari user berdasarkan email
                $user = User::where('email', $email)->first();

                if (! $user) {
                    // Buat user baru (password null = Google-only)
                    $user = User::create([
                        'name'              => $name,
                        'email'             => $email,
                        'password'          => null,
                        'email_verified_at' => now(),
                    ]);
                }

                // 3) Buat tautan social account
                SocialAccount::create([
                    'user_id'          => $user->id,
                    'provider'         => $provider,
                    'provider_user_id' => $providerId,
                    'access_token'     => $oauthUser->token ?? null,
                    'refresh_token'    => $oauthUser->refreshToken ?? null,
                    'expires_at'       => isset($oauthUser->expiresIn)
                        ? now()->addSeconds($oauthUser->expiresIn)
                        : null,
                ]);

                return $user;
            }, 3);

        } catch (QueryException $e) {
            // Jika kejedot unique constraint (race), coba ambil ulang
            if (str_contains($e->getMessage(), 'unique') || str_contains(strtolower($e->getMessage()), 'duplicate')) {
                $link = SocialAccount::where('provider', $provider)
                    ->where('provider_user_id', $providerId)
                    ->first();

                if ($link) {
                    $user = $link->user;
                } else {
                    $user = User::where('email', $email)->first();
                    if (! $user) {
                        throw $e; // biar kelihatan jelas kalau mismatch aneh
                    }
                }
            } else {
                throw $e;
            }
        }

        Auth::login($user, remember: true);
        $request->session()->regenerate(); // penting: anti session fixation
        // *** BAGIAN INI SUDAH TEPAT MENGARAHKAN KE HOME ***
        return redirect()->intended('/home');
    }
}