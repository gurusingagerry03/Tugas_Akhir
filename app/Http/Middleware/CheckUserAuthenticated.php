<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Models\UserPriviledgeMapping;
use Carbon\Carbon;
use Closure;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserAuthenticated
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
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'message' => 'Failed to authenticate',
                'status' => false,
                'error' => 'Unauthorized'
            ], 401);
        }

        $client = new Client();

        try {
            $response = $client->get('https://api-gateway.telkomuniversity.ac.id/issueprofile', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                ],
            ]);

            if ($response->getStatusCode() !== 200) {
                return response()->json([
                    'message' => 'Failed to authenticate',
                    'status' => false,
                    'error' => 'Invalid token'
                ], 401);
            }

            $profileData = json_decode($response->getBody()->getContents(), true);
            $username = $profileData['user'] ?? null;

            $user = User::where('username', $username)->first();

            if (!$user) {
                return response()->json([
                    'message' => 'Failed to authenticate',
                    'status' => false,
                    'error' => 'User not found'
                ], 404);
            }

            $currentTime = Carbon::now();
            if ($user->updated_at->lt($currentTime)) {
                return response()->json([
                    'message' => 'Failed to authenticate',
                    'status' => false,
                    'error' => 'Session expired'
                ], 401);
            }

            $userPrivilege = UserPriviledgeMapping::where('user_id', $user->id)
                ->with('priviledge')
                ->first();

            if (!$userPrivilege || !in_array($userPrivilege->priviledge->name, $requiredPrivileges)) {
                return response()->json([
                    'message' => 'Failed to authenticate',
                    'status' => false,
                    'error' => 'Forbidden access'
                ], 403);
            }

            return $next($request);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to authenticate',
                'status' => false,
                'error' => $e->getMessage()
            ], 401);
        }
    }
}