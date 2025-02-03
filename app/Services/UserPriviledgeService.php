<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\UserPriviledge;

class UserPriviledgeService
{
    
    public static function createUserPriviledge (Request $request)
    {
       try{
        $create = $request->validate([
            'id' => 'required',
            'name' => 'required',
        ]);

        $create = UserPriviledge::create($create);

        return response()->json([
            'message' => 'User Priviledge  create successfully',
            'status' => true,
        ],200
        );
       }
       catch (\Throwable $th){
        return response()->json([
            'message' => 'Failed to create User Priviledge  ',
            'status' => false,
            'error' => $th->getMessage(),
        ],500
        );
        } 
    }

    public static function ReadUserPriviledge ()
    {
        try{
            $read = UserPriviledge::all();

            return response()->json([
                'message' => 'User priviledge retrieved successfully',
                'status' => true,
                'data' => $read
            ],200
            );        
        }
        catch (\Throwable $th){
            return response()->json([
                'message' => 'Failed to retrieved viewer page ',
                'status' => false,
                'error' => $th->getMessage(),
            ],200
            );
            } 
    }

    public static function UpdateUserPriviledge (Request $request, string $id)
    {
        try{
            $validated = $request->validate([
                'name' => 'required',
            ]);
    
            $user = UserPriviledge::findOrFail($id);
            $user->update($validated);
            // return $request;
            return response()->json([
                'message' => 'User Priviledge  update successfully',
                'status' => true,
            ],200
            );  
        }
        catch (\Throwable $th){
            return response()->json([
                'message' => 'Failed to update User Priviledge  ',
                'status' => false,
                'error' => $th->getMessage(),
            ],500
            );
        }
    }

    public static function DeleteUserPriviledge ($id)
    {
        try{
            $user = UserPriviledge::findOrFail($id);
            $user->delete();
    
            // return $request;
            return response()->json([
                'message' => 'User Priviledge  delete successfully',
                'status' => true,
            ],200
            );          }
        catch (\Throwable $th){
            return response()->json([
                'message' => 'Failed to delete User Priviledge  ',
                'status' => false,
                'error' => $th->getMessage(),
            ],500
            );
        }
    }

}