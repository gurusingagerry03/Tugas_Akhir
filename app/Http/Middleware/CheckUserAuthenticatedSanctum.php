<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Models\UserPriviledgeMapping;
use Carbon\Carbon;
use Closure;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserAuthenticatedSanctum
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$requiredPrivileges)
    {
        
        if (!Auth::check()) {
            return response()->json([
                'message' => 'Unauthorized',
                'status' => 'false',
                'error' => 'Unauthorized'
            ], 401);
        }

        $user = Auth::user();

        $userPrivileges = UserPriviledgeMapping::where('user_id', $user->id)
            ->with('priviledge')
            ->get()
            ->pluck('priviledge.name')
            ->toArray();

        $hasRequiredPrivilege = collect($requiredPrivileges)
            ->intersect($userPrivileges)
            ->isNotEmpty();

        if (!$hasRequiredPrivilege) {
            return response()->json([
                'message' => 'Insufficient privileges, required privileges: ' . implode(', ', $requiredPrivileges),
                'status' => 'false',
                'error' => 'Forbidden'
            ], 403);
        }

        return $next($request);
    }
}
