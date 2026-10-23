<?php

declare(strict_types=1);

namespace LaravelPlus\FeatureRequests\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Gate;
use LaravelPlus\FeatureRequests\Models\FeatureRequest;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function canManage(): bool
    {
        return Gate::allows(config('feature-requests.permissions.manage_feature_requests', 'manage feature requests'));
    }

    /**
     * 404 unless the request is public, or the user owns/is assigned to it, or can manage requests.
     */
    protected function abortUnlessVisible(FeatureRequest $featureRequest): void
    {
        if ($featureRequest->is_public) {
            return;
        }

        $user = auth()->user();

        abort_unless(
            $user && ($featureRequest->user_id === $user->id || $featureRequest->assigned_to === $user->id || $this->canManage()),
            404,
            'Feature request not found.'
        );
    }
}
