<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserPriviledge;
use App\Models\UserPriviledgeMapping;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class UserService
{
    
    public static function createUser (Request $request)
    {
       try{
        $create = $request->validate([
            'author_id' => 'required',
            'username' => 'required',
            'password' => 'required',
            'fullname' => 'required',
            'email' => 'required'
        ]);

        $create = User::create($create);

        return response()->json([
            'message' => 'User create successfully',
            'status' => true,
        ],200
        );
       }
       catch (\Throwable $th){
        return response()->json([
            'message' => 'Failed to create User',
            'status' => false,
            'error' => $th->getMessage(),
        ],500
        );
        } 
    }

    public static function getProfile()
    {
        try{
            /** @var \App\Models\MyUserModel $user **/
            $user = Auth::user();

            $privilege = UserPriviledgeMapping::where('user_id', $user->id)->first();
            $user->privilege = UserPriviledge::find($privilege->priviledge_id)->name;

            $user->makeHidden(['password', 'created_at']);

            return response()->json([
                'message' => 'User retrieved successfully',
                'status' => true,
                'data' => $user
            ],200
            );
        }
        catch (\Throwable $th){
            return response()->json([
                'message' => 'Failed to retrieved User',
                'status' => false,
                'error' => $th->getMessage(),
            ],500
            );
        }
    }

    public static function ReadUser ()
    {
        try{
            $read = User::all();

            return response()->json([
                'message' => 'User retrieved successfully',
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

    public static function UpdateUser (Request $request, string $id)
    {
        try{
            $validated = $request->validate([
                'id_author' => 'required',
                'username' => 'required',
                'pass' => 'required',
                'full_name' => 'required',
                'email' => 'required'
            ]);
            $user = User::findOrFail($id);
            $user->update($validated);
    
    
            // return $request;
            return response()->json([
                'message' => 'User update successfully',
                'status' => true,
            ],200
            );  
        }
        catch (\Throwable $th){
            return response()->json([
                'message' => 'Failed to update User',
                'status' => false,
                'error' => $th->getMessage(),
            ],500
            );
        }
    }

    public static function UpdateRoleUser(Request $request, $id)
    {
        try{
            $validated = $request->validate([
                'role' => 'required|array|exists:user_priviledge,id',
            ]);

            $user = User::findOrFail($id);
            $user->user_priviledge()->sync($validated['role']);
    
    
            return response()->json([
                'message' => 'User role update successfully',
                'status' => true,
            ], 200);  
        } catch (ValidationException $e){
            return response()->json([
                'message' => 'Validation failed',
                'status' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $th){
            return response()->json([
                'message' => 'Failed to update User',
                'status' => false,
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public static function DeleteUser ($id)
    {
        try{
            $user = User::findOrFail($id);
            $user->delete();
    
            // return $request;
            return response()->json([
                'message' => 'User delete successfully',
                'status' => true,
            ],200
            );          }
        catch (\Throwable $th){
            return response()->json([
                'message' => 'Failed to delete User',
                'status' => false,
                'error' => $th->getMessage(),
            ],500
            );
        }
    }

}
