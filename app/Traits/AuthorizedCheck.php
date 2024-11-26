<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait AuthorizedCheck
{
    protected function BannerPermission($agentId)
    {
        $user = Auth::user();
        $master = $user->hasRole('Master');
        $isAuthorized = $master ? in_array($agentId, $user->agents()->pluck('id')->toArray()) : $user->id === $agentId;
        if ($isAuthorized) {
            return true;
        }else{
            abort(403, 'Unauthorized');
        }
    }
}
