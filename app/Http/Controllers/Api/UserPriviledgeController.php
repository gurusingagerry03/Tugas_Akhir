<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UserPriviledgeService;
use Illuminate\Http\Request;

class UserPriviledgeController extends Controller
{

    protected $userPriviledgeService;

    public function __construct(UserPriviledgeService $userPriviledgeService){
        $this->userPriviledgeService = $userPriviledgeService;
    }

    public function index()
    {
        return $this->userPriviledgeService->ReadUserPriviledge();
    }

    public function store(Request $request)
    {
        return $this->userPriviledgeService->createUserPriviledge($request);
    }

    public function update(Request $request, $id)
    {
        return $this->userPriviledgeService->updateUserPriviledge($request, $id);
    }

    public function destroy($id)
    {
        return $this->userPriviledgeService->deleteUserPriviledge($id);
    }
}
