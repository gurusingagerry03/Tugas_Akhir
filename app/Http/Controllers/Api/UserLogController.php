<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UserLogService;
use Illuminate\Http\Request;

class UserLogController extends Controller
{
    protected $userLog;
    public function __construct(UserLogService $userLog){
        $this->userLog = $userLog;
    }
    //Read All Product
    public function index()
    {
        return $this->userLog->getAllUserLog();
    }
    // Read Paginate Product
    public function show($id)
    {
        return $this->userLog->getUserLogById($id);
    }
    // Update Product
    public function update(Request $request, $id){
        return $this->userLog->updateUserLog($request, id);
    }
    // Delete Product
    public function destroy($id)
    {
        return $this->userLog->deleteUserLog($id);
    }
    // Store Product
    public function store(Request $request){
        return $this->userLog->createUserLog($request);
    }
}
