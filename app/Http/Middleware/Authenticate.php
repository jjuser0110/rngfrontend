<?php

namespace App\Http\Middleware;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    /**
     * Logged in, and still allowed on the website: a customer deleted or made inactive in rngcarrental
     * is signed out on their next page, not only at their next login.
     */
    protected function authenticate($request, array $guards)
    {
        parent::authenticate($request, $guards);

        $problem = Auth::user()->websiteLoginProblem();
        if (!$problem) {
            return;
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            throw new AuthenticationException($problem);
        }
        throw new HttpResponseException(redirect()->route('login')->withErrors(['username' => $problem]));
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route('login');
    }
}
