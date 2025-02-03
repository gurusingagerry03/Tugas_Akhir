<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\GrantFundsExternal;
use App\Rules\GrantIdExists;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GrantFundsEksternalService{

    public static function getAllGrantFundsEksternal()
    {
        try {
            $data = GrantFundsExternal::with('collaboratorCategory')->get();

            return response()->json([
                'status'=>true,
                'message'=> 'All Grand Funds Eksternal found',
                'data' => $data
            ], 200);
        } catch (\Throwable $th){
            return response()->json([
                'status' => false,
                'message' => 'failed to get All Grand Funds Eksternal',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public static function getPaginateGrantFundsEksternal()
    {
        try {
            $data = GrantFundsExternal::with('collaboratorCategory')->paginate();

            return response()->json([
                'status'=>true,
                'message'=> 'All Grand Funds Eksternal found',
                'data' => $data
            ], 200);
        } catch (\Throwable $th){
            return response()->json([
                'status' => false,
                'message' => 'failed to get All Grand Funds Eksternal',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function getGrantFundsEksternalById($id)
    {
        try{
            $data=GrantFundsExternal::with('collaboratorCategory')->findOrFail($id);

            return response()->json([
                'status'=>true,
                'message'=>'Grand fund eksternal found',
                'data'=>$data
            ], 200);
        }catch (\Throwable $th){
            return response()->json([
                'status' => false,
                'message' => 'Grand fund eksternal not found',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public static function createGrantFundsEksternal(Request $req)
    {
        try {
            $validated = $req->validate([
                'grant_id' => ['required', new GrantIdExists],
                'collaborator_name' => 'required',
                'collaborator_category_id' => 'required|exists:grant_collaborator_category,id',
                'funds_approved' => 'required',
                'funds_category' => 'required',
                'funds_program_name' => 'required'
            ]);

            $validated['grant_category_id'] = DB::table('doc_communityservice_author')
                ->where('id', $validated['grant_id'])
                ->exists() ? 2 : 1;

            GrantFundsExternal::create($validated);
            
            return response()->json([
                'status' => true,
                'message' => 'grand funds eksternal successfully created',
            ], 200);
        } catch (ValidationException $e){
            return response()->json([
                'message' => 'Validation failed',
                'status' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $th){
            return response()->json([
                'status' => false,
                'message' => 'create grant funds eksternal failed',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public static function updateGrantFundsEksternal(Request $req, $id)
    {
        try {
            $validated = $req->validate([
                'grant_id' => ['required', new GrantIdExists],
                'collaborator_name' => 'required',
                'collaborator_category_id' => 'required|exists:grant_collaborator_category,id',
                'funds_approved' => 'required',
                'funds_category' => 'required',
                'funds_program_name' => 'required'
            ]);

            $validated['grant_category_id'] = DB::table('doc_communityservice_author')
                ->where('id', $validated['grant_id'])
                ->exists() ? 2 : 1;

            $data = GrantFundsExternal::findorFail($id);

            $data->update($validated);

            return response()->json([
                'status'=>true,
                'message'=>'grand funds eksternal successfully updated',
                'data' => $data
            ],200);
            
        } catch (\Throwable $th){
            return response()->json([
                'status' =>false,
                'message' => 'update grand funds eksternal failed',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public static function deleteGrantFundsEksternal($id)
    {
        try {
            $data = GrantFundsExternal::findorFail($id);
            $data->delete();
            return response()->json([
                'status' => true,
                'message' => 'grand funds eksternal deleted'
            ], 200);
        } catch (\Throwable $th){
            return response()->json([
                'status' => false,
                'message' => 'delete grand funds eksternal failed',
                'error' => $th->getMessage()], 500);
        }
    }
}
