<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAppProfileSlug
{
    /**
     * @var array<string, string>
     */
    protected array $slugToProfile = [
        'particulier' => User::PROFILE_PARTICULIER,
        'artisan' => User::PROFILE_ARTISAN,
        'batiment' => User::PROFILE_ENTREPRENEUR_BATIMENT,
        'fournisseur' => User::PROFILE_ENTREPRISE_FOURNISSEUR,
    ];

    public function handle(Request $request, Closure $next, string $slug): Response
    {
        $expected = $this->slugToProfile[$slug] ?? null;
        $user = $request->user();
        if ($expected === null || ! $user || $user->profile_type !== $expected) {
            return redirect()->route('app.home');
        }

        return $next($request);
    }
}
