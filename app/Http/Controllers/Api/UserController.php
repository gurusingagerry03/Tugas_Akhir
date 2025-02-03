<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService){
        $this->userService = $userService;
    }

    public function index()
    {
        return $this->userService->ReadUser();
    }

    public function profile()
    {
        return $this->userService->getProfile();
    }

    public function store(Request $request)
    {
        return $this->userService->createUser($request);
    }

    public function update(Request $request, $id)
    {
        return $this->userService->UpdateUser($request, $id);
    }

    public function updateRole(Request $request, $id)
    {
        return $this->userService->UpdateRoleUser($request, $id);
    }

    public function destroy($id)
    {
        return $this->userService->DeleteUser($id);
    }
}
