<?php

declare(strict_types=1);

namespace LaravelPlus\FeatureRequests\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

final class AuthorizeFeatureRequestsAdmin
{
    /**
     * Deny (404) unless the user holds the manage permission/gate ability.
     * Unknown abilities are denied by Gate, so this is deny-by-default.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ability = config('feature-requests.permissions.manage_feature_requests', 'manage feature requests');

        abort_unless($request->user() && Gate::allows($ability), 404);

        return $next($request);
    }
}
