<?php 

namespace App\Services;

use App\Models\User;
use App\Models\UserLog;
use App\Models\UserPriviledge;
use App\Models\UserPriviledgeMapping;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginService
{
    public function login(Request $request) {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $username = $request->input('username');
        $password = $request->input('password');

        $client = new Client();

        try {

            $existingUser = User::where('username', $username)->first();
            $expiredInSeconds = 86400;

            if (!$existingUser) {
                $loginResponse = $client->post('https://api-gateway.telkomuniversity.ac.id/issueauth', [
                    'json' => [
                        'username' => $username,
                        'password' => $password,
                    ],
                ]);

                $loginData = json_decode($loginResponse->getBody()->getContents(), true);
                $token = $loginData['token'] ?? null;
                $expiredInSeconds = $loginData['expired'] ?? 0;

                if (!$token) {
                    return response()->json(['error' => 'Token not found'], 401);
                }

                $profileResponse = $client->get('https://api-gateway.telkomuniversity.ac.id/issueprofile', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $token,
                    ],
                ]);

                $profileData = json_decode($profileResponse->getBody()->getContents(), true);

                $newUser = new User();
                $newUser->author_id = null;
                $newUser->username = $profileData['username'] ?? $username;
                $newUser->fullname = $profileData['fullname'] ?? null;
                $newUser->email = $profileData['email'] ?? null;
                $newUser->password = Hash::make($password);
                $newUser->updated_at = Carbon::now()->addSeconds($expiredInSeconds);
                $newUser->save();

                $guestPrivilege = UserPriviledge::where('name', 'Guest')->first();

                $newPrivilegeMapping = new UserPriviledgeMapping();
                $newPrivilegeMapping->user_id = $newUser->id;
                $newPrivilegeMapping->priviledge_id = $guestPrivilege->id;

                $newPrivilegeMapping->save();
            }

            $credentials = $request->only('username', 'password');

            if (Auth::attempt($credentials)) {
                /** @var \App\Models\MyUserModel $user **/
                $user = Auth::user();
                $token = $user->createToken('authToken')->plainTextToken;

                if ($existingUser) {
                    $user->updated_at = Carbon::now()->addSeconds($expiredInSeconds);
                    $user->save();
                }

                $this->saveUserLog($username, $token, $expiredInSeconds);

                return response()->json([
                    'token' => $token,
                    'expired' => $expiredInSeconds,
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Unauthorized',
                    'status' => 'false',
                    'error' => 'Invalid credentials',
                ], 401);
            }

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Login failed',
                'status' => 'false',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function saveUserLog($username, $token, $expiredInSeconds) {
        try {
            $userLog = new UserLog();
            $userLog->access_datetime = Carbon::now();
            $userLog->expired = Carbon::now()->addSeconds($expiredInSeconds);
            $userLog->token = $token;
            $userLog->username = $username;
            $userLog->ip = request()->ip();
            $userLog->user_agent = request()->header('User-Agent');
            $userLog->stat = 'login';
            $userLog->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to save user log',
                'status' => 'false',
                'error' => $e->getMessage(),
            ], 500);
        }
        
    }

    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out',
            'status' => 'true',
        ], 200);
    }
}