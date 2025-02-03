<?php

namespace App\Services;

use App\Models\GrantCollaboratorCategory;
use Illuminate\Http\Request;

class GrantCollaboratorCategoryService{

    public static function getAllGrantCollaboratorCategory()
    {
        try {
            $data = GrantCollaboratorCategory::all();

            return response()->json([
                'status'=>true,
                'message'=> 'Grant collaborator category retrieved successfully',
                'data' => $data
            ], 200);

        } catch (\Throwable $th){
            return response()->json([
                'status' => false,
                'message' => 'Failed to get grant collaborator category',
                'error' => $th->getMessage()
            ], 500);

        }

    }

    public static function getPaginateGrantCollaboratorCategory()
    {
        try {
            $data = GrantCollaboratorCategory::paginate();

            return response()->json([
                'status'=>true,
                'message'=> 'Grant collaborator category retrieved successfully',
                'data' => $data
            ], 200);

        } catch (\Throwable $th){
            
            return response()->json([
                'status' => false,
                'message' => 'failed to get grant collaborator category',
                'error' => $th->getMessage()
            ], 500);

        }
    }

    public function getGrantCollaboratorCategoryById($id){
        try{
            $data = GrantCollaboratorCategory::findOrFail($id);

            return response()->json([
                'status'=>true,
                'message'=>'Grant collaborator category found',
                'data'=>$data
            ], 200);
        }catch (\Throwable $th){
            return response()->json([
                'status' => false,
                'message' => 'Grant collaborator category not found',
                'error' => $th->getMessage()
            ], 500);
        }

    }

    public static function createGrantCollaboratorCategory(Request $req)
    {
        try {
            $validated = $req->validate([
                'category_name' => 'required'
            ]);

            $validated = GrantCollaboratorCategory::create($validated);
            
            return response()->json([
                'status' => true,
                'message' => 'Grant collaborator category successfully created',
                'data' => $validated
            ], 200);
        } catch (\Throwable $th){
            return response()->json([
                'status' => false,
                'message' => 'create grant collaborator category failed',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public static function updateGrantCollaboratorCategory(Request $req, string $id)
    {
        try {
            $validated = $req->validate([
                'category_name' => 'required'
            ]);

            $data = GrantCollaboratorCategory::findorFail($id);
            $data->update($validated);

            return response()->json([
                'status'=>true,
                'message'=>'Grant collaborator category successfully updated',
                'data' => $data
            ], 200);
            
        } catch (\Throwable $th){
            return response()->json([
                'status' =>false,
                'message' => 'Update Grant collaborator category failed',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public static function deleteGrantCollaboratorCategory(string $id)
    {
        try {
            $data = GrantCollaboratorCategory::findorFail($id);
            $data->delete();

            return response()->json([
                'status' => true,
                'message' => 'Grant collaborator category deleted'
            ], 200);
        } catch (\Throwable $th){
            return response()->json([
                'status' => false,
                'message' => 'delete Grant collaborator category failed',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
