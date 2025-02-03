<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\UserLog;

class UserLogService
{
    /**
     * Display a listing of the resource.
     */
    public static function getAllUserLog()
    {
        //
        try {
            $data = UserLog::orderBy('id', 'asc')->get();
            return response()->json([
                'status'=> true,
                'message'=>'All user log find',
                'data' => $data
            ],200);
        } catch (\Throwable $th){
            return response()->json([
                'status' => false,
                'message' => 'Failed to get all user log',
                'error' => $th->getMessage()], 500);
        }

    }

    public function getUserLogById(string $id){
        try{
            $data = UserLog::findOrFail($id);
            return response()->json([
                'status'=>true,
                'message'=>'User log found',
                'data'=>$data
            ],200);
        }catch (\Throwable $th){
            return response()->json([
                'status' => false,
                'message' => 'User log not found',
                'error' => $th->getMessage()], 500);
        }

    }

    public static function createUserLog(Request $req)
    {
        try {
            $data = new UserLog;
            $data->access_datetime = $req->access_datetime;
            $data->expired = $req->expired;
            $data->token = $req->token;
            $data->username = $req->username;
            $data->ip = $req->ip;
            $data->user_agent = $req->user_agent;
            $data->stat = $req->stat;
            $data->save();
            return response()->json([
                'status' => true,
                'message' => 'User log successfully created',
                'data' => $data
            ],200);
        } catch (\Throwable $th){
            return response()->json([
                'status' => false,
                'message' => 'Create user log failed',
                'error' => $th->getMessage()], 500);
        }
    }

    public static function updateUserLog(Request $req, string $id)
    {
        try {
            $data = UserLog::findOrFail($id);
            $data->access_datetime = $req->access_datetime;
            $data->expired = $req->expired;
            $data->token = $req->token;
            $data->username = $req->username;
            $data->ip = $req->ip;
            $data->user_agent = $req->user_agent;
            $data->stat = $req->stat;
            $data->save();

            return response()->json([
                'status'=>true,
                'message'=>'User log successfully updated',
                'data' => $data
            ],200);
        } catch (\Throwable $th){
            return response()->json([
                'status' => false,
                'message' => 'Update user log failed',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public static function deleteUserLog(string $id)
    {
        try {
            $data = UserLog::findorFail($id);
            $data->delete();
            return response()->json([
                'status' => true,
                'message' => 'User log deleted'
            ], 200);
        } catch (\Throwable $th){
            return response()->json([
                'status' => false,
                'message' => 'Delete user log failed',
                'error' => $th->getMessage()], 500);
        }
    }
}